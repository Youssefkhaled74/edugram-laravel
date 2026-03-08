@extends(app('extend_view'))
@section('title')
    {{Settings('site_title') ? Settings('site_title') : 'Infix LMS'}} | @lang('chat.chat') @lang('chat.new')
@endsection

@section('mainContent')
    {{generateBreadcrumb()}}
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
                                <a class="primary-btn radius_30px  fix-gr-bg" href="{{ route('chat.new') }}"><i
                                        class="ti-plus"></i>@lang('chat.new') @lang('chat.chat')</a>
                            </div>
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
                        </div>

                        <div class="chat_flow_list_wrapper ">
                            <div class="box_header">
                                <div class="main-title">
                                    <h3 class="m-0">@lang('chat.list')</h3>
                                </div>
                            </div>
                            <div class="chat_flow_list crm_full_height">
                                <div class="chat_flow_list_inner">
                                    <ul>
                                        @forelse($users as $user)
                                            <li>
                                                <div class="single_list d-flex align-items-center">
                                                    <div class="thumb">
                                                        <a href="{{ route('chat.index', $user->id) }}">
                                                            @if($user->avatar)
                                                                <img src="{{asset($user->avatar)}}" alt="">
                                                            @elseif($user->avatar_url)
                                                                <img src="{{asset($user->avatar_url)}}" alt="">
                                                            @else
                                                                <img src="{{asset('chat/images/spondon-icon.png')}}"
                                                                     alt="">
                                                            @endif
                                                        </a>
                                                    </div>
                                                    <div class="list_name">
                                                        <a href="{{ route('chat.index', $user->id) }}">
                                                            <h4>{{ $user->first_name }} {{ $user->last_name }}
                                                                <span class="active_chat"></span>
                                                            </h4>
                                                        </a>
                                                    </div>
                                                    <a style="padding: 7px 40px 7px 25px;"
                                                       href="{{ route('chat.index', $user->id) }}"
                                                       class="primary-btn radius_30px fix-gr-bg">@lang('chat.start')</a>
                                                </div>
                                            </li>
                                        @empty
                                            <p>@lang('chat.no') @lang('chat.user') @lang('chat.found') @lang('chat.to') @lang('chat.chat')
                                                !</p>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
