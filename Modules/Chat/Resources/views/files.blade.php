@extends(app('extend_view'))
@section('title'){{Settings('site_title') ? Settings('site_title') : 'Infix LMS'}} | @lang('chat.files') @endsection

@section('mainContent')
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="chat_main_wrapper">
                        <div class="chat_flow_list_wrapper ">
                            <div class="box_header">
                                <div class="main-title">
                                    <h3 class="m-0">@lang('chat.chat_list')</h3>
                                </div>
                                <a class="primary-btn radius_30px  fix-gr-bg" href="{{ route('chat.new') }}"><i class="ti-plus"></i>@lang('chat.new') @lang('chat.chat')</a>
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
                                :asset_type="{{ json_encode('/public') }}"
                                :all_users="{{ json_encode(\App\Models\User::where('id', '!=', auth()->id())->get()) }}"
                                :can_create_group="{{
                                    json_encode(auth()->user()->role_id != 3 && Settings('chat_can_make_group') == 'yes')
                                 }}"
                            ></side-panel-component>
                            <!--/ chat_list  -->
                        </div>

                        <div class="chat_view_list ">
                            <div class="box_header">
                                <a class="primary-btn radius_30px  fix-gr-bg text-white">
                                    {{ $name }}'s @lang('files')
                                </a>

                            </div>

                            <div class="row align-items-center fileshow">
                                @forelse($messages as $message)
                                    <div class="single-entry">
                                        @if($message->message_type == 1)
                                            <img style="max-height: 120px;width: 100%;" class="img-fluid" src="{{ asset($message->file_name) }}" alt="">
                                        @elseif( in_array($message->message_type, [2,3]) )
                                            @if($type == 'group')
                                            <a href="{{ route('chat.file.download.group', ['id' => $message->id, 'group' => $group->id]) }}">{{ $message->original_file_name }}</a>
                                            @else
                                                <a href="{{ route('chat.file.download', $message->id) }}">{{ $message->original_file_name }}</a>
                                            @endif
                                        @else
                                            <div>
                                                <audio class="w-100" src="{{ asset($message->file_name) }}" controls></audio>
                                            </div>
                                        @endif
                                    </div>
                                @empty
                                    <p>@lang('no') @lang('files') @lang('found')!</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
