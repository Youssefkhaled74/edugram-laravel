<?php

namespace Modules\Setting\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\Filepond;
use Brian2694\Toastr\Facades\Toastr;
use Google_Client;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Modules\AmazonS3\Http\Controllers\AmazonS3Controller;
use Modules\BunnyStorage\Http\Controllers\BunnyStreamController;
use Modules\Setting\Repositories\MediaManagerRepository;
use Modules\Storage\Http\Controllers\StorageController;
use Modules\Storage\Services\SettingValidation;

class MediaManagerController extends Controller
{
    use Filepond;

    protected $mediaManagerRepo;

    public function __construct(MediaManagerRepository $mediaManagerRepo)
    {
        $this->mediaManagerRepo = $mediaManagerRepo;
    }

    public function index(Request $request)
    {
        $files = $this->mediaManagerRepo->getFiles($request);
        return view('setting::storage.index', compact('files'));
    }

    public function create()
    {
        return view('setting::storage.create',);

    }

    public function getfilesForModal(Request $request)
    {
        return response()->json([
            'files' => $this->mediaManagerRepo->getFiles($request)
        ]);
    }

    public function destroy($id)
    {
        $result = $this->mediaManagerRepo->destroy($id);
        if ($result === true) {
            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->back();
        } else {
            Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
            return redirect()->back();
        }
    }

    public function getModal(Request $request)
    {
        return view('setting::storage.media_modal',);
    }

    public function getMediaById(Request $request)
    {
        return $this->mediaManagerRepo->getMediaById($request);
    }

    public function setting()
    {
        $cloud_hosts = ['LocalStorage',
//            'GoogleDrive'
        ];
//        if (isModuleActive('AmazonS3')) {
//            $cloud_hosts[] = 'AmazonS3';
//        }
//        if (isModuleActive('BunnyStorage')) {
//            $cloud_hosts[] = 'BunnyStorage';
//        }
//        if (isModuleActive('Storage')) {
//            $cloud_hosts = array_merge($cloud_hosts, [
//                'DigitalOcean', 'Wasabi', 'Backblaze', 'Dropbox', 'Contabo'
//            ]);
//        }
        return view('setting::storage.setting', compact('cloud_hosts'));
    }

