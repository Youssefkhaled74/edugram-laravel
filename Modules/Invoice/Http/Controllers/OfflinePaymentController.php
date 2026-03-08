<?php

namespace Modules\Invoice\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Support\Renderable;
use Modules\Invoice\Repositories\Interfaces\OfflinePaymentRepositoryInterface;

class OfflinePaymentController extends Controller
{
    protected $offlineRepository;
    public function __construct(
        OfflinePaymentRepositoryInterface $offlineRepository
    ) {
        $this->offlineRepository = $offlineRepository;
    }
    public function index()
    {
        $data = $this->offlineRepository->index();
        return view('invoice::payment.index', $data);
    }

    public function create()
    {
        return view('invoice::create');
    }


    public function store(Request $request)
    {
        try {
            $this->offlineRepository->create($request->all());
            Toastr::success(trans('invoice.Payment Successful'), trans('common.Success'));
            return redirect()->route('myInvoice');
        } catch (\Throwable $th) {
            throw $th;
            Toastr::error(trans('invoice.Payment Failed'), trans('common.Error'));
            return redirect()->route('myInvoice');
        }
    }


    public function show($id)
    {
        return view('invoice::show');
    }

    public function approve($id)
    {
        try {
            $this->offlineRepository->approve($id);
            Toastr::success(trans('invoice.Payment Successful'), trans('common.Success'));
            return redirect()->back();
        } catch (\Throwable $th) {
            Toastr::error(trans('invoice.Payment Failed'), trans('common.Error'));
            return redirect()->back();
        }
    }


    public function destroy($id)
    {
        try {
            $this->offlineRepository->deleteById($id);
            Toastr::success(trans('common.Operation Successful'), trans('common.Common'));
            return redirect()->route('invoice.offline-payment');
        } catch (\Throwable $th) {
            Toastr::error(trans('common.Operation Failed'), trans('common.Error'));
            return redirect()->route('invoice.offline-payment');
        }
    }
}
