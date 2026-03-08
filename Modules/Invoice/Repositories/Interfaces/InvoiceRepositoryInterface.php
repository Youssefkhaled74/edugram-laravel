<?php

namespace Modules\Invoice\Repositories\Interfaces;

use App\Repositories\Interfaces\EloquentRepositoryInterface;
use Illuminate\Contracts\Support\Jsonable;

interface InvoiceRepositoryInterface extends EloquentRepositoryInterface
{
    public function index():array;
    public function createData():array;
    public function show($modelId);
    public function billingUpdate(array $payload);
    public function billingData(array $payload);
    public function getCourse($course_id);
    public function getInvoice($payment_type = null);
    public function edit($modelId):array;
    public function sendPaymentLink(object $invoice = null);
    public function invoice(int $student_id, object $invoice = null, object $checkout = null);
    public function sendInvoice(int $student_id, object $invoice = null, object $checkout = null);
}
