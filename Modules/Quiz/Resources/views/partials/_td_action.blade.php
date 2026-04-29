<div class="dropdown CRM_dropdown">
    @php
        $isTeacherBankPage = request()->routeIs('teacher.question-banks.*', 'teacher.questions.*');
    @endphp
    <button class="btn btn-secondary dropdown-toggle" type="button"
            id="dropdownMenu2" data-bs-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false">
        {{trans('common.Action')}}
    </button>
    <div class="dropdown-menu dropdown-menu-right"
         aria-labelledby="dropdownMenu2">
        @if ($isTeacherBankPage || permissionCheck('question-bank.edit'))
            <a class="dropdown-item edit_brand"
               href="{{ $isTeacherBankPage ? route('teacher.questions.edit', [$query->id]) : route('question-bank-edit', [$query->id]) }}">{{trans('common.Edit') }}</a>
        @endif
        @if ($isTeacherBankPage || permissionCheck('question-bank.delete'))
            <button class="dropdown-item deleteQuiz_bank"
                    data-id="{{$query->id}}"
                    data-total="{{$query->quiz_assign_count}}"
                    type="button">{{trans('common.Delete')}}
            </button>
        @endif
    </div>
</div>
