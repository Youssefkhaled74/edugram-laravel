<ul class="quiz_select">
    @foreach($options as $option)

        <li>
            <label
                class="primary_bulet_checkbox d-flex">
                <input class="quizAns"
                       name="ans[{{$option->question_bank_id}}][]"
                       type="checkbox"
                       value="{{$option->id}}">

                <span
                    class="checkmark mr_10"></span>
                <span
                    class="label_name">
                    @if($option->image)
                        <img src="{{asset($option->image)}}" alt="" style="max-width:60px;max-height:60px;vertical-align:middle;margin-inline-end:8px;border-radius:4px;object-fit:cover">
                    @endif
                    {{$option->title}}
                </span>
            </label>
        </li>
    @endforeach

</ul>