    public function settingUpdate(Request $request)
    {

        $validate_rules = [
            'active_storage' => 'required',
        ];

        $rules = $this->dataValidation($request, $validate_rules);

        $validated = $request->validate($rules, validationMessage($rules));
        try {
            $active = $request->get('active_storage', 'LocalStorage');
            UpdateGeneralSetting('active_storage', $active);
            if ($active == 'AmazonS3') {
                $amazons3 = new AmazonS3Controller();
                $amazons3->store($request);
            } elseif ($active == 'GoogleDrive') {
                $GoogleDrive = new GoogleDriveController(new Google_Client());
                $GoogleDrive->update($request);
            } elseif ($active == 'BunnyStorage') {
                $GoogleDrive = new BunnyStreamController();
                $GoogleDrive->settingUpdateAjax($request);
            } elseif (in_array($request->active_storage, [
                'DigitalOcean', 'Wasabi', 'Backblaze', 'Dropbox', 'Contabo'
            ])) {
                $setting = new StorageController();
                $setting->settingUpdate($validated);
            }

            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->back();
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());

        }
    }

    private function dataValidation($request, $validate_rules)
    {
        if ($request->active_storage == 'BunnyStorage') {
            $controller = new BunnyStreamController();
            return $controller->dataValidation($request->service_type, $validate_rules);
        } elseif ($request->active_storage == 'GoogleDrive') {
            $controller = new GoogleDriveController(new Google_Client());
            return $controller->dataValidation($validate_rules);
        } elseif ($request->active_storage == 'AmazonS3') {
            $controller = new AmazonS3Controller();
            return $controller->dataValidation($validate_rules);
        } elseif (in_array($request->active_storage, [
            'DigitalOcean', 'Wasabi', 'Backblaze', 'Dropbox', 'Contabo'
        ])) {
            $service = new SettingValidation();
            return $service->dataValidation($request->active_storage, $validate_rules);
        }
        return $validate_rules;
    }

    public function store(Request $request)
    {
        $validate_rules = [
            'file' => 'required|file',
        ];
        $request->validate($validate_rules, validationMessage($validate_rules));

        return $this->mediaManagerRepo->store($request->file('file'));

    }

    public function startChunk(Request $request)
    {
        $rawLength = $request->header('Upload-Length', '');
        $name = basename(str_replace('\\', '/', $request->header('Upload-Name', '')));
        abort_unless(ctype_digit($rawLength) && (float) $rawLength <= PHP_INT_MAX && (int) $rawLength > 0 && $name !== '', 422);

        $directory = $this->getBasePath() . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR
            . auth()->id() . DIRECTORY_SEPARATOR . Str::random(32);
        File::makeDirectory($directory, 0775, true);
        file_put_contents($directory . DIRECTORY_SEPARATOR . 'meta.json', json_encode([
            'name' => $name,
            'length' => (int) $rawLength,
        ]));

        return response($this->getServerIdFromPath($directory), 200)
            ->header('Content-Type', 'text/plain');
    }

    public function appendChunk(Request $request)
    {
        $directory = $this->mediaChunkDirectory($request->query('patch', ''));
        $meta = json_decode(file_get_contents($directory . DIRECTORY_SEPARATOR . 'meta.json'), true);
        $offset = $request->header('Upload-Offset', '');
        abort_unless(ctype_digit($offset) && (float) $offset <= PHP_INT_MAX, 422);

        $path = $directory . DIRECTORY_SEPARATOR . 'upload.bin';
        $output = fopen($path, 'c+b');
        abort_unless($output && flock($output, LOCK_EX), 500);

        try {
            $current = fstat($output)['size'];
            abort_unless((int) $offset === $current, 409);
            fseek($output, 0, SEEK_END);
            $input = fopen('php://input', 'rb');
            $written = stream_copy_to_stream($input, $output, 5 * 1048576 + 1);
            fclose($input);
            if ($written === false || $written === 0 || $written > 5 * 1048576 || $current + $written > $meta['length']) {
                ftruncate($output, $current);
                abort(422);
            }
            fflush($output);
        } finally {
            flock($output, LOCK_UN);
            fclose($output);
        }

        return response('', 204)->header('Upload-Offset', (string) ($current + $written));
    }

    public function chunkOffset(Request $request)
    {
        $directory = $this->mediaChunkDirectory($request->query('patch', ''));
        $path = $directory . DIRECTORY_SEPARATOR . 'upload.bin';

        return response('', 200)->header('Upload-Offset', (string) (is_file($path) ? filesize($path) : 0));
    }

    public function finishChunk(Request $request)
    {
        $request->validate(['server_id' => 'required|string']);
        $directory = $this->mediaChunkDirectory($request->input('server_id'));
        $meta = json_decode(file_get_contents($directory . DIRECTORY_SEPARATOR . 'meta.json'), true);
        $path = $directory . DIRECTORY_SEPARATOR . 'upload.bin';
        abort_unless(is_file($path) && filesize($path) === $meta['length'], 422);

        $file = new UploadedFile($path, $meta['name'], null, null, true);
        $media = $this->mediaManagerRepo->store($file);
        File::deleteDirectory($directory);

        return response()->json($media);
    }

    private function mediaChunkDirectory(string $serverId): string
    {
        $directory = $this->getPathFromServerId($serverId);
        $ownerDirectory = $this->getBasePath() . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . auth()->id() . DIRECTORY_SEPARATOR;
        abort_unless(str_starts_with($directory, $ownerDirectory) && is_dir($directory), 404);

        return $directory;
    }


}
