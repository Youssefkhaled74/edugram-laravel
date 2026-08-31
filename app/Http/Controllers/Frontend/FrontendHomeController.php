<?php

namespace App\Http\Controllers\Frontend;


use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Modules\FrontendManage\Entities\FrontPage;
use Modules\Setting\Entities\VersionHistory;


class FrontendHomeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['maintenanceMode', 'onlyAppMode']);
    }

    public function index()
    {
        try {
            if (!\auth()->check()) {
                if (Settings('start_site') == 'loginpage') {
                    return redirect()->route('login');
                }
            }

            $row = FrontPage::where('slug', '/')->first();
            $teachers = User::query()
                ->where('role_id', 2)
                ->where('status', 1)
                ->orderByDesc('total_rating')
                ->orderByDesc('id')
                ->limit(12)
                ->get(['id', 'name', 'image', 'headline']);

            return view(theme('snippets.pages.home_v8_forced'), compact('row', 'teachers'));

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function test()
    {

        dd('ok');
    }

    public function version()
    {
        return VersionHistory::select('version', 'release_date')->get()->pluck('version', 'release_date')->toArray();
    }


}
