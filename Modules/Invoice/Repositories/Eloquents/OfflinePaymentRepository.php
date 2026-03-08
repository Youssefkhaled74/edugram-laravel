<?php

namespace Modules\Invoice\Repositories\Eloquents;

use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\PaymentController;
use App\Repositories\Eloquents\BaseRepository;
use Brian2694\Toastr\Facades\Toastr;
use Modules\Invoice\Entities\InvoiceOfflinePayment;
use Modules\Invoice\Repositories\Interfaces\OfflinePaymentRepositoryInterface;
use Modules\Payment\Entities\Checkout;

class OfflinePaymentRepository extends BaseRepository implements OfflinePaymentRepositoryInterface
{
    protected $checkout;
    protected $offlinePayment;
    protected $paymentController;
    public function __construct(
        InvoiceOfflinePayment $model,
        Checkout $checkout,
        PaymentController $paymentController
    ) {
        parent::__construct($model);
        $this->checkout = $checkout;
        $this->paymentController = $paymentController;
    }
    public function index(): array
    {
        $data = [];
        $data['models'] = $this->offlinePayments($status = 0);
        return $data;        
    }
    public function create(array $payload): ?Model
    {
        $checkout = null;
        if (gv($payload, 'tracking')) {
            $checkout = $this->checkout->where('tracking', $payload['tracking'])->where('user_id', auth()->user()->id)->first();
            if(!$checkout) {
                Toastr::error(trans('common.Operation failed'), trans('common.Error'));
                return redirect()->route('/')->send();
            }
           
        }

        return $this->model->create($this->formatParam($payload, $modelId = null, $checkout));
     
    }
    private function formatParam($payload, $modelId = null, $checkout)
    {
     
        $upload_path = 'public/uploads/bankpayment/';
        $formatParam  = [           
           'invoice_id' => $checkout->invoice_id,
           'checkout_id' => $checkout->id,
           'user_id' => auth()->user()->id,
           'bank_name' => gv($payload, 'bank_name'),
           'branch_name' => gv($payload, 'branch_name'),
           'account_number' => gv($payload, 'account_number'),
           'account_holder' => gv($payload, 'account_holder'),
           'account_type' => gv($payload, 'type'),
           'amount' => $checkout->price,
           'image'=>fileUpload(gv($payload, 'image'), $upload_path),
        ];
        return $formatParam;
    }
    public function deleteAble($modelId)
    {
        return $this->findById($modelId);
    }
    public function deleteById(int $modelId): bool
    {
        $model = $this->deleteAble($modelId);
        if ($model) {
            $model->delete();
        }
        return true;
    }
    public function approve($modelId)
    {
        $model = $this->findById($modelId);
        $approve = $this->paymentController->payWithGateWay($response=[], "Bank Payment", $user = $model->invoice->user ,$model->invoice);
        if($approve) {
            $model->update([
                'accept_or_reject'=>auth()->user()->id,
                'status'=>1,
            ]);
            return true;
        }
        return false;
    }
    public function offlinePayments($status = null)
    {
        return $this->model->with('user')->where('status', $status)->get();
    }
}
