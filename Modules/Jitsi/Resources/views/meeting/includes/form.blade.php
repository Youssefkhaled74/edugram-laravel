<div class="col-lg-3" @if(!isset($editdata)) style="display: none" @endif>

    <div class="main-title">
        <h3 class="mb-20">
            @if(isset($editdata))
                {{__('jitsi.Edit')}}
            @else
                {{__('jitsi.Add')}}
            @endif
            {{__('jitsi.Classes')}}
        </h3>
    </div>


    <form class="form-horizontal"
          action=" @if(isset($editdata)){{ route('jitsi.meetings.update',$editdata->id) }}@else{{ route('jitsi.meetings.store') }} @endif"
          method="POST"
          enctype="multipart/form-data">
        @csrf


        <div class="row">
            <div class="col-lg-12">
                <div class="white-box">
                    <div class="row d-none">
                        <div class="col-lg-12">
                            <div class="input-effect">


                                <select
                                    class="niceSelect w-100 bb user_type form-control{{ $errors->has('class_id') ? ' is-invalid' : '' }}"
                                    name="class_id">
                                    <option data-display="  {{__('zoom.Class')}}*"
                                            value="">{{__('zoom.Class')}} *
                                    </option>


                                    @foreach($classes as $class)
                                        @if (isset($editdata))
                                            <option
                                                value="{{$class->id}}" {{ old('class_id',$editdata->class_id) == $class->id  ? 'selected':''}}> {{$class->title}}</option>
                                        @else
                                            <option
                                                value="{{$class->id}}" {{ old('class_id') ==  $class->id   ? 'selected':''}} >   {{$class->title}}</option>
                                        @endif

                                    @endforeach

                                </select>


                                @if ($errors->has('class_id'))
                                    <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('class_id') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row d-none mt-10">
                        <div class="col-lg-12">
                            <div class="input-effect">
                                <select required
                                        class="niceSelect w-100 bb user_type form-control{{ $errors->has('instructor_id') ? ' is-invalid' : '' }}"
                                        name="instructor_id">
                                    <option data-display="  {{__('jitsi.Instructor')}}*"
                                            value="">{{__('jitsi.Instructor')}} *
                                    </option>
                                    @foreach($instructors as $instructor)

                                        @if (isset($editdata))
                                            <option
                                                value="{{$instructor->id}}" {{ old('instructor_id',$editdata->instructor_id) == $instructor->id  ? 'selected':''}}> {{$instructor->name}}</option>
                                        @else
                                            <option
                                                value="{{$instructor->id}}" {{ old('instructor_id') ==  $instructor->id   ? 'selected':''}} > {{$instructor->name}}</option>
                                        @endif

                                    @endforeach

                                </select>
                                @if ($errors->has('instructor_id'))
                                    <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('instructor_id') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>


                    <div class="row mt-10">
                        <div class="col-lg-12">
                            <div class="input-effect">
                                <input
                                    class="primary-input form-control{{ $errors->has('topic') ? ' is-invalid' : '' }}"
                                    type="text" name="topic" autocomplete="off"
                                    value="{{ isset($editdata) ?  old('topic',$editdata->topic) : old('topic') }}">
                                <label>{{__('jitsi.Topic')}} <span class="required_mark">*</span></label>
                                <span class="focus-border"></span>
                                @if ($errors->has('topic'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('topic') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row mt-40">
                        <div class="col-lg-12">
                            <div class="input-effect">
                                <input
                                    class="primary-input form-control{{ $errors->has('description') ? ' is-invalid' : '' }}"
                                    type="text" name="description" autocomplete="off"
                                    value="{{ isset($editdata) ?  old('description',$editdata->description) : old('description') }}">
                                <label>{{__('jitsi.Description')}} </label>
                                <span class="focus-border"></span>
                                @if ($errors->has('topic'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('description') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>


                    <div class="row mt-40">
                        <div class="col-lg-6">
                            <label>{{__('jitsi.Date Of Class')}} <span class="required_mark">*</span></label>
                            <input class="primary-input date form-control" id="startDate" type="text"
                                   name="date" readonly="true"
                                   value="{{ isset($editdata) ? old('date',Carbon\Carbon::parse($editdata->date_of_meeting)->format('m/d/Y')): old('date',Carbon\Carbon::now()->format('m/d/Y'))}}"
                                   required>
                            @if ($errors->has('date'))
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('date') }}</strong>
                                    </span>
                            @endif
                        </div>
                        <div class="col-lg-6">
                            <label>{{__('jitsi.Time Of Class')}} <span class="required_mark">*</span></label>
                            <input
                                class="primary-input time form-control{{ @$errors->has('time') ? ' is-invalid' : '' }}"
                                type="text" name="time"
                                value="{{ isset($editdata) ? old('time',$editdata->time): old('time')}}">
                            <span class="focus-border"></span>
                            @if ($errors->has('time'))
                                <span class="invalid-feedback" role="alert">
                                            <strong>{{ @$errors->first('time') }}</strong>
                                        </span>
                            @endif
                        </div>
                    </div>


                    {{-- Start setting  --}}


                    <div class="row mt-40">
                        <div class="col-lg-12 text-center">
                            @if(empty($setting->jitsi_server))
                                <small class="text-danger">* Please make sure Jitsi Server setup
                                    successfully. Without Jitsi Server, you can't create class</small>
                            @else
                                <button type="submit" class="primary-btn fix-gr-bg">
                                    <span class="ti-check"></span>
                                    @if(isset($editdata))
                                        {{__('jitsi.Update')}}
                                    @else
                                        {{__('jitsi.Save')}}
                                    @endif
                                    {{__('jitsi.Class')}}

                                </button>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </form>
</div>



