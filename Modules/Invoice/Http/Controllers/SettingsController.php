<?php

namespace Modules\Invoice\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Support\Renderable;
use Modules\Invoice\Http\Requests\SettingRequestForm;
use Modules\Invoice\Repositories\Interfaces\SettingsRepositoryInterface;
use Throwable;

class SettingsController extends Controller
{
    protected $settingRepository;

    public function __construct(
        SettingsRepositoryInterface $settingRepository
    )
    {
        $this->settingRepository = $settingRepository;
    }

    public function index()
    {
        $data = $this->settingRepository->index();
        return view('invoice::settings', $data);
    }


    public function create()
    {
        return view('invoice::create');
    }
    public function show($id)
    {
        return view('invoice::show');
    }


    public function edit($id)
    {
        return view('invoice::edit');
    }

    public function update(SettingRequestForm $request, $id)
    {
        try {
            $this->settingRepository->update($id, $request->all());
            Toastr::success(trans('invoice.Setting Update Successfully'), trans('common.Success'));
            return redirect()->back();
            // return response()->json(['msg'=>trans('invoice.Setting Update Successfully')]);
        } catch (Throwable $th) {
            return response()->json(['msg' => trans('invoice.Setting Update Failed')]);
        }
    }
}
