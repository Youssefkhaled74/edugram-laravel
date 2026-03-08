@extends('backend.master')
@push('styles')
    <style>
        .w-max {
            width: max-content !important;
        }

        .table thead th {
            padding: 12px;
        }

        .dark table#invoice_table tbody tr td {
            border: 1px solid var(--backend-border-color);
        }

        .dark table#invoice_table thead tr th {
            border: 1px solid var(--backend-border-color);
        }

        .dark table#invoice_table tfoot tr td {
            border: 1px solid var(--backend-border-color);
            color: var(--dynamic-text-color) !important;
        }

        .price,
        .removeCourseBtn i {
            color: var(--dynamic-text-color) !important;

        }
    </style>
@endpush

@section('mainContent')
    {{generateBreadcrumb()}}

    <section class="admin-visitor-area up_st_admin_visitor">


        <div class="white_box mb_30  student-details header-menu">
            <div class="white_box_tittle list_header">
                <h4>{{ __('invoice.Add New Invoice') }}</h4>
            </div>
            <div class="col-lg-12">


                <input type="hidden" id="url" value="{{ url('/') }}">


                <form method="POST" action="{{ route('invoice.update', $edit->id) }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="total" id="total_input" value="{{ $edit->price }}">
                    <input type="hidden" id="row_id" value="{{ $edit->latest()->value('id') }}">
                    <div class="row">
                        <div class="col-md-12  ">

                            <div class="row mb-30">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-xl-6">
                                            <label class="primary_input_label" for="student_id">
                                                {{ __('student.Student') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div class="primary_input mb-15">
                                                <select name="student" id="student_id"
                                                        class="primary_select mb-15 e1">
                                                    <option value="">{{ __('common.Select Student') }}</option>
                                                    @foreach ($students as $student)
                                                        <option
                                                            value="{{ $student->id }}" {{ $edit->user_id == $student->id ? 'selected' :'' }}>
                                                            {{ $student->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xl-6 mt-25">
                                            <div class="row mt-3">
                                                <div class="col-2 mb-25">
                                                    <label class="primary_input_label" for="">
                                                        {{ __('invoice.Payment') }} <span class="text-danger">*</span>
                                                    </label>
                                                </div>
                                                <div class="col-2 mb-25">
                                                    <label class="primary_checkbox d-flex mr-12">
                                                        <input type="radio" id="type1" name="payment_type" value="1"
                                                            {{ isset($edit) ?  ($edit->payment_type == 1 ? 'checked' : '') : ''  }}>
                                                        <span class="checkmark me-2"></span>{{ __('invoice.Online') }}
                                                    </label>
                                                </div>
                                                <div class="col-2 mb-25">
                                                    <label class="primary_checkbox d-flex mr-12">
                                                        <input type="radio" id="type2" name="payment_type" value="2"
                                                            {{ isset($edit) ?  ($edit->payment_type == 2 ? 'checked' : '') : ''  }}>
                                                        <span class="checkmark me-2"></span>
                                                        {{ __('invoice.Offline') }}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="text-end">
                                        <a class="primary-btn radius_30px   fix-gr-bg addMoreButton w-fit">
                                            <i class="ti-plus"></i>{{ __('invoice.Add More') }}</a>
                                    </div>

                                    <table class="table mt-4" id="invoice_table">
                                        <thead>

                                        <tr>
                                            <th class="col-6">{{ __('common.Course') }}</th>
                                            <th class="col-2 text-end">{{ __('payment.Price') }}</th>
                                            <th class="col-2 text-end">{{ __('common.Action') }}</th>

                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($edit->courses as $key=>$item)
                                            <tr class="table_row" id="table_row_{{ $key+1 }}">
                                                <td>
                                                    <select class="primary_select selectCourse" name="courses[]"
                                                            id="selectCourse" data-row_id="{{ $key+1 }}">
                                                        <option value="">{{ __('courses.Select Course') }}</option>
                                                        @foreach ($courses as $course)
                                                            @php
                                                                if ($course->type == 1) {
                                                                    $type = trans('courses.Courses');
                                                                } elseif ($course->type == 2) {
                                                                    $type = trans('quiz.Quiz');
                                                                } elseif ($course->type == 3) {
                                                                    $type = trans('virtual-class.Class');
                                                                } else {
                                                                    $type = '';
                                                                }

                                                            @endphp
                                                            <option
                                                                value="{{ $course->id }}" {{  $item->course_id == $course->id ? 'selected' : '' }}>
                                                                {{ $course->title }} ({{ $type }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="text-end">
                                                    <div class="mt-10">
                                                    <span class="price"
                                                          id="price_{{ $key+1 }}">{{ showPrice($item->price) }}</span>
                                                    </div>
                                                </td>
                                                <td class="text-end">
                                                    <a class="btn removeCourseBtn" data-row_id="{{ $key+1 }}"> <i
                                                            class="ti-trash "></i> </a>
                                                </td>
                                            </tr>
                                        @endforeach

                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <td class="text-end">
                                                <span>{{ __('payment.Total') }}</span>
                                            </td>
                                            <td class="text-end">
                                                <span class="total" id="total">{{showPrice($edit->price)}}</span>
                                            </td>
                                            <td></td>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">

                        <div class="col-12  ">
                            <label class="primary_checkbox d-flex mr-12 w-max">
                                <input type="checkbox" id="payment_status" name="status" value="send"
                                    {{ isset($edit) ?  ($edit->status == 'send'? 'checked' : '') : ''  }}
                                >
                                <span class="checkmark me-2"></span> {{ __('invoice.Notify To Email') }}
                            </label>
                        </div>
                    </div>

                    <div class="row ">
                        <div class="col-lg-12 d-flex justify-content-center align-content-center">
                            <button class="primary-btn fix-gr-bg" data-bs-toggle="tooltip">
                                <i class="ti-check"></i>
                                {{ __('common.Submit') }}
                            </button>
                        </div>
                    </div>

                </form>

            </div>
        </div>

    </section>
@endsection
@push('js')
    <script>
        (function ($) {
            "use strict";
            $(document).ready(function () {
                $(document).on('change', '.selectCourse', function () {
                    let student_id = $('#student_id').val();
                    if (!student_id) {
                        setTimeout(() => {
                            toastr.warning('Please Select Student First', 'Warning', {
                                timeOut: 5000,
                            });
                        }, 500);

                        $(".selectCourse").val('').niceSelect('update');
                        return;
                    }
                    let course_id = $(this).val();
                    let url = $('#url').val();
                    let row_id = $(this).data('row_id');
                    $.ajax({
                        type: "GET",
                        dataType: "JSON",
                        data: {
                            id: course_id
                        },
                        url: url + '/invoice-admin/get-price/' + course_id,
                        success: function (data) {
                            var price = data.price;
                            var discount = data.discount_price;
                            if (discount > 0) {
                                  price = discount;
                            }
                            $('#price_' + row_id).html(price);
                            totalCourseAmount();
                        }
                    })
                })
                $(document).on('click', '.removeCourseBtn', function (e) {
                    e.preventDefault();
                    let row_id = $(this).data('row_id');
                    $('#table_row_' + row_id).remove();
                    totalCourseAmount();
                })
                $(document).on('click', '.addMoreButton', function () {
                    var tableLength = $("#invoice_table tbody tr").length;
                    var row_id = parseInt($('#row_id').val()) + 1;
                    $('#row_id').val(row_id);
                    var tr = `<tr class="table_row" id="table_row_${row_id}">
                                            <td class="">${row_id}</td>
                                            <td>
                                                <select class="primary_select selectCourse" name="courses[]"
                                                    id="selectCourse" data-row_id = "${row_id}">
                                                    <option value="">{{ __('courses.Select Course') }}</option>
                                                    @foreach ($courses as $course)
                    @php
                        if ($course->type == 1) {
                            $type = trans('courses.Courses');
                        } elseif ($course->type == 2) {
                            $type = trans('quiz.Quiz');
                        } elseif ($course->type == 3) {
                            $type = trans('virtual-class.Class');
                        } else {
                            $type = '';
                        }

                    @endphp
                    <option value="{{ $course->id }}">
                                                            {{ $course->title }} ({{ $type }})
                                                        </option>
                                                    @endforeach
                    </select>
                </td>
                <td class="text-end">
                    <div class="mt-10">
                        <span class="price" id="price_${row_id}"></span>
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <input type="hidden" id="remove_price_${row_id}"
                                                >
                                                <a class="btn removeCourseBtn" data-row_id = "${row_id}"> <i class="ti-trash "></i> </a>
                                            </td>
                                        </tr>`;
                    $("#invoice_table tbody").append(tr);
                    $('.primary_select').niceSelect('destroy');
                    $(".primary_select").niceSelect();
                })

                function totalCourseAmount() {
                    var sum = 0;
                    $('.price').each(function () {
                        var combat = $(this).text();
                        if (!isNaN(combat) && combat.length !== 0) {
                            sum += parseInt(combat);
                        }
                    });
                    $('#total_input').val(sum);
                    $('#total').html(sum);
                }
            })
        })(jQuery);

    </script>
@endpush
