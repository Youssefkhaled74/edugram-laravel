@php
    $user = \Illuminate\Support\Facades\Auth::user();
    $isTeacherSidebar = (int) $user->role_id === 2;

    /**
     * Instructor sidebar labels are display-only aliases.
     * Routes, permissions and menu records stay unchanged, so admin behavior is not affected.
     */
    $containsAny = function ($value, array $needles) {
        $value = strtolower((string) $value);

        foreach ($needles as $needle) {
            if (strpos($value, strtolower($needle)) !== false) {
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

        if ($route === 'dashboard' || $containsAny($haystack, ['dashboard', 'لوحة التحكم'])) {
            return 'لوحة التحكم';
        }

        if ($containsAny($haystack, ['profile', 'setting', 'settings', 'my_panel', 'account', 'الملف', 'حساب'])) {
            return 'الملف الشخصي';
        }

        if ($containsAny($haystack, ['payout', 'withdraw', 'withdrawal', 'payment', 'paid', 'unpaid', 'earnings', 'earning', 'revenue', 'المدفوعات', 'السحب', 'الأرباح'])) {
            return 'طلبات السحب';
        }

        if ($containsAny($haystack, ['instructor', 'teacher', 'المدرسون', 'المدرسين', 'المدربون'])) {
            return 'الأرباح';
        }

        if ($containsAny($haystack, ['enroll', 'student', 'students', 'student-list', 'students-list', 'الطلاب', 'المسجلين'])) {
            return 'الطلاب المسجلون';
        }

        if ($containsAny($haystack, ['course', 'courses', 'course-list', 'class-course', 'الدورات', 'المواد'])) {
            return 'دوراتي';
        }

        if ($containsAny($haystack, ['quiz', 'test', 'exam', 'question', 'bank', 'اختبار', 'اختبارات'])) {
            return 'الاختبارات';
        }

        if ($containsAny($haystack, ['virtual', 'online', 'zoom', 'bbb', 'jitsi', 'class', 'classes', 'live', 'meeting', 'حصص', 'اونلاين', 'أونلاين'])) {
            return 'الحصص الأونلاين';
        }

        if ($containsAny($haystack, ['report', 'reports', 'analytics', 'statistics', 'التقارير', 'تقرير'])) {
            return 'تقارير الأداء';
        }

        return $fallback;
    };

    $teacherSectionLabel = function ($section, $fallback) use ($isTeacherSidebar, $containsAny) {
        if (!$isTeacherSidebar) {
            return $fallback;
        }

        $haystack = (string) $fallback . ' ' . (string) ($section->name ?? '');

        foreach ($section->activeMenus as $sectionMenu) {
            $haystack .= ' ' . (string) ($sectionMenu->route ?? '') . ' ' . (string) ($sectionMenu->name ?? '');
        }

        if ($containsAny($haystack, ['payout', 'withdraw', 'payment', 'earning', 'revenue', 'instructor', 'teacher', 'users', 'USERS', 'المدرسون', 'الأرباح', 'السحب'])) {
            return 'الأرباح';
        }

        if ($containsAny($haystack, ['course', 'education', 'quiz', 'test', 'class', 'report', 'EDUCATION', 'الدورات', 'التقارير', 'التعليم'])) {
            return 'التدريس';
        }

        if ($containsAny($haystack, ['profile', 'setting', 'account', 'my_panel', 'حساب', 'الملف'])) {
            return 'حسابي';
        }

        return $fallback;
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
                        $sectionName = $section->getTranslation('name', app()->getLocale());
                        $sectionName = $teacherSectionLabel($section, $sectionName);
                    @endphp
                    @if(!empty($section->name))
                        <span class="menu_seperator">
                            {{ $sectionName }}
                        </span>
                    @endif
                    @if($section->activeMenus->count())
                        @foreach($section->activeMenus as  $menu)
                            @php
                                if (isModuleActive('LmsSaas')){
                                    if ($menu->power==1){
                                        continue;
                                    }
                                }
                                    $ignoreDynamicPage=[];
                                        $submenus =$section->activeSubmenus->where('parent_route',$menu->route)->where('parent_route','!=','dashboard');
                                        if(hasDynamicPage()){
                                            $ignoreDynamicPage=[
        //                                        'frontend.homeContent',
                                                'frontend.privacy_policy',
                                                'frontend.privacy_policy',
                                                'frontend.AboutPage',
                                                'frontend.ContactPageContent',
        //                                        'frontend.pageContent'
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
                                           $submenus =   $submenus->whereNotIn('route',$ignoreDynamicPage);
                            @endphp

                            @if(auth()->user()->role_id==1)
                                @if($menu->route == 'users.my_panel.index')
                                    @continue
                                @endif
                            @endif

                            @if(permissionCheck($menu->route))

                                @if(!$menu->module ||  isModuleActive($menu->module))

                                    @php
                                        $hasChild =$submenus->count();

                                        if ($menu->theme && $menu->theme!=currentTheme()){
                                            $hasChild--;
                                            continue;
                                        }

                                        $menuName = $menu->getTranslation('name', app()->getLocale());
                                        $menuName = $teacherMenuLabel($menu, $menuName);
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
                                                        if (isModuleActive('LmsSaas')){
                                                           if ($submenu->power==1){
                                                               continue;
                                                           }
                                                       }
                                                    @endphp
                                                    @if(permissionCheck($submenu->route))
                                                        @if(!$submenu->module ||  isModuleActive($submenu->module))
                                                            @php
                                                                if ($submenu->theme && $submenu->theme!=currentTheme()){
                                                                      continue;
                                                                  }

                                                                $submenuName = $submenu->getTranslation('name', app()->getLocale());
                                                                $submenuName = $teacherMenuLabel($submenu, $submenuName);
                                                            @endphp
                                                            <li class="{{spn_active_link(childrenRoute($submenu))}}">
                                                                <a href="@if(!empty(validRouteUrl($submenu->route))) {{validRouteUrl($submenu->route)}} @else # @endif">
                                                                    {{ $submenuName }}
                                                                </a>
                                                            </li>
                                                        @endif
                                                    @endif
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                @endif
                            @endif

                        @endforeach
                    @endif
                @endforeach
            @endif
        @endif
    </ul>

</nav>
