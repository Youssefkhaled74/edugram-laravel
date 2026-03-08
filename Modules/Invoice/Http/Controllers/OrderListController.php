<?php

namespace Modules\Invoice\Http\Controllers;

use PDF;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\Support\Renderable;
use Modules\Invoice\Repositories\Interfaces\OrderCertificateRepositoryInterface;
use Throwable;

class OrderListController extends Controller
{
    protected $orderCertificateRepository;

    public function __construct(
        OrderCertificateRepositoryInterface $orderCertificateRepository
    )
    {
        $this->orderCertificateRepository = $orderCertificateRepository;
    }

    public function index(Request $request)
    {
        $data = $this->orderCertificateRepository->index($request);
        return view('invoice::printedCertificate.order_list', $data);
    }

    public function orderNow($certificate_id)
    {
        $data = $this->orderCertificateRepository->orderNow($certificate_id);
        $invoice = null;
        return view(theme('pages.payment'), compact('invoice'));
    }

    public function shipped($id)
    {
        try {
            $this->orderCertificateRepository->changesStatus($id, 'shipped');
            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->route('prc.order.index');
        } catch (Throwable $th) {
            Toastr::error(trans('common.Operation failed'), trans('common.Error'));
            return redirect()->route('prc.order.index');
        }
    }

    public function pdfPrint($id)
    {
        $data = $this->orderCertificateRepository->pdfPrint($id);
         $pdf = PDF::loadView('invoice::printedCertificate.invoice', $data)->setPaper('A4', 'landscape');
        return $pdf->stream('billing.pdf');
    }



    public function show($id)
    {
        return view('invoice::show');
    }


    public function edit($id)
    {
        return view('invoice::edit');
    }
}
