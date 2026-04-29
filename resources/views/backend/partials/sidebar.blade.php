@php
    $user = \Illuminate\Support\Facades\Auth::user();
    $isTeacherSidebar = isInstructor();
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
            <span class="menu_seperator">التدريس</span>

            <li class="{{ request()->routeIs('dashboard') ? 'mm-active' : '' }}">
                <a href="{{ route('dashboard') }}">
                    <div class="nav_icon_small">
                        <span class="fas fa-tachometer-alt"></span>
                    </div>
                    <div class="nav_title"><span>لوحة التحكم</span></div>
                </a>
            </li>

            <li class="{{ request()->routeIs('getAllCourse') || request()->routeIs('course.store') ? 'mm-active' : '' }}">
                <a href="#" class="has-arrow" aria-expanded="false">
                    <div class="nav_icon_small">
                        <span class="fas fa-book"></span>
                    </div>
                    <div class="nav_title"><span>كورساتي</span></div>
                </a>
                <ul>
                    @if(Route::has('getAllCourse') && permissionCheck('getAllCourse'))
                        <li class="{{ request()->routeIs('getAllCourse') ? 'mm-active' : '' }}">
                            <a href="{{ route('getAllCourse') }}">كل الكورسات</a>
                        </li>
                    @endif
                    @if(Route::has('course.store') && permissionCheck('course.store'))
                        <li class="{{ request()->routeIs('course.store') ? 'mm-active' : '' }}">
                            <a href="{{ route('course.store') }}">إنشاء كورس جديد</a>
                        </li>
                    @endif
                    <li><a href="#">مسودات الكورسات (قريباً)</a></li>
                    <li><a href="#">الكورسات بانتظار الموافقة (قريباً)</a></li>
                </ul>
            </li>

            <li>
                <a href="#" class="has-arrow" aria-expanded="false">
                    <div class="nav_icon_small">
                        <span class="fas fa-chalkboard-teacher"></span>
                    </div>
                    <div class="nav_title"><span>المحتوى التعليمي</span></div>
                </a>
                <ul>
                    @if(Route::has('courseDetails') && permissionCheck('course.edit'))
                        <li><a href="{{ route('getAllCourse') }}">المحاضرات</a></li>
                    @endif
                    <li><a href="#">الفيديوهات (قريباً)</a></li>
                    <li><a href="#">ملفات PDF (قريباً)</a></li>
                    <li><a href="#">الواجبات (قريباً)</a></li>
                    <li><a href="#">الاختبارات (قريباً)</a></li>
                    <li><a href="#">بنك الأسئلة (قريباً)</a></li>
                </ul>
            </li>

            <li>
                <a href="#" class="has-arrow" aria-expanded="false">
                    <div class="nav_icon_small">
                        <span class="fas fa-user-graduate"></span>
                    </div>
                    <div class="nav_title"><span>الطلاب</span></div>
                </a>
                <ul>
                    @if(Route::has('course.enrolled_students') && permissionCheck('course.enrolled_students'))
                        <li><a href="{{ route('getAllCourse') }}">الطلاب المسجلون</a></li>
                    @endif
                    <li><a href="#">تقدم الطلاب (قريباً)</a></li>
                    <li><a href="#">نتائج الاختبارات (قريباً)</a></li>
                    <li><a href="#">تسليمات الواجبات (قريباً)</a></li>
                </ul>
            </li>

            <li>
                <a href="#" class="has-arrow" aria-expanded="false">
                    <div class="nav_icon_small">
                        <span class="fas fa-wallet"></span>
                    </div>
                    <div class="nav_title"><span>الحسابات</span></div>
                </a>
                <ul>
                    <li><a href="#">كشف الحساب (قريباً)</a></li>
                    <li><a href="#">الإيرادات (قريباً)</a></li>
                    <li><a href="#">طلبات السحب (قريباً)</a></li>
                </ul>
            </li>

            <li>
                <a href="#" class="has-arrow" aria-expanded="false">
                    <div class="nav_icon_small">
                        <span class="fas fa-chart-line"></span>
                    </div>
                    <div class="nav_title"><span>التقارير</span></div>
                </a>
                <ul>
                    @if(Route::has('course.courseStatistics') && permissionCheck('course.courseStatistics'))
                        <li><a href="{{ route('course.courseStatistics') }}">إحصائيات الكورسات</a></li>
                    @endif
                    <li><a href="#">تقرير التسجيلات (قريباً)</a></li>
                    <li><a href="#">تقرير الأرباح (قريباً)</a></li>
                </ul>
            </li>
        @else
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
                                    if (isModuleActive('LmsSaas')){
                                        if ($menu->power==1){
                                            continue;
                                        }
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
                                        @endphp

                                        <li class="{{spn_active_link(childrenRoute($menu))}}">
                                            <a href="@if(!$hasChild && validRouteUrl($menu->route)) {{validRouteUrl($menu->route)}} @else # @endif"
                                               class=" @if($hasChild) has-arrow @endif"
                                               aria-expanded="false">
                                                <div class="nav_icon_small">
                                                    <span class="{{@$menu->icon??'fas fa-th'}}"></span>
                                                </div>
                                                <div class="nav_title">
                                                    <span>{{$menu->getTranslation('name', app()->getLocale())}}</span>
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
                                                                @endphp
                                                                <li class="{{spn_active_link(childrenRoute($submenu))}}">
                                                                    <a href="@if(!empty(validRouteUrl($submenu->route))) {{validRouteUrl($submenu->route)}} @else # @endif">
                                                                        {{$submenu->getTranslation('name', app()->getLocale())}}
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
        @endif
    </ul>

</nav>
