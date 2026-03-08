<div class="parent-sidebar" style="width: 280px;">
    <div class="p-4 text-center border-bottom border-white" style="border-opacity: 0.2;">
        <h4 class="mb-1" style="font-weight: 800;">بوابة أولياء الأمور</h4>
        <small style="opacity: 0.9;">نظام إدارة التعلم</small>
    </div>
    
    <nav class="nav flex-column py-3">
        <a href="{{ route('parent.dashboard') }}" class="nav-link {{ request()->routeIs('parent.dashboard') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            الصفحة الرئيسية
        </a>
        
        <a href="{{ route('parent.children.index') }}" class="nav-link {{ request()->routeIs('parent.children.*') ? 'active' : '' }}">
            <i class="fas fa-users"></i>
            الأبناء
        </a>
        
        
        
        <a href="{{ route('parent.payment.history') }}" class="nav-link {{ request()->routeIs('parent.payment.*') ? 'active' : '' }}">
            <i class="fas fa-credit-card"></i>
            المدفوعات
        </a>
        

        
        <div class="dropdown-divider mx-3 my-2" style="border-color: rgba(255,255,255,0.2);"></div>
        
        <a href="{{ route('parent.profile') }}" class="nav-link {{ request()->routeIs('parent.profile') ? 'active' : '' }}">
            <i class="fas fa-user-circle"></i>
            الملف الشخصي
        </a>

        
        <a href="{{ route('parent.logout') }}" class="nav-link" 
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fas fa-sign-out-alt"></i>
            تسجيل الخروج
        </a>
    </nav>
    
    <form id="logout-form" action="{{ route('parent.logout') }}" method="POST" class="d-none">
        @csrf
    </form>
</div>