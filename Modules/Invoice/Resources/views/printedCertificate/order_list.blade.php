@extends('backend.master')
@php
    $table_name = 'invoices';
@endphp
@section('table')
    {{ $table_name }}
@endsection
@push('styles')
    <style>
        .created {
            color: #ffffda;
            background: #e1cc13;
            font-size: 13px !important;
            font-weight: 500 !important;
            border: 0;
            display: inline-block;
            border-radius: 10px;
            padding: 7px 21px;
            white-space: nowrap;
            line-height: 1.2;
            text-transform: capitalize;
        }

        .send {
            color: #ffffda;
            background: #4BCF90 !important;
            /* background: rgba(75, 207, 144, 0.1); */
            font-size: 13px !important;
            font-weight: 500 !important;
            border: 0;
            display: inline-block;
            border-radius: 10px;
            padding: 7px 21px;
            white-space: nowrap;
            line-height: 1.2;
            text-transform: capitalize;
        }

        .paid {
            color: #ffffda;
            background: #0cc10c;
            font-size: 13px !important;
            font-weight: 500 !important;
            border: 0;
            display: inline-block;
            border-radius: 10px;
            padding: 7px 21px;
            white-space: nowrap;
            line-height: 1.2;
            text-transform: capitalize;
        }

        .online {
            color: #ffffda;
            background: #0cc10c;
            font-size: 13px !important;
            font-weight: 500 !important;
            border: 0;
            display: inline-block;
            border-radius: 10px;
            padding: 7px 21px;
            white-space: nowrap;
            line-height: 1.2;
            text-transform: capitalize;

        }

        .offline {
            color: #ffffda;
            background: #eb890f;
            font-size: 13px !important;
            font-weight: 500 !important;
            border: 0;
            display: inline-block;
            border-radius: 10px;
            padding: 7px 21px;
            white-space: nowrap;
            line-height: 1.2;
            text-transform: capitalize;
        }
    </style>
