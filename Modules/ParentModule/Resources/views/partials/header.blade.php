<div class="parent-header d-flex justify-content-between align-items-center">
    <div>
        <h5 class="mb-0" style="font-weight: 700; color: #667eea;">@yield('page-title', 'لوحة التحكم')</h5>
        <small class="text-muted">@yield('page-subtitle', 'مرحباً بك في بوابة أولياء الأمور')</small>
    </div>
    
    <div class="d-flex align-items-center">
        <!-- Notifications Dropdown -->
        <div class="dropdown ml-3">
            <button class="btn btn-link position-relative" type="button" id="notificationDropdown" data-toggle="dropdown">
                <i class="fas fa-bell fa-lg" style="color: #667eea;"></i>
                @if(isset($parent) && $parent->unread_notification_count > 0)
                    <span class="badge badge-danger position-absolute" style="top: -5px; left: -5px; font-size: 11px; padding: 4px 7px;">
                        {{ $parent->unread_notification_count }}
                    </span>
                @endif
            </button>
            <div class="dropdown-menu dropdown-menu-left" style="width: 350px; max-height: 450px; overflow-y: auto;">
                <h6 class="dropdown-header" style="font-weight: 700; color: #667eea; font-size: 16px;">
                    <i class="fas fa-bell ml-2"></i>
                    الإشعارات
                </h6>
                <div class="dropdown-divider"></div>
                @if(isset($parent) && $parent->unreadNotifications()->count() > 0)
                    @foreach($parent->unreadNotifications()->limit(5)->get() as $notification)
                        <a href="{{ $notification->action_url ?? '#' }}" class="dropdown-item" 
                           onclick="event.preventDefault(); markNotificationRead({{ $notification->id }});"
                           style="border-right: 3px solid #667eea; margin-bottom: 5px;">
                            <small class="text-muted">
                                <i class="fas fa-clock ml-1"></i>
                                {{ $notification->created_at->diffForHumans() }}
                            </small>
                            <p class="mb-1 mt-1"><strong>{{ $notification->title }}</strong></p>
                            <small style="color: #666;">{{ Str::limit($notification->message, 60) }}</small>
                        </a>
                        <div class="dropdown-divider"></div>
                    @endforeach
                    <a href="{{ route('parent.notifications') }}" class="dropdown-item text-center" style="color: #667eea; font-weight: 600;">
                        <i class="fas fa-list ml-2"></i>
                        عرض جميع الإشعارات
                    </a>
                @else
                    <div class="dropdown-item text-center text-muted py-4">
                        <i class="fas fa-bell-slash fa-2x mb-2" style="opacity: 0.3;"></i>
                        <p class="mb-0">لا توجد إشعارات جديدة</p>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- User Profile Dropdown -->
        <div class="dropdown">
            <button class="btn btn-link dropdown-toggle d-flex align-items-center" type="button" id="userDropdown" data-toggle="dropdown" style="text-decoration: none;">
                <img src="{{ Auth::user()->image ? asset(Auth::user()->image) : asset('public/demo/user/user.jpg') }}" 
                     alt="الصورة الشخصية" class="rounded-circle ml-2" width="40" height="40"
                     style="border: 2px solid #667eea;">
                <span style="color: #333; font-weight: 600;">{{ Auth::user()->name }}</span>
                <i class="fas fa-chevron-down mr-2" style="font-size: 12px; color: #999;"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-left">
                <div class="dropdown-header" style="background: #f8f9fa;">
                    <strong>{{ Auth::user()->name }}</strong>
                    <br>
                    <small class="text-muted">{{ Auth::user()->email }}</small>
                </div>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="{{ route('parent.profile') }}">
                    <i class="fas fa-user ml-2" style="color: #667eea; width: 20px;"></i>
                    الملف الشخصي
                </a>
                <a class="dropdown-item" href="{{ route('parent.change-password') }}">
                    <i class="fas fa-lock ml-2" style="color: #667eea; width: 20px;"></i>
                    تغيير كلمة المرور
                </a>
                <a class="dropdown-item" href="{{ route('parent.notifications') }}">
                    <i class="fas fa-bell ml-2" style="color: #667eea; width: 20px;"></i>
                    الإشعارات
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item text-danger" href="{{ route('parent.logout') }}" 
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt ml-2" style="width: 20px;"></i>
                    تسجيل الخروج
                </a>
            </div>
        </div>
    </div>
</div>

<form id="logout-form" action="{{ route('parent.logout') }}" method="POST" class="d-none">
    @csrf
</form>