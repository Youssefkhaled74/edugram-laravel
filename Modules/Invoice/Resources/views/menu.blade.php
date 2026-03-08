@if (permissionCheck('invoice'))
<li>
    <a href="javascript:;" class="has-arrow" aria-expanded="false">
        <div class="nav_icon_small">
            <span class="fas fa-file-invoice"></span>
        </div>
        <div class="nav_title">
            <span>{{ __('invoice.Invoice') }}</span>
        </div>
    </a>
    <ul>
        @if (permissionCheck('invoice.offline-payment'))
        <li>
            <a href="{{ route('invoice.offline-payment') }}">{{ __('invoice.Pending Offline Payment') }}</a>
        </li>
        @endif
        @if (permissionCheck('invoice.index'))
        <li>
            <a href="{{ route('invoice.index') }}">{{ __('invoice.Invoice') }}</a>
        </li>
        @endif
        @if (permissionCheck('invoice.settings.index'))
        <li>
            <a href="{{ route('invoice.settings.index') }}">{{ __('invoice.Settings') }}</a>
        </li>
        @endif

    </ul>
</li>
@endif
{{-- printed certificate --}}
@if (permissionCheck('prc'))
<li>
    <a href="javascript:;" class="has-arrow" aria-expanded="false">
        <div class="nav_icon_small">
            <span class="fas fa-file-invoice"></span>
        </div>
        <div class="nav_title">
            <span>{{ __('invoice.Printed certificate') }}</span>
        </div>
    </a>
    <ul>
        @if (permissionCheck('prc.order.index'))
        <li>
            <a href="{{ route('prc.order.index') }}">{{ __('invoice.Order List') }}</a>
        </li>
        @endif
        @if (permissionCheck('prc.settings.index'))
        <li>
            <a href="{{ route('prc.settings.index') }}">{{ __('common.Settings') }}</a>
        </li>
        @endif       

    </ul>
</li>
@endif