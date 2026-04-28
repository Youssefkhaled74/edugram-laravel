@extends(theme('layouts.dashboard_master'))
@section('title'){{Settings('site_title')  ? Settings('site_title')  : 'Infix LMS'}} | {{__('payment.Fund Deposit')}} @endsection
@section('css') @endsection
@section('js')
    <script src="{{asset('public/frontend/infixlmstheme/js/deposit.js')}}"></script>
    <script>
        (function () {
            const input = document.getElementById('points_to_convert');
            const output = document.getElementById('points-convert-preview');
            if (!input || !output) {
                return;
            }
            const rate = Number(input.dataset.rate || 10);
            const render = () => {
                const points = parseInt(input.value || 0, 10);
                const amount = points > 0 ? (points / rate) : 0;
                output.textContent = Number.isFinite(amount) ? amount : 0;
            };
            input.addEventListener('input', render);
            render();
        })();
    </script>


@endsection

@section('mainContent')
    <x-deposit-page-section :request="$request"/>
@endsection
