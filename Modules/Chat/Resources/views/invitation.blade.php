@extends(app('extend_view'))
@section('title')
    {{Settings('site_title') ? Settings('site_title') : 'Infix LMS'}} | @lang('chat.chat') @lang('chat.invitation')
@endsection

@section('mainContent')
    @if(\Illuminate\Support\Facades\Auth::user()->role_id!=3)
        {{generateBreadcrumb()}}
    @endif
    <section class="admin-visitor-area up_st_admin_visitor ">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-xl-6 mt-5 mt-xl-0">
                    <div class="chat_flow_list_wrapper ">
                        <div class="box_header">
                            <div class="main-title">
                                <h3 class="m-0">@lang('chat.you_requests')</h3>
                            </div>
                        </div>
                        <div class="chat_flow_list crm_full_height">
                            <div class="chat_flow_list_inner">
                                <ul>
                                    @forelse($ownRequest as $myRequest)
                                        <li>
                                            <div class="single_list d-flex align-items-center">
                                                <div class="thumb">
                                                    @if($myRequest->requestTo->avatar)
                                                        <a href="#"><img src="{{asset($myRequest->requestTo->avatar)}}"
                                                                         alt=""></a>
                                                    @else
                                                        <a href="#"><img src="{{asset('images/spondon-icon.png')}}"
                                                                         alt=""></a>
                                                    @endif
                                                </div>
                                                <div class="list_name">
                                                    <a href="#"><h4>{{ $myRequest->requestTo->first_name }} <span
                                                                    class="active_chat"></span></h4></a>
                                                    <p>Your request to {{ $myRequest->requestTo->first_name }} </p>
                                                </div>
                                            </div>
                                        </li>
                                    @empty
                                        <p>@lang('chat.no') @lang('chat.connection') @lang('chat.request') @lang('chat.found')</p>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6 mt-5 mt-xl-0">
                    <div class="chat_flow_list_wrapper">
                        <div class="box_header">
                            <div class="main-title">
                                <h3 class="m-0">@lang('chat.people') @lang('chat.requests') @lang('chat.you') @lang('chat.to') @lang('chat.connect')</h3>
                            </div>
                        </div>
                        <div class="chat_flow_list crm_full_height">
                            <div class="chat_flow_list_inner">
                                <ul>
                                    @forelse($peopleRequest as $request)
                                        <li>
                                            <div class="single_list d-flex align-items-center">
                                                <div class="thumb">
                                                    @if($request->requestFrom->avatar)
                                                        <a href="#"><img src="{{asset($request->requestFrom->avatar)}}"
                                                                         alt=""></a>
                                                    @else
                                                        <a href="#"><img src="{{asset('images/spondon-icon.png')}}"
                                                                         alt=""></a>
                                                    @endif
                                                </div>
                                                <div class="list_name w-50">
                                                    <a href="#"><h4>{{ $request->requestFrom->first_name }} <span
                                                                    class="active_chat"></span></h4></a>
                                                    <p>{{ $request->requestFrom->first_name }} @lang('chat.requested') @lang('chat.to') @lang('chat.connect')</p>
                                                </div>
                                                <div>
                                                    <a href="{{ route('chat.invitation.action',['type' => 'accept', 'id' => $request->id]) }}"
                                                       class="single-icon primary-btn small fix-gr-bg text-white"
                                                       title="Accept">
                                                        <span class="ti-check pr-2"></span>
                                                    </a>

                                                    <a href="{{ route('chat.invitation.action',['type' => 'reject', 'id' => $request->id]) }}"
                                                       class="single-icon primary-btn small fix-gr-bg text-white"
                                                       title="Reject">
                                                        <span class="ti-close pr-2"></span>
                                                    </a>
                                                </div>
                                            </div>
                                        </li>
                                    @empty
                                        <p>@lang('chat.no') @lang('chat.connection') @lang('chat.request') @lang('chat.found')</p>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col-xl-6">
                    <div class="chat_flow_list_wrapper ">
                        <div class="box_header">
                            <div class="main-title">
                                <h3 class="m-0">@lang('chat.people_connected_with_you')</h3>
                            </div>
                        </div>
                        <!-- chat_list  -->
                        <div class="chat_flow_list crm_full_height">
                            <div class="chat_flow_list_inner">
                                <ul>
                                    @forelse($connectedPeoples as $request)
                                        <li>
                                            <div class="single_list d-flex align-items-center">
                                                <div class="thumb">
                                                    @if($request->avatar)
                                                        <a href="#"><img src="{{asset($request->avatar)}}" alt=""></a>
                                                    @else
                                                        <a href="#"><img src="{{asset('images/spondon-icon.png')}}"
                                                                         alt=""></a>
                                                    @endif
                                                </div>
                                                <div class="list_name w-50">
                                                    <a href="#"><h4>{{ $request->first_name }} <span
                                                                    class="active_chat"></span></h4></a>
                                                    <p>{{ $request->first_name }} @lang('chat.connected_with_you')</p>
                                                </div>
                                            </div>
                                        </li>
                                    @empty
                                        <p>@lang('chat.no') @lang('chat.connection') @lang('chat.request') @lang('chat.found')</p>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
