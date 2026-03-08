<?php

namespace Modules\AmazonS3\Http\Controllers;

use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

class AmazonS3Controller extends Controller
{
    public static function s3Upload($storageDisk, $fileContentsBase64, $fileExtension)
    {
        $fileIdentifier = uniqid();
        $fileNameForDB = "files/{$fileIdentifier}.{$fileExtension}";

        Storage::disk($storageDisk)->put($fileNameForDB, base64_decode($fileContentsBase64), 'public');
        return Storage::disk($storageDisk)->url($fileNameForDB);
    }

    public function index()
    {
        return view('amazons3::s3setting');

    }

    public function storeFile($file)
    {
        $filePath = '/';
        $path = $file->store($filePath, 's3');
        $url = Storage::disk('s3')->url($path);
        return $url;
    }

    public function store(Request $request)
    {
        $access_key_id = $request->access_key_id;
        $secret_key = $request->secret_key;
        $default_region = $request->default_region;
        $bucket = $request->bucket;

        if (!empty($access_key_id)) {
            SaasEnvSetting(SaasDomain(), 'AWS_ACCESS_KEY_ID', $access_key_id);
        }

        if (!empty($secret_key)) {
            SaasEnvSetting(SaasDomain(), 'AWS_SECRET_ACCESS_KEY', $secret_key);
        }
        if (!empty($default_region)) {
            SaasEnvSetting(SaasDomain(), 'AWS_DEFAULT_REGION', $default_region);
        }
        if (!empty($bucket)) {
            SaasEnvSetting(SaasDomain(), 'AWS_BUCKET', $bucket);
        }
        Toastr::success(trans('common.Operation successful'), trans('common.Success'));
        return redirect()->back();
    }

    public function deleteFile($url)
    {
        try {
            $url = explode('images', $url);
            $path = '/images' . $url[1];
            return Storage::disk('s3')->delete($path);
        } catch (\Exception $exception) {
            return false;
        }
    }

    public function dataValidation($validate_rules)
    {
        $validate_rules['access_key_id'] = 'required';
        $validate_rules['secret_key'] = 'required';
        $validate_rules['default_region'] = 'required';
        $validate_rules['bucket'] = 'required';
        return $validate_rules;
    }
}
