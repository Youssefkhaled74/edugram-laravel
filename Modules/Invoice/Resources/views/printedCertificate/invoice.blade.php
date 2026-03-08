<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title> 00{{ $model->id }}- {{ __('invoice.Printed Certificate') }}</title>
    <link rel="stylesheet" href="{{ asset('public/backend/boostrap/bootstrap.min.css') }}{{assetVersion()}}">
</head>
<style>
    /*==== transcript & invoice start ====*/
    .transcript {
        padding: 10px;
    }

    .transcript_headContent {
        border-top: 1px solid #E3E6EF;
        padding-top: 30px;
        margin-bottom: 40px;
    }

    .transcript_head {
        margin-bottom: 20px;
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

    /*==== transcript & invoice end ====*/
</style>

<body>

<div class="transcript">
    <div class="">
        <div class="row">
            <div class="col-md-12">
                <div class="transcript_head d-flex justify-content-between reverse-none">
                    <table class="table">
                        <thead>
                        <td>
                            <div class="left">
                                <div id="logo">
                                    <img src="{{ getLogoImage(Settings('logo')) }}" alt=""
                                         width="180">
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="right text-end">
                                <p>{{ Settings('site_title') }}</p>
                                <address>
                                    <p>{{ Settings('address') }}</p>
                                </address>
                            </div>
                        </td>
                        </thead>
                    </table>
                </div>
                <div class="transcript_headContent">

                    <table class="table">
                        <thead>
                        <td>
                            <div class="" style="margin: 0;padding:0;">
                                <p>{{ __('invoice.Invoice Number') }}: 00{{ $model->id }}</p>
                                <p>Date: {{ showDate($model->created_at) }}</p>
                                <p></p>
                                <p></p>
                                <p></p>
                            </div>
                        </td>
                        <td>
                            <div class="" style="margin: 0;padding:0;">
                                <p>{{ @$billings->first_name . ' ' . @$billings->last_name }}</p>
                                @if (@$billings->email)
                                    <p>{{ @$billings->email }}</p>
                                @endif
                                @if (@$billings->address1 || @$billings->address2)
                                    <p>{{ @$billings->address1 }} <br>
                                        {{ @$billings->address2 ? '#' . @$billings->address2 : '' }} </p>
                                @endif

                                @if(@$billings->city)
                                    {{ @$billings->cityDetails->name }}
                                @endif
                                @if (@$billings->country)
                                    <p>{{ @$billings->countryDetails->name }}</p>
                                @endif
                            </div>
                        </td>
                        </thead>
                    </table>

                </div>

                <div class="transcript_table">
                    <div class="overflow-auto">


                        <table class="table">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Certificate/Course Title</th>
                                <th>Qty</th>
                                <th> Price</th>
                                <th>Total Price</th>
                            </tr>
                            </thead>
                            <tbody>


                            <tr>
                                <td>1</td>
                                <td> {{ __('invoice.Printed Certificate') }} <br>{{ $model->course->title }}
                                </td>
                                <td>1</td>
                                <td>{{ getPriceFormat($model->price) }}</td>
                                <td>{{ getPriceFormat($model->price) }}</td>

                            </tr>

                            </tbody>
                            <tfoot>
                            <tr>
                                <td class="border-0"></td>
                                <td class="border-0"></td>
                                <td class="border-0"></td>
                                <td><strong>Total</strong></td>
                                <td><Strong>{{ getPriceFormat($model->price) }}</Strong></td>
                            </tr>
                            </tfoot>
                        </table>

                    </div>
                </div>
                <div class="transcript_content w-80">

                </div>
            </div>
        </div>
    </div>
</div>
</body>

</html>
