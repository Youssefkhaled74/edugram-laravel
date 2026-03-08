@extends(app('extend_view'))
@section('title'){{Settings('site_title') ? Settings('site_title') : 'Infix LMS'}} | @lang('chat.Search result of') @endsection


@section('mainContent')
    <section class="admin-visitor-area up_st_admin_visitor" id="admin-visitor-area">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="chat_main_wrapper">
                        <side-panel-component
                            :asset_type="{{ json_encode('/public') }}"
                            :search_url="{{ json_encode(route('chat.user.search')) }}"
                            :single_chat_url="{{ json_encode(route('chat.index')) }}"
                            :chat_block_url="{{ json_encode(route('chat.user.block')) }}"
                            :create_group_url="{{ json_encode(route('chat.group.create')) }}"
                            :group_chat_show="{{ json_encode(route('chat.group.show')) }}"
                            :users="{{ json_encode($users) }}"
                            :groups="{{ json_encode(collect()) }}"
                            :all_users="{{ json_encode(\App\Models\User::where('id', '!=', auth()->id())->get()) }}"
                            :can_create_group="{{
                                    json_encode(auth()->user()->role_id != 3 && Settings('chat_can_make_group') == 'yes')
                                 }}"
                        ></side-panel-component>
                        <div class="chat_flow_list_wrapper ">
                            <div class="box_header">
                                <div class="main-title">
                                    <h3 class="m-0">@lang('chat.search_result_of') '{{ $keywords }}'</h3>
                                </div>
                            </div>
                            <!-- chat_list  -->
                            <div class="chat_flow_list crm_full_height">

                                <div class="main-title2 mt-0">
                                    <h4 class="">@lang('chat.people')</h4>
                                </div>

                                <div class="chat_flow_list_inner">
                                    <ul>
                                        @forelse($users as $index => $user)
                                        <li>
                                            <div class="single_list d-flex align-items-center">
                                                <div class="thumb">
                                                    @if($user->avatar)
                                                        <img src="{{asset($user->avatar)}}" alt="">
                                                    @else
                                                        <img src="{{asset('images/spondon-icon.png')}}" alt="">
                                                    @endif
                                                </div>
                                                <div class="list_name">
                                                    <a href="#">
                                                        <h4>{{ $user->first_name }} {{ $user->last_name }}
                                                            @include('chat::partials._active_status')
                                                        </h4>
                                                    </a>
                                                </div>
                                                <div>
                                                    @if(Settings('chat_invitation_requirement') == 'none')
                                                        <form action="{{ route('chat.invitation.open') }}" method="post">
                                                            @csrf
                                                            <input type="hidden" value="{{ $user->id }}" name="to">
                                                            <a class="primary-btn radius_30px  fix-gr-bg" onclick="$(this).closest('form').submit();" href="#">
                                                                @lang('chat.start')
                                                            </a>
                                                        </form>
                                                    @else
                                                        @if(!$user->connectedWithLoggedInUser())
                                                            <form action="{{ route('chat.invitation.create') }}" method="post">
                                                                @csrf
                                                                <input type="hidden" value="{{ $user->id }}" name="to">
                                                                <a href="#" onclick="$(this).closest('form').submit();" class="round-icon single-icon primary-btn small fix-gr-bg">
                                                                    <span class="ti-plus pr-2"></span>
                                                                </a>
                                                            </form>
                                                        @else
                                                            @if($user->connectionPending())
                                                                <p class="round-icon single-icon primary-btn small fix-gr-bg text-white">
                                                                    <span class="ti-timer pr-2"></span>
                                                                </p>
                                                            @elseif($user->connectionSuccess())
                                                                <p class="round-icon single-icon primary-btn small fix-gr-bg text-white">
                                                                    <span class="ti-check pr-2"></span>
                                                                </p>
                                                            @else
                                                                <p class="round-icon single-icon primary-btn small fix-gr-bg text-white">
                                                                    <span class="ti-close pr-2"></span>
                                                                </p>
                                                            @endif
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                        </li>
                                        <div class="modal fade admin-query" id="profileEditForm{{$index}}" aria-modal="true">
                                                <div class="modal-dialog modal_800px modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">
                                                                <div class="thumb" style="display: inline">
                                                                    <a href="#"><img src="{{ asset($user->avatar) }}" height="50" width="50" alt=""></a>
                                                                </div>
                                                                {{ $user->first_name }} {{ $user->last_name }}
                                                            </h4>
                                                            <button type="button" class="close" data-dismiss="modal">
                                                                <i class="ti-close "></i>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row">
                                                                <div class="col-xl-6">
                                                                    <div class="primary_input mb-25">
                                                                        <label class="primary_input_label" for="">User Name <span class="text-danger">*</span></label>
                                                                        <input name="name" disabled class="primary_input_field name" value="{{ $user->username }}" type="text">
                                                                    </div>
                                                                </div>
                                                                <div class="col-xl-6">
                                                                    <div class="primary_input mb-25">
                                                                        <label class="primary_input_label" for="">Email <span class="text-danger">*</span></label>
                                                                        <input name="email" class="primary_input_field name" disabled value="{{ $user->email }}" type="email" readonly="">
                                                                        <span class="text-danger"></span>
                                                                    </div>
                                                                </div>

                                                                <div class="col-xl-6">
                                                                    <div class="primary_input mb-25">
                                                                        <label class="primary_input_label" for="">Phone</label>
                                                                        <input name="username" class="primary_input_field name" disabled value="{{ $user->phone }}" type="text" readonly="">
                                                                    </div>
                                                                    @if($user->blockedByMe())
                                                                        <a href="{{ route('chat.user.block', ['type' => 'unblock', 'user' => $user->id]) }}" class="primary-btn small fix-gr-bg"><span class="ripple rippleEffect" style="width: 30px; height: 30px; top: -6.99219px; left: 19.2578px;"></span>
                                                                            @lang('unblock_this_user')
                                                                        </a>
                                                                    @else
                                                                        <a href="{{ route('chat.user.block', ['type' => 'block', 'user' => $user->id]) }}" class="primary-btn small fix-gr-bg"><span class="ripple rippleEffect" style="width: 30px; height: 30px; top: -6.99219px; left: 19.2578px;"></span>
                                                                            @lang('chat.block_this_user')
                                                                        </a>
                                                                    @endif
                                                                </div>

                                                                <div class="col-xl-6">
                                                                    <div class="primary_input mb-25">
                                                                        <label class="primary_input_label" for="">@lang('chat.description')</label>
                                                                        <p>
                                                                            {{ $user->description }}
                                                                        </p>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <p>@lang('no') @lang('user') @lang('found')</p>
                                        @endforelse

                                    </ul>
                                </div>
                            </div>
                            <!--/ chat_list  -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
