@extends('backend.master')

@section('mainContent')
    {!! generateBreadcrumb() !!}


    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="">
                        <div class="row">

                            <div class="col-lg-12">

                                <div class="tab-content " id="myTabContent">


                                    <div class="tab-pane fade white_box_30px  show active" id="Company_Information"
                                         role="tabpanel" aria-labelledby="Company_Information-tab">

                                        <div class="main-title mb-25">
                                            <h3 class="mb-0">{{__('common.Aws S3 Setting')}}</h3>
                                        </div>

                                        <form action="{{route('AwsS3SettingSubmit')}}" method="post">
                                            @csrf

                                            @includeIf('amazons3::_form')


                                            <div class="col-12 mb-10 pt_15">
                                                <div class="submit_btn text-center">
                                                    <button type="submit" class="primary_btn_large"
                                                            data-toggle="tooltip"><i
                                                            class="ti-check"></i> {{__('common.Save')}}</button>
                                                </div>
                                            </div>

                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

