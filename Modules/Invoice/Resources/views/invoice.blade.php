<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title> {{ $model->invoice_number }}- {{ __('invoice.Invoice') }}</title>
</head>
<style>

    @php
        $pdf_font= Settings('datatable_default_font');
        if(!$pdf_font || !file_exists(base_path('public/fonts/DejaVuSans.ttf'))){
            $pdf_font =asset('public/fonts/DejaVuSans.ttf');
        }else{
            $pdf_font = asset(Settings('datatable_default_font'));
        }
    @endphp

     @font-face {
        font-family: 'PDFFont';
        src: url('{{$pdf_font}}') format('truetype');
    }

    *{
        font-family: "PDFFont", Arial, sans-serif;
    }

    .left {
        widows: 30%;
        float: left;
    }

    .right {
        widows: 70%;
        float: right;
    }

    .transcript_headContent {
        border-top: 1px solid #E3E6EF;
        padding-top: 30px;
        margin-bottom: 40px;

    }


    .transcript_head {
        margin-bottom: 20px;
        height: 100px;
    }


    .transcript_head h4 {
        font-size: 20px;
        line-height: 1.5;
        margin-bottom: 15px;
    }


    .transcript_head p {
        font-size: 22px;
        line-height: 1.18182;
        margin-bottom: 15px;
    }


    .transcript_head address {
        margin-bottom: 0px;
    }

    .transcript_head address p {
        font-size: 16px;
        line-height: 1.5;
    }

    .transcript_head address p:not(:last-child) {
        margin-bottom: 0px;
    }


    .transcript_table h4 {
        font-size: 20px;
        line-height: 1.5;
        margin-bottom: 20px;
    }


    .transcript_table:not(:last-child) {
        margin-bottom: 30px;
    }


    .transcript_table table {
        margin-bottom: 0px;
    }


    .transcript_table table tr th {
        font-weight: 500;
    }


    .transcript_table table tr td,
    .transcript_table table tr th {
        border: 1px solid #E3E6EF;
        padding: 10px 15px;
    }

    .transcript_content h4 {
        margin-bottom: 25px;
    }


    .transcript_content p {
        font-size: 16px;
        line-height: 1.5;
    }

    .row {
        display: flex;
        flex-wrap: wrap;
        margin-right: -15px;
        margin-left: -15px;
    }

    .row > * {
        padding-left: 15px;
        padding-right: 15px;
    }

    .col-md-12 {
        flex: 0 0 auto;
        width: 100%;
    }

    .justify-content-between {
        justify-content: space-between !important;
    }

    .d-flex {
        display: flex !important;
    }

    .text-end {
        text-align: right !important;
    }

    .flex-grow-1 {
        flex-grow: 1 !important;
    }

    .overflow-auto {
        overflow: auto !important;
    }

    .table {
        width: 100%;
        margin-bottom: 1rem;
        color: #212529;
        vertical-align: top;
        border-color: #dee2e6;
    }

    table {
        caption-side: bottom;
        border-collapse: collapse;
    }

    .table > thead {
        vertical-align: bottom;
    }

    /*==== transcript & invoice end ====*/
</style>

<body>

<div class="transcript">

    <div class="transcript_head">
        <div class="left">
            <div id="logo">
                <img src="{{ getLogoImage(Settings('logo')) }}" alt="" width="180">
            </div>
        </div>
        <div class="right">
            <p>{{ Settings('site_title') }}</p>
            <address>
                <p>{{ Settings('address') }}</p>
            </address>
        </div>
    </div>
    <div class="transcript_headContent">
        <div class="d-flex">
            <div class="flex-grow-1" style="float: left; width:50%">
                <p>{{ __('invoice.Invoice Number') }}: {{ $model->invoice_number }}</p>
                <p>Date: {{ showDate($model->created_at) }}</p>
                <p>{{ __('common.Status') }}: {{ __('invoice.' .ucfirst($model->status)) }}</p>
            </div>
            <div class="flex-grow-1">
              <div class="row">
                  @if (@$billings->first_name)
                      <p>{{ __('student.Name') }}: {{ @$billings->first_name . ' ' . @$billings->last_name }}</p>
                  @endif
                  @if (@$billings->email)
                      <p>{{ __('student.Email') }}: {{ @$billings->email }}</p>
                  @endif
                  @if (@$billings->phone)
                      <p>{{ __('student.Phone') }}: {{ @$billings->phone }}</p>
                  @endif
                  @if (@$billings->company_name)
                      <p>{{__('frontend.Company Name')}}: {{ @$billings->company_name }}</p>
                  @endif

                  {{ @$billings->bill->address1 }} {{ @$billings->bill->address2 }}
                  {{ @$billings->bill->cityDetails->name }}
                  {{ @$billings->bill->zip_code }}
                  {{ @$billings->bill->countryDetails->name }}
              </div>
            </div>
        </div>
    </div>

    <div class="transcript_table">
        <div class="overflow-auto">
            @php
                $total = 0;
            @endphp
            @if (isset($model->courses))

                <table class="table">
                    <thead>
                    <tr>
                        <td>#</td>
                        <td>{{ __('invoice.Subscription/Product Title') }}</td>
                        <td>{{__('invoice.Qty')}}</td>
                        <td>{{__('invoice.Unit Price')}}</td>
                        <td>{{__('invoice.Total Price')}}</td>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($model->courses as $key => $item)
                        @php
                            $price = $item->course->discount_price != 0 ? $item->course->discount_price : $item->course->price;
                            $total = $total + $price;
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->course->title }}</td>
                            <td>1</td>
                            <td>{{ getPriceFormat($price) }}</td>
                            <td>{{ getPriceFormat($price) }}</td>

                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <td class="border-0"></td>
                        <td class="border-0"></td>
                        <td class="border-0"></td>
                        <td>{{__('common.Total')}}</td>
                        <td>{{ (showPrice($total)) }}</td>
                    </tr>
                    </tfoot>
                </table>
            @endif
        </div>
    </div>
    <div class="transcript_content w-80 " style="margin-top: 20px;text-align: center">
        {{ isset($settings) ? $settings->footer_text : '' }}
    </div>

</div>
</body>

</html>
