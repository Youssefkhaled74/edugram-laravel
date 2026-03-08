@extends('backend.master')
@push('styles')
    <link rel="stylesheet" href="{{asset('public/backend/css/jitsi.css')}}{{assetVersion()}}">
@endpush
@section('mainContent')
    {{generateBreadcrumb()}}
    <section class="admin-visitor-area up_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-lg-12">
                    <form action="{{ route('jitsi.settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="white-box">
                            <div class="row p-0">
                                <div class="col-lg-12">


                                    <div class="row  mt-40">

                                        <div class="col-lg-12">
                                            <div class="input-effect sm2_mb_20 md_mb_20">
                                                <input
                                                        class="primary-input form-control{{ $errors->has('server_base_url') ? ' is-invalid' : '' }}"
                                                        type="text" name="jitsi_server"
                                                        value="@if(!empty($setting)){{ old('server_base_url',$setting->jitsi_server) }}@endif"
                                                        placeholder="https://meet.jit.si/">
                                                <label>{{__('jitsi.Jitsi Server Base URL') }}<span
                                                            class="required_mark">*</span></label>
                                                <span class="focus-border"></span>
                                                @if ($errors->has('server_base_url'))
                                                    <span class="invalid-feedback invalid-select" role="alert">
                                                                <strong>{{ $errors->first('jitsi_server') }}</strong>
                                                            </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <br><br>
                                    <div class="row mt-40">
                                        <div class="col-lg-12 text-center">
                                            <button type="submit" class="primary-btn fix-gr-bg"
                                                    id="_submit_btn_admission">
                                                <span class="ti-check"></span>
                                                {{__('jitsi.Update')}}
                                            </button>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
