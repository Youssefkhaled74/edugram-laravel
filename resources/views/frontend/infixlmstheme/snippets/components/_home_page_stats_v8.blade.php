@php
    use App\User;
    use Modules\CourseSetting\Entities\Course;
    use Modules\Quiz\Entities\QuizeSetup;

    $totalInstructors = User::where('role_id', 2)->count();
    $totalCourses = Course::count();
    $totalQuiz = QuizeSetup::count();
    $totalStudents = User::where('role_id', 3)->count();
@endphp

<div data-type="component-text"
     data-preview="{{!function_exists('themeAsset')?'':themeAsset('img/snippets/preview/home/homepage_stats.jpg')}}"
     data-aoraeditor-title="Homepage V8 Stats" data-aoraeditor-categories="Home Page;Stats">

    <div class="v8-stats">
        <div class="container">
            <div class="v8-stats-grid">
                <div class="v8-stat-card">
                    <div class="v8-stat-icon v8-stat-icon-blue">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="v8-stat-info">
                        <span class="v8-stat-number">120K+</span>
                        <span class="v8-stat-label">طالب نشط</span>
                    </div>
                </div>

                <div class="v8-stat-card">
                    <div class="v8-stat-icon v8-stat-icon-green">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <div class="v8-stat-info">
                        <span class="v8-stat-number">{{$totalInstructors}}</span>
                        <span class="v8-stat-label">معلم محترف</span>
                    </div>
                </div>

                <div class="v8-stat-card">
                    <div class="v8-stat-icon v8-stat-icon-purple">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <div class="v8-stat-info">
                        <span class="v8-stat-number">{{$totalCourses}}</span>
                        <span class="v8-stat-label">كورس تعليمي</span>
                    </div>
                </div>

                <div class="v8-stat-card">
                    <div class="v8-stat-icon v8-stat-icon-orange">
                        <i class="fas fa-laptop"></i>
                    </div>
                    <div class="v8-stat-info">
                        <span class="v8-stat-number">{{$totalQuiz}}</span>
                        <span class="v8-stat-label">اختبار تفاعلي</span>
                    </div>
                </div>

                <div class="v8-stat-card">
                    <div class="v8-stat-icon v8-stat-icon-red">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="v8-stat-info">
                        <span class="v8-stat-number">{{$totalStudents}}</span>
                        <span class="v8-stat-label">طالب مسجل</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
