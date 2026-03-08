@extends(app('extend_view'))
@section('title')
    {{Settings('site_title') ? Settings('site_title') : 'Infix LMS'}} | @lang('chat.blocked') @lang('chat.user')
@endsection

@section('mainContent')
    @if(\Illuminate\Support\Facades\Auth::user()->role_id!=3)
        {{generateBreadcrumb()}}
    @endif
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-xl-12 mt-5 mt-lg-0">

                    <div class="box_header">
                        <div class="main-title">
                            <h3 class="m-0">@lang('chat.blocked_user')</h3>
                        </div>
                    </div>
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
                                                <a href="#" data-toggle="modal"
                                                   data-target="#profileEditForm{{$index}}">
                                                    @if($user->avatar)
                                                        <img src="{{ asset($user->avatar) }}" alt="">
                                                    @else
                                                        <img src="{{ asset('public/chat/images/spondon-icon.png') }}"
                                                             alt="">
                                                    @endif
                                                </a>
                                            </div>
                                            <div class="list_name">
                                                <a href="#"><h4>{{ $user->first_name }} {{ $user->last_name }} <span
                                                                class="active_chat"></span></h4></a>
                                            </div>
                                            <div>
                                                <a href="{{ route('chat.user.block', ['type' => 'unblock', 'user' => $user->id]) }}"
                                                   class="primary-btn small fix-gr-bg"><span class="ripple rippleEffect"
                                                                                             style="width: 30px; height: 30px; top: -6.99219px; left: 19.2578px;"></span>
                                                    @lang('chat.unblock')
                                                </a>
                                            </div>
                                        </div>
                                    </li>
                                    <div class="modal fade admin-query" id="profileEditForm{{$index}}"
                                         aria-modal="true">
                                        <div class="modal-dialog modal_800px modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title">
                                                        <div class="thumb" style="display: inline">
                                                            <a href="#"><img src="{{ asset($user->avatar) }}"
                                                                             height="50" width="50" alt=""></a>
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
                                                                <label class="primary_input_label" for="">User Name
                                                                    <span class="text-danger">*</span></label>
                                                                <input name="name" disabled
                                                                       class="primary_input_field name"
                                                                       placeholder="Name" value="{{ $user->username }}"
                                                                       type="text">
                                                            </div>
                                                        </div>
                                                        <div class="col-xl-6">
                                                            <div class="primary_input mb-25">
                                                                <label class="primary_input_label" for="">Email <span
                                                                            class="text-danger">*</span></label>
                                                                <input name="email" class="primary_input_field name"
                                                                       disabled placeholder="Email"
                                                                       value="{{ $user->email }}" type="email"
                                                                       readonly="">
                                                                <span class="text-danger"></span>
                                                            </div>
                                                        </div>

                                                        <div class="col-xl-6">
                                                            <div class="primary_input mb-25">
                                                                <label class="primary_input_label" for="">Phone</label>
                                                                <input name="username" class="primary_input_field name"
                                                                       disabled value="{{ $user->phone }}" type="text"
                                                                       readonly="">
                                                            </div>
                                                            @if($user->blockedByMe())
                                                                <a href="{{ route('chat.user.block', ['type' => 'unblock', 'user' => $user->id]) }}"
                                                                   class="primary-btn small fix-gr-bg"><span
                                                                            class="ripple rippleEffect"
                                                                            style="width: 30px; height: 30px; top: -6.99219px; left: 19.2578px;"></span>
                                                                    @lang('chat.unblock_this_user')
                                                                </a>
                                                            @else
                                                                <a href="{{ route('chat.user.block', ['type' => 'block', 'user' => $user->id]) }}"
                                                                   class="primary-btn small fix-gr-bg"><span
                                                                            class="ripple rippleEffect"
                                                                            style="width: 30px; height: 30px; top: -6.99219px; left: 19.2578px;"></span>
                                                                    @lang('chat.block_this_user')
                                                                </a>
                                                            @endif
                                                        </div>

                                                        <div class="col-xl-6">
                                                            <div class="primary_input mb-25">
                                                                <label class="primary_input_label"
                                                                       for="">@lang('chat.description')</label>
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
                </div>
            </div>
        </div>
    </section>
@endsection
