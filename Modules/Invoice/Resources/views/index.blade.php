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
    {{generateBreadcrumb()}}
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="white_box mb_20">
                        <div class="white_box_tittle list_header main-title mb-0">
                            <h3 class="mb-20">{{__('courses.Advanced Filter')}} </h3>
                        </div>

                        <form method="POST" action="{{ route('invoice.index.search') }}">
                            @csrf
                            <div class="row mt-0">
                                <div class="col-lg-3">
                                    <div class="primary_input">
                                        <label class="primary_input_label"
                                               for="">{{ __('common.Start Date') }}</label>
                                        <div class="primary_datepicker_input">
                                            <div class="g-0 input-right-icon">
                                                <div class="col">
                                                    <div class="">
                                                        <input placeholder="{{__('common.Date')}}" readonly
                                                               class="primary_input_field primary-input date form-control  {{ @$errors->has('date') ? ' is-invalid' : '' }}"
                                                               id="start_date" type="text"
                                                               name="start_date"
                                                               value="{{isset($search['start_date']) ?  date('m/d/Y', strtotime($search['start_date'])) : ''}}"
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
                                <div class="col-lg-3">
                                    <div class="primary_input">
                                        <label class="primary_input_label"
                                               for="">{{ __('common.End Date') }}</label>
                                        <div class="primary_datepicker_input">
                                            <div class="g-0 input-right-icon">
                                                <div class="col">
                                                    <div class="">
                                                        <input placeholder="{{__('common.Date')}}" readonly
                                                               class="primary_input_field primary-input date form-control  {{ @$errors->has('date') ? ' is-invalid' : '' }}"
                                                               id="end_date" type="text"
                                                               name="end_date"
                                                               value="{{isset($search['end_date']) ? date('m/d/Y', strtotime($search['end_date'])) : ''}}"
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

                                <div class="col-lg-3 ">

                                    <label class="primary_input_label" for="status">{{__('common.Status')}}</label>
                                    <select class="primary_select" name="status" id="status">
                                        <option data-display="{{__('common.Select')}} {{__('common.Status')}}"
                                                value="">{{__('common.Select')}} {{__('common.Status')}}</option>
                                        <option
                                            value="created" {{ isset($search['status']) ? ($search['status']=='created' ? 'selected':''):'' }}>{{ __('invoice.Created') }}</option>
                                        <option
                                            value="send" {{ isset($search['status']) ? ($search['status']=='send' ? 'selected':''):'' }}>{{ __('invoice.Send') }}</option>
                                        <option
                                            value="paid" {{ isset($search['status']) ? ($search['status']=='paid' ? 'selected':''):'' }}>{{ __('invoice.Paid') }}</option>
                                    </select>

                                </div>
                                <div class="col-lg-3 ">

                                    <label class="primary_input_label"
                                           for="payment_type">{{__('invoice.Payment Type')}}</label>
                                    <select class="primary_select" name="payment_type" id="payment_type">
                                        <option data-display="{{__('common.Select')}} {{__('invoice.Payment Type')}}"
                                                value="">{{__('common.Select')}} {{__('invoice.Payment Type')}}</option>
                                        <option
                                            value="1" {{ isset($search['payment_type']) ? ($search['payment_type']==1 ? 'selected':''):'' }}>{{ __('invoice.Online') }}</option>
                                        <option
                                            value="2" {{ isset($search['payment_type']) ? ($search['payment_type']==2 ? 'selected':''):'' }}>{{ __('invoice.Offline') }}</option>
                                    </select>

                                </div>

                                <div class="col-12 mt-20">
                                    <div class="search_course_btn text-end">
                                        <button type="submit"
                                                class="primary-btn radius_30px fix-gr-bg">{{__('courses.Filter')}} </button>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
                <div class="col-12">
                    <div class="white-box">
                        <div class="row">
                            @if(permissionCheck('invoice.create'))
                                <div class="col-12">
                                    <div class="box_header common_table_header">
                                        <div class="main-title d-md-flex">
                                            <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px"
                                                id="page_title">{{ __('invoice.Invoice List') }}</h3>

                                            <ul class="d-flex">

                                                <li><a class="primary-btn radius_30px fix-gr-bg"
                                                       href="{{ route('invoice.create') }}"><i
                                                            class="ti-plus"></i>{{ __('invoice.New Invoice') }}</a></li>
                                            </ul>

                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="col-lg-12">

                                <div class="QA_section QA_section_heading_custom check_box_table">
                                    <div class="QA_table ">
                                        <!-- table-responsive -->
                                        <div class="">
                                            <table id="lms_table" class="table Crm_table_active3">
                                                <thead>
                                                <tr>
                                                    <th scope="col">{{ __('common.SL') }}</th>
                                                    <th scope="col">{{ __('invoice.Invoice Number') }}
                                                    <th scope="col">{{ __('common.Student') }}</th>
                                                    <th scope="col">{{ __('common.Date') }}</th>
                                                    <th scope="col">{{ __('common.Total Courses') }}</th>
                                                    <th scope="col">{{ __('payment.Total Price') }}</th>
                                                    <th scope="col">{{ __('common.Status') }}</th>
                                                    <th scope="col">{{ __('common.Payment Type') }}</th>
                                                    <th scope="col">{{ __('common.Action') }}</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach ($invoices as $key => $invoice)
                                                    <tr>
                                                        <th class="m-2">{{ $loop->iteration }}</th>
                                                        <td>{{ $invoice->invoice_number }}</td>
                                                        <td>{{ $invoice->user->name }}</td>
                                                        <td>{{ showDate($invoice->created_at) }}</td>
                                                        <td>{{ $invoice->courses_count }}</td>
                                                        <td>{{ getPriceFormat($invoice->purchase_price) }}</td>
                                                        <td>
                                                            @if ($invoice->status == 'created')
                                                                <span
                                                                    class="created">{{ strtoupper($invoice->status) }}</span>
                                                            @elseif($invoice->status == 'send')
                                                                <span
                                                                    class="send">{{ strtoupper($invoice->status) }}</span>
                                                            @elseif($invoice->status == 'paid')
                                                                <span
                                                                    class="paid">{{ strtoupper($invoice->status) }}</span>
                                                            @else

                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($invoice->payment_type == 1)
                                                                <span class="online">{{ __('invoice.Online') }}</span>
                                                            @else
                                                                <span class="offline">{{ __('invoice.Offline') }}</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <!-- shortby  -->
                                                            <div class="dropdown CRM_dropdown">
                                                                <button class="btn btn-secondary dropdown-toggle"
                                                                        type="button"
                                                                        id="dropdownMenu1{{ @$invoice->id }}"
                                                                        data-bs-toggle="dropdown"
                                                                        aria-haspopup="true" aria-expanded="false">
                                                                    {{ __('common.Select') }}
                                                                </button>
                                                                <div class="dropdown-menu dropdown-menu-right"
                                                                     aria-labelledby="dropdownMenu1{{ @$invoice->id }}">
                                                                    @if (permissionCheck('invoice.show'))
                                                                        <a class="dropdown-item edit_brand"
                                                                           target="_blank"
                                                                           href="{{ route('invoice.show', @$invoice->id) }}">{{ __('common.View') }}</a>
                                                                    @endif
                                                                    @if($invoice->status !='paid')
                                                                        @if (permissionCheck('invoice.edit'))
                                                                            <a class="dropdown-item edit_brand"
                                                                               href="{{ route('invoice.edit', @$invoice->id) }}">{{ __('common.Edit') }}</a>
                                                                        @endif
                                                                        @if (permissionCheck('invoice.destroy'))
                                                                            <a onclick="confirm_modal('{{ route('invoice.destroy', @$invoice->id) }}');"
                                                                               class="dropdown-item edit_brand">{{ __('common.Delete') }}</a>
                                                                        @endif
                                                                    @endif


                                                                </div>
                                                            </div>
                                                            <!-- shortby  -->
                                                        </td>
                                                    </tr>
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
    <div id="edit_form">

    </div>
    <div id="view_details">

    </div>


    @include('backend.partials.delete_modal')
@endsection
@push('scripts')
@endpush
