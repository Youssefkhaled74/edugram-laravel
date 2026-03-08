<?php

namespace Modules\Invoice\Repositories\Eloquents;

use App\BillingDetails;
use App\Repositories\Eloquents\BaseRepository;
use App\Traits\SendNotification;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Modules\Certificate\Entities\CertificateRecord;
use Modules\Invoice\Entities\OrderCertificate;
use Modules\Invoice\Repositories\Interfaces\OrderCertificateRepositoryInterface;
use Modules\Invoice\Repositories\Interfaces\PrintedCertificateRepositoryInterface;

class OrderCertificateRepository extends BaseRepository implements OrderCertificateRepositoryInterface
{
    use SendNotification;

    protected $certificateRecord;
    protected $printedCertificate;
    protected $billingDetails;
    protected $printedCertificateRepository;

    public function __construct(
        OrderCertificate                      $model,
        BillingDetails                        $billingDetails,
        CertificateRecord                     $certificateRecord,
        PrintedCertificateRepositoryInterface $printedCertificateRepository

    )
    {
        parent::__construct($model);
        $this->billingDetails = $billingDetails;
        $this->certificateRecord = $certificateRecord;
        $this->printedCertificateRepository = $printedCertificateRepository;
    }

    public function index($request = null): array
    {
        $data = [];
        $data['search'] = [
            'start_date' => $request->start_date ?? null,
            'end_date' => $request->end_date ?? null,
            'status' => $request->status ?? null
        ];
        $data['models'] = $this->filter($request);
        return $data;
    }

    public function create(array $payload): ?Model
    {
        return $this->model->create($this->formatParam($payload));
    }

    private function formatParam(array $payload = [], $certificate_number = null, $modelId = null)
    {
        $setting = $this->printedCertificateRepository->index();
        $formatParam = [
            'tracking' => getTrx(),
            'price' => $setting->price,
            'certificate_id' => $certificate_number ?? null,
            'user_id' => auth()->user()->id,
            'status' => gv($payload, 'status'),
            'course_id' => gv($payload, 'course_id')
        ];

        return $formatParam;
    }

    public function changesStatus(int $modelId, string $status = null): bool
    {
        $model = $this->model->where('id', $modelId)->update([
            'status' => $status,
            'updated_at' => Carbon::now(),
            'accepted' => auth()->user()->id
        ]);
        $address = null;
        $model = $this->findById($modelId);
        $billing = $this->billingDetails->where('tracking_id', $model->tracking)->where('user_id', $model->user_id)->first();
        if ($billing) {
            $address = $billing->address . ' ' . $billing->stateDetails->name . ' ' . $billing->cityDetails->name . ' ' . $billing->zip_code . ' ' . $billing->countryDetails->name;
        }

        $this->sendNotification('certificate_shipped', $model->user, [
            'student_name' => $model->user->name,
            'course_name' => $model->course->title,
            'address' => $address
        ]);
        if (!$model) {
            return false;
        }
        return true;

    }

    public function orderNow($certificate_number)
    {
        session()->forget('certificate_order');
        $certificate_record = CertificateRecord::where('certificate_id', $certificate_number)->first();
        $alreadyOrdered = $this->model->where('certificate_id', $certificate_number)->where('user_id', auth()->user()->role_id)->where('payment_status', 1)->first();

        if ($alreadyOrdered || !$certificate_record) {
            Toastr::error(trans('common.Operation failed'), trans('common.Success'));
            return redirect()->back()->send();
        }

        $setting = $this->printedCertificateRepository->index();
        $payload['status'] = 'pending';
        $check = $this->model->where('user_id', auth()->id())
            ->where('status', 'pending')->first();
        if ($check) {
            $model = $this->findById($check->id);
            $model->price = $setting->price;
            $model->certificate_id = $certificate_number;
            $model->course_id = $certificate_record->course_id;
            $model->status = gv($payload, 'status');
            $model->save();
            $certificate_order = $model;
        } else {
            $payload['course_id'] = $certificate_record->course_id;
            $certificate_order = $this->model->create($this->formatParam($payload, $certificate_number));
        }
        session()->put('certificate_order', $certificate_order);
    }

    public function pdfPrint($modelId)
    {
        $data = [];
        $model = $this->model->where('id', $modelId)->first();
        $data['model'] = $model;
        $billing = $this->billingDetails->where('tracking_id', $model->tracking)
            ->where('user_id', $model->user_id)->first();
        $data['billings'] = $billing;

        return $data;
    }

    private function filter($request)
    {
        $startDate = $request->start_date ? Carbon::createFromFormat(getActivePhpDateFormat(), $request->start_date)->startOfDay() : null;
        $endDate = $request->end_date ? Carbon::createFromFormat(getActivePhpDateFormat(), $request->end_date)->endOfDay() : null;

        return $this->model->with('user')
            ->when($request->status, function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($startDate, function ($q) use ($startDate, $endDate) {
                if ($endDate) {
                    $q->whereBetween('created_at', [$startDate, $endDate]);
                } else {
                    $q->whereDate('created_at', $startDate);
                }
            })
            ->when($endDate && !$startDate, function ($q) use ($endDate) {
                $q->whereDate('created_at', $endDate);
            })
            ->get();

     }

    public function dataTable($request)
    {
        $model = $this->filter($request);
    }
}
