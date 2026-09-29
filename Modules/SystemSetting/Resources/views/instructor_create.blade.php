@extends('backend.master')
@push('styles')
    <link rel="stylesheet" href="{{asset('public/backend/css/student_list.css')}}"/>
@endpush
@php
    $table_name='users';
@endphp
@section('table')
    {{$table_name}}
@endsection

@section('mainContent')

    {!! generateBreadcrumb() !!}

    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">

                <div class="col-lg-12 ">
                    <div class="QA_section QA_section_heading_custom check_box_table">
                        <div class="QA_table ">
                            <div class="white-box">
                                <form action="{{isset($user)?route('instructor.update'):route('instructor.store')}}"
                                      method="POST"
                                      enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="id" value="{{isset($user)?$user->id:''}}">

                                    <div class="row">
                                        <div class="col-xl-12">
                                            <div class="primary_input mb-25">
                                                <label class="primary_input_label" for="">{{__('common.Name')}} <strong
                                                        class="text-danger">*</strong></label>
                                                <input class="primary_input_field" name="name" placeholder="-"
                                                       id="addName"
                                                       type="text"
                                                       value="{{ old('name',isset($user)?$user->name:'') }}" {{$errors->first('name') ? 'autofocus' : ''}}>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-xl-12">
                                            <div class="primary_input mb-35">
                                                <label class="primary_input_label"
                                                       for="">{{__('instructor.About')}}</label>
                                                <textarea class="lms_summernote" name="about" id="addAbout" cols="30"
                                                          rows="10">{{ old('about',isset($user)?$user->about:'') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl-6">
                                            <div class="primary_input mb-25">
                                                <label class="primary_input_label"
                                                       for=""> {{__('common.Semester')}}</label>
                                                @php
                                                    $selectedSemesters = old('category_ids', isset($user) ? ($user->instructorSemesters->pluck('id')->all() ?: ($user->category_id ? [$user->category_id] : [])) : []);
                                                @endphp
                                                <select class="primary_input_field" name="category_ids[]" id="category_id" multiple size="5">
                                                    @php
                                                        $parentCategories = \Modules\CourseSetting\Entities\Category::where('parent_id', NULL)->where('status', 1)->orderBy('position_order')->get();
                                                        $allSubCategories = \Modules\CourseSetting\Entities\SubCategory::where('status', 1)->get()->keyBy('category_id');
                                                        $subCatsFromCategories = \Modules\CourseSetting\Entities\Category::where('parent_id', '!=', NULL)->where('status', 1)->get()->keyBy('parent_id');
                                                    @endphp
                                                    @foreach($parentCategories as $cat)
                                                        @php
                                                            $subKey = 'cat_' . $cat->id;
                                                        @endphp
                                                        <option value="{{$cat->id}}" {{in_array($cat->id, $selectedSemesters) ? 'selected' : ''}}>{{$cat->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xl-6">
                                            <div class="primary_input mb-25">
                                                <label class="primary_input_label"
                                                       for="">{{__('common.Sub Category')}}</label>
                                                @php
                                                    $selectedSubcategories = old('subcategory_ids', isset($user) ? $user->instructorSubcategoryIds() : []);
                                                @endphp
                                                <select class="primary_input_field" name="subcategory_ids[]" id="subcategory_id" multiple size="5">
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl-6">
                                            <div class="primary_input mb-25">
                                                <label class="primary_input_label" for="academic_year">{{__('common.Academic Year')}}</label>
                                                <input class="primary_input_field" name="academic_year" id="academic_year" type="text" maxlength="20" placeholder="2025/2026" value="{{ old('academic_year', isset($user) ? $user->academic_year : '') }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl-6">
                                            <div class="primary_input mb-15">
                                                <label class="primary_input_label" for="">{{__('common.Date of Birth')}}
                                                </label>
                                                <div class="primary_datepicker_input">
                                                    <div class="g-0  input-right-icon">
                                                        <div class="col">
                                                            <div class="">
                                                                <input placeholder="{{__('common.Date')}}"
                                                                       class="primary_input_field primary-input date form-control"
                                                                       id="" type="text" name="dob" data-prevent-future="1"
                                                                       value="{{ old('dob',isset($user)?$user->dob:'') }}"
                                                                       {{$errors->first('dob') ? 'autofocus' : ''}}
                                                                       autocomplete="off">
                                                            </div>
                                                        </div>
                                                        <button class="" type="button">
                                                            <i class="ti-calendar" id="start-date-icon"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-6">
                                            <div class="primary_input mb-25">
                                                <label class="primary_input_label"
                                                       for="">{{__('common.Phone')}} </label>
                                                <input class="primary_input_field phoneNumberInput"
                                                       value="{{ old('phone',isset($user)?$user->phone:'') }}"
                                                       id="addPhone"
                                                       name="phone"
                                                       placeholder="-" {{$errors->first('phone') ? 'autofocus' : ''}}
                                                       type="number">
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row">

                                        <div class="col-xl-6">
                                            <div class="primary_input mb-25">
                                                <label class="primary_input_label" for="">{{__('common.Email')}} <strong
                                                        class="text-danger">*</strong></label>
                                                <input class="primary_input_field" name="email" placeholder="-"
                                                       id="addEmail"
                                                       value="{{ old('email',isset($user)?$user->email:'') }}"
                                                       {{$errors->first('email') ? 'autofocus' : ''}}
                                                       type="email">
                                            </div>
                                        </div>
                                        <div class="col-xl-6">
                                            <div class="  mb-35">

                                                <x-upload-file
                                                    name="image"
                                                    type="image"
                                                    media_id="{{isset($user)?$user->image_media?->media_id:''}}"
                                                    label="{{ __('common.Image') }}"
                                                    note="{{__('student.Recommended size')}} (330x400)"
                                                />


                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">

                                        <div class="col-xl-6">
                                            <div class="primary_input mb-25">
                                                <label class="primary_input_label" for="">{{__('Password')}} <strong
                                                        class="text-danger">*</strong></label>
                                                <div class="input-group mb-2 mr-sm-2">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text"><i style="cursor:pointer;"
                                                                                         class="fas fa-eye-slash eye toggle-password"></i>
                                                        </div>
                                                    </div>
                                                    <input type="password" class="form-control primary_input_field"
                                                           id="addPassword" name="password"
                                                           autocomplete="new-password"
                                                           placeholder="{{__('common.Minimum 8 characters')}}" {{$errors->first('password') ? 'autofocus' : ''}}>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-6">
                                            <div class="primary_input mb-25">
                                                <label class="primary_input_label"
                                                       for="">{{__('Confirm Password')}} <strong
                                                        class="text-danger">*</strong></label>
                                                <div class="input-group mb-2 mr-sm-2">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text"><i style="cursor:pointer;"
                                                                                         class="fas fa-eye-slash eye toggle-password"></i>
                                                        </div>
                                                    </div>
                                                    <input type="password" class="form-control primary_input_field"
                                                           {{$errors->first('password_confirmation') ? 'autofocus' : ''}}
                                                           id="addCpassword" name="password_confirmation"
                                                           placeholder="{{__('common.Minimum 8 characters')}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">

                                        <div class="col-xl-6">
                                            <div class="primary_input mb-25">
                                                <label class="primary_input_label"
                                                       for=""> {{__('common.Facebook URL')}}</label>
                                                <input class="primary_input_field" name="facebook" placeholder="-"
                                                       id="addFacebook"
                                                       type="text"
                                                       value="{{ old('facebook',isset($user)?$user->facebook:'') }}">

                                            </div>
                                        </div>
                                        <div class="col-xl-6">
                                            <div class="primary_input mb-25">
                                                <label class="primary_input_label"
                                                       for=""> {{__('common.Twitter URL')}}</label>
                                                <input class="primary_input_field" name="twitter" placeholder="-"
                                                       id="addTwitter"
                                                       type="text"
                                                       value="{{ old('twitter',isset($user)?$user->twitter:'') }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">

                                        <div class="col-xl-6">
                                            <div class="primary_input mb-25">
                                                <label class="primary_input_label"
                                                       for=""> {{__('common.LinkedIn URL')}}</label>
                                                <input class="primary_input_field" name="linkedin" placeholder="-"
                                                       id="addLinkedin"
                                                       type="text"
                                                       value="{{ old('linkedin',isset($user)?$user->linkedin:'') }}">
                                            </div>
                                        </div>
                                        <div class="col-xl-6">
                                            <div class="primary_input mb-25">
                                                <label class="primary_input_label"
                                                       for=""> {{__('common.Instagram URL')}}</label>
                                                <input class="primary_input_field" name="instagram" placeholder="-"
                                                       id="addInstagram"
                                                       type="text"
                                                       value="{{ old('instagram',isset($user)?$user->instagram:'') }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-12 text-center pt_15">
                                        <div class="d-flex justify-content-center">
                                            <button class="primary-btn semi_large2  fix-gr-bg" id="save_button_parent"
                                                    type="submit"><i
                                                    class="ti-check"></i> {{__('common.Save')}} {{__('courses.Instructor')}}
                                            </button>
                                        </div>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </section>

@endsection
@push('scripts')

    <script src="{{asset('public/backend/js/student_list.js')}}"></script>

    @php
        $subCatsFromCategories = \Modules\CourseSetting\Entities\Category::where('parent_id', '!=', NULL)->where('status', 1)->get();
        $allSubCategories = \Modules\CourseSetting\Entities\SubCategory::where('status', 1)->get();
        $subData = [];
        foreach ($subCatsFromCategories as $sc) {
            $catId = $sc->parent_id;
            if (!isset($subData[$catId])) $subData[$catId] = [];
            $name = is_array($sc->name) ? ($sc->getTranslation('name', app()->getLocale()) ?? reset($sc->name)) : $sc->name;
            $subData[$catId][] = ['value' => 'category:' . (int)$sc->id, 'name' => $name];
        }
        foreach ($allSubCategories as $sc) {
            $catId = $sc->category_id;
            if (!isset($subData[$catId])) $subData[$catId] = [];
            $subData[$catId][] = ['value' => 'sub_category:' . (int)$sc->id, 'name' => (string)$sc->name];
        }
    @endphp

    <script>
        $(document).ready(function () {
            var selectedSubcategories = @json(array_map('strval', $selectedSubcategories));
            var subCategoriesData = {!! json_encode($subData) !!};

            function loadSubcategories(categoryIds) {
                var $sub = $('#subcategory_id');
                var options = {};
                $.each(categoryIds || [], function (categoryIndex, categoryId) {
                    $.each(subCategoriesData[categoryId] || [], function (itemIndex, item) {
                        options[item.value] = item;
                    });
                });
                $sub.empty();
                $.each(options, function (id, item) {
                    var option = $('<option>').val(item.value).text(item.name);
                    option.prop('selected', selectedSubcategories.indexOf(String(item.value)) !== -1);
                    $sub.append(option);
                });
                selectedSubcategories = $sub.val() || [];
            }

            loadSubcategories($('#category_id').val());

            $('#category_id').on('change', function () {
                selectedSubcategories = $('#subcategory_id').val() || [];
                loadSubcategories($(this).val());
            });
        });
    </script>

@endpush
