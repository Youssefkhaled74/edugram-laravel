@extends(app('extend_view'))
@section('title'){{Settings('site_title') ? Settings('site_title') : 'Infix LMS'}} | @lang('chat.create') @lang('chat.group') @endsection

@section('mainContent')
    <section class="admin-visitor-area up_st_admin_visitor" id="admin-visitor-area">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="chat_main_wrapper">
                        <div class="chat_flow_list_wrapper ">
                            <div class="box_header">
                                <div class="main-title">
                                    <h3 class="m-0">@lang('chat.chat') @lang('chat.list')</h3>
                                </div>
                                    <a class="primary-btn radius_30px  fix-gr-bg" href="{{ route('chat.new') }}"><i class="ti-plus"></i>@lang('chat.new') @lang('chat.chat')</a>
                            </div>
                            <side-panel-component
                                :search_url="{{ json_encode(route('chat.user.search')) }}"
                                :single_chat_url="{{ json_encode(route('chat.index')) }}"
                                :chat_block_url="{{ json_encode(route('chat.user.block')) }}"
                                :create_group_url="{{ json_encode(route('chat.group.create')) }}"
                                :group_chat_show="{{ json_encode(route('chat.group.show')) }}"
                                :users="{{ json_encode($users) }}"
                                :groups="{{ json_encode($myGroups) }}"
                                :all_users="{{ json_encode(\App\Models\User::where('id', '!=', auth()->id())->get()) }}"
                                :can_create_group="{{
                                    json_encode(auth()->user()->role_id != 3 && Settings('chat_can_make_group') == 'yes')
                                 }}"
                                :asset_type="{{ json_encode('/public') }}"
                            ></side-panel-component>
                        </div>
                        <div class="chat_view_list ">
                            <div class="box_header">
                                <div class="main-title">
                                    <h3 class="m-0">@lang('chat.create') @lang('chat.group')</h3>
                                </div>
                            </div>
                            <div class="chat_view_list_inner crm_full_height ">
                                <form action="{{ route('chat.group.create') }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <div class="chat_view_list_inner_scrolled" style="overflow: unset;padding-bottom: 0;">
                                        <div class="input-group mb_20">
                                            <label class="primary_input_label" for="">@lang('chat.group') @lang('chat.name') *</label>
                                            <input class="primary_input_name" type="text" name="name" required>
                                        </div>
                                        <div class="primary_input mb_20 mt-5">
                                            <div class="row no-gutters input-right-icon">
                                                <div class="col">
                                                    <div class="input-effect sm2_mb_20 md_mb_20">
                                                        <input class="primary-input" type="text" id="placeholderGroupPhoto" placeholder="@lang('chat.group') @lang('chat.photo')" readonly="">
                                                        <span class="focus-border"></span>
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <button class="primary-btn-small-input" type="button">
                                                        <label class="primary-btn small fix-gr-bg" for="group_photo">@lang('chat.browse')</label>
                                                        <input type="file" class="d-none" name="group_photo" id="group_photo">
                                                    </button>
                                                </div>
                                            </div>
                                            <input type="hidden" name="created_by" value="{{ auth()->id() }}">
                                        </div>


                                        <div class="input-group mb-30" style="margin-bottom: 20px;">
                                            <label class="primary_input_label" for="usersI">
                                                @lang('chat.member') *
                                            </label>
                                            <select name="users[]" id="users" class="primary_input_name mb-15" multiple>
                                                @foreach ($users as $key => $user)
                                                    <option value="{{ $user->id }}">{{ $user->first_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>


                                        <button type="submit" class="primary-btn radius_30px  fix-gr-bg" href="#">@lang('chat.create') @lang('chat.group')</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')
    <script>
        $('#users').select2({});
    </script>
@endsection
