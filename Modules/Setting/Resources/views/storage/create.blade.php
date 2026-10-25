@extends('backend.master')

@push('styles')
    <style>
        .UppyDragDrop {
            height: 500px;
        }

        .uppy-Dashboard-inner {
            width: 100% !important;
            height: 500px !important;
        }

        .uppy-Dashboard-AddFiles-info {
            display: none !important;
        }
    </style>
@endpush
@section('mainContent')
    {{generateBreadcrumb()}}
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">

                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-lg-6">
                                    <h4 class="mt-15 mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ __('setting.File Upload') }}</h4>
                                </div>
                            </div>

                        </div>
                        <div class="card-body">
                            <form action="{{route('setting.media-manager.store')}}" method="post"
                                  enctype="multipart/form-data">
                                @csrf

                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="UppyDragDrop"></div>
                                        <div class="for-ProgressBar"></div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection

@push('scripts')
    <script>
        window.addEventListener('DOMContentLoaded', function () {
            Amaz.uploader.initMediaUploads('.UppyDragDrop', false);
        });
    </script>
@endpush
