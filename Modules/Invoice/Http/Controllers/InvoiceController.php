<?php

namespace Modules\Invoice\Http\Controllers;

use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Crypt;
use Modules\Invoice\Http\Requests\InvoiceRequestForm;
use Modules\Invoice\Repositories\Interfaces\InvoiceRepositoryInterface;
use PDF;

class InvoiceController extends Controller
{
    protected $invoiceRepository;

    public function __construct(
        InvoiceRepositoryInterface $invoiceRepository
    )
    {
        $this->invoiceRepository = $invoiceRepository;
    }

    public function index(Request $request)
    {
        $data = $this->invoiceRepository->index($request);
        return view('invoice::index', $data);
    }


    public function create()
    {
        $data = $this->invoiceRepository->createData();
        return view('invoice::create', $data);
    }


    public function store(InvoiceRequestForm $request)
    {

        try {
            $this->invoiceRepository->create($request->all());
            Toastr::success(trans('invoice.Invoice Created Successfully'), trans('common.Success'));
            return redirect()->route('invoice.index');
        } catch (\Throwable $th) {
            Toastr::error(trans('invoice.Invoice Created Failed'), trans('common.Error'));
            return redirect()->route('invoice.index');
        }
    }

    public function show($id)
    {
        if (!is_numeric($id)) {
            $id = Crypt::decrypt($id);
        }
        $data = $this->invoiceRepository->show($id);
        $pdf = PDF::loadView('invoice::invoice', $data)
            ->setOption('fontDir', public_path('/fonts'))
            ->setPaper('A4', 'landscape');
        return $pdf->stream('invoice.pdf');

    }

    public function edit($id)
    {
        $data = $this->invoiceRepository->edit($id);
        if (!$data['edit']) {
            return abort(404);
        }
        return view('invoice::edit', $data);
    }

    public function update(InvoiceRequestForm $request, $id)
    {
        try {
            $this->invoiceRepository->update($id, $request->all());
            Toastr::success(trans('invoice.Invoice Updated Successfully'), trans('common.Success'));
            return redirect()->route('invoice.index');
        } catch (\Throwable $th) {
            Toastr::error(trans('invoice.Invoice Update Failed'), trans('common.Error'));
            return redirect()->route('invoice.index');
        }
    }

    public function billingUpdate(Request $request)
    {
        $this->invoiceRepository->billingUpdate($request->all());
        return response()->json(['msg' => 'Ok']);
    }

    public function billingData(Request $request)
    {
        $billing = $this->invoiceRepository->billingData($request->all());
        return view('invoice::billing_data', compact('billing'));
    }

    public function invoice()
    {
        # code...
    }

    public function destroy($id)
    {
        $data = $this->invoiceRepository->deleteById($id);
        if ($data) {
            Toastr::success(trans('invoice.Invoice Deleted Successfully'), trans('common.Success'));
        } else {
            Toastr::error(trans('invoice.Invoice Delete Failed'), trans('common.Error'));
        }

        return redirect()->route('invoice.index');
    }

    public function getCourse($course_id)
    {
        $data = $this->invoiceRepository->getCourse($course_id);
        return response()->json($data);
    }
}
