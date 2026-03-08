@extends('backend.master')
@push('styles')
    <style>
        .primary_text_field {
            border: 1px solid var(--backend-border-color);
            font-size: 14px;
            color: #415094;
            padding-left: 20px;
            height: 146px;
            border-radius: 30px;
            width: 100%;
            padding-right: 15px;
        }
    </style>
@endPush
@section('mainContent')
    <input type="hidden" name="id" value="{{ $settings->id }}">
    {{generateBreadcrumb()}}

    <section class="admin-visitor-area up_st_admin_visitor" id="admin-visitor-area">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="white-box">


                                    <div class="main-title mb-20">
                                        <h3 class="mb-0">@lang('invoice.Invoice Setting')
                                        </h3>
                                    </div>

                                    <form method="POST" action="{{ route('invoice.settings.update', $settings->id) }}"
                                          enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="type" id="profileType" value="profile">

                                        <div class="row">
                                            <div class="col-xl-12">
                                                <div class="primary_input mb-25">
                                                    <label class="primary_input_label"
                                                           for="nameInput">{{ __('invoice.Prefix') }}
                                                        <strong class="text-danger">*</strong></label>
                                                    <input name="prefix" id="nameInput"
                                                           class="primary_input_field prefix {{ @$errors->has('prefix') ? ' is-invalid' : '' }}"
                                                           placeholder="{{ __('invoice.Prefix') }}" type="text"
                                                           value="{{ isset($settings) ? @$settings->prefix : old('prefix') }}">
                                                    @if ($errors->has('prefix'))
                                                        <span class="invalid-feedback d-block mb-10" role="alert">
                                                        <strong>{{ @$errors->first('prefix') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-xl-12">
                                                <div class="primary_input mb-25">
                                                    <label class="primary_input_label"
                                                           for="nameInput">{{ __('invoice.footer text') }}
                                                        <strong class="text-danger">*</strong></label>
                                                    <textarea name="footer_text" class="primary_text_field" id=""
                                                              cols="30"
                                                              rows="100">{{ isset($settings) ? $settings->footer_text : '' }}</textarea>
                                                    @if ($errors->has('footer_text'))
                                                        <span class="invalid-feedback d-block mb-10" role="alert">
                                                        <strong>{{ @$errors->first('footer_text') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">


                                        </div>
                                        <div class="row mt-20">
                                            <div
                                                class="col-lg-12 text-center d-flex justify-content-center align-content-center">
                                                <button type="submit" class="primary-btn   fix-gr-bg profileSettingBtn">
                                                    <i
                                                        class="ti-check"></i>@lang('common.Update')</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('scripts')

@endpush
