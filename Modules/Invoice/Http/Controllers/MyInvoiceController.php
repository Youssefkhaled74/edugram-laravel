<?php

namespace Modules\Invoice\Http\Controllers;

use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Modules\Invoice\Repositories\Interfaces\InvoiceRepositoryInterface;

class MyInvoiceController extends Controller
{
    protected $inventoryRepository;

    public function __construct(
        InvoiceRepositoryInterface $inventoryRepository
    )
    {
        $this->inventoryRepository = $inventoryRepository;
    }

    public function index()
    {

        return view(theme('pages.invoice_page'));
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


    public function payment($invoice_id)
    {
        try {
            if (!auth()->user()) {
                session(['redirectTo' => route('invoice.orderPayment', $invoice_id)]);
                return \redirect(route('login'));
            }
            $id = Crypt::decrypt($invoice_id);
            $invoice = $this->inventoryRepository->findById($id);
            if (Auth::user()->id != $invoice->user_id) {
                abort(403, trans('auth.Permission denied'));
            }
            if ($invoice->offlinePayment) {
                Toastr::warning(trans('common.Operation failed'), trans('common.Warning'));
                return redirect('/');
            }
            if ($invoice && count($invoice->courses) == 0) {
                return redirect('/');
            }
            return view(theme('pages.payment'), compact('invoice'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());

        }
    }

}
