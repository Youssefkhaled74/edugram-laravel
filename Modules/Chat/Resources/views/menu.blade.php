@if(permissionCheck('chat.index'))
<li>
    <a href="#" class="has-arrow" aria-expanded="false">
        <div class="nav_icon_small">
            <span class="fa fa-comment"></span>
        </div>
        <div class="nav_title">
            <span>@lang('chat.chat')</span>
            @if(env('APP_SYNC'))
                <span class="demo_addons">Addon</span>
            @endif
        </div>
    </a>
    <ul>
        @if(permissionCheck('chat.index'))
        <li>
            <a href="{{ route('chat.index') }}">{{ __('chat.chat_box') }}</a>
        </li>
        @endif

        @if(permissionCheck('chat.invitation'))
            <li>
                <a href="{{ route('chat.invitation') }}">{{ __('chat.invitation') }}</a>
            </li>
        @endif

        @if(permissionCheck('chat.blocked.users'))
            <li>
                <a href="{{ route('chat.blocked.users') }}">{{ __('chat.blocked_user') }}</a>
            </li>
        @endif

        @if(permissionCheck('chat.settings'))
            <li>
                <a href="{{ route('chat.settings') }}">{{ __('Settings') }}</a>
            </li>
        @endif
    </ul>
</li>
@endif
