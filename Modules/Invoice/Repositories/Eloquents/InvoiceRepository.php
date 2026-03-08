<?php

namespace Modules\Invoice\Repositories\Eloquents;

use App\BillingDetails;
use App\Repositories\Eloquents\BaseRepository;
use App\Traits\SendNotification;
use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Modules\CourseSetting\Entities\Course;
use Modules\Invoice\Entities\Invoice;
use Modules\Invoice\Entities\InvoiceBilling;
use Modules\Invoice\Entities\InvoiceCourse;
use Modules\Invoice\Entities\InvoiceSetting;
use Modules\Invoice\Repositories\Interfaces\InvoiceRepositoryInterface;
use Modules\Invoice\Repositories\Interfaces\SettingsRepositoryInterface;
use Modules\Payment\Entities\Checkout;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceRepository extends BaseRepository implements InvoiceRepositoryInterface
{
    use SendNotification;

    protected $user;
    protected $course;
    protected $setting;
    protected $checkout;
    protected $billingDetails;
    protected $invoiceBilling;
    protected $settingsRepository;

    public function __construct(
        User                        $user,
        Course                      $course,
        Invoice                     $model,
        Checkout                    $checkout,
        InvoiceSetting              $setting,
        BillingDetails              $billingDetails,
        InvoiceBilling              $invoiceBilling,
        SettingsRepositoryInterface $settingsRepository
    )
    {
        parent::__construct($model);
        $this->user = $user;
        $this->course = $course;
        $this->setting = $setting;
        $this->checkout = $checkout;
        $this->billingDetails = $billingDetails;
        $this->invoiceBilling = $invoiceBilling;
        $this->settingsRepository = $settingsRepository;
    }

    public function create(array $payload): ?Model
    {
        $model = $this->model->create($this->formatParam($payload));
        $this->invoiceCourse($payload, $model);
        if (gv($payload, 'status')) {
            $this->sendPaymentLink($model);
        }
        return $model;
    }

    private function formatParam(array $payload, $modelId = null)
    {
        $formatParam = [
            'purchase_price' => gv($payload, 'total')??0,
            'price' => gv($payload, 'total')??0,
            'user_id' => gv($payload, 'student'),
            'payment_type' => gv($payload, 'payment_type'),
            'status' => gv($payload, 'status', 'created'),
        ];
        if (!$modelId) {
            $prefix = session()->get('invoice_prefix')
                ?? $this->settingsRepository->settings()->prefix;
            $lastModelId = $this->model->latest()->value('id') + 1;
            $invoice_number = $prefix . $lastModelId;
            $formatParam['invoice_number'] = $invoice_number;
            $formatParam['tracking'] = getTrx();
        }
        return $formatParam;
    }

    private function invoiceCourse($payload, $model)
    {
        if (gv($payload, 'courses')) {
            $courses = gv($payload, 'courses', []);
            foreach ($courses as $courseId) {
                $course = Course::findOrFail($courseId);
                $invoiceCourse = new InvoiceCourse();
                $invoiceCourse->invoice_id = $model->id;
                $invoiceCourse->tracking = $model->tracking;
                $invoiceCourse->course_id = $course->id;
                $invoiceCourse->instructor_id = $course->user_id;
                $invoiceCourse->user_id = $model->user_id;
                $invoiceCourse->price = $course->discount_price ?? $course->price;
                $invoiceCourse->save();
            }
            $billingDetail = $this->billingDetail($payload, $model->tracking);
            if ($billingDetail) {
                $model->update(['billing_detail_id' => $billingDetail->id]);
            }
        }
        return true;
    }

    private function billingDetail($payload, $tracking)
    {
        $billingAddress = $this->invoiceBilling->where('user_id', $payload['student'])->latest()->first();

        if (!$billingAddress) {
            $user = $this->user->where('id', $payload['student'])->first();
            $billing = $this->invoiceBilling;
            $billing->user_id = $payload['student'];
            $billing->tracking_id = $tracking;
            $billing->first_name = $user->name;
            $billing->country = $user->country;
            $billing->address1 = $user->address;
            $billing->city = $user->cityDetails->name;
            $billing->zip_code = $user->zip_code;
            $billing->phone = $user->phone;
            $billing->email = $user->email;
            $billing->payment_method = null;
            $billing->save();
        } elseif ($billingAddress) {
            $billing = $billingAddress->replicate();
            $billing->tracking_id = $tracking;
            $billing->save();
        }
        return $billing;

    }

    public function update(int $modelId, array $payload): bool
    {
        $model = $this->findById($modelId);
        $updateModel = $model->update($this->formatParam($payload, $modelId));
        if ($updateModel) {
            $this->updateInvoiceCourse($payload, $model);
        }
        return $updateModel;
    }

    private function updateInvoiceCourse($payload, $model)
    {

        if (gv($payload, 'courses')) {
            InvoiceCourse::where('invoice_id', $model->id)->delete();
            $courses = gv($payload, 'courses', []);
            foreach ($courses as $courseId) {
                $course = Course::findOrFail($courseId);
                $invoiceCourse = new InvoiceCourse();
                $invoiceCourse->invoice_id = $model->id;
                $invoiceCourse->tracking = $model->tracking;
                $invoiceCourse->course_id = $course->id;
                $invoiceCourse->instructor_id = $course->user_id;
                $invoiceCourse->user_id = $model->user_id;
                $invoiceCourse->price = $course->discount_price ?? $course->price;
                $invoiceCourse->save();
            }
        }
    }

    public function sendPaymentLink(object $invoice = null)
    {

        $user = $this->user->where('id', $invoice->user_id)->first();
        $file = $this->invoice($invoice->user_id, $invoice, null);
        $current_date = Carbon::now()->format('d-m-Y');
        $domain = SaasDomain();
        $path = 'public/invoice/' . $domain . '/pdf/' . $current_date . '/' . $file;
        if ($invoice) {
            $student_name = $user->name;
            $invoice_number = $invoice->id;
            $payment_link = route('invoice.orderPayment', [Crypt::encrypt($invoice->id)]);

            $this->sendNotification('payment_link', $invoice->user, [
                'invoice' => asset($path),
                'student_name' => $student_name,
                'invoice_number' => $invoice_number,
                'payment_link' => $payment_link
            ]);
        }
    }

    public function invoice(int $student_id, object $invoice = null, object $checkout = null)
    {
        if ($invoice) {
            $invoice = $this->findById($invoice->id);
        }
        if ($checkout) {
            $data['enroll'] = $this->checkout->where('id', $checkout->id)
                ->where('user_id', $student_id)
                ->with('courses', 'user', 'courses.course.enrollUsers')->first();
        }
        $invoice_number = $invoice ? $invoice->id : $checkout->invoice_id;
        $data['user'] = $this->user->where('id', $student_id)->first();
        $data['setting'] = $this->setting->first();
        $data += $this->show($invoice_number);

        $pdf = PDF::loadView('invoice::invoice', $data)->setPaper('A4', 'landscape');
        $current_date = Carbon::now()->format('d-m-Y');
        $domain = SaasDomain();
        $path = 'public/invoice/' . $domain . '/pdf/' . $current_date;

        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0777, true, true);
        }
        $fileName = Str::slug($data['user']->name) . $invoice_number . '_invoice.pdf';
        $pdf->save($path . '/' . $fileName);
        return $fileName;
    }

    public function show($modelId)
    {
        $data = [];
        $model = $this->model->where('id', $modelId)->first();
        $data['model'] = $model;
        $beforeCheckout = $this->invoiceBilling->where('tracking_id', $model->tracking)
            ->where('user_id', $model->user_id)->first();
        $afterCheckout = $this->billingDetails->where('tracking_id', $model->tracking)
            ->where('user_id', $model->user_id)->first();
        $data['billings'] = $afterCheckout ?? $beforeCheckout;
        $data += $this->settingsRepository->index();

        return $data;
    }

    public function index($request = null): array
    {
        $data = [];
        $data['search'] = [
            'start_date' => $request->start_date ?? null,
            'end_date' => $request->end_date ?? null,
            'status' => $request->status ?? null,
            'payment_type' => $request->payment_type ?? null,
        ];
        if ($data['search']['start_date']) {
        }
        $data['invoices'] = $this->filterInvoice($request);
        return $data;
    }

    private function filterInvoice($request)
    {
        $startDate = $request->start_date ? date('Y-m-d', strtotime($request->start_date)) : null;
        $endDate = $request->end_date ? date('Y-m-d', strtotime($request->end_date)) : null;
        $invoice = $this->model->when($startDate && !$endDate, function ($q) use ($startDate) {
            $q->whereDate('created_at', $startDate);
        })->when($endDate && !$startDate, function ($q) use ($endDate) {
            $q->whereDate('created_at', $endDate);
        })->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate, $endDate]);
        })->when($request->status, function ($q) use ($request) {
            $q->where('status', $request->status);
        })->when($request->payment_type, function ($q) use ($request) {
            $q->where('payment_type', $request->payment_type);
        })->when(auth()->user()->role_id != 1, function ($q) {
            $q->where('created_by', auth()->user()->id);
        })->withCount('courses')->with('user')->latest()->get();

        return $invoice;
    }

    public function edit($modelId): array
    {
        $data['edit'] = $this->model->where('id', $modelId)->when(auth()->user()->role_id != 1, function ($query) {
            $query->where('created_by', auth()->user()->id);
        })->first();
        if ($data['edit']->status == 'paid') {
            Toastr::warning(trans('invoice.Already Paid, You Cant Edit/Update'), trans('common.Warning'));
            return redirect()->back()->send();
        }
        $data += $this->createData();
        return $data;
    }

    public function createData(): array
    {
        $data['courses'] = $this->course->where('status', 1)->select('id', 'title', 'type')->get();
        $query = $this->user->where('status', 1)->select('id', 'name');
        if (isModuleActive('LmsSaas')) {
            $query->where('lms_id', app('institute')->id);
        } else {
            $query->where('lms_id', 1);
        }
        if (isModuleActive('UserType')) {
            $query->whereHas('userRoles', function ($q) {
                $q->where('role_id', 3);
            });
        } else {
            $query->where('role_id', 3);
        }
        $data['students'] = $query->get();
        return $data;
    }

    public function sendInvoice(int $student_id, object $invoice = null, object $checkout = null)
    {
        $user = $this->user->where('id', $student_id)->first();

        $file = $this->invoice($student_id, null, $checkout);

        $current_date = Carbon::now()->format('d-m-Y');
        $domain = SaasDomain();
        $path = 'public/invoice/' . $domain . '/pdf/' . $current_date . '/' . $file;
        if ($checkout) {
            $this->sendNotification('invoice', $checkout->user, [
                'invoice' => $path
            ]);
        }
    }

    public function getCourse($course_id)
    {
        return $this->course->where('id', $course_id)->first();
    }

    public function deleteById(int $modelId): bool
    {
        $model = $this->model->where('id', $modelId)->when(auth()->user()->role_id != 1, function ($query) {
            $query->where('created_by', auth()->user()->id);
        })->where('status', '!=', 'paid')->first();
        if ($model) {
            $checkout = $this->checkout->where('invoice_id', $modelId)->where('type', 'invoice')->first();
            if ($checkout) {
                $billing = $this->billingDetails->where('id', $checkout->billing_detail_id)->where('tracking_id', $checkout->tracking)->first();
                $billing->delete();
                $checkout->delete();
            }
            $model->delete();
            return true;
        }
        return false;
    }

    public function getInvoice($payment_type = null)
    {
        return $this->model->withCount('courses')->when($payment_type, function ($query) use ($payment_type) {
            $query->where('payment_type', $payment_type);
        })->latest()->get();
    }

    public function billingUpdate(array $payload)
    {
        $formatParam = [
            'first_name' => gv($payload, 'first_name'),
            'last_name' => gv($payload, 'last_name'),
            'company_name' => gv($payload, 'company_name'),
            'country' => gv($payload, 'country'),
            'city' => gv($payload, 'city'),
            'state' => gv($payload, 'state'),
            'address1' => gv($payload, 'address1'),
            'address2' => gv($payload, 'address2'),
            'zip_code' => gv($payload, 'zip_code'),
            'details' => gv($payload, 'details'),
        ];


        $billing = $this->billingDetails->where('id', $payload['billing_detail_id'])
            ->where('tracking_id', $payload['tracking_id'])->where('user_id', auth()->user()->id)->first();
        if (!$billing) {
            Toastr::error(trans('common.Operation failed'), trans('common.Error'));
            return redirect()->back()->send();
        }
        $billing->update($formatParam);
    }

    public function billingData(array $payload)
    {
        return $billing = $this->billingDetails->where('id', $payload['billing_detail_id'])
            ->where('tracking_id', $payload['tracking_id'])->where('user_id', auth()->user()->id)->first();
    }
}
