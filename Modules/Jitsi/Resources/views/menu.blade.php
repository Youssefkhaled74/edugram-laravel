<li>
    <a href="#" class="has-arrow" aria-expanded="false">
        <div class="nav_icon_small">
            <i class="fas fa-vr-cardboard"></i>
        </div>
        <div class="nav_title">
            <span>{{__('jitsi.Jitsi')}}</span>
            @if(env('APP_SYNC'))
                <span class="demo_addons">Addon</span>
            @endif
        </div>
    </a>
    <ul>

        @if (permissionCheck('jitsi.settings'))
            <li>
                <a href="{{ route('jitsi.settings') }}">  {{__('jitsi.Jitsi Setting')}}</a>
            </li>
        @endif
    </ul>
</li>
