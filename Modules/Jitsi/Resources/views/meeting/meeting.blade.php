@extends('backend.master')
@section('mainContent')

    <section class="sms-breadcrumb mb-40 white-box">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>{{__('jitsi.Jitsi')}} {{__('jitsi.Classes')}} {{__('jitsi.Manage')}}</h1>
                <div class="bc-pages">
                    <a href="{{route('dashboard')}}">{{__('dashboard.Dashboard')}}</a>
                    <a href="{{ route('virtual-class.index') }}">{{__('jitsi.Classes')}}</a>
                    <a href="#">{{__('jitsi.List')}}</a>
                </div>
            </div>
        </div>
    </section>


    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row">
                @include('jitsi::meeting.includes.form')
                @include('jitsi::meeting.includes.list')
            </div>
        </div>
    </section>
    <input type="hidden" name="get_user" class="get_user" value="{{ url('get-user-by-role') }}">

@endsection

@push('scripts')
    <script src="{{asset('public/backend/js/zoom.js')}}"></script>
@endpush
