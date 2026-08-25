@extends('backend.master')
@section('mainContent')
    @php
        $isTeacherQuestionPage = request()->routeIs('teacher.questions.*');
        $teacherBankIdForForm = old('group', request('group', isset($bank) ? $bank->q_group_id : null));
    @endphp
    <style>
        .note-equation-preview .katex { font-size: 1.2em; }
        .equation-dialog .modal-body { max-height: 70vh; overflow-y: auto; }
        .equation-templates::-webkit-scrollbar { width: 4px; }
        .equation-templates::-webkit-scrollbar-thumb { background: #ccc; border-radius: 4px; }
        .eq-tpl-btn:hover { background: #e2e8f0 !important; border-color: #94a3b8 !important; }
        .math-writer-area {
            padding: 16px 18px;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            gap: 10px;
            background: #fff;
            border-bottom: 1px solid #e8edf5;
        }
        .math-writer-heading {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 12px;
            direction: rtl;
        }
        .math-writer-heading strong {
            display: block;
            color: #172033;
            font-size: 13px;
            font-weight: 800;
        }
        .math-writer-heading span {
            color: #7b879d;
            font-size: 10px;
        }
        .math-writer-area textarea {
            width: 100%;
            font-family: 'Courier New', monospace;
            font-size: 16px;
            line-height: 1.75;
            padding: 12px 14px;
            border: 1px solid #cfd8e6;
            border-radius: 12px;
            resize: vertical;
            min-height: 96px;
            max-height: 400px;
            outline: none;
            direction: ltr;
            background: #fbfcfe;
            color: #172033;
            box-sizing: border-box;
            transition: border-color .15s, box-shadow .15s;
        }
        .math-writer-area textarea:focus {
            background: #fff;
            border-color: #2f67f6;
            box-shadow: 0 0 0 4px rgba(47, 103, 246, .1);
        }
        .math-writer-area textarea::placeholder {
            color: #94a3b8;
            font-size: 13px;
        }
        .math-writer-toolbar {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            align-items: stretch;
            gap: 10px;
        }
        .math-preview-shell {
            min-width: 0;
            padding: 8px 12px 10px;
            background: #f8fafc;
            border: 1px solid #dce3ed;
            border-radius: 12px;
        }
        .math-preview-label {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 4px;
            color: #738096;
            font-size: 10px;
            font-weight: 700;
            direction: rtl;
        }
        .math-writer-preview {
            min-height: 42px;
            padding: 6px 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: #263247;
            overflow-x: auto;
            transition: border-color .15s;
        }
        .math-writer-preview.has-error {
            border-color: #fca5a5;
            color: #dc2626;
            font-size: 12px;
            font-family: 'Courier New', monospace;
        }
        .math-writer-preview .katex { font-size: 1.15em; }
        .math-writer-preview:empty::before {
            content: '\\[ \\; \\]';
            color: #cbd5e1;
            font-family: 'Courier New', monospace;
        }
        .math-writer-actions {
            display: flex;
            align-items: stretch;
        }
        .math-writer-insert-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            min-width: 150px;
            justify-content: center;
            padding: 10px 18px;
            background: #2457f5;
            color: #fff;
            border: 0;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 8px 18px rgba(36, 87, 245, .2);
            transition: background .15s, box-shadow .15s, transform .1s;
            white-space: nowrap;
        }
        .math-writer-insert-btn:hover { background: #1748dc; box-shadow: 0 10px 22px rgba(36, 87, 245, .28); }
        .math-writer-insert-btn:active { transform: scale(.97); }
        .math-writer-insert-btn:disabled { opacity: .4; cursor: not-allowed; transform: none; }
        .math-writer-insert-btn .btn-icon { font-size: 16px; font-weight: 700; }

        .math-digit-tools {
            display: flex;
            align-items: center;
            gap: 7px;
            padding: 7px 10px;
            direction: rtl;
            background: #f8fafc;
            border: 1px solid #e1e7f0;
            border-radius: 10px;
        }
        .math-digit-tools-label {
            color: #334155;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
        }
        .math-digit-convert-btn {
            min-width: 48px;
            padding: 4px 10px;
            color: #0f172a;
            background: #fff;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            font-size: 14px;
            font-weight: 700;
            line-height: 1.3;
            cursor: pointer;
            transition: color .15s, border-color .15s, background-color .15s;
        }
        .math-digit-convert-btn:hover,
        .math-digit-convert-btn:focus {
            color: #0d6efd;
            background: #eff6ff;
            border-color: #60a5fa;
            outline: none;
        }
        .math-digit-tools-help {
            color: #64748b;
            font-size: 10px;
            margin-inline-start: 2px;
        }
        .math-shortcuts {
            color: #77849a;
            font-size: 10px;
            direction: rtl;
        }
        .math-shortcuts summary {
            width: fit-content;
            color: #526078;
            font-weight: 700;
            cursor: pointer;
            user-select: none;
        }
        .math-shortcuts-list {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            padding-top: 7px;
            direction: ltr;
        }
        .math-shortcuts-list span {
            padding: 3px 7px;
            background: #f4f6f9;
            border: 1px solid #e5e9f0;
            border-radius: 6px;
            font-family: 'Courier New', monospace;
            white-space: nowrap;
        }

        .math-tabs-section {
            padding: 12px 18px 0;
            flex-shrink: 0;
            background: #f7f9fc;
        }
        .math-tabs-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
            direction: rtl;
        }
        .math-tabs-header .tabs-label {
            font-size: 11px;
            font-weight: 800;
            color: #435069;
        }
        @media only screen and (min-width: 992px) {
            .drawflow-node.ans {
                margin-right: 0 !important;
            }
        }

        .drawflow .drawflow-node {
            width: calc(100% / 12 * 5) !important;
        }

        .drawflow .drawflow-node .primary_input_field {
            width: 180px;
            align-self: start;
        }

        .drawflow .drawflow-node .input {
            z-index: 999 !important;
        }

        .thumb_img_div {
            height: 50px !important;
        }

        .thumb_img_div img {
            height: 50px !important;
        }

        .drawflow {
            min-width: 1500px;
        }

        .drawflow_content_node .primary_input.single-uploader {
            display: flex;
        }

        .ansNode .drawflow_content_node .primary_input.single-uploader {
            display: flex;
            flex-direction: row-reverse;
        }

        .drawflow_content_node .product_image_all_div {
            margin-top: 0;
            height: 46px;
            width: fit-content;
        }

        .drawflow_content_node .product_image_all_div img {
            height: 46px !important;
            width: 80px !important;
            object-fit: cover;
        }

        .drawflow_content_node .thumb_img_div {
            height: 46px !important;
            min-width: 90px !important;
            border: 0 !important;
        }

        .drawflow_content_node .primary_file_uploader {
            flex-grow: 1;
        }

        html[dir='rtl'] #drawflow {
            direction: ltr;
        }

        html[dir='rtl'] .drawflow .connection {
            right: auto;
            left: 0;
        }

        .drawflow-node .product_image_all_div {
            margin-left: 16px;
        }

        html[dir="rtl"] .drawflow-node .product_image_all_div {
            margin-right: 16px;
            margin-left: 0;
        }

        .math-keyboard-toggle {
            position: fixed;
            left: 24px;
            bottom: 24px;
            z-index: 10500;
            width: 52px;
            height: 52px;
            border-radius: 50%;
            border: 0;
            color: #fff;
            background: #0d6efd;
            box-shadow: 0 8px 20px rgba(13, 110, 253, .3);
            font-size: 24px;
            line-height: 1;
            cursor: pointer;
            transition: transform .2s, box-shadow .2s;
        }
        .math-keyboard-toggle:hover {
            transform: scale(1.08);
            box-shadow: 0 10px 28px rgba(13, 110, 253, .45);
        }
        .math-keyboard-toggle:active {
            transform: scale(.94);
        }
        .math-keyboard-toggle .kbd-hint {
            position: absolute;
            bottom: -6px;
            right: -6px;
            background: #198754;
            color: #fff;
            font-size: 8px;
            font-weight: 700;
            padding: 1px 4px;
            border-radius: 4px;
            line-height: 1.3;
            letter-spacing: .3px;
            box-shadow: 0 2px 6px rgba(0,0,0,.2);
        }
        .math-keyboard-toggle .pulse-ring {
            position: absolute;
            inset: -4px;
            border-radius: 50%;
            border: 2px solid rgba(13, 110, 253, .35);
            animation: mathPulse 2s ease-in-out infinite;
            pointer-events: none;
        }
        @keyframes mathPulse {
            0%, 100% { transform: scale(1); opacity: .6; }
            50% { transform: scale(1.12); opacity: 0; }
        }

        .math-resize-handle {
            position: absolute;
            right: 0;
            bottom: 0;
            width: 16px;
            height: 16px;
            cursor: nwse-resize;
            z-index: 10;
        }
        .math-resize-handle::after {
            content: '';
            position: absolute;
            right: 4px;
            bottom: 4px;
            width: 8px;
            height: 8px;
            border-right: 2px solid #ccc;
            border-bottom: 2px solid #ccc;
        }
        .math-resize-handle:hover::after {
            border-color: #0d6efd;
        }
        .math-keyboard-panel {
            position: fixed;
            left: 24px;
            bottom: 90px;
            width: 720px;
            max-width: calc(100vw - 32px);
            max-height: calc(100vh - 32px);
            overflow: hidden;
            background: #f7f9fc;
            border: 1px solid rgba(207, 216, 230, .9);
            border-radius: 20px;
            padding: 0;
            box-shadow: 0 24px 70px rgba(24, 34, 55, .22), 0 4px 12px rgba(24, 34, 55, .08);
            z-index: 10501;
            display: flex;
            flex-direction: column;
            opacity: 0;
            visibility: hidden;
            transform: translateY(12px) scale(.97);
            transition: opacity .2s ease, transform .25s ease, visibility .2s;
            transform-origin: bottom left;
        }
        .math-keyboard-panel.open {
            opacity: 1;
            visibility: visible;
            transform: translateY(0) scale(1);
        }
        .math-keyboard-panel.d-none {
            display: none !important;
        }

        .math-keyboard-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 64px;
            padding: 12px 18px;
            flex-shrink: 0;
            direction: rtl;
            background: linear-gradient(135deg, #ffffff 0%, #f3f7ff 100%);
            border-bottom: 1px solid #e3e9f2;
        }
        .math-keyboard-title {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: grab;
            user-select: none;
            -webkit-user-select: none;
        }
        .math-keyboard-title:active { cursor: grabbing; }
        .math-keyboard-title .title-icon {
            width: 36px;
            height: 36px;
            background: #2457f5;
            color: #fff;
            border-radius: 11px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 700;
            flex-shrink: 0;
        }
        .math-keyboard-title .title-text {
            font-size: 16px;
            font-weight: 800;
            color: #172033;
        }
        .math-keyboard-title .title-copy {
            display: flex;
            flex-direction: column;
            gap: 1px;
        }
        .math-keyboard-title .title-subtitle {
            color: #7b879d;
            font-size: 10px;
            font-weight: 500;
        }
        .math-keyboard-title .title-badge {
            font-size: 9px;
            font-weight: 700;
            color: #2457f5;
            background: #e9efff;
            padding: 3px 7px;
            border-radius: 6px;
            letter-spacing: .5px;
        }
        .header-actions {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .header-action-btn {
            width: 34px;
            height: 34px;
            border: 1px solid #e0e6ef;
            background: #fff;
            border-radius: 10px;
            font-size: 14px;
            line-height: 1;
            cursor: pointer;
            color: #536078;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color .15s, background .15s, border-color .15s, transform .15s;
        }
        .header-action-btn:hover { background: #edf3ff; border-color: #b8c9fb; color: #2457f5; transform: translateY(-1px); }
        .math-keyboard-close {
            width: 34px;
            height: 34px;
            border: 1px solid #e0e6ef;
            background: #fff;
            border-radius: 10px;
            font-size: 14px;
            line-height: 1;
            cursor: pointer;
            color: #536078;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background .15s;
        }
        .math-keyboard-close:hover { background: #fff0f0; border-color: #fecaca; color: #dc2626; }

        .math-keyboard-search {
            padding: 12px 18px 0;
            flex-shrink: 0;
            background: #f7f9fc;
        }
        .math-keyboard-search input {
            width: 100%;
            height: 42px;
            padding: 9px 40px 9px 12px;
            border: 1px solid #dce3ed;
            border-radius: 12px;
            background: #fff;
            font-size: 13px;
            direction: rtl;
            outline: 0;
            transition: background .15s, border-color .15s;
            box-sizing: border-box;
        }
        .math-keyboard-search input:focus {
            background: #fff;
            border-color: #0d6efd;
            box-shadow: 0 0 0 3px rgba(13, 110, 253, .1);
        }
        .math-keyboard-search .search-icon {
            position: absolute;
            right: 31px;
            top: calc(50% + 6px);
            transform: translateY(-50%);
            color: #768399;
            font-size: 14px;
            pointer-events: none;
        }

        .math-keyboard-recent {
            display: flex;
            gap: 6px;
            padding: 9px 18px 0;
            flex-shrink: 0;
            flex-wrap: wrap;
            min-height: 0;
        }
        .math-keyboard-recent .recent-label {
            font-size: 10px;
            font-weight: 600;
            color: #aaa;
            letter-spacing: .5px;
            display: flex;
            align-items: center;
            text-transform: uppercase;
        }
        .math-keyboard-recent .recent-symbols {
            display: flex;
            gap: 4px;
            flex-wrap: wrap;
        }
        .math-keyboard-recent .recent-symbols .math-key-btn {
            padding: 3px 6px;
            font-size: 13px;
            border-color: #e8e8e8;
            background: #f5f7fa;
        }

        .math-keyboard-tabs {
            display: flex;
            gap: 6px;
            padding: 0 0 9px;
            flex-shrink: 0;
            overflow-x: auto;
            direction: rtl;
            -webkit-overflow-scrolling: touch;
        }
        .math-keyboard-tabs::-webkit-scrollbar { height: 2px; }
        .math-keyboard-tabs::-webkit-scrollbar-thumb { background: #d0d0d0; border-radius: 4px; }

        .math-tab-btn {
            border: 1px solid #dce3ed;
            border-radius: 9px;
            background: #fff;
            color: #536078;
            padding: 7px 13px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            white-space: nowrap;
            transition: all .18s;
            flex-shrink: 0;
        }
        .math-tab-btn:hover {
            background: #eef2ff;
            border-color: #c7d2fe;
            color: #334;
        }
        .math-tab-btn.active {
            background: #2457f5;
            border-color: #2457f5;
            color: #fff;
            box-shadow: 0 5px 12px rgba(36, 87, 245, .22);
        }

        .math-keyboard-grid-wrap {
            padding: 12px 18px 18px;
            overflow: hidden;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 0;
        }
        .math-keyboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(76px, 1fr));
            grid-auto-rows: minmax(50px, auto);
            gap: 8px;
            overflow-y: auto;
            padding: 2px 4px 6px 2px;
            scrollbar-gutter: stable;
        }
        .math-keyboard-grid::-webkit-scrollbar { width: 3px; }
        .math-keyboard-grid::-webkit-scrollbar-thumb { background: #ccc; border-radius: 4px; }

        .math-keyboard-grid .no-results {
            grid-column: 1 / -1;
            text-align: center;
            color: #999;
            font-size: 13px;
            padding: 24px 0;
        }

        .math-key-btn {
            min-width: 0;
            border: 1px solid #dfe5ee;
            border-radius: 11px;
            background: #fff;
            color: #253149;
            padding: 10px 5px;
            font-size: 17px;
            line-height: 1.3;
            text-align: center;
            cursor: pointer;
            transition: background .15s, border-color .15s, transform .15s, box-shadow .15s;
            position: relative;
        }
        .math-key-btn:hover {
            background: #f2f6ff;
            border-color: #7da0ff;
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(36, 87, 245, .12);
            z-index: 2;
        }
        .math-key-btn:active {
            transform: scale(.92);
            transition-duration: .05s;
        }
        .math-key-btn.inserted {
            animation: mathKeyPop .35s ease;
        }
        @keyframes mathKeyPop {
            0% { transform: scale(1); }
            40% { transform: scale(1.25); background: #dbeafe; border-color: #0d6efd; }
            100% { transform: scale(1); }
        }

        .math-active-indicator {
            padding: 4px 8px;
            font-size: 11px;
            color: #66748a;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            gap: 6px;
            min-height: 0;
            background: #fff;
            border: 1px solid #dfe5ee;
            border-radius: 999px;
        }
        .math-active-indicator .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            flex-shrink: 0;
        }
        .math-active-indicator .dot.active-dot { background: #16a066; box-shadow: 0 0 0 3px rgba(22, 160, 102, .12); }
        .math-active-indicator .dot.inactive-dot { background: #ccc; }

        @media (max-width: 575px) {
            .math-keyboard-toggle {
                left: 12px;
                bottom: 12px;
                width: 46px;
                height: 46px;
                font-size: 20px;
            }
            .math-keyboard-panel {
                left: 8px !important;
                right: 8px !important;
                top: auto !important;
                bottom: 8px !important;
                width: auto !important;
                height: auto !important;
                max-width: none;
                max-height: calc(100dvh - 16px);
                border-radius: 18px;
                transform-origin: bottom center;
            }
            .math-keyboard-header {
                min-height: 58px;
                padding: 10px 12px;
            }
            .math-keyboard-title .title-icon {
                width: 32px;
                height: 32px;
            }
            .math-keyboard-title .title-subtitle,
            .math-keyboard-title .title-badge {
                display: none;
            }
            .header-actions {
                gap: 4px;
            }
            .header-action-btn,
            .math-keyboard-close {
                width: 32px;
                height: 32px;
            }
            .math-writer-area {
                padding: 12px;
            }
            .math-writer-heading span {
                display: none;
            }
            .math-writer-area textarea {
                min-height: 78px;
                font-size: 14px;
            }
            .math-writer-toolbar {
                grid-template-columns: 1fr;
            }
            .math-writer-insert-btn {
                width: 100%;
                min-height: 44px;
            }
            .math-keyboard-search,
            .math-tabs-section {
                padding-inline: 12px;
            }
            .math-keyboard-grid {
                grid-template-columns: repeat(auto-fill, minmax(58px, 1fr));
                gap: 6px;
            }
            .math-keyboard-grid-wrap {
                padding: 10px 12px 14px;
            }
            .math-digit-tools {
                align-items: flex-start;
                flex-wrap: wrap;
            }
            .math-digit-tools-help {
                flex-basis: 100%;
            }
            .math-shortcuts {
                display: none;
            }
            .math-resize-handle {
                display: none;
            }
        }

        @media (max-width: 400px) {
            #mathSaveTemplateBtn,
            #mathHistoryBtn {
                display: none;
            }
            .math-keyboard-title .title-text {
                font-size: 14px;
            }
            .math-tabs-header .tabs-label {
                display: none;
            }
        }

    </style>
    <link rel="stylesheet" href="{{ asset('public/backend/css/katex.min.css') }}">
    {!! generateBreadcrumb() !!}
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            @if(isset($bank))
                @if (permissionCheck('question-bank.store'))
                    <div class="row">
                        <div class="offset-lg-10 col-lg-2 text-end col-md-12 mb-20">

                        </div>
                    </div>
                @endif
            @endif
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-12">


                            @if(isset($bank))

                                <form method="POST" action="{{ $isTeacherQuestionPage ? route('teacher.questions.update', $bank->id) : route('question-bank-update', $bank->id) }}"
                                      class="form-horizontal" enctype="multipart/form-data" id="question_bank">
                                    @method('PUT')
                                    @csrf
                                    @else
                                        @if ($isTeacherQuestionPage || permissionCheck('question-bank.store'))
                                            <form method="POST" action="{{ $isTeacherQuestionPage ? route('teacher.questions.store', ['bank' => $teacherBankIdForForm]) : route('question-bank.store') }}"
                                                  class="form-horizontal" enctype="multipart/form-data"
                                                  id="question_bank">
                                                @csrf
                                                @endif
                                                @endif
                                                <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">

                                                <input type="hidden" name="connection" id="connection"
                                                       value="{{old('connection',isset($bank) && $bank->type=='X'?$bank->connection:null)}}">
                                                {{--                            <input type="hidden" name="data" id="data">--}}

                                                <div class="white-box ">
                                                    <div class="add-visitor">
                                                        <div class="row row-gap-3">
                                                            <div class="col-lg-4">
                                                                @php
                                                                    if(isset($bank)){
                                                                         request()->replace(['group'=>$bank->q_group_id]);
                                                                    }
                                                                @endphp
                                                                <label class="primary_input_label"
                                                                       for="groupInput">{{__('quiz.Group')}} <span
                                                                        class="required_mark">*</span></label>
                                                                <select {{ $errors->has('group') ? ' autofocus' : '' }}
                                                                        class="primary_select{{ $errors->has('group') ? ' is-invalid' : '' }}"
                                                                        name="group" id="groupInput">
                                                                    <option
                                                                        data-display="{{__('common.Select')}} {{__('quiz.Group')}} "
                                                                        value="">{{__('common.Select')}} {{__('quiz.Group')}}
                                                                    </option>
                                                                    @if(isModuleActive('AdvanceQuiz'))
                                                                        @foreach($groups->where('parent_id',0) as $group)
                                                                            @include('advancequiz::group._single_select_option_id',['group'=>$group,'level'=>1])
                                                                        @endforeach
                                                                    @else
                                                                        @foreach($groups as $group)
                                                                            @if(isset($bank))
                                                                                <option
                                                                                    value="{{$group->id}}" {{$group->id == $bank->q_group_id? 'selected': ''}}>{{$group->title}}</option>
                                                                            @else
                                                                                <option
                                                                                    value="{{$group->id}}" {{old('group')!='' ? (old('group') == $group->id ? 'selected':'') : ((request('group') == $group->id) ? 'selected' : '')}} >{{$group->title}}</option>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif

                                                                 </select>

                                                             </div>
                                                             <input type="hidden" name="category" value="{{Auth::user()->category_id}}">
                                                             <input type="hidden" name="sub_category" value="{{Auth::user()->subcategory_id}}">
                                                             @if(isModuleActive('AdvanceQuiz'))
                                                                <div class="col-lg-4">
                                                                    <label class="primary_input_label"
                                                                           for="level">{{__('quiz.Question Level')}} </label>
                                                                    <select
                                                                        {{ $errors->has('level') ? ' autofocus' : '' }}
                                                                        class="primary_select {{ $errors->has('level') ? ' is-invalid' : '' }}"
                                                                        id="level" name="level">

                                                                        @foreach($levels as $level)
                                                                            @if(isset($bank))
                                                                                <option
                                                                                    value="{{$level->id}}" {{$bank->level == $level->id? 'selected': ''}}>{{$level->title}}</option>
                                                                            @else
                                                                                <option
                                                                                    value="{{$level->id}}" {{old('level')!=''? (old('level') == $level->id? 'selected':''):''}}>{{$level->title}}</option>
                                                                            @endif

                                                                        @endforeach
                                                                    </select>

                                                                </div>
                                                                <div class="col-lg-4 mt-30-md" id="preConditionQus">
                                                                    <label class="primary_input_label"
                                                                           for="subcategory_id">{{__('quiz.Pre-Condition Question')}}</label>
                                                                    <select
                                                                        {{ $errors->has('pre_condition') ? ' autofocus' : '' }}
                                                                        class="primary_select{{ $errors->has('pre_condition') ? ' is-invalid' : '' }} select_section"
                                                                        id="pre_condition" name="pre_condition">

                                                                        <option value="0"
                                                                                @if(isset($bank) && $bank->pre_condition==0)
                                                                                    selected
                                                                            @endif
                                                                        >{{__('common.No')}}</option>

                                                                        <option value="1"
                                                                                @if(isset($bank) && $bank->pre_condition==1)
                                                                                    selected
                                                                            @endif
                                                                        >{{__('common.Yes')}}</option>
                                                                    </select>

                                                                </div>
                                                            @endif
                                                            <div class="col-lg-4">
                                                                <label id="QuestionTypeLevel"
                                                                       class="primary_input_label {{isModuleActive('AdvanceQuiz')?'mt-25':''}}"
                                                                       for="question-type">{{__('quiz.Question Type')}}
                                                                    <span
                                                                        class="required_mark">*</span></label>
                                                                <select
                                                                    {{ $errors->has('question_type') ? ' autofocus' : '' }}
                                                                    class="primary_select{{ $errors->has('question_type') ? ' is-invalid' : '' }}"
                                                                    name="question_type" id="question-type">
                                                                    <option data-display="{{__('quiz.Question Type')}} "
                                                                            value="">{{__('quiz.Question Type')}}
                                                                    </option>

                                                                    <option
                                                                        value="M" {{ old('question_type',isset($bank)? $bank->type:'') == 'M'? 'selected': '' }}> {{__('quiz.Multiple Choice')}}</option>

                                                                    <option
                                                                        value="O" {{ old('question_type',isset($bank)? $bank->type:'') == 'O'? 'selected': '' }} > {{__('quiz.Sorting')}} </option>

                                                                    <option
                                                                        value="X" {{ old('question_type',isset($bank)? $bank->type:'') == 'X'? 'selected': '' }} > {{__('quiz.Matching')}} </option>

                                                                    <option
                                                                        value="C" {{ old('question_type',isset($bank)? $bank->type:'') == 'C'? 'selected': '' }} > {{__('quiz.Cloze question')}} </option>

                                                                    <option
                                                                        value="P" {{ old('question_type',isset($bank)? $bank->type:'') == 'P'? 'selected': '' }} > {{__('quiz.Puzzle')}} </option>

                                                                     <option
                                                                         value="S" {{ old('question_type',isset($bank)? $bank->type:'') == 'S'? 'selected': '' }}> {{__('quiz.Short Answer')}} </option>
                                                                     <option
                                                                         value="L" {{ old('question_type',isset($bank)? $bank->type:'') == 'L'? 'selected': '' }}> {{__('quiz.Long Answer')}} </option>
                                                                     <option
                                                                         value="T" {{ old('question_type',isset($bank)? $bank->type:'') == 'T'? 'selected': '' }}> {{__('quiz.True False')}} </option>
                                                                     <option
                                                                         value="F" {{ old('question_type',isset($bank)? $bank->type:'') == 'F'? 'selected': '' }}> {{__('quiz.Fill In The Blanks')}} </option>
                                                                </select>

                                                            </div>
                                                            <div class="col-lg-2">
                                                                <div
                                                                    class="input-effect {{isModuleActive('AdvanceQuiz')?'mt-25':''}}">
                                                                    <label
                                                                        class="primary_input_label"> {{__('quiz.Marks')}}
                                                                        <span
                                                                            class="required_mark">*</span>
                                                                    </label>
                                                                    <input
                                                                        {{ $errors->has('marks') ? ' autofocus' : '' }}
                                                                        class="primary_input_field name{{ $errors->has('marks') ? ' is-invalid' : '' }}"
                                                                        type="number" name="marks"
                                                                        value="{{isset($bank)? $bank->marks:(old('marks')!=''?(old('marks')):'')}}">
                                                                    <span class="focus-border"></span>

                                                                </div>
                                                            </div>

                                                            <div class="col-lg-2" id="shuffleBox">
                                                                <div
                                                                    class="input-effect @if(isModuleActive('AdvanceQuiz'))  mt-25 @endif">
                                                                    <label
                                                                        class="primary_input_label mt-1"> {{__('quiz.Shuffle Answer')}}
                                                                        <span
                                                                            class="required_mark">*</span>
                                                                    </label>
                                                                    <select
                                                                        {{ $errors->has('shuffle') ? ' autofocus' : '' }}
                                                                        class="primary_select{{ $errors->has('shuffle') ? ' is-invalid' : '' }}"
                                                                        name="shuffle" id="shuffle">
                                                                        <option
                                                                            value="1" {{isset($bank)? $bank->shuffle ==1? 'selected': '' : 'selected'}}> {{__('common.Yes')}}</option>
                                                                        <option
                                                                            value="0" {{isset($bank)? $bank->shuffle ==0? 'selected': '' : ''}}> {{__('common.No')}}</option>

                                                                    </select>

                                                                </div>
                                                            </div>


                                                            <div class="col-xl-4">
                                                                <div class=" mt-25">
                                                                    <x-upload-file
                                                                        name="image"
                                                                        type="image"
                                                                        media_id="{{isset($bank)?$bank->image_media?->media_id:''}}"
                                                                        label="{{ __('common.Image') }}"
                                                                    />
                                                                </div>

                                                            </div>
                                                        </div>
                                                        <div class="row mt-25">
                                                            <div class="col-lg-12">
                                                                <div class="input-effect">
                                                                    <label
                                                                        class="primary_input_label mt-1"> {{__('quiz.Question')}}
                                                                        <span
                                                                            class="required_mark">*</span></label>
                                                                    <textarea
                                                                        class="textArea lms_summernote {{ @$errors->has('details') ? ' is-invalid' : '' }}"
                                                                        cols="30" rows="10"
                                                                        name="question">{{isset($bank)? $bank->question:(old('question')!=''?(old('question')):'')}}</textarea>

                                                                    <span class="focus-border textarea"></span>

                                                                </div>
                                                            </div>
                                                        </div>


                                                        @php
                                                            if((isset($bank) && $bank->type == "M") || old('question_type') == "M"){
                                                                 $multiple_choice = "";
                                                                 $multiple_options = "";
                                                             }

                                                              if((isset($bank) && $bank->type == "X") || old('question_type') == "X"){
                                                                 $matching_choice = "";
                                                                 $matching_options = "";
                                                             }

                                                              if((isset($bank) && $bank->type == "O") || old('question_type') == "O"){
                                                                     $sorting_choice = "";
                                                                     $sorting_options = "";
                                                                 }

                                                              if((isset($bank) && $bank->type == "P") || old('question_type') == "P"){
                                                                 $puzzle_choice = "";
                                                                 $puzzle_options = "";
                                                             }
                                                        @endphp
                                                        <div class="multiple-choice">
                                                            <div class="row  mt-25 align-items-end">
                                                                <div class="col-lg-8">
                                                                    <div class="input-effect">
                                                                        <label
                                                                            class="primary_input_label mt-1"> {{__('quiz.Number Of Options')}}
                                                                            <span
                                                                                class="required_mark">*</span></label>
                                                                        <input
                                                                            {{ $errors->has('number_of_option') ? ' autofocus' : '' }}
                                                                            class="primary_input_field name{{ $errors->has('number_of_option') ? ' is-invalid' : '' }}"
                                                                            type="number" name="number_of_option"
                                                                            autocomplete="off"
                                                                            id="number_of_option"
                                                                            value="{{isset($bank)? $bank->number_of_option: ''}}">
                                                                        <span class="focus-border"></span>

                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2 mt-40 mb-2">
                                                                    <button type="button"
                                                                            class="primary-btn small fix-gr-bg"
                                                                            id="create-option">{{__('quiz.Create')}} </button>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="matching-choice ">
                                                            <div class="row  mt-25">
                                                                <div class="col-lg-3">
                                                                    <div class="input-effect">
                                                                        <label
                                                                            class="primary_input_label mt-1"> {{__('quiz.Number Of Options')}}
                                                                            <span
                                                                                class="required_mark">*</span></label>
                                                                        <input
                                                                            {{ $errors->has('number_of_option') ? ' autofocus' : '' }}
                                                                            class="primary_input_field name{{ $errors->has('number_of_option') ? ' is-invalid' : '' }}"
                                                                            type="number" name="number_of_qus"
                                                                            autocomplete="off"
                                                                            id="number_of_qus"
                                                                            data-title="{{__('quiz.Option')}}"
                                                                            value="{{isset($bank)? $bank->number_of_qus: ''}}">
                                                                        <span class="focus-border"></span>

                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-3 mt-40">
                                                                    <button type="button"
                                                                            class="primary-btn small fix-gr-bg"
                                                                            id="create-qus-option">{{__('quiz.Create')}} </button>
                                                                </div>

                                                                <div class="col-lg-3">
                                                                    <div class="input-effect">
                                                                        <label
                                                                            class="primary_input_label mt-1"> {{__('quiz.Number Of Answer')}}
                                                                            <span
                                                                                class="required_mark">*</span></label>
                                                                        <input
                                                                            {{ $errors->has('number_of_ans') ? ' autofocus' : '' }}
                                                                            class="primary_input_field name{{ $errors->has('number_of_ans') ? ' is-invalid' : '' }}"
                                                                            type="number" name="number_of_ans"
                                                                            autocomplete="off"
                                                                            id="number_of_ans"
                                                                            data-title="{{__('quiz.Answer')}}"
                                                                            value="{{old('number_of_ans',isset($bank)? $bank->number_of_ans: '')}}">
                                                                        <span class="focus-border"></span>

                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-3 mt-40">
                                                                    <button type="button"
                                                                            class="primary-btn small fix-gr-bg"
                                                                            id="create-ans-option">{{__('quiz.Create')}} </button>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="sorting-choice">
                                                            <div class="row  mt-25 align-items-end">
                                                                <div class="col-lg-8">
                                                                    <div class="input-effect">
                                                                        <label
                                                                            class="primary_input_label mt-1"> {{__('quiz.Number Of Options')}}
                                                                            <span
                                                                                class="required_mark">*</span></label>
                                                                        <input
                                                                            {{ $errors->has('number_of_sorting_option') ? ' autofocus' : '' }}
                                                                            class="primary_input_field name{{ $errors->has('number_of_sorting_option') ? ' is-invalid' : '' }}"
                                                                            type="number"
                                                                            name="number_of_sorting_option"
                                                                            autocomplete="off"
                                                                            id="number_of_sorting_option"
                                                                            value="{{isset($bank)? $bank->number_of_option: ''}}">
                                                                        <span class="focus-border"></span>

                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2 mt-40 mb-2">
                                                                    <button type="button"
                                                                            class="primary-btn small fix-gr-bg"
                                                                            id="create-sorting-option">{{__('quiz.Create')}} </button>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="cloze-choice">
                                                            <div class="row  mt-25 align-items-end">
                                                                <div class="col-lg-8">
                                                                    <div class="input-effect">
                                                                        <div class="instruction-box pt-3 pb-3">
                                                                            <strong>{{__('quiz.Instruction')}}:</strong>
                                                                            {{__('quiz.Please enter your question in the following format')}}
                                                                            :
                                                                            use <code>[1]</code>, <code>[2]</code>, etc.
                                                                            to indicate the
                                                                            blanks.
                                                                            <br>Example: "The capital of France is
                                                                            <code>[1]</code>."
                                                                        </div>

                                                                        <label
                                                                            class="primary_input_label mt-1"> {{__('quiz.Number Of Blanks')}}
                                                                            <span
                                                                                class="required_mark">*</span>
                                                                        </label>

                                                                        <input
                                                                            {{ $errors->has('number_of_cloze_option') ? ' autofocus' : '' }}
                                                                            class="primary_input_field name{{ $errors->has('number_of_cloze_option') ? ' is-invalid' : '' }}"
                                                                            type="number" name="number_of_cloze_option"
                                                                            autocomplete="off"
                                                                            id="number_of_cloze_option"
                                                                            value="{{isset($bank)? $bank->number_of_option: ''}}">
                                                                        <span class="focus-border"></span>

                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2 mt-40 mb-2">
                                                                    <button type="button"
                                                                            class="primary-btn small fix-gr-bg"
                                                                            id="create-cloze-option">{{__('quiz.Create')}} </button>
                                                                </div>
                                                            </div>
                                                        </div>


                                                        <div class="puzzle-choice ">
                                                            <div class="row  mt-25">
                                                                <div class="col-lg-3">
                                                                    <div class="input-effect">
                                                                        <label
                                                                            class="primary_input_label mt-1"> {{__('quiz.Number Of Options')}}
                                                                            <span
                                                                                class="required_mark">*</span></label>
                                                                        <input
                                                                            {{ $errors->has('puzzle_number_of_qus') ? ' autofocus' : '' }}
                                                                            class="primary_input_field name{{ $errors->has('puzzle_number_of_qus') ? ' is-invalid' : '' }}"
                                                                            type="number" name="puzzle_number_of_qus"
                                                                            autocomplete="off"
                                                                            id="puzzle_number_of_qus"
                                                                            data-title="{{__('quiz.Option')}}"
                                                                            value="{{isset($bank)? $bank->number_of_qus: ''}}">
                                                                        <span class="focus-border"></span>

                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-3 mt-40">
                                                                    <button type="button"
                                                                            class="primary-btn small fix-gr-bg"
                                                                            id="create-puzzle-qus-option">{{__('quiz.Create')}} </button>
                                                                </div>

                                                                <div class="col-lg-3">
                                                                    <div class="input-effect">
                                                                        <label
                                                                            class="primary_input_label mt-1"> {{__('quiz.Number Of Answer')}}
                                                                            <span
                                                                                class="required_mark">*</span></label>
                                                                        <input
                                                                            {{ $errors->has('puzzle_number_of_ans') ? ' autofocus' : '' }}
                                                                            class="primary_input_field name{{ $errors->has('puzzle_number_of_ans') ? ' is-invalid' : '' }}"
                                                                            type="number" name="puzzle_number_of_ans"
                                                                            autocomplete="off"
                                                                            id="puzzle_number_of_ans"
                                                                            data-title="{{__('quiz.Answer')}}"
                                                                            value="{{old('puzzle_number_of_ans',isset($bank)? $bank->number_of_ans: '')}}">
                                                                        <span class="focus-border"></span>

                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-3 mt-40">
                                                                    <button type="button"
                                                                            class="primary-btn small fix-gr-bg"
                                                                            id="create-puzzle-ans-option">{{__('quiz.Create')}} </button>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{--question option start--}}
                                                        <div class="multiple-options questionBoxDiv"
                                                             id="{{isset($multiple_options)? "": 'multiple-options'}}">
                                                            @php
                                                                $i=0;
                                                                $multiple_options = [];

                                                                if(isset($bank)){
                                                                    if($bank->type == "M"){
                                                                        $multiple_options = $bank->questionMuInSerial;
                                                                    }
                                                                }
                                                            @endphp
                                                             @foreach($multiple_options as $multiple_option)

                                                                 @php $i++; @endphp
                                                                  <div class='row  mt-25'>
                                                                      <div class='col-lg-7'>
                                                                          <div class='input-effect'>
                                                                               <input class='primary_input_field name'
                                                                                      type='text'
                                                                                      name='option[]' autocomplete='off'
                                                                                      value="{{$multiple_option->title}}">
                                                                              <span class='focus-border'></span>
                                                                          </div>
                                                                      </div>
                                                                      <div class='col-lg-3'>
                                                                          <div class="primary_input single-uploader">
                                                                              <div class="primary_file_uploader" data-bs-toggle="infixUploader" data-multiple="false" data-type="image" data-name="option_image[{{$i}}]">
                                                                                  <input class="primary-input file_amount" type="text" id="file_option_image_{{$i}}" placeholder="{{__('common.Browse')}}" readonly>
                                                                                  <button type="button">
                                                                                      <label class="primary-btn small fix-gr-bg" for="file_option_image_{{$i}}">{{__('common.Browse')}}</label>
                                                                                      <input type="hidden" class="selected_files" value="{{$multiple_option->image_media?->media_id ?? ''}}">
                                                                                  </button>
                                                                              </div>
                                                                              <div class="product_image_all_div">
                                                                                  @if($multiple_option->image_media?->media_id)
                                                                                      <input type="hidden" name="option_image[{{$i}}]" value="{{$multiple_option->image_media->media_id}}">
                                                                                  @endif
                                                                              </div>
                                                                          </div>
                                                                      </div>
                                                                      <div class='col-lg-2 mt-40'>
                                                                          <label class="primary_checkbox d-flex mr-12 "
                                                                                 for="option_check_{{$i}}">
                                                                              <input type="checkbox"
                                                                                     @if ($multiple_option->status==1) checked
                                                                                     @endif id="option_check_{{$i}}"
                                                                                     name="option_check_{{$i}}" value="1">
                                                                              <span class="checkmark"></span>
                                                                          </label>
                                                                      </div>
                                                                  </div>
                                                             @endforeach
                                                        </div>

                                                        <div class="sorting-options questionBoxDiv"
                                                             id="{{isset($multiple_options)? "": 'multiple-options'}}">
                                                            @php
                                                                $i=0;
                                                                $sorting_options = [];

                                                                if(isset($bank)){
                                                                    if($bank->type == "O"){
                                                                        $sorting_options = $bank->questionSortingOptionsSerial;
                                                                    }
                                                                }
                                                            @endphp
                                                            @foreach($sorting_options as $key=>$sorting_option)

                                                                <div class='row  mt-25' id='option-{{$key}}'>
                                                                    <div class='col-lg-10'>
                                                                        <div class='input-effect'>
                                                                            <input class='primary_input_field name'
                                                                                   type='text'
                                                                                   name='sorting_option[]'
                                                                                   autocomplete='off' required
                                                                                   value="{{$sorting_option->title}}">
                                                                            <span class='focus-border'></span>
                                                                        </div>
                                                                    </div>
                                                                    <div class='col-lg-2 mt-15 '>
                                                                        <span class='drag-handle' style='cursor: move;'>&#9776;</span>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>

                                                        <div class="matching-options "
                                                             id="{{isset($matching_choice)? "": 'matching-options'}}">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div id="drawflow" ondrop="drop(event)"
                                                                         ondragover="allowDrop(event)"
                                                                         style="width: 100%;overflow: auto">


                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="cloze-options QA_section   table-responsive">
                                                            <table class=" QA_table table table-borderless mt-3">
                                                                <thead>
                                                                <tr>
                                                                    <th class="p-2">{{trans('quiz.Number')}}</th>
                                                                    <th class="p-2 w-75">{{trans('quiz.Options')}}</th>
                                                                    <th class="p-2">{{trans('common.Action')}}</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                @php
                                                                    $i = 0;
                                                                    $cloze_options = [];

                                                                    if (isset($bank) && $bank->type == "C") {
                                                                        $cloze_options = $bank->questionMuInSerial;
                                                                    }
                                                                @endphp
                                                                @if(isset($cloze_options) && count($cloze_options) > 0)
                                                                    @foreach($cloze_options->groupBy('group') as $key => $cloze_group)
                                                                        <tr class='option-row'>
                                                                            <td class='p-2'>{{ $loop->iteration }}</td>
                                                                            <td class='p-2'>
                                                                                <div class='options-container'
                                                                                     data-row-number='{{ $loop->iteration }}'>
                                                                                    @foreach($cloze_group as $option)
                                                                                        <div
                                                                                            class='input-effect mb-2 d-flex align-items-center'>
                                                                                            {{-- Pre-fill option value --}}
                                                                                            <input
                                                                                                class='primary_input_field name'
                                                                                                placeholder='Option {{ $loop->iteration }}'
                                                                                                type='text'
                                                                                                name='cloze_option[{{ $loop->parent->iteration }}][]'
                                                                                                value="{{ $option->title }}"
                                                                                                autocomplete='off'
                                                                                                required>

                                                                                            {{-- Checkbox for correct answer --}}
                                                                                            <label
                                                                                                class='primary_checkbox d-flex ms-3'>
                                                                                                <input
                                                                                                    name='cloze_answer[{{ $loop->parent->iteration }}]'
                                                                                                    value='{{ $loop->iteration }}'
                                                                                                    type='radio' {{ $option->status ? 'checked' : '' }}>
                                                                                                <span
                                                                                                    class='checkmark'></span>
                                                                                            </label>
                                                                                        </div>
                                                                                    @endforeach
                                                                                </div>
                                                                            </td>
                                                                            <td class='p-2'>
                                                                                <div class='d-flex'>
                                                                                    <button type='button'
                                                                                            class='primary-btn small fix-gr-bg add-option-btn'>
                                                                                        <i class='ti ti-plus m-0 p-0'></i>
                                                                                    </button>
                                                                                    <button type='button'
                                                                                            class='primary-btn small fix-gr-bg remove-option-btn ms-2'>
                                                                                        <i class='ti ti-trash m-0 p-0'></i>
                                                                                    </button>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                @endif
                                                                </tbody>


                                                            </table>

                                                        </div>


                                                        <div class="puzzle-options">


                                                            @php
                                                                $i=0;
                                                                $puzzle_options = [];

                                                                $puzzleQus =[];
                                                                $puzzleAns =[];
                                                                if(isset($bank) && $bank->type == "P"){
                                                                    $puzzle_options = $bank->questionSortingOptionsSerial;
                                                                    $puzzleQus = $puzzle_options->where('type',1);
                                                                    $puzzleAns = $puzzle_options->where('type',0);
                                                                }
                                                            @endphp

                                                            <div class="row">
                                                                <div class="col-6 mt-3" id="puzzleQus">
                                                                    @php
                                                                        $puzzleQusIndex=0;
                                                                    @endphp
                                                                    @foreach($puzzleQus as  $qus)
                                                                        <div class='row optionType mb-3' data-type='qus'
                                                                             data-index='{{ $puzzleQusIndex }}'>
                                                                            <div class='col-lg-12 optionTitle'>
                                                                                <div class='input-group'>
                                                                                    <input
                                                                                        class='form-control option_title'
                                                                                        type='text'
                                                                                        value="{{$qus->title}}"
                                                                                        name='puzzle_qus[{{ $puzzleQusIndex }}]'
                                                                                        placeholder='Enter Question {{ $puzzleQusIndex + 1 }}'
                                                                                        required>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        @php
                                                                            $puzzleQusIndex++;
                                                                        @endphp
                                                                    @endforeach
                                                                </div>
                                                                <div class="col-6 mt-3" id="puzzleAns">
                                                                    @php
                                                                        $puzzleAnsIndex=0;
                                                                    @endphp
                                                                    @foreach($puzzleAns as  $ans)
                                                                        <div class='row optionType mb-3' data-type='ans'
                                                                             data-index='{{ $puzzleAnsIndex }}'>
                                                                            <div class='col-lg-12 optionTitle'>
                                                                                <div class='input-group'>
                                                                                    <input
                                                                                        class='form-control ans_title'
                                                                                        type='text'
                                                                                        value="{{$ans->title}}"
                                                                                        name='puzzle_ans[{{ $puzzleAnsIndex }}]'
                                                                                        placeholder='Enter Answer {{ $puzzleAnsIndex + 1 }}'>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        @php
                                                                            $puzzleAnsIndex++;
                                                                        @endphp
                                                                    @endforeach
                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-12" id="puzzleCombine">
                                                                    @if(isset($bank))
                                                                        <div class="col-12">
                                                                            <h4>{{ __('quiz.Correct option') }}</h4>
                                                                        </div>
                                                                        @php
                                                                            $puzzleQusIndex=0;
                                                                        @endphp
                                                                        @foreach($puzzleQus as $i => $qus)
                                                                            <div class="row mb-5">
                                                                                <div class="col-lg-6">
                                                                                    <label>{{__('quiz.Question')}} {{ $puzzleQusIndex=$puzzleQusIndex + 1 }}
                                                                                        :</label>
                                                                                </div>
                                                                                <div
                                                                                    class="col-lg-6 d-flex flex-column gap-3">
                                                                                    @php
                                                                                        $puzzleAnsIndex=0;
                                                                                    @endphp
                                                                                    @foreach($puzzleAns as $j => $ans)
                                                                                        @php
                                                                                            $checkboxId = "questionReview_{$i}_{$j}";
                                                                                            $hasItem= $bank->matchingOptions->where('option_id',$qus->id)->where('answer_id',$ans->id)->first();
                                                                                        @endphp
                                                                                        <label
                                                                                            class="primary_checkbox d-flex text-nowrap mr-12">
                                                                                            <input
                                                                                                name="question_review[{{ $i }}][{{$puzzleAnsIndex}}]"
                                                                                                value="{{ $puzzleAnsIndex }}"
                                                                                                id="{{ $checkboxId }}"
                                                                                                {{$hasItem? 'checked': ''}}
                                                                                                type="checkbox">
                                                                                            <span
                                                                                                class="checkmark"></span>
                                                                                            <span
                                                                                                class="ms-2">{{ trans('quiz.Answer').' ' . ($puzzleAnsIndex =$puzzleAnsIndex+1) }}</span>
                                                                                        </label>
                                                                                    @endforeach
                                                                                </div>
                                                                            </div>
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- question options end--}}

                                                        {{-- True / False --}}
                                                        <div class="true-false-choice" style="display:none;">
                                                            <div class="row mt-25">
                                                                <div class="col-lg-12">
                                                                    <label class="primary_input_label">{{__('quiz.Select Correct Answer')}}</label>
                                                                    <div class="mt-2">
                                                                        <label class="primary_checkbox d-flex mr-12">
                                                                            <input type="radio" name="trueOrFalse" value="1"
                                                                                {{old('trueOrFalse',isset($bank)? $bank->trueFalse:'')=='1'?'checked':''}}>
                                                                            <span class="checkmark mr-2"></span>
                                                                            {{__('quiz.True')}}
                                                                        </label>
                                                                        <label class="primary_checkbox d-flex mr-12 mt-2">
                                                                            <input type="radio" name="trueOrFalse" value="0"
                                                                                {{old('trueOrFalse',isset($bank)? $bank->trueFalse:'')=='0'?'checked':''}}>
                                                                            <span class="checkmark mr-2"></span>
                                                                            {{__('quiz.False')}}
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- Fill in the Blanks --}}
                                                        <div class="fill-blanks-choice" style="display:none;">
                                                            <div class="row mt-25">
                                                                <div class="col-lg-12">
                                                                    <div class="input-effect">
                                                                        <label class="primary_input_label mt-1">
                                                                            {{__('quiz.Suitable Words')}}
                                                                            <span class="required_mark">*</span>
                                                                        </label>
                                                                        <textarea
                                                                            class="primary_input_field name"
                                                                            cols="30" rows="3"
                                                                            name="suitable_words">{{old('suitable_words',isset($bank)? $bank->suitable_words:'')}}</textarea>
                                                                        <span class="focus-border textarea"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="">
                                                            <div class="row  mt-25">
                                                                <div class="col-lg-12">
                                                                    <div class="input-effect">
                                                                        <label
                                                                            class="primary_input_label mt-1"> {{__('quiz.Explanation')}} </label>
                                                                        <textarea
                                                                            class="textArea lms_summernote {{ @$errors->has('details') ? ' is-invalid' : '' }}"
                                                                            cols="10" rows="10"
                                                                            name="explanation">{{isset($bank)? $bank->explanation:(old('explanation')!=''?(old('explanation')):'')}}</textarea>

                                                                        <span class="focus-border textarea"></span>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                         </div>

                                                         <div class="col-xl-6">
                                                             <div class=" mt-25">
                                                                 <x-upload-file
                                                                     name="video"
                                                                     type="video"
                                                                     media_id="{{isset($bank)?$bank->video_media?->media_id:''}}"
                                                                     label="{{__('quiz.Video')}}"
                                                                 />
                                                             </div>
                                                         </div>

                                                        <div class="row mt-3">
                                                            <div class="col-lg-12 text-center">
                                                                <button class="primary-btn fix-gr-bg questionSubmitBtn"
                                                                        data-bs-toggle="tooltip"
                                                                        type="submit">
                                                                    <i class="ti-check"></i>
                                                                    {{ isset($bank) ? __('common.Update') : __('common.Save') }} {{ __('quiz.Question') }}
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <button type="button" class="math-keyboard-toggle" id="mathKeyboardToggle" title="{{__('quiz.Math Keyboard')}} (Ctrl+Shift+M)">
        <span class="pulse-ring"></span>
        ∑
        <span class="kbd-hint">⌘M</span>
    </button>
    <div class="math-keyboard-panel d-none" id="mathKeyboardPanel">
        <div class="math-keyboard-header">
            <div class="math-keyboard-title" id="mathKeyboardDragHandle">
                <span class="title-icon">∑</span>
                <span class="title-copy">
                    <span class="title-text">محرر المعادلات</span>
                    <span class="title-subtitle">اكتب، عاين، ثم أدرج المعادلة داخل السؤال</span>
                </span>
                <span class="title-badge">LaTeX</span>
            </div>
            <div class="header-actions">
                <button type="button" class="header-action-btn" id="mathSaveTemplateBtn" title="حفظ كقالب" aria-label="حفظ المعادلة كقالب"><i class="ti-save" aria-hidden="true"></i></button>
                <button type="button" class="header-action-btn" id="mathHistoryBtn" title="المعادلات السابقة" aria-label="عرض المعادلات السابقة"><i class="ti-timer" aria-hidden="true"></i></button>
                <button type="button" class="header-action-btn" id="mathClearBtn" title="مسح المعادلة" aria-label="مسح المعادلة"><i class="ti-reload" aria-hidden="true"></i></button>
                <button type="button" class="header-action-btn" id="mathResetPanelBtn" title="استعادة حجم النافذة" aria-label="استعادة حجم النافذة"><i class="ti-layout-width-default" aria-hidden="true"></i></button>
                <button type="button" class="math-keyboard-close" id="mathKeyboardClose" title="إغلاق" aria-label="إغلاق محرر المعادلات"><i class="ti-close" aria-hidden="true"></i></button>
            </div>
        </div>

        <div class="math-writer-area" id="mathWriterArea">
            <div class="math-writer-heading">
                <strong>كود المعادلة</strong>
                <span>استخدم LaTeX أو اختر رمزاً من المكتبة بالأسفل</span>
            </div>
            <textarea id="eqLatexInput" placeholder="اكتب LaTeX هنا، مثال: x^{٢}+٣=٧" rows="3" spellcheck="false"></textarea>
            <div class="math-digit-tools" role="group" aria-label="اختيار أرقام المعادلة">
                <span class="math-digit-tools-label">أرقام المعادلة:</span>
                <button type="button" class="math-digit-convert-btn" data-digit-style="arabic" title="تحويل أرقام المعادلة إلى العربية">١٢٣</button>
                <button type="button" class="math-digit-convert-btn" data-digit-style="latin" title="تحويل أرقام المعادلة إلى الإنجليزية">123</button>
                <span class="math-digit-tools-help">اكتب بأي نوع ثم حوّل المعادلة بضغطة واحدة.</span>
            </div>
            <details class="math-shortcuts">
                <summary>اختصارات لوحة المفاتيح</summary>
                <div class="math-shortcuts-list">
                    <span>Ctrl+F → \frac</span>
                    <span>Ctrl+R → \sqrt</span>
                    <span>Ctrl+Shift+P → ^{ }</span>
                    <span>Ctrl+Shift+I → _{ }</span>
                    <span>Ctrl+Enter → إدراج</span>
                    <span>Alt+Click → إدراج مباشر</span>
                </div>
            </details>
            <div class="math-writer-toolbar">
                <div class="math-preview-shell">
                    <div class="math-preview-label"><span>المعاينة المباشرة</span><span>تتحدث أثناء الكتابة</span></div>
                    <div class="math-writer-preview" id="eqPreview">\\[ \\; \\]</div>
                </div>
                <div class="math-writer-actions">
                    <button type="button" class="math-writer-insert-btn" id="eqInsertBtn">
                        <i class="ti-plus" aria-hidden="true"></i>
                        إدراج المعادلة
                    </button>
                </div>
            </div>
        </div>

        <div class="math-keyboard-search" style="position:relative">
            <span class="search-icon"><i class="ti-search" aria-hidden="true"></i></span>
            <input type="text" id="mathKeyboardSearch" placeholder="ابحث عن رمز أو عملية رياضية..." autocomplete="off" aria-label="البحث في مكتبة الرموز">
        </div>

        <div class="math-keyboard-recent" id="mathKeyboardRecent" style="display:none">
            <span class="recent-label">⚡ آخر</span>
            <span class="recent-symbols" id="mathKeyboardRecentList"></span>
        </div>

        <div class="math-tabs-section">
            <div class="math-tabs-header">
                <span class="tabs-label">مكتبة الرموز والقوالب</span>
                <div class="math-active-indicator" id="mathActiveIndicator">
                    <span class="dot inactive-dot" id="mathActiveDot"></span>
                    <span id="mathActiveLabel">اختر مكان إدراج المعادلة</span>
                </div>
            </div>
            <div class="math-keyboard-tabs" id="mathKeyboardTabs"></div>
        </div>

        <div class="math-keyboard-grid-wrap">
            <div class="math-keyboard-grid" id="mathKeyboardGrid"></div>
        </div>
        <div class="math-resize-handle" id="mathResizeHandle"></div>
    </div>

    {{--
        <div class="modal fade admin-query" id="deleteBank">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">{{__('common.Delete')}} </h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"><i
                                class="ti-close "></i></button>
                    </div>

                    <div class="modal-body">
                        <form action="{{route('question-bank-delete')}}" method="post">
                            @csrf

                            <div class="text-center">

                                <h4>{{__('common.Are you sure to delete ?')}} </h4>
                            </div>
                            <input type="hidden" name="id" value="" id="classQusId">
                            <div class="mt-40 d-flex justify-content-between">
                                <button type="button" class="primary-btn tr-bg"
                                        data-bs-dismiss="modal">{{__('common.Cancel')}}</button>

                                <button class="primary-btn fix-gr-bg"
                                        type="submit">{{__('common.Delete')}}</button>

                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>


        <div class="modal fade admin-query" id="removeImageModal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">{{__('common.Confirm')}} </h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"><i
                                class="ti-close "></i></button>
                    </div>

                    <div class="modal-body">
                        <form action="#" method="post">
                            @csrf

                            <div class="text-center">

                                <h4>{{__('common.Are you sure to remove')}}? </h4>
                            </div>
                            <input type="hidden" value="" id="quizId">
                            <input type="hidden" value="" id="targetContent">
                            <div class="mt-40 d-flex justify-content-between">
                                <button type="button" class="primary-btn tr-bg"
                                        data-bs-dismiss="modal">{{__('common.Cancel')}}</button>

                                <button class="primary-btn fix-gr-bg removeImageConfirm"
                                        type="button">{{__('common.Remove')}}</button>

                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>

        --}}
