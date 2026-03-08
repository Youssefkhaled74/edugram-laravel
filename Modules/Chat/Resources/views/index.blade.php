@extends(app('extend_view'))
@section('title')
    {{Settings('site_title') ? Settings('site_title') : 'Infix LMS'}} | @lang('chat.chat')
@endsection

@section('mainContent')
    @if(\Illuminate\Support\Facades\Auth::user()->role_id!=3)
        {{generateBreadcrumb()}}
    @endif
    <section class="main_content_iner main_content_padding" id="admin-visitor-area">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="chat_main_wrapper">
                        <div class="chat_flow_list_wrapper ">
                            <div class="box_header">
                                <div class="main-title">
                                    <h3 class="m-0">@lang('chat.chat') @lang('chat.list')</h3>
                                </div>
                                <a class="primary-btn radius_30px  fix-gr-bg" href="{{ route('chat.new') }}"><i
                                        class="ti-plus"></i>@lang('chat.new') @lang('chat.chat') </a>
                            </div>
                            <!-- chat_list  -->
                            <side-panel-component
                                :search_url="{{ json_encode(route('chat.user.search')) }}"
                                :single_chat_url="{{ json_encode(route('chat.index')) }}"
                                :chat_block_url="{{ json_encode(route('chat.user.block')) }}"
                                :create_group_url="{{ json_encode(route('chat.group.create')) }}"
                                :group_chat_show="{{ json_encode(route('chat.group.show')) }}"
                                :users="{{ json_encode($users) }}"
                                :groups="{{ json_encode($groups) }}"
                                :all_users="{{ json_encode(\App\Models\User::where('id', '!=', auth()->id())->get()) }}"
                                :can_create_group="{{
                                    json_encode(auth()->user()->role_id != 3 && Settings('chat_can_make_group') == 'yes')
                                 }}"
                                :asset_type="{{ json_encode('/public') }}"
                            ></side-panel-component>
                            <!--/ chat_list  -->
                        </div>

                        @if($activeUser)
                            @if(env('BROADCAST_DRIVER') == null)
                                <jquery-chat-component
                                    :new_message_check_url="{{ json_encode(route('chat.message.check')) }}"
                                    :to_user="{{ json_encode($activeUser->load('activeStatus')) }}"
                                    :from_user="{{ json_encode(auth()->user()->load('activeStatus')) }}"
                                    :send_message_url="{{ json_encode(route('chat.send')) }}"
                                    :app_url="{{ json_encode(env('APP_URL')) }}"
                                    :files_url="{{ json_encode(route('chat.files', ['type' => 'single', 'id' => $activeUser->id])) }}"
                                    :loaded_conversations="{{ json_encode(collect($messages)) }}"
                                    :connected_users="{{ json_encode(collect($users)) }}"
                                    :forward_message_url="{{ json_encode(route('chat.send.forward')) }}"
                                    :delete_message_url="{{ json_encode(route('chat.delete')) }}"
                                    :load_more_url="{{ json_encode(route('chat.load.more')) }}"
                                    :can_file_upload="{{ json_encode(Settings('chat_can_upload_file')== 'yes') }}"
                                    {{--                                        :asset_type="{{ json_encode('/public') }}"--}}
                                ></jquery-chat-component>
                            @else
                                <chat-component
                                    :to_user="{{ json_encode($activeUser->load('activeStatus')) }}"
                                    :from_user="{{ json_encode(auth()->user()->load('activeStatus')) }}"
                                    :send_message_url="{{ json_encode(route('chat.send')) }}"
                                    :app_url="{{ json_encode(env('APP_URL')) }}"
                                    :files_url="{{ json_encode(route('chat.files', ['type' => 'single', 'id' => $activeUser->id])) }}"
                                    :loaded_conversations="{{ json_encode($messages) }}"
                                    :connected_users="{{ json_encode(collect($users)) }}"
                                    :forward_message_url="{{ json_encode(route('chat.send.forward')) }}"
                                    :delete_message_url="{{ json_encode(route('chat.delete')) }}"
                                    :load_more_url="{{ json_encode(route('chat.load.more')) }}"
                                    :can_file_upload="{{ json_encode(Settings('chat_can_upload_file')== 'yes') }}"
                                    {{--                                    :asset_type="{{ json_encode('/public') }}"--}}
                                ></chat-component>
                            @endif
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
