@extends('backend.master')
@php
    $table_name = 'lms_classes';
@endphp
@section('table')
    {{ $table_name }}
@endsection
@section('mainContent')

    {{generateBreadcrumb()}}

    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="box_header common_table_header">
                        <div class="main-title d-md-flex">
                            <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">
                                @if (!isset($model))
                                    {{ __('class.Add New Class') }}
                                @else
                                    {{ __('invoice.Printed Certificate') }}
                                @endif
                            </h3>
                        </div>
                    </div>
                    <div class="white-box mb_30  student-details header-menu">
                        <form method="POST" action="{{ route('prc.update', $model->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="row pt-0">
                            <div class="col-xl-6">
                                <div class="primary_input mb-25">
                                    <label class="primary_input_label" for="nameInput">{{ __('common.Title') }}
                                        <strong class="text-danger">*</strong></label>
                                    <input name="title" id="nameInput"
                                           class="primary_input_field name {{ @$errors->has('title') ? ' is-invalid' : '' }}"
                                           placeholder="{{ __('class.Class Name') }}" type="text"
                                           value="{{ isset($model) ? $model->title : '' }}">
                                    @if ($errors->has('title'))
                                        <span class="invalid-feedback d-block mb-10" role="alert">
                                            <strong>{{ @$errors->first('title') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="primary_input mb-25">
                                    <label class="primary_input_label" for="priceInput">{{ __('common.Price') }}
                                        <strong class="text-danger">*</strong></label>
                                    <input name="price" id="priceInput" type="number"
                                           class="primary_input_field name {{ @$errors->has('price') ? ' is-invalid' : '' }}"
                                           placeholder="{{ __('class.Class Name') }}" type="text"
                                           value="{{ isset($model) ? $model->price : '' }}">
                                    @if ($errors->has('price'))
                                        <span class="invalid-feedback d-block mb-10" role="alert">
                                            <strong>{{ @$errors->first('price') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 text-center">
                            <div class="d-flex justify-content-center pt_20">
                                <button type="submit" class="primary-btn semi_large fix-gr-bg" data-bs-toggle="tooltip"
                                        title="{{ @$tooltip }}" id="save_button_parent">
                                    <i class="ti-check"></i>
                                    @if (!isset($model))
                                        {{ __('common.Save') }}
                                    @else
                                        {{ __('common.Update') }}
                                    @endif
                                </button>
                            </div>
                        </div>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </section>

@endsection
