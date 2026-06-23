<div>
    <style>
        /* drawflow */

        .drawflow_content_node .image-preview {
            height: 60px;
            background: #f1f1f1;
            border-radius: 4px;
            overflow: hidden;
            border: 1px solid #D1D1D1;
        }

        .drawflow_content_node .image-preview img {
            height: 60px;
            width: 100px;
            object-fit: cover;
            min-width: 100px
        }

        .drawflow_content_node .option_title,
        .drawflow_content_node .ans_title{
            flex-grow: 1;
        }

        .drawflow_content_node .primary_label2{
            gap: 10px;
        }

        /* .ansNode .drawflow_content_node .primary_label2{
            flex-direction: row-reverse;
        } */

        .drawflow_content_node .option_title,
        .drawflow_content_node .ans_title{
            height: 60px;
            font-size: 22px;
        }

        .drawflow {
            min-width: 900px;
            overflow: auto;
            max-width: 100%;
            width: 100%;
        }

        .drawflow .drawflow-node{
            width: calc(100% / 12* 5)!important;
        }

        .parent-drawflow{
            overflow: auto!important;
        }
        html[dir='rtl'] .drawflow {
            direction: ltr;
        }

        html[dir='rtl'] .drawflow .connection {
            right: auto;
            left: 0;
        }

    </style>

    <div class="quiz__details">
        <div class="container">
            <div class="row justify-content-center ">
                <div class="col-xl-10">
                    <div class="row">
                        <div class="col-12">

                            <div class="mb_30">
                                <!-- quiz_test_header  -->
                                {{--<div class="quiz_test_header">
                                    <h3>{{__('student.Result Sheet')}}</h3>
                                </div>--}}
                                <!-- quiz_test_body  -->
                                <div class="quiz_result_sheet_body">
                                    <div class="result_sheet_view">
                                        @php
                                            $count=1;
                                        @endphp
                                        @if(isset($questions))
                                            @foreach($questions as $question)
                                                @php
                                                    if ($quiz->show_only_wrong_ans_in_ans_sheet==1 && !$question['isWrong'] && $quiz->total_correct_ans!=count($questions) ){
                                                       continue;
                                                    }
                                                @endphp


                                                @php
                                                    if(isset($question['isSubmit'])){
                                                        if(isset($question['isWrong']) &&  $question['isWrong']){
                                                            $isWrongQus= true;
                                                        }else{
                                                            $isWrongQus =false;
                                                        }
                                                    }else{
                                                        $isWrongQus =true;
                                                    }

                                                @endphp
                                                <div
                                                    class="single_result_view  {{$isWrongQus ? 'wrong' : 'correct'}}">
                                                    <h5 class="single_result_count">{{__('frontend.Question')}}
                                                        : {{$count}}</h5>
                                                    <div class="single_result_view_inner">
                                                        <h4 class="single_result_question">
                                                            @if($question['type']=="C")
                                                                {!! getClozeOptions($question['qusBank'],$question['option']) !!}
                                                            @else
                                                                {!! @$question['qus'] !!}
                                                            @endif


                                                            @if(!$question['isSubmit'])

                                                                <small class="text-danger">
                                                                    ({{trans('quiz.Not Submitted')}}
                                                                    )
                                                                </small>

                                                            @endif
                                                        </h4>
                                                        @if(!empty($question['qusBank']->explanation))
                                                            <p class=" mb-4">
                                                                <span>{{__('quiz.Explanation')}}:</span>
                                                                {!! $question['qusBank']->explanation !!}
                                                            </p>
                                                        @endif
                                                        @if(!empty($question['qusBank']->video_url))
                                                            <div class="mb-4">
                                                                <button type="button" class="theme_text3 f_w_600 font_16 border-0 bg-transparent"
                                                                        data-bs-toggle="modal" data-bs-target="#previewVideoModal"
                                                                        data-video-url="{{ $question['qusBank']->video_url }}">
                                                                    <i class="ti-control-play"></i> {{__('quiz.Show Video')}}
                                                                </button>
                                                            </div>
                                                        @endif
                                                        <div class="row mt-4">
                                                            <div class="col-lg-12">

                                                                @if($question['type']=="M")
                                                                    <ul>
                                                                        @if(!empty($question['option']))
                                                                            @foreach($question['option'] as $option)
                                                                                @php
                                                                                    $showRightAns =true;
                                                                                if ($quiz->show_correct_ans_in_ans_sheet!=1){
                                                                                    if(isset($option['submit']) && $option['submit']){
                                                                                        $showRightAns=true;
                                                                                    }else{
                                                                                        $showRightAns=false;
                                                                                    }
                                                                                }

                                                                                @endphp
                                                                                @if($option['right'] && $showRightAns)
                                                                                    <li>
                                                                                        <label
                                                                                            class="primary_checkbox2 d-flex">
                                                                                            <input checked=""
                                                                                                   type="checkbox"
                                                                                                   disabled>
                                                                                            <span
                                                                                                class="checkmark mr_10"></span>
                                                                                            <span
                                                                                                class="label_name ">{{$option['title']}}</span>
                                                                                        </label>
                                                                                    </li>

                                                                                @else

                                                                                    @if(isset($option['wrong']) && $option['wrong'])
                                                                                        <li>
                                                                                            <label
                                                                                                class="primary_checkbox2 error_ans  d-flex">
                                                                                                <input checked=""
                                                                                                       type="checkbox"
                                                                                                       disabled>
                                                                                                <span
                                                                                                    class="checkmark mr_10"></span>
                                                                                                <span
                                                                                                    class="label_name ">{{$option['title']}} </span>
                                                                                            </label>
                                                                                        </li>
                                                                                    @else
                                                                                        <li>
                                                                                            <label
                                                                                                class="primary_checkbox2 d-flex">
                                                                                                <input type="checkbox"
                                                                                                       disabled>
                                                                                                <span
                                                                                                    class="checkmark mr_10"></span>
                                                                                                <span
                                                                                                    class="label_name ">{{$option['title']}}</span>
                                                                                            </label>
                                                                                        </li>
                                                                                    @endif
                                                                                @endif
                                                                            @endforeach
                                                                        @endif
                                                                    </ul>
                                                                @elseif($question['type']=="X")
                                                                    @if(isset($question['qusBank']))
                                                                        @php
                                                                            $qusBank=     $question['qusBank']
                                                                        @endphp
                                                                        @include(theme('partials._quiz_matching_type_preview'),compact('qusBank'))
                                                                    @endif
                                                                @elseif($question['type']=="O")
                                                                    <ul>
                                                                        @if(!empty($question['option']))
                                                                            @foreach($question['option'] as $option)
                                                                                @php
                                                                                    $showRightAns =true;
                                                                                if ($quiz->show_correct_ans_in_ans_sheet!=1){
                                                                                    if(isset($option['submit']) && $option['submit']){
                                                                                        $showRightAns=true;
                                                                                    }else{
                                                                                        $showRightAns=false;
                                                                                    }
                                                                                }

                                                                                @endphp
                                                                                @if($option['right'] && $showRightAns)
                                                                                    <li>
                                                                                        <label
                                                                                            class="primary_checkbox2 d-flex">
                                                                                            <input checked=""
                                                                                                   type="checkbox"
                                                                                                   disabled>
                                                                                            <span
                                                                                                class="checkmark d-none mr_10"></span>
                                                                                            <span
                                                                                                class="label_name ">{{$option['title']}}</span>
                                                                                        </label>
                                                                                    </li>

                                                                                @else

                                                                                    @if(isset($option['wrong']) && $option['wrong'])
                                                                                        <li>
                                                                                            <label
                                                                                                class="primary_checkbox2 error_ans  d-flex">
                                                                                                <input checked=""
                                                                                                       type="checkbox"
                                                                                                       disabled>
                                                                                                <span
                                                                                                    class="checkmark  d-none mr_10"></span>
                                                                                                <span
                                                                                                    class="label_name ">{{$option['title']}} </span>
                                                                                            </label>
                                                                                        </li>
                                                                                    @else
                                                                                        <li>
                                                                                            <label
                                                                                                class="primary_checkbox2 d-flex">
                                                                                                <input type="checkbox"
                                                                                                       disabled>
                                                                                                <span
                                                                                                    class="checkmark  d-none mr_10"></span>
                                                                                                <span
                                                                                                    class="label_name ">{{$option['title']}}</span>
                                                                                            </label>
                                                                                        </li>
                                                                                    @endif
                                                                                @endif
                                                                            @endforeach
                                                                        @endif
                                                                    </ul>
                                                                @elseif($question['type']=="P")

                                                                    <ul>
                                                                        @if(!empty($question['option']))
                                                                            @foreach($question['option'] as $option)
                                                                                <li>
                                                                                    <label
                                                                                        class="primary_checkbox2 d-flex">

                                                                                        <span
                                                                                            class="label_name ">{{$option['title']}}</span>
                                                                                    </label>
                                                                                    <ul>
                                                                                        @php
                                                                                            $qusBank=     $question['qusBank'];
                                                                                             $matching_assigns =$question['matching']??[];
                                                                                        @endphp
                                                                                        @foreach($question['answer'] as $answer)

                                                                                            @php
                                                                                                $has_answer = $matching_assigns
                                                                                                ->where('question_id',$qusBank->id)
                                                                                                ->where('option_id',$option['id']??'')
                                                                                                ->where('answer_id',$answer['id']??'')
                                                                                                ->count();
                                                                                            @endphp
                                                                                            @if($has_answer>0)
                                                                                                <li class="text-{{$answer['status']==1?'success':'danger'}}">{{$answer['title']}}</li>
                                                                                            @endif
                                                                                        @endforeach
                                                                                    </ul>
                                                                                    {{--                                                                                    @endif--}}
                                                                                </li>
                                                                            @endforeach
                                                                        @endif
                                                                    </ul>

                                                                @elseif($question['type']=="S" || $question['type']=="L")
                                                                    {!! $question['answer']??"" !!}
                                                                @endif
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                                @php
                                                    $count++;
                                                @endphp
                                            @endforeach
                                        @endif
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="previewVideoModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('quiz.Video')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="video-wrapper" style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;">
                    <iframe id="previewVideoIframe" style="position:absolute;top:0;left:0;width:100%;height:100%;border:0;" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var modal = document.getElementById('previewVideoModal');
        if (modal) {
            modal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var url = button.getAttribute('data-video-url');
                var iframe = document.getElementById('previewVideoIframe');
                if (url) {
                    var embedUrl = url;
                    var ytMatch = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/);
                    if (ytMatch) {
                        embedUrl = 'https://www.youtube.com/embed/' + ytMatch[1];
                    }
                    var vimeoMatch = url.match(/vimeo\.com\/(\d+)/);
                    if (vimeoMatch) {
                        embedUrl = 'https://player.vimeo.com/video/' + vimeoMatch[1];
                    }
                    iframe.src = embedUrl;
                }
            });
            modal.addEventListener('hidden.bs.modal', function () {
                var iframe = document.getElementById('previewVideoIframe');
                iframe.src = '';
            });
        }
    });
</script>
