@php
    $instructors = \App\User::where('role_id', 2)->where('active', 1)->take(4)->get();
@endphp

<div data-type="component-text"
     data-preview="{{!function_exists('themeAsset')?'':themeAsset('img/snippets/preview/home/homepage_instructor_section.jpg')}}"
     data-aoraeditor-title="Homepage V8 Teachers" data-aoraeditor-categories="Home Page;Teachers">

    <div class="v8-teachers">
        <div class="container">
            <div class="v8-section-header">
                <h2 class="v8-section-title">الكادر التعليمي لمنصة EduGram</h2>
                <p class="v8-section-subtitle">نخبة من أفضل المعلمين المتخصصين في جميع المراحل الدراسية</p>
            </div>

            <div class="v8-teachers-grid">
                @foreach($instructors as $instructor)
                    <div class="v8-teacher-card">
                        <div class="v8-teacher-img">
                            @if($instructor->image)
                                <img src="{{getProfileImage($instructor->image, $instructor->name)}}" alt="{{$instructor->name}}">
                            @else
                                <div class="v8-teacher-placeholder">
                                    <i class="fas fa-user"></i>
                                </div>
                            @endif
                        </div>
                        <div class="v8-teacher-info">
                            <h4 class="v8-teacher-name">{{$instructor->name}}</h4>
                            <p class="v8-teacher-bio">{{@$instructor->headline ?: 'معلم متخصص'}}</p>
                            <span class="v8-teacher-subject-pill">
                                <i class="fas fa-book-reader"></i>
                                {{@$instructor->headline ?: 'تدريس عام'}}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
