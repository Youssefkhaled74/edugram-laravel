@extends(app('extend_view'))
@section('title')
    {{Settings('site_title') ? Settings('site_title') : 'Infix LMS'}} | @lang('chat.chat') @lang('chat.settings')
@endsection
@section('mainContent')
    {{generateBreadcrumb()}}

    <section class="admin-visitor-area up_st_admin_visitor" id="admin-visitor-area">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="">
                        <div class="row">
                            <div class="col-lg-12">

                                <div class="white_box_30px">
                                    <div class="main-title">
                                        <h3 class="mb-0">
                                            @lang('chat.chatting') @lang('chat.method') @lang('chat.settings')
                                        </h3>
                                    </div>

                                    <form action="{{ route('chat.settings') }}" method="post"
                                          class="bg-white p-4 rounded">
                                        @csrf
                                        <div class="row">
                                            <div class="col-lg-6 d-flex relation-button justify-content-between mb-3">
                                                <p class="text-uppercase">@lang('chat.chat') @lang('chat.settings')</p>
                                                <div class="d-flex   ml-30">
                                                    <div class="d-flex mr-20">
                                                        <input type="radio" name="chat_method" id="relationFather6343"
                                                               value="pusher"
                                                               class="common-radio relationButton" {{ env('BROADCAST_DRIVER') == 'pusher' ? 'checked' : ''}}>
                                                        <label for="relationFather6343">@lang('chat.pusher')</label>
                                                    </div>
                                                    <div class="d-flex mr-20">
                                                        <input type="radio" name="chat_method" id="relationMother733"
                                                               value="jquery"
                                                               class="common-radio relationButton" {{ env('BROADCAST_DRIVER') == null ? 'checked' : ''}}>
                                                        <label for="relationMother733">@lang('chat.jquery')</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row" id="pusher" style="display: none">
                                            <div class="col-xl-6">
                                                <div class="primary_input mb-25">
                                                    <label class="primary_input_label"
                                                           for="">{{ __('setting.pusher_app_id') }}</label>
                                                    <input class="primary_input_field" placeholder="-" type="text"
                                                           name="pusher_app_id" value="{{ env('PUSHER_APP_ID') }}">
                                                </div>
                                            </div>

                                            <div class="col-xl-6">
                                                <div class="primary_input mb-25">
                                                    <label class="primary_input_label"
                                                           for="">{{ __('setting.pusher_app_key') }}</label>
                                                    <input class="primary_input_field" placeholder="-" type="text"
                                                           name="pusher_app_key" value="{{ env('PUSHER_APP_KEY') }}">
                                                </div>
                                            </div>
                                            <div class="col-xl-6">
                                                <div class="primary_input mb-25">
                                                    <label class="primary_input_label"
                                                           for="">{{ __('setting.pusher_app_secret') }}</label>
                                                    <input class="primary_input_field" placeholder="-" type="text"
                                                           name="pusher_app_secret"
                                                           value="{{ env('PUSHER_APP_SECRET') }}">
                                                </div>
                                            </div>

                                            <div class="col-xl-6">
                                                <div class="primary_input mb-25">
                                                    <label class="primary_input_label"
                                                           for="">{{ __('setting.pusher_app_cluster') }}</label>
                                                    <input class="primary_input_field" placeholder="-" type="text"
                                                           name="pusher_app_cluster"
                                                           value="{{ env('PUSHER_APP_CLUSTER') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <button class="primary-btn small fix-gr-bg"><i
                                                class="ti-check"></i>@lang('chat.update')</button>
                                    </form>
                                </div>

                                <div class="white_box_30px mt-5">
                                    <!-- SMTP form  -->
                                    <div class="main-title  ">
                                        <h3 class="mb-0">@lang('chat.chat') @lang('chat.settings')</h3>
                                    </div>
                                    <form action="{{ route('chat.settings.permission.store') }}" method="post"
                                          class="bg-white p-4 rounded">
                                        @csrf
                                        <div class="row">
                                            <div class="col-lg-6 d-flex relation-button justify-content-between mb-3">
                                                <p class="text-uppercase  ">@lang('chat.admin_can_chat_without_invitation')</p>
                                                <div class="d-flex   ml-30">
                                                    <div class="d-flex mr-20">
                                                        <input type="radio" name="admin_can_chat_without_invitation"
                                                               id="relationFather3" value="yes"
                                                               class="common-radio relationButton" {{ Settings('chat_admin_can_chat_without_invitation') == 'yes' ? 'checked' : ''}}>
                                                        <label for="relationFather3">@lang('chat.yes')</label>
                                                    </div>
                                                    <div class="d-flex mr-20">
                                                        <input type="radio" name="admin_can_chat_without_invitation"
                                                               id="relationMother4" value="no"
                                                               class="common-radio relationButton" {{ Settings('chat_admin_can_chat_without_invitation') == 'no' ? 'checked' : ''}}>
                                                        <label for="relationMother4">@lang('chat.no')</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 d-flex relation-button justify-content-between mb-3">
                                                <p class="text-uppercase mb-0">@lang('chat.open_chat_system')</p>
                                                <div class="d-flex radio-btn-flex ml-30">
                                                    <div class="d-flex mr-20">
                                                        <input type="radio" name="open_chat_system" id="relationFather5"
                                                               value="yes"
                                                               class="common-radio relationButton" {{ Settings('chat_open') == 'yes' ? 'checked' : ''}}>
                                                        <label for="relationFather5">@lang('chat.yes')</label>
                                                    </div>
                                                    <div class="d-flex mr-20">
                                                        <input type="radio" name="open_chat_system" id="relationMother6"
                                                               value="no"
                                                               class="common-radio relationButton" {{ Settings('chat_open') == 'no' ? 'checked' : ''}}>
                                                        <label for="relationMother6">@lang('chat.no')</label>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <button class="primary-btn small fix-gr-bg"><i
                                                class="ti-check"></i>@lang('chat.update')</button>
                                    </form>
                                </div>


                                <div class="white_box_30px mt-5">
                                    <!-- SMTP form  -->
                                    <div class="main-title mb-25">
                                        <h3 class="mb-0 text-nowrap">@lang('chat.invitation') @lang('chat.settings')</h3>
                                    </div>
                                    <form action="{{ route('chat.invitation.requirement') }}" method="post"
                                          class="bg-white p-4 rounded">
                                        @csrf
                                        <div class="row">
                                            <div class="col-lg-6 d-flex relation-button justify-content-between mb-3">
                                                <p class="text-uppercase mb-0">@lang('chat.invitation') @lang('chat.requirement')</p>
                                                <div class="d-flex   ml-30">
                                                    <div class="d-flex mr-20">
                                                        <input type="radio" name="invitation_requirement"
                                                               id="relationFather6" value="required"
                                                               class="common-radio relationButton" {{ Settings('chat_invitation_requirement') == 'required' ? 'checked' : ''}}>
                                                        <label for="relationFather6">@lang('chat.required')</label>
                                                    </div>
                                                    <div class="d-flex mr-20">
                                                        <input type="radio" name="invitation_requirement"
                                                               id="relationMother7" value="none"
                                                               class="common-radio relationButton" {{ Settings('chat_invitation_requirement') == 'none' ? 'checked' : ''}}>
                                                        <label
                                                            for="relationMother7">@lang('chat.not') @lang('chat.required')</label>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <button class="primary-btn small fix-gr-bg"><i
                                                class="ti-check"></i>@lang('chat.update')</button>
                                    </form>
                                </div>

                                @if( is_null(Settings('chat_generate')) || Settings('chat_generate') != 'generated')
                                    <div class="white_box_30px mt-5">
                                        <div class="main-title mb-25">
                                            <h3 class="mb-0">@lang('chat.generate') @lang('chat.connections')</h3>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12 mb-3">
                                                <form action="{{ route('chat.invitation.generate','single') }}"
                                                      method="get" class="bg-white p-4 rounded">
                                                    <p class="text-uppercase mb-0">
                                                        @lang('chat.generate') @lang('chat.teacher') @lang('chat.and') @lang('chat.student') @lang('chat.connection') @lang('chat.for') @lang('chat.old') @lang('chat.courses')
                                                    </p>
                                                    <br>
                                                    <button class="primary-btn radius_30px  fix-gr-bg"><i
                                                            class="ti-check"></i>@lang('chat.generate')</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="white_box_30px mt-5">
                                    <!-- SMTP form  -->
                                    <div class="main-title mb-25">
                                        <h3 class="mb-0">@lang('chat.permission') @lang('chat.settings')</h3>
                                    </div>
                                    <form action="{{ route('chat.settings.edu') }}" method="post"
                                          class="bg-white p-4 rounded">
                                        @csrf
                                        <div class="row">
                                            <div class="col-lg-6 d-flex relation-button justify-content-between mb-3">
                                                <p class="text-uppercase mb-0">@lang('chat.can_upload_file')</p>
                                                <div class="d-flex radio-btn-flex ml-30">
                                                    <div class="d-flex mr-20">
                                                        <input type="radio" name="can_upload_file"
                                                               id="relationFather6334" value="yes"
                                                               class="common-radio relationButton" {{ Settings('chat_can_upload_file') == 'yes' ? 'checked' : ''}}>
                                                        <label for="relationFather6334">@lang('chat.yes')</label>
                                                    </div>
                                                    <div class="d-flex mr-20">
                                                        <input type="radio" name="can_upload_file"
                                                               id="relationMother7334" value="no"
                                                               class="common-radio relationButton" {{ Settings('chat_can_upload_file') == 'no' ? 'checked' : ''}}>
                                                        <label for="relationMother7334">@lang('chat.no')</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 d-flex relation-button justify-content-between mb-3">
                                                <div class="primary_input">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label class="primary_input_label"
                                                                   for="">@lang('chat.upload_file_limit')
                                                                (@lang('chat.mb'))</label>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input class="primary_input_field" placeholder="-"
                                                                   type="number" name="file_upload_limit"
                                                                   value="{{ Settings('chat_file_limit') ?? 0 }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-6 d-flex relation-button justify-content-between mb-3">
                                                <p class="text-uppercase mb-0">@lang('chat.can_make_group')</p>
                                                <div class="d-flex radio-btn-flex ml-30">
                                                    <div class="d-flex mr-20">
                                                        <input type="radio" name="can_make_group" id="relationFather63"
                                                               value="yes"
                                                               class="common-radio relationButton" {{ Settings('chat_can_make_group') == 'yes' ? 'checked' : ''}}>
                                                        <label for="relationFather63">@lang('chat.yes')</label>
                                                    </div>
                                                    <div class="d-flex mr-20">
                                                        <input type="radio" name="can_make_group" id="relationMother73"
                                                               value="no"
                                                               class="common-radio relationButton" {{ Settings('chat_can_make_group') == 'no' ? 'checked' : ''}}>
                                                        <label for="relationMother73">@lang('chat.no')</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <button class="primary-btn small fix-gr-bg"><i
                                                class="ti-check"></i>@lang('chat.update')</button>
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
    <script>
        $(document).ready(function () {
            let method = $('input[name="chat_method"]:checked').val();
            if (method == 'pusher') {
                $('#pusher').css('display', '');
                $('#jquery').hide();
                $('#pusher').show();
            } else {
                $('#pusher').hide();
            }
            $('input[name=chat_method]').change(function () {
                let method = $('input[name="chat_method"]:checked').val();
                if (method == 'pusher') {
                    $('#jquery').hide();
                    $('#pusher').show();
                } else {
                    $('#pusher').hide();
                }
            });
        });
    </script>
@endpush
