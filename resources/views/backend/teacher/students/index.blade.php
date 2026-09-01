@extends('backend.master')

@section('mainContent')
    {!! generateBreadcrumb() !!}
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="white_box mb_30">
                <div class="main-title mb-20 d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">طلابي</h3>
                    <span class="primary-btn small fix-gr-bg">{{ $enrollments->total() }} تسجيل</span>
                </div>
                <p class="text-muted mb-4">تظهر جميع تسجيلات الطلاب في دوراتك. عدد الدورات يوضح إن كان الطالب مشتركًا في أكثر من دورة لديك.</p>
                <div class="QA_section QA_section_heading_custom check_box_table">
                    <div class="QA_table">
                        <table class="table Crm_table_active3">
                            <thead>
                            <tr>
                                <th>الطالب</th>
                                <th>الدورة</th>
                                <th>عدد دوراته معك</th>
                                <th>تاريخ التسجيل</th>
                                <th>إجراء</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($enrollments as $enrollment)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="{{ getProfileImage($enrollment->user->image, $enrollment->user->name) }}" alt="" style="width:38px;height:38px;border-radius:50%;object-fit:cover">
                                            <div><strong>{{ $enrollment->user->name }}</strong><br><small>{{ $enrollment->user->email }}</small></div>
                                        </div>
                                    </td>
                                    <td>{{ $enrollment->course->title }}</td>
                                    <td><span class="badge badge_{{ $enrollment->teacher_courses_count > 1 ? 'info' : 'primary' }}">{{ $enrollment->teacher_courses_count }} دورة</span></td>
                                    <td>{{ showDate($enrollment->created_at) }}</td>
                                    <td>
                                        <form method="POST" action="{{ route('teacher.students.remove', $enrollment) }}" onsubmit="return confirm('هل تريد إزالة الطالب من هذه الدورة؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="primary-btn small bg-danger">إزالة من الدورة</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center py-4">لا يوجد طلاب مسجلون في دوراتك حتى الآن.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="mt-4">{{ $enrollments->links() }}</div>
            </div>
        </div>
    </section>
@endsection
