<?php

namespace Modules\Jitsi\Http\Controllers;

use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Jitsi\Entities\JitsiSetting;

class JitsiSettingController extends Controller
{
    public function settings()
    {
        $setting = JitsiSetting::getData();

        return view('jitsi::settings', compact('setting'));
    }

    public function updateSettings(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        $request->validate([
            'jitsi_server' => 'required',
        ]);

        try {
            JitsiSetting::updateOrCreate([
                'lms_id' => SaasInstitute()->id,
            ], [
                'jitsi_server' => strtolower($request->get('jitsi_server')),
            ]);

            Toastr::success('Jitsi Setting updated successfully !', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            Toastr::error($e->getMessage(), trans('common.Failed'));
            return redirect()->back();
        }
    }


}