@endpush
@section('mainContent')
    <section class="sms-breadcrumb mb-20 white-box">
        <div class="container-fluid">
            <div class="d-flex flex-wrap justify-content-between">
                <h1>{{ __('invoice.Certificate Order List') }}</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">{{ __('common.Dashboard') }}</a>
                    <a href="#">{{ __('invoice.Printed Certificate') }}</a>
                    <a class="active" href="{{ route('prc.order.index') }}">{{ __('invoice.Order List') }}</a>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="white_box mb_20">
                        <div class="white_box_tittle list_header main-title mb-0">
                            <h3>{{ __('courses.Advanced Filter') }} </h3>
                        </div>
                        <form method="POST" action="{{ route('prc.order.index.search') }}">
                            @csrf
                            <div class="row">

                                <div class="col-lg-4">
                                    <div class="primary_input">
                                        <label class="primary_input_label" for="">{{ __('common.Start Date') }}</label>
                                        <div class="primary_datepicker_input">
                                            <div class="g-0 input-right-icon">
                                                <div class="col">
                                                    <div class="">
                                                        <input placeholder="{{__('common.Date')}}" readonly
                                                               class="primary_input_field primary-input date form-control  {{ @$errors->has('date') ? ' is-invalid' : '' }}"
                                                               id="start_date" type="text" name="start_date"
                                                               value="{{ isset($search['start_date']) ? date('m/d/Y', strtotime($search['start_date'])) : '' }}"
                                                               autocomplete="off">
                                                    </div>
                                                </div>

                                                <button class="" type="button">
                                                    <i class="ti-calendar" id="start-date-icon"></i>
                                                </button>
                                            </div>
                                            @if ($errors->has('start_date'))
                                                <span class="invalid-feedback d-block mb-10" role="alert">
                                                <strong>{{ @$errors->first('start_date') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="primary_input">
                                        <label class="primary_input_label" for="">{{ __('common.End Date') }}</label>
                                        <div class="primary_datepicker_input">
                                            <div class="g-0 input-right-icon">
                                                <div class="col">
                                                    <div class="">
                                                        <input placeholder="{{__('common.Date')}}" readonly
                                                               class="primary_input_field primary-input date form-control  {{ @$errors->has('date') ? ' is-invalid' : '' }}"
                                                               id="end_date" type="text" name="end_date"
                                                               value="{{ isset($search['end_date']) ? date('m/d/Y', strtotime($search['end_date'])) : '' }}"
                                                               autocomplete="off">
                                                    </div>
                                                </div>
                                                <button class="" type="button">
                                                    <i class="ti-calendar" id="start-date-icon"></i>
                                                </button>
                                            </div>
                                            @if ($errors->has('start_date'))
                                                <span class="invalid-feedback d-block mb-10" role="alert">
                                                <strong>{{ @$errors->first('start_date') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">

                                    <label class="primary_input_label" for="status">{{ __('common.Status') }}</label>
                                    <select class="primary_select" name="status" id="status">
                                        <option data-display="{{ __('common.Select') }} {{ __('common.Status') }}"
                                                value="">{{ __('common.Select') }} {{ __('common.Status') }}</option>
                                        <option value="pending"
                                            {{ isset($search['status']) ? ($search['status'] == 'pending' ? 'selected' : '') : '' }}>
                                            {{ __('invoice.Pending') }}</option>
                                        <option value="ordered"
                                            {{ isset($search['status']) ? ($search['status'] == 'ordered' ? 'selected' : '') : '' }}>
                                            {{ __('invoice.Ordered') }}</option>
                                        <option value="shipped"
                                            {{ isset($search['status']) ? ($search['status'] == 'shipped' ? 'selected' : '') : '' }}>
                                            {{ __('invoice.Shipped') }}</option>
                                    </select>

                                </div>

                                <div class="col-12 mt-20">
                                    <div class="search_course_btn text-end">
                                        <button type="submit"
                                                class="primary-btn radius_30px fix-gr-bg">
                                            <i class="ti-search"></i>
                                            {{ __('courses.Filter') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>

                <div class="col-12">
                    <div class="white-box">
                        <div class="row">
                            <div class="col-12">
                                <div class="box_header common_table_header">
                                    <div class="main-title d-md-flex">
                                        <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px"
                                            id="page_title">{{ __('invoice.Certificate Order List') }}</h3>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12">

                                <div class="QA_section QA_section_heading_custom check_box_table">
                                    <div class="QA_table ">
                                        <!-- table-responsive -->
                                        <div class="">
                                            <table id="lms_table" class="table Crm_table_active3">
                                                <thead>
                                                <tr>
                                                    <th scope="col">{{ __('common.SL') }}</th>
                                                    <th scope="col">{{ __('common.Student') }}</th>
                                                    <th scope="col">{{ __('common.Course') }}</th>
                                                    <th scope="col">{{ __('common.Date') }}</th>
                                                    <th scope="col">{{ __('payment.Total Price') }}</th>
                                                    <th scope="col">{{ __('common.Status') }}</th>
                                                    <th scope="col">{{ __('common.Action') }}</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach ($models as $key => $order)
                                                    <tr>
                                                        <td class="m-2">{{ $loop->iteration }}</td>
                                                        <td>{{ $order->user->name }}</td>
                                                        <td>{{ $order->course->title }}</td>
                                                        <td>{{ showDate($order->created_at) }}</td>
                                                        <td>{{ getPriceFormat($order->price) }}</td>
                                                        <td>
                                                            @if ($order->status == 'pending')
                                                                <span
                                                                    class="created">{{ strtoupper($order->status) }}</span>
                                                            @elseif($order->status == 'ordered')
                                                                <span
                                                                    class="send">{{ strtoupper($order->status) }}</span>
                                                            @elseif($order->status == 'shipped')
                                                                <span
                                                                    class="paid">{{ strtoupper($order->status) }}</span>
                                                            @else
                                                            @endif
                                                        </td>

                                                        <td>
                                                            <!-- shortby  -->
                                                            <div class="dropdown CRM_dropdown">
                                                                <button class="btn btn-secondary dropdown-toggle"
                                                                        type="button"
                                                                        id="dropdownMenu1{{ @$order->id }}"
                                                                        data-bs-toggle="dropdown"
                                                                        aria-haspopup="true" aria-expanded="false">
                                                                    {{ __('common.Select') }}
                                                                </button>
                                                                <div class="dropdown-menu dropdown-menu-right"
                                                                     aria-labelledby="dropdownMenu1{{ @$order->id }}">

                                                                    @if (permissionCheck('prc.certificate.pdfPrint'))
                                                                        <a class="dropdown-item edit_brand"
                                                                           target="_blank"
                                                                           href="{{ route('prc.certificate.pdfPrint', @$order->id) }}">{{ __('common.Print') }}</a>
                                                                    @endif

                                                                    @if (permissionCheck('prc.certificate.shipped'))
                                                                        @if ($order->status == 'ordered')
                                                                            <button data-bs-toggle="modal"
                                                                                    data-bs-target="#shipped_{{@$order->id}}"
                                                                                    class="dropdown-item"
                                                                                    type="button">{{ __('invoice.Shipped') }}</button>
                                                                        @endif
                                                                    @endif


                                                                </div>
                                                            </div>
                                                            <!-- shortby  -->
                                                        </td>
                                                    </tr>
                                                    <div class="modal fade admin-query" id="shipped_{{@$order->id}}">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h4 class="modal-title">{{__('invoice.Certificate Shipped')}} </h4>
                                                                    <button type="button" class="btn-close"
                                                                            data-bs-dismiss="modal"><i
                                                                            class="ti-close "></i></button>
                                                                </div>

                                                                <div class="modal-body">
                                                                    <div class="text-center">

                                                                        <h4>{{ __('common.Are you sure') }} ?</h4>
                                                                    </div>

                                                                    <div class="mt-40 d-flex justify-content-between">
                                                                        <button type="button" class="primary-btn tr-bg"
                                                                                data-bs-dismiss="modal">{{__('common.Cancel')}}</button>
                                                                        <form method="POST" action="{{ route('prc.certificate.shipped', $order->id) }}">
                                                                            @csrf
                                                                            @method('PUT')                                                                        <button class="primary-btn fix-gr-bg"
                                                                                type="submit">{{ __('invoice.Shipped') }}
                                                                        </button>
                                                                        </form>


                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
