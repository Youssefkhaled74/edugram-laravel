<style>
    .primary_input {
        border-radius: 30px !important
    }
</style>
<ul class="nav nav-tabs ms-0 mb-3 border-0">
    <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="tab"
           href="#basic_information_tab">{{__('profile.basic_information')}}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#change_password_tab">{{__('profile.change_password')}}</a>
    </li>
    @if(isModuleActive('TwoFA') && Settings('enable_student_two_fa'))
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#fa_tab">{{__('profile.2FA')}}</a>
        </li>
    @endif
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#images_tab">{{__('profile.images')}}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#skills_tab">{{__('profile.skills')}}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#social_tab">{{__('profile.social_and_contact')}}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#delete_account_tab">{{__('profile.delete_account')}}</a>
    </li>
</ul>