@endsection
@push('scripts')
    <script src="{{ asset('public/backend/js/katex.min.js') }}"></script>

    <script>
        function sendFile(files, editor, name) {
            let url = $('meta[name="_token"]').attr('content') ? window.location.origin : '{{url('/')}}';
            let formData = new FormData();
            $.each(files, function (i, v) {
                formData.append("files[" + i + "]", v);
            })
            $.ajax({
                url: url + '/summer-note-file-upload',
                type: 'post',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'JSON',
                success: function (response) {
                    let $summernote;
                    if (name) {
                        $summernote = $(editor + "[name='" + name + "']");
                    } else {
                        $summernote = $(editor);
                    }
                    $.each(response, function (i, v) {
                        $summernote.summernote('insertImage', v);
                    })
                },
                error: function (data) {
                    if (data.status === 404) {
                        toastr.error("What you are looking is not found", 'Opps!');
                        return;
                    } else if (data.status === 500) {
                        toastr.error('Something went wrong...', 'Opps');
                        return;
                    } else if (data.status === 200) {
                        toastr.error('Something is not right', 'Error');
                        return;
                    }
                    try {
                        let jsonValue = $.parseJSON(data.responseText);
                        let errors = jsonValue.errors;
                        if (errors) {
                            $.each(errors, function (key, value) {
                                toastr.error(value, 'Validation Error');
                            });
                        } else {
                            toastr.error(jsonValue.message, 'Opps!');
                        }
                    } catch(e) {
                        toastr.error('Upload failed', 'Error');
                    }
                }
            });
        }

        if ($('.lms_summernote').length) {
            $('.lms_summernote').each(function () {
                try { $(this).summernote('destroy'); } catch(e) {}
                $(this).summernote({
                    codeviewFilter: true,
                    codeviewIframeFilter: true,
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'underline', 'clear']],
                        ['fontname', ['fontname']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['table', ['table']],
                        ['insert', ['link', 'picture', 'video']],
                        ['view', ['fullscreen']],
                    ],
                    placeholder: '',
                    tabsize: 2,
                    height: 188,
                    callbacks: {
                        onImageUpload: function (files) {
                            sendFile(files, '.lms_summernote', $(this).attr('name'))
                        }
                    },
                    tooltip: false
                });
                $(this).data('summernote-inited', true);
                $(this).closest('.note-editor').find('[data-toggle]').each(function () {
                    $(this).attr('data-bs-toggle', $(this).attr('data-toggle')).removeAttr('data-toggle');
                });
            });
        }

        $("body").on('change', '.fileUpload1', function () {
            let placeholder = $(this).closest(".primary_file_uploader").find(".filePlaceholder");
            let fileInput = event.srcElement;
            placeholder.val(fileInput.files[0].name);
            console.log(fileInput.files[0].name);
            $('.removeImage1').removeClass('d-none');
        });


        $(document).on("click", ".questionSubmitBtn", function (e) {

            e.preventDefault();
            let type = $('#question-type').val();
            if (type == 'M') {
                let div = $('.questionBoxDiv');
                let count = div.find('[type=checkbox]:checked').length;
                let errorOptionCount = 0;

                if (count < 1) {
                    toastr.error('{{__('common.At least one correct answer is required')}} ', '{{__('common.Error')}}');
                    return false;
                }

                $('.questionBoxDiv .row').each(function (index) {
                    var titleInput = $(this).find('input[name="option[]"]');
                    var imageInput = $(this).find('.selected_files').val();
                    if (titleInput.length && titleInput.val().trim() == "" && !imageInput) {
                        errorOptionCount++;
                    }
                });

                if (errorOptionCount != 0) {
                    toastr.error('{{__("quiz.Option title is required when no image is uploaded")}}', '{{__("common.Error")}}');
                    return false;
                }

            } else if (type == 'X') {
                let connection = $('#connection').val().length;
                if (connection == 0) {
                    toastr.error('{{__('common.At least one correct answer is required')}} ', '{{__('common.Error')}}');
                    return false;
                }
                let errorCount = 0;
                $('.option_title').each(function (index) {
                    if ($(this).val().trim() == "") {
                        errorCount++;
                        toastr.error('{{__('quiz.Option title is required')}} ', '{{__('common.Error')}}');
                    }
                });
                $('.ans_title').each(function (index) {
                    if ($(this).val().trim() == "") {
                        errorCount++;
                        toastr.error('{{__('quiz.Answer title is required')}} ', '{{__('common.Error')}}');
                    }
                });
                if (errorCount != 0) {
                    return false;
                }
                $('#data').val(JSON.stringify(editor.export()));
            }else if(type =='P'){
                let errorCount = 0;
                $('.option_title').each(function (index) {
                    if ($(this).val().trim() == "") {
                        errorCount++;
                        toastr.error('{{__('quiz.Option title is required')}} ', '{{__('common.Error')}}');
                    }
                });
                $('.ans_title').each(function (index) {
                    if ($(this).val().trim() == "") {
                        errorCount++;
                        toastr.error('{{__('quiz.Answer title is required')}} ', '{{__('common.Error')}}');
                    }
                });

                if (errorCount != 0) {
                    return false;
                }
            }
            $(this).closest('form').submit();
        });

        $('#question-type').change(function (e) {

            let type = $('#question-type').val();
            if (type == 'M') {
                $('.multiple-choice').show();
                $('.multiple-options').show();

                $('.sorting-choice').hide();
                $('.sorting-options').hide();
                $('.cloze-choice').hide();
                $('.cloze-options').hide();
                $('.matching-choice').hide();
                $('.matching-options').hide();
                $('.puzzle-choice').hide();
                $('.puzzle-options').hide();
                $('.true-false-choice').hide();
                $('.fill-blanks-choice').hide();
                $('#shuffleBox').show();
                $('#preConditionQus').show();
                @if(isModuleActive('AdvanceQuiz'))
                $('#QuestionTypeLevel').addClass('mt-25');
                @endif
            } else if (type == 'O') {
                $('.multiple-choice').hide();
                $('.multiple-options').hide();

                $('.sorting-choice').show();
                $('.sorting-options').show();
                $('.cloze-choice').hide();
                $('.cloze-options').hide();
                $('.matching-choice').hide();
                $('.matching-options').hide();
                $('.puzzle-choice').hide();
                $('.puzzle-options').hide();
                $('.true-false-choice').hide();
                $('.fill-blanks-choice').hide();
                $('#shuffleBox').hide();
                $('#preConditionQus').hide();
                @if(isModuleActive('AdvanceQuiz'))
                $('#QuestionTypeLevel').addClass('mt-25');
                @endif
            } else if (type == 'C') {
                $('.multiple-choice').hide();
                $('.multiple-options').hide();
                $('.sorting-choice').hide();
                $('.sorting-options').hide();
                $('.cloze-choice').show();
                $('.cloze-options').show();
                $('.matching-choice').hide();
                $('.matching-options').hide();
                $('.puzzle-choice').hide();
                $('.puzzle-options').hide();
                $('.true-false-choice').hide();
                $('.fill-blanks-choice').hide();
                $('#shuffleBox').hide();
                $('#preConditionQus').hide();
                @if(isModuleActive('AdvanceQuiz'))
                $('#QuestionTypeLevel').addClass('mt-25');
                @endif
            } else if (type == 'P') {
                $('.multiple-choice').hide();
                $('.multiple-options').hide();
                $('.sorting-choice').hide();
                $('.sorting-options').hide();
                $('.cloze-choice').hide();
                $('.cloze-options').hide();
                $('.matching-choice').hide();
                $('.matching-options').hide();
                $('.puzzle-choice').show();
                $('.puzzle-options').show();
                $('.true-false-choice').hide();
                $('.fill-blanks-choice').hide();
                $('#shuffleBox').hide();
                $('#preConditionQus').hide();
                @if(isModuleActive('AdvanceQuiz'))
                $('#QuestionTypeLevel').addClass('mt-25');
                @endif
            } else if (type == 'X') {
                $('.matching-choice').show();
                $('.matching-options').show();
                $('.sorting-choice').hide();
                $('.sorting-options').hide();
                $('.cloze-choice').hide();
                $('.cloze-options').hide();
                $('.multiple-choice').hide();
                $('.multiple-options').hide();
                $('.puzzle-choice').hide();
                $('.puzzle-options').hide();
                $('.true-false-choice').hide();
                $('.fill-blanks-choice').hide();
                $('#shuffleBox').hide();
                $('#preConditionQus').show();
                @if(isModuleActive('AdvanceQuiz'))
                $('#QuestionTypeLevel').addClass('mt-25');
                @endif
            } else if (type == 'T') {
                $('.multiple-choice').hide();
                $('.multiple-options').hide();
                $('.sorting-choice').hide();
                $('.sorting-options').hide();
                $('.cloze-choice').hide();
                $('.cloze-options').hide();
                $('.matching-choice').hide();
                $('.matching-options').hide();
                $('.puzzle-choice').hide();
                $('.puzzle-options').hide();
                $('.fill-blanks-choice').hide();
                $('.true-false-choice').show();
                $('#shuffleBox').hide();
                $('#preConditionQus').hide();
                @if(isModuleActive('AdvanceQuiz'))
                $('#QuestionTypeLevel').addClass('mt-25');
                @endif
            } else if (type == 'F') {
                $('.multiple-choice').hide();
                $('.multiple-options').hide();
                $('.sorting-choice').hide();
                $('.sorting-options').hide();
                $('.cloze-choice').hide();
                $('.cloze-options').hide();
                $('.matching-choice').hide();
                $('.matching-options').hide();
                $('.puzzle-choice').hide();
                $('.puzzle-options').hide();
                $('.true-false-choice').hide();
                $('.fill-blanks-choice').show();
                $('#shuffleBox').hide();
                $('#preConditionQus').hide();
                @if(isModuleActive('AdvanceQuiz'))
                $('#QuestionTypeLevel').addClass('mt-25');
                @endif
            } else {
                $('.sorting-choice').hide();
                $('.sorting-options').hide();
                $('.multiple-choice').hide();
                $('.multiple-options').hide();
                $('.matching-choice').hide();
                $('.matching-options').hide();
                $('.puzzle-choice').hide();
                $('.puzzle-options').hide();
                $('.cloze-choice').hide();
                $('.cloze-options').hide();
                $('.true-false-choice').hide();
                $('.fill-blanks-choice').hide();
                $('#shuffleBox').hide();
                $('#preConditionQus').hide();
                @if(isModuleActive('AdvanceQuiz'))
                $('#QuestionTypeLevel').removeClass('mt-25');
                @endif
            }

            if (type == "S") {
                $('#marks_required').hide();
            } else {
                $('#marks_required').show();
            }

        });
        $('#question-type').trigger('change')

        $(document).on("click", ".removeImage1", function (e) {
            e.preventDefault();
            let target = $(this).data('target')
            let id = $(this).data('id')
            console.log(id);
            $('#targetContent').val(target);
            $('#quizId').val(id);
            $('#removeImageModal').modal('show');
        });

        $(document).on("click", ".removeImageConfirm", function (e) {
            e.preventDefault();
            let target_name = $('#targetContent').val();
            let id = $('#quizId').val();
            let target = $(target_name);
            target.find('.filePlaceholder').val('');
            target.find('.fileUpload1').val('');
            $('#removeImageModal').modal('hide');
            $('.removeImage1').addClass('d-none');
            if (id != "") {


                var formData = {
                    id: id,
                };
                $.ajax({
                    type: "POST",
                    data: formData,
                    dataType: "json",
                    url: "{{url('quiz/remove-image-ajax')}}",
                    success: function (data) {

                    },
                    error: function (data) {
                        console.log("Error:", data);
                    },
                });
            }
        });


        $(document).on("click", "#create-option", function (event) {
            $('#multiple-options').html('');
            var number_of_option = $('#number_of_option').val();
            for (var i = 1; i <= number_of_option; i++) {
                var appendRow = '';
                appendRow += "<div class='row  mt-25'>";
                appendRow += "<div class='col-lg-7'>";
                appendRow += "<div class='input-effect'>"
                appendRow += "<input class='primary_input_field name' placeholder='option " + i + "' type='text' name='option[]' autocomplete='off'>";
                appendRow += "</div>";
                appendRow += "</div>";
                appendRow += "<div class='col-lg-3'>";
                appendRow += "<div class='primary_input single-uploader'>";
                appendRow += "<div class='primary_file_uploader' data-bs-toggle='infixUploader' data-multiple='false' data-type='image' data-name='option_image[" + i + "]'>";
                appendRow += "<input class='primary-input file_amount' type='text' id='file_option_image_" + i + "' placeholder='Browse' readonly>";
                appendRow += "<button type='button'><label class='primary-btn small fix-gr-bg' for='file_option_image_" + i + "'>Browse</label><input type='hidden' class='selected_files' value=''></button>";
                appendRow += "</div>";
                appendRow += "<div class='product_image_all_div'></div>";
                appendRow += "</div>";
                appendRow += "</div>";
                appendRow += "<div class='col-lg-2 mt-15'>";
                appendRow += "<label class='primary_checkbox d-flex mr-12' for='option_check_" + i + "'>";
                appendRow += "<input type='checkbox' id='option_check_" + i + "' name='option_check_" + i + "' value='1'> <span class='checkmark'></span>";
                appendRow += "</label>";
                appendRow += "</div>";
                appendRow += "</div>";
                $("#multiple-options").append(appendRow);
            }
        });

        $(document).on("click", "#create-sorting-option", function (event) {
            $('#question_bank div.sorting-options').html('');

            var number_of_option = $('#number_of_sorting_option').val();
            for (var i = 0; i < number_of_option; i++) {
                var appendRow = '';
                appendRow += "<div class='row  mt-25' id='option-" + i + "'' data-id='" + i + "'>";
                appendRow += "<div class='col-lg-10 optionTitle'>";
                appendRow += "<div class='input-effect'>"
                appendRow += "<input class='primary_input_field name' type='text' name='sorting_option[]' autocomplete='off' required>";
                appendRow += "</div>";
                appendRow += "</div>";
                appendRow += "<div class='col-lg-2 mt-15 '>";

                appendRow += "            <span class='drag-handle' style='cursor: move;'>&#9776;</span>";

                appendRow += "</div>";
                appendRow += "</div>";

                $(".sorting-options").append(appendRow);
                console.log('here')

                getSortingOrders();

            }
        });


        function getSortingOrders() {
            $('.sorting-options').sortable({
                handle: ".drag-handle",
            });
        }

        getSortingOrders();
        // Cloze option
        $(document).on("click", "#create-cloze-option", function (event) {
            // Clear the options container before adding new rows
            $('.cloze-options').empty();

            // Get the number of rows from input
            var number_of_option = $('#number_of_cloze_option').val();

            // Initialize the table structure outside the loop
            var appendTable = '<table class="QA_table table table-borderless mt-3">';
            appendTable += '<thead>';
            appendTable += '<tr>';
            appendTable += '<th class="p-2">{{trans("quiz.Number")}}</th>';
            appendTable += '<th class="p-2 w-75">{{trans("quiz.Options")}}</th>';
            appendTable += '<th class="p-2">{{trans("common.Action")}}</th>';
            appendTable += '</tr>';
            appendTable += '</thead>';
            appendTable += '<tbody>'; // Start tbody

            // Generate rows based on the number_of_option input
            for (var i = 0; i < number_of_option; i++) {
                appendTable += generateOptionRow(i + 1);  // Generate each row
            }

            appendTable += '</tbody>'; // End tbody
            appendTable += '</table>';  // End table

            // Append the whole table at once
            $('.cloze-options').append(appendTable);

            console.log('Cloze options with multiple choices created');
        });

        // Function to generate a row with multiple options (default 4 options per row)
        function generateOptionRow(rowNumber) {
            var defaultOptions = 4;  // Default number of options per row
            var rowHTML = "<tr class='option-row'>";
            rowHTML += "<td class='p-2'>" + rowNumber + "</td>";
            rowHTML += "<td class='p-2'>";

            // Create a div that contains multiple options (default 4 options)
            rowHTML += "<div class='options-container' data-row-number='" + rowNumber + "'>";

            for (var i = 0; i < defaultOptions; i++) {
                rowHTML += generateOptionInput(rowNumber, i + 1);
            }

            rowHTML += "</div>";  // End options-container div
            rowHTML += "</td>";

            rowHTML += "<td class='p-2'>";
            rowHTML += "<div class='d-flex'><button type='button' class='primary-btn small fix-gr-bg add-option-btn'><i class='ti ti-plus m-0 p-0'></i></button>";
            rowHTML += "<button type='button' class='primary-btn small fix-gr-bg remove-option-btn ms-2'><i class='ti ti-trash  m-0 p-0'></i></button></div>";
            rowHTML += "</td>";
            rowHTML += "</tr>";

            return rowHTML;
        }

        // Function to generate an individual option input
        function generateOptionInput(rowNumber, optionNumber) {
            return "<div class='input-effect mb-2 d-flex align-items-center'>" +
                "<input class='primary_input_field name' placeholder='Option " + optionNumber + "' type='text' name='cloze_option[" + rowNumber + "][]' autocomplete='off' required>" +
                "<label class='primary_checkbox d-flex ms-3'>" +
                "<input name='cloze_answer[" + rowNumber + "]' value='" + optionNumber + "' type='radio'>" +
                "<span class='checkmark'></span>" +
                "</label>" +
                "</div>";
        }


        // Add new option input within a specific row
        $(document).on("click", ".add-option-btn", function () {
            // Get the parent row and number of current options in the row
            var optionsContainer = $(this).closest('tr').find('.options-container');
            var rowNumber = optionsContainer.data('row-number');
            var optionCount = optionsContainer.children().length;

            // Append a new option input to the current row
            optionsContainer.append(generateOptionInput(rowNumber, optionCount + 1));
        });

        // Remove the last option input within a specific row
        $(document).on("click", ".remove-option-btn", function () {
            var optionsContainer = $(this).closest('tr').find('.options-container');
            var optionCount = optionsContainer.children().length;

            // Ensure there's at least 1 option left in the row
            if (optionCount > 1) {
                optionsContainer.children().last().remove();
            } else {
                toastr.error("{{__('quiz.At least one option is required')}}")
            }
        });


        // Generate puzzle question input fields
        $(document).on("click", "#create-puzzle-qus-option", function (event) {
            let qusItem = $('#puzzle_number_of_qus').val();
            let qusRow = '';

            // Translation strings for question
            let enterQuestion = "{{ trans('quiz.Enter Question') }}";

            for (let i = 0; i < qusItem; i++) {
                qusRow += "<div class='row optionType mb-3' data-type='qus' data-index='" + i + "'>";
                qusRow += "<div class='col-lg-12 optionTitle'>";
                qusRow += "<div class='input-group'>";
                qusRow += "<input class='form-control option_title' type='text' name='puzzle_qus[" + i + "]' placeholder='" + enterQuestion + " " + (i + 1) + "' required>";
                qusRow += "</div>";
                qusRow += "</div>";
                qusRow += "</div>";
            }

            $('#puzzleQus').html(qusRow);
            combineQusAndAns();
        });

        // Generate puzzle answer input fields
        $(document).on("click", "#create-puzzle-ans-option", function (event) {
            let ansItem = $('#puzzle_number_of_ans').val();
            let ansRow = '';

            // Translation strings for answer
            let enterAnswer = "{{ trans('quiz.Enter Answer') }}";

            for (let i = 0; i < ansItem; i++) {
                ansRow += "<div class='row optionType mb-3' data-type='ans' data-index='" + i + "'>";
                ansRow += "<div class='col-lg-12 optionTitle'>";
                ansRow += "<div class='input-group'>";
                ansRow += "<input class='form-control ans_title' type='text' name='puzzle_ans[" + i + "]' placeholder='" + enterAnswer + " " + (i + 1) + "'>";
                ansRow += "</div>";
                ansRow += "</div>";
                ansRow += "</div>";
            }

            $('#puzzleAns').html(ansRow);
            combineQusAndAns();
        });


        function combineQusAndAns() {
            let qusElements = $('#puzzleQus .optionType');
            let ansElements = $('#puzzleAns .optionType');
            let combineRow = `
                <div class="col-12">
                    <h4>{{__('quiz.Correct option')}}</h4>
                </div>
            `;

            if (qusElements.length > 0 && ansElements.length > 0) {
                for (let i = 0; i < qusElements.length; i++) {
                    let qus = "{{trans('quiz.Question')}} " + (i + 1) + " :";

                    combineRow += "<div class='row mb-5'>";
                    combineRow += "<div class='col-lg-6 '>";
                    combineRow += "<label> " + qus + "</label>";
                    combineRow += "</div>";
                    combineRow += "<div class='col-lg-6 d-flex flex-column gap-3'>";

                    // Create checkboxes for all available answers
                    for (let j = 0; j < ansElements.length; j++) {
                        let ans = "{{__('quiz.Answer')}} " + (j + 1);

                        // Create unique IDs for each checkbox
                        let checkboxId = `questionReview_${i}_${j}`;

                        combineRow += "<label class='primary_checkbox d-flex text-nowrap mr-12'>";
                        combineRow += `<input name='question_review[${i}][${j}]' value='${j}' id='${checkboxId}' type='checkbox'>`;
                        combineRow += `<span class='checkmark'></span>`;
                        combineRow += `<span class='ms-2'>${ans}</span>`;
                        combineRow += "</label>";
                    }

                    combineRow += "</div>";
                    combineRow += "</div>";
                }
                $('#puzzleCombine').html(combineRow);
            }
        }


        $(document).on("change", ".option_title", function () {
            const index = $(this).closest('.optionType').data('index');
            const newValue = $(this).val();
            console.log(`Question ${index + 1} changed to: "${newValue}"`);
            // Add any additional logic you want to perform on change
        });

        // Handle change events for answer text inputs
        $(document).on("change", ".ans_title", function () {
            const index = $(this).closest('.optionType').data('index');
            const newValue = $(this).val();
            console.log(`Answer ${index + 1} changed to: "${newValue}"`);
            // Add any additional logic you want to perform on change
        });

        (function () {
            // ── Symbol data ──
            var symbolsByCategory = {
                numbers: {
                    label: 'أرقام',
                    icon: '١٢٣',
                    symbols: ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩', '٫', '٬', '٪']
                },
                operators: {
                    label: 'عمليات',
                    icon: '±',
                    symbols: ['±', '×', '÷', '∓', '⋅', '∗', '⊕', '⊗', '⊙', '∘', '∙', '∧', '∨', '∩', '∪']
                },
                relations: {
                    label: 'علاقات',
                    icon: '=',
                    symbols: ['=', '≠', '≈', '≡', '≅', '∼', '∝', '≪', '≫', '≐', '≃', '≍']
                },
                inequalities: {
                    label: 'متباينات',
                    icon: '≤',
                    symbols: ['<', '>', '≤', '≥', '≦', '≧', '≨', '≩', '≮', '≯', '≰', '≱']
                },
                greek: {
                    label: 'يوناني',
                    icon: 'π',
                    symbols: ['α', 'β', 'γ', 'δ', 'ε', 'ζ', 'η', 'θ', 'ι', 'κ', 'λ', 'μ', 'ν', 'ξ', 'ο', 'π', 'ρ', 'σ', 'τ', 'υ', 'φ', 'χ', 'ψ', 'ω', 'Γ', 'Δ', 'Θ', 'Λ', 'Ξ', 'Π', 'Σ', 'Φ', 'Ψ', 'Ω']
                },
                calculus: {
                    label: 'تفاضل',
                    icon: '∫',
                    symbols: ['∫', '∬', '∭', '∮', '∂', '∇', '∆', '∞', '∑', '∏', '∐', '′', '″', '‴']
                },
                roots: {
                    label: 'جذور',
                    icon: '√',
                    symbols: ['√', '∛', '∜', '√()', '∛()', '^', '²', '³', 'ⁿ', 'ˣ']
                },
                arrows: {
                    label: 'أسهم',
                    icon: '→',
                    symbols: ['→', '←', '↑', '↓', '↔', '↕', '⇒', '⇐', '⇔', '⟹', '⟸', '⟺', '↦', '↤']
                },
                sets: {
                    label: 'مجموعات',
                    icon: '∈',
                    symbols: ['∈', '∉', '∋', '∌', '⊂', '⊃', '⊆', '⊇', '∪', '∩', '∅', 'ℕ', 'ℤ', 'ℚ', 'ℝ', 'ℂ']
                },
                geometry: {
                    label: 'هندسة',
                    icon: '∠',
                    symbols: ['∠', '∡', '∟', '⊥', '∥', '∦', '△', '□', '○', '°', '′', '″', 'π', 'ℓ']
                },
                brackets: {
                    label: 'أقواس',
                    icon: '()',
                    symbols: ['()', '[]', '{}', '⟨⟩', '⌊⌋', '⌈⌉', '│', '‖', '‹›']
                },
                trigonometry: {
                    label: 'مثلثات',
                    icon: 'π',
                    symbols: ['sin', 'cos', 'tan', 'csc', 'sec', 'cot', 'arcsin', 'arccos', 'arctan', 'sinh', 'cosh', 'tanh', '°', '′', '″', 'π', 'rad']
                },
                logic: {
                    label: 'منطق',
                    icon: '∴',
                    symbols: ['∀', '∃', '∄', '∴', '∵', '¬', '∧', '∨', '⊕', '⊗', '⇒', '⇔', '⊤', '⊥', '⊢', '⊨', '□', '◇']
                },
                statistics: {
                    label: 'إحصاء',
                    icon: 'μ',
                    symbols: ['μ', 'σ', 'Σ', 'Π', 'Δ', '∂', '∞', 'ℕ', 'ℝ', 'ℂ', '%', '‰', '°', '∠', '∡', '⊥', '∥']
                }
            };
            var categoryOrder = ['numbers', 'operators', 'relations', 'inequalities', 'greek', 'calculus', 'trigonometry', 'logic', 'statistics', 'roots', 'arrows', 'sets', 'geometry', 'brackets'];

            // ── KaTeX template definitions ──
            var katexTemplates = [
                // ── Basic fractions ──
                { label: '\\frac{a}{b}', latex: '\\frac{a}{b}' },
                { label: '\\frac{x}{y}', latex: '\\frac{x}{y}' },
                { label: '\\frac{dy}{dx}', latex: '\\frac{dy}{dx}' },
                { label: '\\tfrac{a}{b}', latex: '\\tfrac{a}{b}' },
                { label: '\\dfrac{a}{b}', latex: '\\dfrac{a}{b}' },
                { label: '\\binom{n}{k}', latex: '\\binom{n}{k}' },

                // ── Powers and indices ──
                { label: 'x^{2}', latex: 'x^{2}' },
                { label: 'x^{n}', latex: 'x^{n}' },
                { label: 'x_{n}', latex: 'x_{n}' },
                { label: 'x^{n}_{m}', latex: 'x^{n}_{m}' },
                { label: 'e^{i\\pi}+1=0', latex: 'e^{i\\pi}+1=0' },

                // ── Roots ──
                { label: '\\sqrt{x}', latex: '\\sqrt{x}' },
                { label: '\\sqrt[n]{x}', latex: '\\sqrt[n]{x}' },
                { label: '\\sqrt{1-x^{2}}', latex: '\\sqrt{1-x^{2}}' },
                { label: '\\sqrt[3]{x}', latex: '\\sqrt[3]{x}' },

                // ── Quadratic formula ──
                { label: 'ax^{2}+bx+c=0', latex: 'ax^{2}+bx+c=0' },
                { label: 'x=\\frac{-b\\pm\\sqrt{b^{2}-4ac}}{2a}', latex: 'x=\\frac{-b\\pm\\sqrt{b^{2}-4ac}}{2a}' },
                { label: 'a^{2}+b^{2}=c^{2}', latex: 'a^{2}+b^{2}=c^{2}' },
                { label: '(a+b)^{2}=a^{2}+2ab+b^{2}', latex: '(a+b)^{2}=a^{2}+2ab+b^{2}' },

                // ── Calculus ──
                { label: '\\int', latex: '\\int' },
                { label: '\\int_{a}^{b}', latex: '\\int_{a}^{b}' },
                { label: '\\int_{a}^{\\infty}', latex: '\\int_{a}^{\\infty}' },
                { label: '\\int_{0}^{\\pi}\\sin x\\,dx', latex: '\\int_{0}^{\\pi}\\sin x\\,dx' },
                { label: '\\iint', latex: '\\iint' },
                { label: '\\iint_{D}', latex: '\\iint_{D}' },
                { label: '\\iiint', latex: '\\iiint' },
                { label: '\\oint', latex: '\\oint' },
                { label: '\\partial', latex: '\\partial' },
                { label: '\\frac{\\partial f}{\\partial x}', latex: '\\frac{\\partial f}{\\partial x}' },
                { label: '\\nabla', latex: '\\nabla' },
                { label: '\\nabla^{2}', latex: '\\nabla^{2}' },

                // ── Limits ──
                { label: '\\lim_{x \\to a}', latex: '\\lim_{x \\to a}' },
                { label: '\\lim_{x \\to \\infty}', latex: '\\lim_{x \\to \\infty}' },
                { label: '\\lim_{x \\to 0}\\frac{\\sin x}{x}=1', latex: '\\lim_{x \\to 0}\\frac{\\sin x}{x}=1' },
                { label: '\\lim_{n \\to \\infty}\\frac{1}{n}=0', latex: '\\lim_{n \\to \\infty}\\frac{1}{n}=0' },

                // ── Sums and products ──
                { label: '\\sum', latex: '\\sum' },
                { label: '\\sum_{i=1}^{n}', latex: '\\sum_{i=1}^{n}' },
                { label: '\\sum_{i=1}^{\\infty}', latex: '\\sum_{i=1}^{\\infty}' },
                { label: '\\prod', latex: '\\prod' },
                { label: '\\prod_{i=1}^{n}', latex: '\\prod_{i=1}^{n}' },
                { label: '\\coprod', latex: '\\coprod' },

                // ── Trigonometry ──
                { label: '\\sin x', latex: '\\sin x' },
                { label: '\\cos x', latex: '\\cos x' },
                { label: '\\tan x', latex: '\\tan x' },
                { label: '\\csc x', latex: '\\csc x' },
                { label: '\\sec x', latex: '\\sec x' },
                { label: '\\cot x', latex: '\\cot x' },
                { label: '\\sin^{2}x+\\cos^{2}x=1', latex: '\\sin^{2}x+\\cos^{2}x=1' },
                { label: '\\sin(a\\pm b)', latex: '\\sin(a\\pm b)' },
                { label: '\\cos(a\\pm b)', latex: '\\cos(a\\pm b)' },
                { label: '\\arcsin x', latex: '\\arcsin x' },
                { label: '\\arccos x', latex: '\\arccos x' },
                { label: '\\arctan x', latex: '\\arctan x' },

                // ── Logarithms ──
                { label: '\\log x', latex: '\\log x' },
                { label: '\\ln x', latex: '\\ln x' },
                { label: '\\log_{a} x', latex: '\\log_{a} x' },
                { label: '\\ln(ab)=\\ln a+\\ln b', latex: '\\ln(ab)=\\ln a+\\ln b' },

                // ── Matrices (2x2) ──
                { label: '\\begin{pmatrix}a&b\\\\c&d\\end{pmatrix}', latex: '\\begin{pmatrix} a & b \\\\ c & d \\end{pmatrix}' },
                { label: '\\begin{bmatrix}a&b\\\\c&d\\end{bmatrix}', latex: '\\begin{bmatrix} a & b \\\\ c & d \\end{bmatrix}' },
                { label: '\\begin{vmatrix}a&b\\\\c&d\\end{vmatrix}', latex: '\\begin{vmatrix} a & b \\\\ c & d \\end{vmatrix}' },
                { label: '\\begin{Vmatrix}a&b\\\\c&d\\end{Vmatrix}', latex: '\\begin{Vmatrix} a & b \\\\ c & d \\end{Vmatrix}' },
                { label: '\\begin{Bmatrix}a&b\\\\c&d\\end{Bmatrix}', latex: '\\begin{Bmatrix} a & b \\\\ c & d \\end{Bmatrix}' },
                { label: '\\begin{smallmatrix}a&b\\\\c&d\\end{smallmatrix}', latex: '\\begin{smallmatrix}a&b\\\\c&d\\end{smallmatrix}' },

                // ── Matrices (3x3) ──
                { label: '3\\times3\\,pmatrix', latex: '\\begin{pmatrix} a & b & c \\\\ d & e & f \\\\ g & h & i \\end{pmatrix}' },
                { label: '3\\times3\\,bmatrix', latex: '\\begin{bmatrix} a & b & c \\\\ d & e & f \\\\ g & h & i \\end{bmatrix}' },
                { label: 'Identity\\,I_{3}', latex: '\\begin{pmatrix} 1 & 0 & 0 \\\\ 0 & 1 & 0 \\\\ 0 & 0 & 1 \\end{pmatrix}' },

                // ── Cases and piecewise ──
                { label: '\\begin{cases}x&y\\\\a&b\\end{cases}', latex: '\\begin{cases} x & y \\\\ a & b \\end{cases}' },
                { label: 'Piecewise f(x)=', latex: 'f(x)=\\begin{cases} x^{2} & x\\ge0 \\\\ -x & x<0 \\end{cases}' },
                { label: 'Three cases', latex: '\\begin{cases} a & b \\\\ c & d \\\\ e & f \\end{cases}' },

                // ── Multi-line ──
                { label: '\\begin{aligned}', latex: '\\begin{aligned} a &= b + c \\\\ d &= e + f \\end{aligned}' },
                { label: '\\begin{gathered}', latex: '\\begin{gathered} x^{2} + y^{2} = 1 \\\\ a + b = c \\end{gathered}' },
                { label: '\\begin{align}', latex: '\\begin{align} x &= 1 & y &= 2 \\\\ a &= 3 & b &= 4 \\end{align}' },
                { label: '\\begin{split}', latex: '\\begin{split} A & = B + C \\\\ & = D + E \\end{split}' },

                // ── System of equations ──
                { label: 'System 2 eq', latex: '\\begin{cases} 2x + 3y = 5 \\\\ x - y = 1 \\end{cases}' },
                { label: 'System 3 eq', latex: '\\begin{cases} x + y + z = 1 \\\\ 2x - y + z = 2 \\\\ x + 2y - z = 3 \\end{cases}' },

                // ── Vectors and geometry ──
                { label: '\\overrightarrow{AB}', latex: '\\overrightarrow{AB}' },
                { label: '\\vec{v}', latex: '\\vec{v}' },
                { label: '\\vec{F}=m\\vec{a}', latex: '\\vec{F}=m\\vec{a}' },
                { label: '\\hat{x}', latex: '\\hat{x}' },
                { label: '\\bar{x}', latex: '\\bar{x}' },
                { label: '\\dot{x}', latex: '\\dot{x}' },
                { label: '\\ddot{x}', latex: '\\ddot{x}' },
                { label: '\\tilde{x}', latex: '\\tilde{x}' },
                { label: '\\widehat{ABC}', latex: '\\widehat{ABC}' },
                { label: '\\angle', latex: '\\angle' },
                { label: '\\triangle', latex: '\\triangle' },
                { label: '\\perp', latex: '\\perp' },
                { label: '\\parallel', latex: '\\parallel' },
                { label: '\\cong', latex: '\\cong' },
                { label: '\\sim', latex: '\\sim' },
                { label: '\\propto', latex: '\\propto' },

                // ── Sets ──
                { label: '\\subset', latex: '\\subset' },
                { label: '\\supset', latex: '\\supset' },
                { label: '\\subseteq', latex: '\\subseteq' },
                { label: '\\supseteq', latex: '\\supseteq' },
                { label: '\\in', latex: '\\in' },
                { label: '\\notin', latex: '\\notin' },
                { label: '\\cup', latex: '\\cup' },
                { label: '\\cap', latex: '\\cap' },
                { label: '\\setminus', latex: '\\setminus' },
                { label: '\\emptyset', latex: '\\emptyset' },
                { label: '\\mathbb{N}', latex: '\\mathbb{N}' },
                { label: '\\mathbb{Z}', latex: '\\mathbb{Z}' },
                { label: '\\mathbb{Q}', latex: '\\mathbb{Q}' },
                { label: '\\mathbb{R}', latex: '\\mathbb{R}' },
                { label: '\\mathbb{C}', latex: '\\mathbb{C}' },

                // ── Calculus notation ──
                { label: '\\to', latex: '\\to' },
                { label: '\\rightarrow', latex: '\\rightarrow' },
                { label: '\\Rightarrow', latex: '\\Rightarrow' },
                { label: '\\leftrightarrow', latex: '\\leftrightarrow' },
                { label: '\\stackrel{}{}', latex: '\\stackrel{a}{b}' },
                { label: '\\xrightarrow{abc}', latex: '\\xrightarrow{abc}' },

                // ── Greek letters ──
                { label: '\\alpha', latex: '\\alpha' },
                { label: '\\beta', latex: '\\beta' },
                { label: '\\gamma', latex: '\\gamma' },
                { label: '\\delta', latex: '\\delta' },
                { label: '\\epsilon', latex: '\\epsilon' },
                { label: '\\varepsilon', latex: '\\varepsilon' },
                { label: '\\zeta', latex: '\\zeta' },
                { label: '\\eta', latex: '\\eta' },
                { label: '\\theta', latex: '\\theta' },
                { label: '\\vartheta', latex: '\\vartheta' },
                { label: '\\iota', latex: '\\iota' },
                { label: '\\kappa', latex: '\\kappa' },
                { label: '\\lambda', latex: '\\lambda' },
                { label: '\\mu', latex: '\\mu' },
                { label: '\\nu', latex: '\\nu' },
                { label: '\\xi', latex: '\\xi' },
                { label: '\\pi', latex: '\\pi' },
                { label: '\\varpi', latex: '\\varpi' },
                { label: '\\rho', latex: '\\rho' },
                { label: '\\sigma', latex: '\\sigma' },
                { label: '\\tau', latex: '\\tau' },
                { label: '\\upsilon', latex: '\\upsilon' },
                { label: '\\phi', latex: '\\phi' },
                { label: '\\varphi', latex: '\\varphi' },
                { label: '\\chi', latex: '\\chi' },
                { label: '\\psi', latex: '\\psi' },
                { label: '\\omega', latex: '\\omega' },
                { label: '\\Gamma', latex: '\\Gamma' },
                { label: '\\Delta', latex: '\\Delta' },
                { label: '\\Theta', latex: '\\Theta' },
                { label: '\\Lambda', latex: '\\Lambda' },
                { label: '\\Xi', latex: '\\Xi' },
                { label: '\\Pi', latex: '\\Pi' },
                { label: '\\Sigma', latex: '\\Sigma' },
                { label: '\\Phi', latex: '\\Phi' },
                { label: '\\Psi', latex: '\\Psi' },
                { label: '\\Omega', latex: '\\Omega' },

                // ── Relations ──
                { label: '\\neq', latex: '\\neq' },
                { label: '\\approx', latex: '\\approx' },
                { label: '\\equiv', latex: '\\equiv' },
                { label: '\\leq', latex: '\\leq' },
                { label: '\\geq', latex: '\\geq' },
                { label: '\\ll', latex: '\\ll' },
                { label: '\\gg', latex: '\\gg' },
                { label: '\\doteq', latex: '\\doteq' },
                { label: '\\simeq', latex: '\\simeq' },
                { label: '\\asymp', latex: '\\asymp' },

                // ── Logic ──
                { label: '\\forall', latex: '\\forall' },
                { label: '\\exists', latex: '\\exists' },
                { label: '\\nexists', latex: '\\nexists' },
                { label: '\\therefore', latex: '\\therefore' },
                { label: '\\because', latex: '\\because' },
                { label: '\\land', latex: '\\land' },
                { label: '\\lor', latex: '\\lor' },
                { label: '\\lnot', latex: '\\lnot' },
                { label: '\\vdash', latex: '\\vdash' },
                { label: '\\models', latex: '\\models' },

                // ── Probability and statistics ──
                { label: 'P(A\\mid B)', latex: 'P(A\\mid B)' },
                { label: '\\mathbb{E}[X]', latex: '\\mathbb{E}[X]' },
                { label: '\\mathrm{Var}(X)', latex: '\\mathrm{Var}(X)' },
                { label: '\\sigma^{2}', latex: '\\sigma^{2}' },
                { label: '\\bar{x}=\\frac{1}{n}\\sum', latex: '\\bar{x}=\\frac{1}{n}\\sum_{i=1}^{n} x_{i}' },
                { label: '\\binom{n}{k}p^{k}q^{n-k}', latex: '\\binom{n}{k}p^{k}q^{n-k}' },
                { label: 'N(\\mu,\\sigma^{2})', latex: 'N(\\mu,\\sigma^{2})' },
                { label: '\\chi^{2}', latex: '\\chi^{2}' },

                // ── Chemistry ──
                { label: '\\mathrm{H_{2}O}', latex: '\\mathrm{H_{2}O}' },
                { label: '\\mathrm{CO_{2}}', latex: '\\mathrm{CO_{2}}' },
                { label: '\\mathrm{CH_{4}}', latex: '\\mathrm{CH_{4}}' },
                { label: '\\mathrm{C_{6}H_{12}O_{6}}', latex: '\\mathrm{C_{6}H_{12}O_{6}}' },
                { label: '\\mathrm{HCl+NaOH}', latex: '\\mathrm{HCl + NaOH \\to NaCl + H_{2}O}' },
                { label: '\\mathrm{CaCO_{3}}', latex: '\\mathrm{CaCO_{3}}' },
                { label: '\\mathrm{H_{2}SO_{4}}', latex: '\\mathrm{H_{2}SO_{4}}' },

                // ── Physics ──
                { label: 'E=mc^{2}', latex: 'E=mc^{2}' },
                { label: 'F=ma', latex: 'F=ma' },
                { label: 'F=G\\frac{m_{1}m_{2}}{r^{2}}', latex: 'F=G\\frac{m_{1}m_{2}}{r^{2}}' },
                { label: 'E=h\\nu', latex: 'E=h\\nu' },
                { label: 'pv=nRT', latex: 'pv=nRT' },
                { label: '\\Delta E = h\\nu', latex: '\\Delta E = h\\nu' },

                // ── Calculus derivatives ──
                { label: "f'(x)", latex: "f'(x)" },
                { label: "f''(x)", latex: "f''(x)" },
                { label: '\\frac{d}{dx}f(x)', latex: '\\frac{d}{dx}f(x)' },
                { label: '\\frac{d^{2}}{dx^{2}}', latex: '\\frac{d^{2}}{dx^{2}}' },

                // ── Brackets ──
                { label: '\\left(\\right)', latex: '\\left( \\right)' },
                { label: '\\left[\\right]', latex: '\\left[ \\right]' },
                { label: '\\langle\\rangle', latex: '\\langle \\rangle' },
                { label: '\\lvert x\\rvert', latex: '\\lvert x\\rvert' },
                { label: '\\|x\\|', latex: '\\|x\\|' },
                { label: '\\lfloor x\\rfloor', latex: '\\lfloor x\\rfloor' },
                { label: '\\lceil x\\rceil', latex: '\\lceil x\\rceil' },

                // ── Spacing ──
                { label: 'a\\,b', latex: 'a\\,b' },
                { label: 'a\\;b', latex: 'a\\;b' },
                { label: 'a\\quad b', latex: 'a\\quad b' },
                { label: 'a\\qquad b', latex: 'a\\qquad b' },
                { label: '\\text{text}', latex: '\\text{text}' },
                { label: '\\displaystyle', latex: '\\displaystyle\\int_{0}^{1} f(x)\\,dx' },
                { label: '\\underset{x\\to a}{\\lim}', latex: '\\underset{x\\to a}{\\lim}' },
                { label: '\\overset{+}{-}', latex: '\\overset{+}{-}' },
            ];

            // ── State ──
            var activeInput = null;
            var activeEditable = null;
            var activeSummernote = null;
            var activeCategory = categoryOrder[0];
            var recentSymbols = [];
            var maxRecent = 10;
            var isDragging = false;
            var dragOffsetX = 0, dragOffsetY = 0;
            var searchQuery = '';

            // ── DOM refs ──
            var $toggle = $('#mathKeyboardToggle');
            var $panel = $('#mathKeyboardPanel');
            var $tabs = $('#mathKeyboardTabs');
            var $grid = $('#mathKeyboardGrid');
            var $search = $('#mathKeyboardSearch');
            var $close = $('#mathKeyboardClose');
            var $recent = $('#mathKeyboardRecent');
            var $recentList = $('#mathKeyboardRecentList');
            var $activeLabel = $('#mathActiveLabel');
            var $activeDot = $('#mathActiveDot');
            var $dragHandle = $('#mathKeyboardDragHandle');
            var $clearBtn = $('#mathClearBtn');

            // ── Writer events ──
            var $customLatex = $('#eqLatexInput');
            var $customPreview = $('#eqPreview');
            var $customInsertBtn = $('#eqInsertBtn');

            function convertEquationDigits(value, style) {
                var arabicDigits = '٠١٢٣٤٥٦٧٨٩';
                var latinDigits = '0123456789';
                var sourceDigits = style === 'arabic' ? latinDigits : arabicDigits;
                var targetDigits = style === 'arabic' ? arabicDigits : latinDigits;
                var pattern = style === 'arabic' ? /[0-9]/g : /[٠-٩]/g;

                return String(value || '').replace(pattern, function (digit) {
                    return targetDigits.charAt(sourceDigits.indexOf(digit));
                });
            }

            // This project uses a KaTeX version that only parses Latin numerals.
            // Render a normalized copy, then localize text nodes without changing the saved LaTeX.
            function renderLatexWithLocalizedDigits(latex, options) {
                var source = String(latex || '');
                var usesArabicDigits = /[٠-٩]/.test(source);
                var usesArabicDecimal = source.indexOf('٫') !== -1;
                var usesArabicThousands = source.indexOf('٬') !== -1;
                var usesArabicPercent = source.indexOf('٪') !== -1;
                var normalized = convertEquationDigits(source, 'latin')
                    .replace(/٫/g, '.')
                    .replace(/٬/g, ',')
                    .replace(/٪/g, '\\%');
                var html = katex.renderToString(normalized, options);

                if (!usesArabicDigits && !usesArabicDecimal && !usesArabicThousands && !usesArabicPercent) {
                    return html;
                }

                return html.replace(/>([^<]*)</g, function (match, text) {
                    var localized = usesArabicDigits ? convertEquationDigits(text, 'arabic') : text;
                    if (usesArabicDecimal) localized = localized.replace(/\./g, '٫');
                    if (usesArabicThousands) localized = localized.replace(/,/g, '٬');
                    if (usesArabicPercent) localized = localized.replace(/%/g, '٪');
                    return '>' + localized + '<';
                });
            }

            $('.math-digit-convert-btn').on('click', function () {
                var input = $customLatex[0];
                if (!input) return;

                var selectionStart = input.selectionStart || 0;
                var selectionEnd = input.selectionEnd || 0;
                input.value = convertEquationDigits(input.value, $(this).data('digit-style'));
                input.focus();
                if (input.setSelectionRange) input.setSelectionRange(selectionStart, selectionEnd);
                $customLatex.trigger('input').trigger('change');
            });

            function autoResizeTextarea() {
                var el = $customLatex[0];
                if (!el) return;
                el.style.height = 'auto';
                var newH = Math.min(Math.max(el.scrollHeight, 80), 400);
                el.style.height = newH + 'px';
            }

            $customLatex.on('input', function () {
                autoResizeTextarea();
                var val = $(this).val().trim();
                if (!val) { $customPreview.html('').removeClass('has-error'); $customInsertBtn.prop('disabled', true); return; }
                $customInsertBtn.prop('disabled', false);
                if (typeof katex !== 'undefined') {
                    try {
                        var rendered = renderLatexWithLocalizedDigits(val, { displayMode: false, throwOnError: true });
                        $customPreview.html(rendered).removeClass('has-error');
                    } catch(e) {
                        $customPreview.text(e.message).addClass('has-error');
                    }
                } else {
                    $customPreview.html('<code>' + val.replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</code>').removeClass('has-error');
                }
            });

            // Initial auto-resize
            setTimeout(autoResizeTextarea, 50);

            $customInsertBtn.on('click', function () {
                var val = $customLatex.val().trim();
                if (!val) return;
                var $fakeBtn = $('<button>').data('katex', val);
                handleKatexInsert($fakeBtn);
            });

            $customLatex.on('keydown', function (e) {
                if (e.key === 'Enter' && e.ctrlKey) { e.preventDefault(); $customInsertBtn.click(); return; }
                // Shortcuts: Ctrl+F=frac, Ctrl+R=sqrt, Ctrl+P=superscript, Ctrl+Shift+I=subscript
                if (e.ctrlKey && !e.shiftKey && e.key === 'f') { e.preventDefault(); insertTemplateSmart($customLatex, '\\frac{}{}'); return; }
                if (e.ctrlKey && !e.shiftKey && e.key === 'r') { e.preventDefault(); insertTemplateSmart($customLatex, '\\sqrt{}'); return; }
                if (e.ctrlKey && e.shiftKey && e.key === 'P') { e.preventDefault(); insertTemplateSmart($customLatex, '^{}'); return; }
                if (e.ctrlKey && e.shiftKey && e.key === 'I') { e.preventDefault(); insertTemplateSmart($customLatex, '_{}'); return; }
            });

            $clearBtn.on('click', function () {
                $customLatex.val('').trigger('input').focus();
            });

            $('#mathResetPanelBtn').on('click', function () {
                $panel.css({ width: '', height: '', left: '', right: '', top: '', bottom: '' });
                try {
                    ['math_panel_width', 'math_panel_height', 'math_panel_left', 'math_panel_top', 'math_panel_right', 'math_panel_bottom'].forEach(function (key) {
                        localStorage.removeItem(key);
                    });
                } catch(e) {}
                toastr.info('تمت استعادة حجم نافذة المعادلات');
            });

            // ── Build tabs ──
            categoryOrder.forEach(function (key) {
                var cat = symbolsByCategory[key];
                $tabs.append('<button type="button" class="math-tab-btn" data-category="' + key + '">' + cat.label + '</button>');
            });
            $tabs.find('.math-tab-btn:first').addClass('active');
            $tabs.append('<button type="button" class="math-tab-btn" data-category="templates">قوالب</button>');

            // ── Render helpers ──
            function renderSymbols(symbols) {
                $grid.empty();
                if (!symbols || symbols.length === 0) {
                    $grid.append('<div class="no-results">لا توجد نتائج</div>');
                    return;
                }
                symbols.forEach(function (symbol) {
                    $grid.append('<button type="button" class="math-key-btn" data-symbol="' + symbol.replace(/"/g, '&quot;') + '">' + symbol + '</button>');
                });
            }

            // ── Writer (already in HTML) ──

            function renderKatexTemplates() {
                $grid.empty();

                $customLatex = $('#eqLatexInput');
                $customPreview = $('#eqPreview');
                $customInsertBtn = $('#eqInsertBtn');

                if (katexTemplates.length === 0) {
                    $grid.append('<div class="no-results">لا توجد نتائج</div>');
                    return;
                }
                katexTemplates.forEach(function (tpl) {
                    var rendered = '';
                    try {
                        if (typeof katex !== 'undefined') {
                            rendered = renderLatexWithLocalizedDigits(tpl.latex, { displayMode: false, throwOnError: false });
                        } else {
                            rendered = '<code>' + tpl.latex.replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</code>';
                        }
                    } catch(e) {
                        rendered = '<code>' + tpl.latex.replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</code>';
                    }
                    $grid.append('<button type="button" class="math-key-btn math-katex-btn" data-katex="' + tpl.latex.replace(/"/g, '&quot;') + '">' + rendered + '</button>');
                });
            }

            function renderCategory(key) {
                if (key === 'templates') {
                    activeCategory = key;
                    searchQuery = '';
                    $search.val('');
                    renderKatexTemplates();
                    return;
                }
                var cat = symbolsByCategory[key];
                if (!cat) return;
                activeCategory = key;
                searchQuery = '';
                $search.val('');
                renderSymbols(cat.symbols);
            }

            function renderFiltered(query) {
                var allSymbols = [];
                categoryOrder.forEach(function (key) {
                    symbolsByCategory[key].symbols.forEach(function (s) { allSymbols.push(s); });
                });
                var q = query.trim().toLowerCase();
                if (!q) {
                    renderCategory(activeCategory);
                    return;
                }
                var filtered = allSymbols.filter(function (s) { return s.toLowerCase().indexOf(q) !== -1; });
                $tabs.find('.math-tab-btn').removeClass('active');
                renderSymbols(filtered);
            }

            function renderRecent() {
                if (recentSymbols.length === 0) {
                    $recent.hide();
                    return;
                }
                $recent.show();
                $recentList.empty();
                var shown = recentSymbols.slice(0, maxRecent);
                shown.forEach(function (symbol) {
                    $recentList.append('<button type="button" class="math-key-btn" data-symbol="' + symbol.replace(/"/g, '&quot;') + '">' + symbol + '</button>');
                });
            }

            function addRecent(symbol) {
                var idx = recentSymbols.indexOf(symbol);
                if (idx !== -1) recentSymbols.splice(idx, 1);
                recentSymbols.unshift(symbol);
                if (recentSymbols.length > maxRecent) recentSymbols.pop();
                renderRecent();
            }

            function updateActiveIndicator() {
                if (activeSummernote && activeSummernote.length) {
                    $activeLabel.text('محرر النصوص (Summernote)');
                    $activeDot.removeClass('inactive-dot').addClass('active-dot');
                } else if (activeEditable) {
                    var name = $(activeEditable).attr('data-placeholder') || 'حقل نص منسق';
                    $activeLabel.text(name);
                    $activeDot.removeClass('inactive-dot').addClass('active-dot');
                } else if (activeInput) {
                    var name = $(activeInput).attr('name') || $(activeInput).attr('placeholder') || 'حقل نص';
                    $activeLabel.text(name);
                    $activeDot.removeClass('inactive-dot').addClass('active-dot');
                } else {
                    $activeLabel.text('لم يتم تحديد حقل');
                    $activeDot.removeClass('active-dot').addClass('inactive-dot');
                }
            }

            // ── Panel open/close ──
            function openPanel() {
                $panel.removeClass('d-none');
                requestAnimationFrame(function () {
                    $panel.addClass('open');
                });
                updateActiveIndicator();
                $search.focus();
            }

            function closePanel() {
                $panel.removeClass('open');
                $panel.one('transitionend', function () {
                    if (!$panel.hasClass('open')) $panel.addClass('d-none');
                });
                setTimeout(function () {
                    if (!$panel.hasClass('open')) $panel.addClass('d-none');
                }, 250);
            }

            function togglePanel() {
                if ($panel.hasClass('d-none')) {
                    openPanel();
                } else if ($panel.hasClass('open')) {
                    closePanel();
                } else {
                    openPanel();
                }
            }

            // ── Render initial ──
            renderCategory(categoryOrder[0]);

            // ── Tab switching ──
            $tabs.on('click', '.math-tab-btn', function () {
                var key = $(this).data('category');
                $tabs.find('.math-tab-btn').removeClass('active');
                $(this).addClass('active');
                renderCategory(key);
            });

            // ── Search ──
            $search.on('input', function () {
                var q = $(this).val();
                searchQuery = q;
                if (q.trim()) {
                    renderFiltered(q);
                } else {
                    renderCategory(activeCategory);
                    $tabs.find('.math-tab-btn[data-category="' + activeCategory + '"]').addClass('active').siblings().removeClass('active');
                }
            });

            // ── Close button ──
            $close.on('click', closePanel);

            // ── Input detection ──
            function canUseMathKeyboard($el) {
                if (!$el || !$el.length) return false;
                if ($el.is('[readonly], [disabled]')) return false;
                if (!$el.is('input, textarea')) return false;
                if ($el.is('input')) {
                    var type = ($el.attr('type') || 'text').toLowerCase();
                    return ['text', 'search', 'tel', 'url', 'email'].indexOf(type) !== -1;
                }
                return true;
            }

            function canUseEditableKeyboard($el) {
                if (!$el || !$el.length) return false;
                if ($el.is('[readonly], [disabled]')) return false;
                if ($el.is('[contenteditable="true"]')) return true;
                return $el.closest('[contenteditable="true"]').length > 0;
            }

            function findSummernoteTextarea($el) {
                var $noteEditable = $el.closest('.note-editable');
                if (!$noteEditable.length) return null;
                var $noteEditingArea = $noteEditable.closest('.note-editing-area');
                if (!$noteEditingArea.length) return null;
                var $noteEditor = $noteEditingArea.closest('.note-editor');
                if (!$noteEditor.length) return null;
                var id = $noteEditor.attr('data-target');
                if (id) { var $ta = $(id); if ($ta.length && $ta.hasClass('lms_summernote')) return $ta; }
                var $ta = $noteEditor.siblings('textarea.lms_summernote');
                if ($ta.length) return $ta;
                var index = $('.note-editor').index($noteEditor);
                return $('.lms_summernote').eq(index);
            }

            function getTargetSummernote($el) {
                var $editable = $el.closest('.note-editable');
                if ($editable.length) return findSummernoteTextarea($editable);
                return null;
            }

            function insertAtCursor(el, value) {
                var start = el.selectionStart || 0;
                var end = el.selectionEnd || 0;
                var text = el.value || '';
                el.value = text.slice(0, start) + value + text.slice(end);
                var cursor = start + value.length;
                el.focus();
                if (typeof el.setSelectionRange === 'function') el.setSelectionRange(cursor, cursor);
                $(el).trigger('input').trigger('change');
            }

            function insertIntoSummernote($ta, value) {
                if ($ta.length && typeof $ta.summernote === 'function') {
                    $ta.summernote('focus');
                    $ta.summernote('insertText', value);
                }
            }

            function insertIntoEditable(el, value) {
                el.focus();
                if (window.getSelection) {
                    var sel = window.getSelection();
                    document.execCommand('insertText', false, value);
                } else {
                    el.textContent += value;
                }
                $(el).trigger('input').trigger('change');
            }

            // ── Focus tracking ──
            $(document).on('focusin click', 'input, textarea', function () {
                var $el = $(this);
                if ($el.hasClass('lms_summernote')) return;
                if (canUseMathKeyboard($el)) {
                    activeInput = this;
                    activeSummernote = null;
                    if ($panel.hasClass('open')) updateActiveIndicator();
                }
            });

            $(document).on('focusin click', '[contenteditable="true"], .note-editable', function () {
                var $el = $(this);
                if (canUseEditableKeyboard($el)) {
                    var $editable = $el.closest('[contenteditable="true"]').length ? $el.closest('[contenteditable="true"]') : $el;
                    activeEditable = $editable[0];
                    var $ta = getTargetSummernote($el);
                    if ($ta && $ta.length) { activeSummernote = $ta; activeInput = null; }
                    else { activeSummernote = null; }
                    if ($panel.hasClass('open')) updateActiveIndicator();
                }
            });

            // ── Toggle ──
            $toggle.on('click', togglePanel);

            // ── Keyboard shortcut ──
            $(document).on('keydown', function (e) {
                if ((e.ctrlKey || e.metaKey) && e.shiftKey && (e.key === 'm' || e.key === 'M')) {
                    e.preventDefault();
                    togglePanel();
                }
                if (e.key === 'Escape' && $panel.hasClass('open')) {
                    closePanel();
                }
            });

            // ── Insert symbol ──
            function handleSymbolInsert(symbol, $btn) {
                if (!symbol) return;

                // Try Summernote
                if (activeSummernote && activeSummernote.length) {
                    insertIntoSummernote(activeSummernote, symbol);
                    addRecent(symbol);
                    if ($btn && $btn.length) $btn.addClass('inserted');
                    return true;
                }

                // Try contenteditable
                if (activeEditable && canUseEditableKeyboard($(activeEditable))) {
                    var $ta = getTargetSummernote($(activeEditable));
                    if ($ta && $ta.length) {
                        activeSummernote = $ta;
                        insertIntoSummernote($ta, symbol);
                    } else {
                        insertIntoEditable(activeEditable, symbol);
                    }
                    addRecent(symbol);
                    if ($btn && $btn.length) $btn.addClass('inserted');
                    return true;
                }

                // Try regular input
                var focused = document.activeElement ? $(document.activeElement) : $();
                if (canUseMathKeyboard(focused)) activeInput = focused[0];
                if (activeInput && canUseMathKeyboard($(activeInput))) {
                    insertAtCursor(activeInput, symbol);
                    addRecent(symbol);
                    if ($btn && $btn.length) $btn.addClass('inserted');
                    return true;
                }

                // Contenteditable from focus
                if (canUseEditableKeyboard(focused)) {
                    var $ta2 = getTargetSummernote(focused);
                    if ($ta2 && $ta2.length) {
                        activeSummernote = $ta2;
                        insertIntoSummernote($ta2, symbol);
                    } else {
                        var editable = focused.closest('[contenteditable="true"]').length ? focused.closest('[contenteditable="true"]')[0] : focused[0];
                        insertIntoEditable(editable, symbol);
                    }
                    addRecent(symbol);
                    if ($btn && $btn.length) $btn.addClass('inserted');
                    return true;
                }

                toastr.warning('اختر حقل النص أولاً');
                return false;
            }

            function handleKatexInsert($btn) {
                var latex = $btn.data('katex');
                if (!latex) return;
                var html = '';
                try {
                    if (typeof katex !== 'undefined') {
                        html = renderLatexWithLocalizedDigits(latex, { displayMode: true, throwOnError: false });
                    } else {
                        html = '<code>' + latex.replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</code>';
                    }
                } catch(e) {
                    html = '<code>' + latex.replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</code>';
                }
                var $node = $('<span class="note-equation" contenteditable="false" style="display:inline-block;padding:0 2px">' + html + '</span>');
                var $hidden = $('<span class="note-equation-latex-src" style="display:none">' + latex.replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</span>');
                $node.append($hidden);

                if (activeSummernote && activeSummernote.length) {
                    activeSummernote.summernote('focus');
                    activeSummernote.summernote('insertNode', $node[0]);
                    if ($btn && $btn.length) $btn.addClass('inserted');
                    return true;
                }

                if (activeEditable) {
                    var $ta = getTargetSummernote($(activeEditable));
                    if ($ta && $ta.length) {
                        activeSummernote = $ta;
                        $ta.summernote('focus');
                        $ta.summernote('insertNode', $node[0]);
                    } else {
                        var sel = window.getSelection();
                        if (sel && sel.getRangeAt) {
                            var range = sel.getRangeAt(0);
                            range.deleteContents();
                            range.insertNode($node[0]);
                            range.collapse();
                        } else {
                            $(activeEditable).append($node);
                        }
                    }
                    if ($btn && $btn.length) $btn.addClass('inserted');
                    return true;
                }

                toastr.warning('اختر حقل النص أولاً');
                return false;
            }

            // ── Smart insert: at cursor, wrap selection, smart cursor ──
            function insertTemplateSmart($ta, templateLatex) {
                var el = $ta[0];
                if (!el) return;
                var start = el.selectionStart || 0;
                var end = el.selectionEnd || 0;
                var text = el.value || '';
                var selected = text.slice(start, end);

                var insertLatex = templateLatex;
                var cursorTarget = -1;

                if (selected) {
                    // Replace first single-letter placeholder {x}, {n}, {a}, etc. with selection
                    var phMatch = insertLatex.match(/\{[a-zA-Z]\}/);
                    if (phMatch) {
                        insertLatex = insertLatex.replace(phMatch[0], '{' + selected + '}');
                        cursorTarget = start + insertLatex.length;
                    } else {
                        // Try two-letter placeholders like {AB}
                        var ph2Match = insertLatex.match(/\{[A-Z]{2}\}/);
                        if (ph2Match) {
                            insertLatex = insertLatex.replace(ph2Match[0], '{' + selected + '}');
                            cursorTarget = start + insertLatex.length;
                        }
                    }
                }

                el.value = text.slice(0, start) + insertLatex + text.slice(end);

                // Find first {…} block to place cursor inside it (skip placeholders like {a}{b} that were already handled)
                if (cursorTarget < 0 && !selected) {
                    var firstOpen = insertLatex.indexOf('{');
                    var firstClose = insertLatex.indexOf('}');
                    if (firstOpen >= 0 && firstClose > firstOpen + 1) {
                        // Place cursor just after the opening {
                        cursorTarget = start + firstOpen + 1;
                    }
                }

                var cursor = cursorTarget >= 0 ? cursorTarget : start + insertLatex.length;
                el.focus();
                if (typeof el.setSelectionRange === 'function') el.setSelectionRange(cursor, cursor);
                $ta.trigger('input').trigger('change');
            }

            $(document).on('click', '.math-key-btn', function (e) {
                var $btn = $(this);
                var latex = $btn.data('katex');
                if (latex) {
                    // Alt+click → insert directly into document (Summernote), not into textarea
                    if (e.altKey) {
                        handleKatexInsert($btn);
                    } else if ($customLatex && $customLatex.length) {
                        insertTemplateSmart($customLatex, latex);
                    } else {
                        handleKatexInsert($btn);
                    }
                } else {
                    handleSymbolInsert($btn.data('symbol') || '', $btn);
                }
            });

            // ── Double-click to insert + close ──
            $(document).on('dblclick', '.math-key-btn', function () {
                var $btn = $(this);
                if ($btn.data('katex')) {
                    if ($customLatex && $customLatex.length) {
                        insertTemplateSmart($customLatex, $btn.data('katex'));
                    }
                } else if ($btn.data('symbol')) {
                    closePanel();
                }
            });

            // ── Close on outside click ──
            $(document).on('mousedown touchstart', function (e) {
                var $target = $(e.target);
                if (!$target.closest('#mathKeyboardPanel, #mathKeyboardToggle').length) {
                    if ($panel.hasClass('open')) closePanel();
                }
            });

            // ── Drag ──
            $dragHandle.on('mousedown touchstart', function (e) {
                if (window.innerWidth <= 575) return;
                if (e.type === 'mousedown' && e.button !== 0) return;
                e.preventDefault();
                var clientX, clientY;
                if (e.type === 'mousedown') {
                    clientX = e.clientX; clientY = e.clientY;
                } else {
                    var touch = e.originalEvent.touches[0];
                    clientX = touch.clientX; clientY = touch.clientY;
                }
                var panelPos = $panel.offset();
                dragOffsetX = clientX - panelPos.left;
                dragOffsetY = clientY - panelPos.top;
                isDragging = true;
                $panel.css('left', panelPos.left + 'px');
                $panel.css('right', 'auto');
                $panel.css('bottom', 'auto');
                $panel.css('transform', 'none');
                $panel.css('transition', 'opacity .2s ease');
            });

            $(document).on('mousemove touchmove', function (e) {
                if (!isDragging) return;
                e.preventDefault();
                var clientX, clientY;
                if (e.type === 'mousemove') {
                    clientX = e.clientX; clientY = e.clientY;
                } else {
                    var touch = e.originalEvent.touches[0];
                    clientX = touch.clientX; clientY = touch.clientY;
                }
                var x = clientX - dragOffsetX;
                var y = clientY - dragOffsetY;
                x = Math.max(0, Math.min(x, window.innerWidth - $panel.outerWidth()));
                y = Math.max(0, Math.min(y, window.innerHeight - $panel.outerHeight()));
                $panel.css({ left: x + 'px', top: y + 'px' });
            });

            $(document).on('mouseup touchend', function () {
                if (isDragging) {
                    isDragging = false;
                    $panel.css('transition', 'opacity .2s ease, transform .25s ease');
                    // Save position to localStorage
                    try {
                        localStorage.setItem('math_panel_left', $panel.css('left'));
                        localStorage.setItem('math_panel_top', $panel.css('top'));
                        localStorage.setItem('math_panel_right', $panel.css('right'));
                        localStorage.setItem('math_panel_bottom', $panel.css('bottom'));
                    } catch(e) {}
                }
            });

            // ── Resize ──
            var isResizing = false;
            var resizeStartX = 0, resizeStartY = 0;
            var resizeStartW = 0, resizeStartH = 0;

            $('#mathResizeHandle').on('mousedown touchstart', function (e) {
                if (e.type === 'mousedown' && e.button !== 0) return;
                e.preventDefault();
                e.stopPropagation();
                var clientX, clientY;
                if (e.type === 'mousedown') {
                    clientX = e.clientX; clientY = e.clientY;
                } else {
                    var touch = e.originalEvent.touches[0];
                    clientX = touch.clientX; clientY = touch.clientY;
                }
                isResizing = true;
                resizeStartX = clientX;
                resizeStartY = clientY;
                resizeStartW = $panel.outerWidth();
                resizeStartH = $panel.outerHeight();
                $panel.css('transition', 'none');
                $('body').css('user-select', 'none');
            });

            $(document).on('mousemove touchmove', function (e) {
                if (!isResizing) return;
                e.preventDefault();
                var clientX, clientY;
                if (e.type === 'mousemove') {
                    clientX = e.clientX; clientY = e.clientY;
                } else {
                    var touch = e.originalEvent.touches[0];
                    clientX = touch.clientX; clientY = touch.clientY;
                }
                var newW = Math.min(window.innerWidth - 16, Math.max(360, resizeStartW + (clientX - resizeStartX)));
                var newH = Math.min(window.innerHeight - 16, Math.max(280, resizeStartH + (clientY - resizeStartY)));
                $panel.css({ width: newW + 'px', height: newH + 'px' });
            });

            $(document).on('mouseup touchend', function () {
                if (isResizing) {
                    isResizing = false;
                    $('body').css('user-select', '');
                    $panel.css('transition', '');
                    // Save size to localStorage
                    try {
                        localStorage.setItem('math_panel_width', $panel.outerWidth());
                        localStorage.setItem('math_panel_height', $panel.outerHeight());
                    } catch(e) {}
                }
            });

            // Restore saved size and position
            try {
                var savedW = localStorage.getItem('math_panel_width');
                var savedH = localStorage.getItem('math_panel_height');
                if (savedW) $panel.css('width', Math.min(parseInt(savedW, 10), window.innerWidth - 16) + 'px');
                if (savedH) $panel.css('height', Math.min(parseInt(savedH, 10), window.innerHeight - 16) + 'px');
                var savedL = localStorage.getItem('math_panel_left');
                var savedT = localStorage.getItem('math_panel_top');
                var savedR = localStorage.getItem('math_panel_right');
                var savedB = localStorage.getItem('math_panel_bottom');
                if (savedL && savedT && savedL !== 'auto') {
                    $panel.css({ left: savedL, top: savedT, right: 'auto', bottom: 'auto' });
                } else if (savedR && savedB && savedR !== 'auto') {
                    $panel.css({ right: savedR, bottom: savedB });
                }

                var maxLeft = Math.max(0, window.innerWidth - $panel.outerWidth());
                var maxTop = Math.max(0, window.innerHeight - $panel.outerHeight());
                if ($panel.position().left > maxLeft) $panel.css('left', maxLeft + 'px');
                if ($panel.position().top > maxTop) $panel.css('top', maxTop + 'px');
            } catch(e) {}

            // ── Custom Template Save ──
            var customTemplates = [];
            var CUSTOM_TEMPLATES_KEY = 'math_custom_templates';

            function loadCustomTemplates() {
                try {
                    var stored = localStorage.getItem(CUSTOM_TEMPLATES_KEY);
                    if (stored) customTemplates = JSON.parse(stored);
                    if (!Array.isArray(customTemplates)) customTemplates = [];
                } catch(e) { customTemplates = []; }
            }

            function saveCustomTemplates() {
                try {
                    localStorage.setItem(CUSTOM_TEMPLATES_KEY, JSON.stringify(customTemplates));
                } catch(e) {}
            }

            loadCustomTemplates();

            function renderCustomTemplates() {
                $grid.empty();
                if (customTemplates.length === 0) {
                    $grid.append('<div class="no-results">لا توجد قوالب مخصصة. اكتب معادلة ثم اضغط 💾 للحفظ</div>');
                    return;
                }
                customTemplates.forEach(function (tpl, idx) {
                    var rendered = '';
                    try {
                        if (typeof katex !== 'undefined') {
                            rendered = renderLatexWithLocalizedDigits(tpl.latex, { displayMode: false, throwOnError: false });
                        } else {
                            rendered = '<code>' + tpl.latex.replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</code>';
                        }
                    } catch(e) {
                        rendered = '<code>' + tpl.latex.replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</code>';
                    }
                    $grid.append(
                        '<div class="custom-tpl-wrap" style="position:relative;display:inline-block" data-idx="' + idx + '">' +
                        '<button type="button" class="math-key-btn math-katex-btn custom-tpl-btn" data-katex="' + tpl.latex.replace(/"/g, '&quot;') + '">' + rendered + '</button>' +
                        '<button type="button" class="custom-tpl-del" data-idx="' + idx + '" title="حذف القالب" style="position:absolute;top:-4px;right:-4px;width:18px;height:18px;border-radius:50%;border:0;background:#ef4444;color:#fff;font-size:11px;line-height:1;cursor:pointer;display:flex;align-items:center;justify-content:center;padding:0;box-shadow:0 2px 4px rgba(0,0,0,.2)">✕</button>' +
                        '</div>'
                    );
                });
            }

            function addCustomTab() {
                var $existingTab = $tabs.find('.math-tab-btn[data-category="custom"]');
                if ($existingTab.length) return;
                $tabs.append('<button type="button" class="math-tab-btn" data-category="custom">💾 قوالب محفوظة</button>');
            }

            // Remove custom templates
            $(document).on('click', '.custom-tpl-del', function (e) {
                e.stopPropagation();
                var idx = $(this).data('idx');
                customTemplates.splice(idx, 1);
                saveCustomTemplates();
                if (activeCategory === 'custom') renderCustomTemplates();
            });

            // Tab handler for custom
            var _origRenderCategory = renderCategory;
            renderCategory = function (key) {
                if (key === 'custom') {
                    activeCategory = key;
                    searchQuery = '';
                    $search.val('');
                    renderCustomTemplates();
                    return;
                }
                _origRenderCategory(key);
            };

            $('#mathSaveTemplateBtn').on('click', function () {
                var val = $customLatex.val().trim();
                if (!val) { toastr.warning('اكتب معادلة أولاً'); return; }
                var exists = customTemplates.some(function (t) { return t.latex === val; });
                if (exists) { toastr.info('هذه المعادلة محفوظة بالفعل'); return; }
                customTemplates.push({ latex: val });
                saveCustomTemplates();
                addCustomTab();
                toastr.success('تم حفظ القالب');
                // Switch to custom tab
                $tabs.find('.math-tab-btn').removeClass('active');
                var $customTab = $tabs.find('.math-tab-btn[data-category="custom"]');
                if ($customTab.length) { $customTab.addClass('active'); renderCategory('custom'); }
            });

            // Load custom tab on init
            if (customTemplates.length > 0) addCustomTab();

            // ── Equation History ──
            var equationHistory = [];
            var HISTORY_KEY = 'math_equation_history';
            var maxHistory = 20;
            var showHistory = false;

            function loadEquationHistory() {
                try {
                    var stored = localStorage.getItem(HISTORY_KEY);
                    if (stored) equationHistory = JSON.parse(stored);
                    if (!Array.isArray(equationHistory)) equationHistory = [];
                } catch(e) { equationHistory = []; }
            }

            function saveEquationHistory() {
                try {
                    localStorage.setItem(HISTORY_KEY, JSON.stringify(equationHistory));
                } catch(e) {}
            }

            loadEquationHistory();

            function addEquationToHistory(latex) {
                var idx = equationHistory.indexOf(latex);
                if (idx !== -1) equationHistory.splice(idx, 1);
                equationHistory.unshift(latex);
                if (equationHistory.length > maxHistory) equationHistory.pop();
                saveEquationHistory();
            }

            function renderHistory() {
                $grid.empty();
                if (equationHistory.length === 0) {
                    $grid.append('<div class="no-results">لا توجد معادلات سابقة</div>');
                    return;
                }
                var count = 0;
                equationHistory.forEach(function (latex) {
                    if (count >= 20) return;
                    count++;
                    var rendered = '';
                    try {
                        if (typeof katex !== 'undefined') {
                            rendered = renderLatexWithLocalizedDigits(latex, { displayMode: false, throwOnError: false });
                        } else {
                            rendered = '<code>' + latex.replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</code>';
                        }
                    } catch(e) {
                        rendered = '<code>' + latex.replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</code>';
                    }
                    $grid.append(
                        '<div class="history-item" style="grid-column:span 2;display:flex;align-items:center;gap:6px;background:#f8f9fa;border:1px solid #e6e6e6;border-radius:10px;padding:6px 10px">' +
                        '<button type="button" class="math-key-btn math-katex-btn history-use-btn" data-katex="' + latex.replace(/"/g, '&quot;') + '" style="flex:1;padding:8px 4px" title="استخدام">' + rendered + '</button>' +
                        '<button type="button" class="history-insert-btn" data-katex="' + latex.replace(/"/g, '&quot;') + '" title="إدراج في المحرر" style="width:28px;height:28px;border:0;border-radius:8px;background:#0d6efd;color:#fff;font-size:14px;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0">⇥</button>' +
                        '</div>'
                    );
                });
            }

            $('#mathHistoryBtn').on('click', function () {
                if (activeCategory === '_history') {
                    // Toggle back to previous category
                    $tabs.find('.math-tab-btn').removeClass('active');
                    $tabs.find('.math-tab-btn[data-category="' + categoryOrder[0] + '"]').addClass('active');
                    renderCategory(categoryOrder[0]);
                } else {
                    activeCategory = '_history';
                    searchQuery = '';
                    $search.val('');
                    $tabs.find('.math-tab-btn').removeClass('active');
                    renderHistory();
                }
            });

            // History insert button – insert at cursor
            $(document).on('click', '.history-insert-btn', function () {
                var latex = $(this).data('katex');
                if (latex && $customLatex && $customLatex.length) {
                    insertTemplateSmart($customLatex, latex);
                }
            });

            // ── Track equation history on insert ──
            $customInsertBtn.off('click').on('click', function () {
                var val = $customLatex.val().trim();
                if (!val) return;
                addEquationToHistory(val);
                var $fakeBtn = $('<button>').data('katex', val);
                handleKatexInsert($fakeBtn);
            });

            // ── Expose for debugging ──
            window.__mathKeyboard = {
                open: openPanel,
                close: closePanel,
                toggle: togglePanel,
                addRecent: addRecent,
                saveTemplate: function () { $('#mathSaveTemplateBtn').click(); },
                history: equationHistory
            };
        })();
    </script>
    <script src="{{asset('/')}}/Modules/CourseSetting/Resources/assets/js/course.js"></script>


    @includeIf("quiz::partials._quiz_bank_script")
@endpush
