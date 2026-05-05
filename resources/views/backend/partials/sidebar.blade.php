@php
    $user = \Illuminate\Support\Facades\Auth::user();
    $isTeacherSidebar = isInstructor();

    $containsAny = function ($value, array $needles) {
        $value = mb_strtolower((string) $value);
        foreach ($needles as $needle) {
            if (mb_strpos($value, mb_strtolower($needle)) !== false) {
                return true;
            }
        }
        return false;
    };

    $teacherMenuLabel = function ($item, $fallback) use ($isTeacherSidebar, $containsAny) {
        if (!$isTeacherSidebar) {
            return $fallback;
        }

        $route = (string) ($item->route ?? '');
        $label = (string) $fallback;
        $haystack = $route . ' ' . $label;

        if (
            $containsAny($haystack, ['الدورات', 'courses'])
            || preg_match('/(^|\\.)course(s)?(\\.|$)/i', $route)
        ) {
            return 'كورساتي';
        }

        if ($containsAny($haystack, ['payment', 'الدفع']) && $containsAny($haystack, ['withdraw', 'payout', 'earning', 'revenue', 'سحب', 'أرباح', 'إيرادات'])) {
            return 'طلبات السحب';
        }

        return $fallback;
    };

    $shouldHideForTeacher = function ($item, $label) use ($isTeacherSidebar, $containsAny) {
        if (!$isTeacherSidebar) {
            return false;
        }

        $route = (string) ($item->route ?? '');
        $haystack = $route . ' ' . (string) $label;

        $isPaymentLike = $containsAny($haystack, [
            'deposit', 'payment', 'checkout', 'gateway', 'wallet',
            'إيداع', 'الدفع', 'دفع', 'طريقة الدفع', 'محفظة', 'سلة'
        ]);
        $isTeacherAccountingLike = $containsAny($haystack, [
            'withdraw', 'payout', 'earning', 'earnings', 'revenue', 'statement',
            'سحب', 'السحب', 'أرباح', 'الإيرادات', 'ايرادات', 'كشف الحساب', 'الحسابات'
        ]);

        return $isPaymentLike && !$isTeacherAccountingLike;
    };
