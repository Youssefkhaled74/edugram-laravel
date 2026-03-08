<?php

namespace Modules\Invoice\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Support\Renderable;
use Modules\Invoice\Http\Requests\PrintedCertificateRequestForm;
use Modules\Invoice\Repositories\Interfaces\PrintedCertificateRepositoryInterface;

class PrcSettingsController extends Controller
{
    protected $printedCertificate;
    public function __construct(
        PrintedCertificateRepositoryInterface $printedCertificate
    )
    {
        $this->printedCertificate = $printedCertificate;
    }
    public function index()
    {
        $model = $this->printedCertificate->index();
        return view('invoice::printedCertificate.settings', compact('model'));
    }

    public function update(PrintedCertificateRequestForm $request, $id)
    {
        try {
            $data = $this->printedCertificate->update($id, $request->validated());
            Toastr::success(trans('common.Operation Successfully'), trans('common.Success'));
            return redirect()->route('prc.index');
        } catch (\Throwable $th) {
            //throw $th;
            Toastr::error(trans('common.Operation Failed'), trans('common.Error'));
            return redirect()->route('prc.index');
        }
    }
}