@endphp
<!-- sidebar part here -->
<nav id="sidebar" class="sidebar  {{$user->sidebar!=1?'d-none':''}}">

    <div class="sidebar-header update_sidebar">
        <a class="large_logo" href="{{ url('/') }}">
            <img src="{{ getLogoImage(Settings('logo')) }}" alt="">
        </a>
        <a class="mini_logo" href="{{ url('/') }}">
            <img src="{{ getLogoImage(Settings('logo')) }}" alt="">
        </a>
        <a id="close_sidebar" class="d-lg-none">
            <i class="ti-close"></i>
        </a>
    </div>
    @if($user->role_id!=1)
        <div class="sidebar-user text-center">
            <div class="sidebar-profile mx-auto">
                <img src="{{getProfileImage($user->image,Auth::user()->name)}}"
                     alt="">
            </div>
            <h4>{{$user->name}} </h4>
            @if(isModuleActive('UserGroup') && $user->userGroup  && $user->userGroup->group->status)
                <p class="text-nowrap mb-2">{{$user->userGroup->group->title}}</p>
            @endif

            <div class="sidebar-badge">
                @php
                    $already=[];
                @endphp
                @foreach($user->userLatestBadges as $badge)
                    @php
                        $b =$badge->badge;

                        if (in_array($b->type,$already)){
                            continue;
                        }else{
                            $already[]=$b->type;
                        }
                    @endphp
                    <div class="sidebar-badge-list"
                         data-bs-toggle="tooltip" data-placement="top"
                         title="{{$b->title}} {{ucfirst($b->type)}} {{trans('setting.Badge')}}">
                        <img
                            src="{{asset($b->image)}}" alt=""></div>
                @endforeach

            </div>
        </div>
    @endif
    <ul id="sidebar_menu">
        @if($isTeacherSidebar)
            <li class="{{request()->routeIs('teacher.question-banks.*','teacher.questions.*') ? 'mm-active' : ''}}">
                <a href="{{ route('teacher.question-banks.index') }}" aria-expanded="false">
                    <div class="nav_icon_small">
                        <span class="fas fa-question-circle"></span>
                    </div>
                    <div class="nav_title">
                        <span>بنك الأسئلة</span>
                    </div>
                </a>
            </li>
            <li class="{{request()->routeIs('teacher.statistics.*','teacher.courses.analytics') ? 'mm-active' : ''}}">
                <a href="{{ route('teacher.statistics.index') }}" aria-expanded="false">
                    <div class="nav_icon_small">
                        <span class="fas fa-chart-line"></span>
                    </div>
                    <div class="nav_title">
                        <span>الإحصائيات</span>
                    </div>
                </a>
            </li>
        @endif

        @if ((isModuleActive('LmsSaas') || isModuleActive('LmsSaasMD')) && SaasDomain() != 'main' && !hasActiveSaasPlan())
            <li>
                <a href="#" class="has-arrow" aria-expanded="false">
                    <div class="nav_icon_small">
                        <span class="fas fa-university"></span>
                    </div>
                    <div class="nav_title">
                        <span>{{ __('saas.Saas Management') }}</span>
                    </div>
                </a>

                <ul>
                    <li>
                        <a href="{{ route('saas.myPlan') }}">{{ __('saas.My Plan') }}</a>
                    </li>
                </ul>
            </li>
        @else

            @if ((isModuleActive('LmsSaas') || isModuleActive('LmsSaasMD')) && SaasDomain() != 'main' && hasActiveSaasPlan())
                <li>
                    <a href="#" class="has-arrow" aria-expanded="false">
                        <div class="nav_icon_small">
                            <span class="fas fa-university"></span>
                        </div>
                        <div class="nav_title">
                            <span>{{ __('saas.Saas Management') }}</span>
                        </div>
                    </a>

                    <ul>
                        <li>
                            <a href="{{ route('saas.myPlan') }}">{{ __('saas.My Plan') }}</a>
                        </li>
                    </ul>
                </li>
            @endif
            @if(isset($sections))
                @foreach($sections as $key => $section)
                    @php
                        $count = $section->activeMenus->count();
                        if ($count == 0){
                            continue;
                        }
                    @endphp
                    @if(!empty($section->name))
                        <span class="menu_seperator">
                            {{$section->getTranslation('name', app()->getLocale())}}
                        </span>
                    @endif
                    @if($section->activeMenus->count())
                        @foreach($section->activeMenus as  $menu)
                            @php
                                if (isModuleActive('LmsSaas') && $menu->power==1){
                                    continue;
                                }
                                $ignoreDynamicPage=[];
                                $submenus =$section->activeSubmenus->where('parent_route',$menu->route)->where('parent_route','!=','dashboard');
                                if(hasDynamicPage()){
                                    $ignoreDynamicPage=[
                                        'frontend.privacy_policy',
                                        'frontend.privacy_policy',
                                        'frontend.AboutPage',
                                        'frontend.ContactPageContent',
                                    ];
                                }
                                if (isModuleActive('AdvanceQuiz')){
                                    $setup =\Modules\Quiz\Entities\QuizeSetup::getData();
                                    if ($setup->advance_test_mode_status!=1){
                                        $ignoreDynamicPage[] ='quiz.test-list';
                                        $ignoreDynamicPage[] ='quiz.mark-test';
                                        $ignoreDynamicPage[] ='quiz.supervisor';
                                    }
                                }
                                $submenus = $submenus->whereNotIn('route',$ignoreDynamicPage);
                            @endphp

                            @if(auth()->user()->role_id==1 && $menu->route == 'users.my_panel.index')
                                @continue
                            @endif

                            @if(permissionCheck($menu->route) && (!$menu->module ||  isModuleActive($menu->module)))
                                @php
                                    $hasChild = $submenus->count();
                                    if ($menu->theme && $menu->theme!=currentTheme()){
                                        $hasChild--;
                                        continue;
                                    }

                                    $menuName = $teacherMenuLabel($menu, $menu->getTranslation('name', app()->getLocale()));
                                    if ($shouldHideForTeacher($menu, $menuName)) {
                                        continue;
                                    }
                                @endphp

                                <li class="{{spn_active_link(childrenRoute($menu))}}">
                                    <a href="@if(!$hasChild && validRouteUrl($menu->route)) {{validRouteUrl($menu->route)}} @else # @endif"
                                       class=" @if($hasChild) has-arrow @endif"
                                       aria-expanded="false">
                                        <div class="nav_icon_small">
                                            <span class="{{@$menu->icon??'fas fa-th'}}"></span>
                                        </div>
                                        <div class="nav_title">
                                            <span>{{ $menuName }}</span>
                                            @if((env('APP_SYNC') || config('app.demo_mode'))&& !empty($menu->module))
                                                <span class="demo_addons">Addon</span>
                                            @endif
                                        </div>
                                    </a>
                                    @if($hasChild)
                                        <ul>
                                            @foreach($submenus as $submenu)
                                                @php
                                                    if (isModuleActive('LmsSaas') && $submenu->power==1){
                                                        continue;
                                                    }
                                                @endphp
                                                @if(permissionCheck($submenu->route) && (!$submenu->module ||  isModuleActive($submenu->module)))
                                                    @php
                                                        if ($submenu->theme && $submenu->theme!=currentTheme()){
                                                            continue;
                                                        }

                                                        $submenuName = $teacherMenuLabel($submenu, $submenu->getTranslation('name', app()->getLocale()));
                                                        if ($shouldHideForTeacher($submenu, $submenuName)) {
                                                            continue;
                                                        }
                                                    @endphp
                                                    <li class="{{spn_active_link(childrenRoute($submenu))}}">
                                                        <a href="@if(!empty(validRouteUrl($submenu->route))) {{validRouteUrl($submenu->route)}} @else # @endif">
                                                            {{ $submenuName }}
                                                        </a>
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endif

                        @endforeach
                    @endif
                @endforeach
            @endif
        @endif
    </ul>

</nav>
