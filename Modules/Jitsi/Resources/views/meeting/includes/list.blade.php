<div class="@if(isset($editdata)) col-lg-9 @else  col-lg-12  @endif">
    <div class="main-title">
        <h3 class="mb-20">{{__('jitsi.Class')}} {{__('jitsi.List')}}</h3>
    </div>

    <div class="QA_section QA_section_heading_custom check_box_table">
        <div class="QA_table ">

            <div class="">
                <table id="lms_table" class="table Crm_table_active3">
                    <thead>
                    <tr>
                    <tr>
                        <th>{{__('common.SL')}}</th>
                        <th>   {{__('jitsi.ID')}}</th>
                        <th>   {{__('zoom.Class')}}</th>
                        <th>   {{__('zoom.Instructor')}}</th>
                        <th>   {{__('jitsi.Topic')}}</th>
                        <th>   {{__('jitsi.Date')}}</th>
                        <th>   {{__('jitsi.Time')}}</th>
                        <th>   {{__('jitsi.Duration')}}</th>
                        <th>{{__('jitsi.Actions')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($meetings as $key => $meeting )
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $meeting->meeting_id }}</td>
                            <td>{{ $meeting->class->title }}</td>
                            <td>{{ $meeting->instructor->name }}</td>
                            <td>{{ $meeting->topic }}</td>
                            <td>{{ $meeting->date }}</td>
                            <td>{{ $meeting->time }}</td>
                            <td> @if($meeting->duration==0) Unlimited @else {{ $meeting->duration }} @endif Min</td>


                            <td>
                                <div class="dropdown CRM_dropdown">
                                    <button class="btn btn-secondary dropdown-toggle" type="button"
                                            id="dropdownMenu2" data-toggle="dropdown"
                                            aria-haspopup="true"
                                            aria-expanded="false">
                                        {{ __('common.Select') }}
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right"
                                         aria-labelledby="dropdownMenu2">
                                        <a class="dropdown-item" target="_blank"
                                           href="{{ route('jitsi.meetings.show', $meeting->id) }}">{{__('jitsi.Start')}}</a>

                                        <a class="dropdown-item"
                                           href="{{ route('jitsi.meetings.edit',$meeting->id )}}">{{__('jitsi.Edit')}}</a>

                                        <a class="dropdown-item" data-toggle="modal"
                                           data-target="#d{{$meeting->id}}"
                                           href="#">{{__('jitsi.Delete')}}</a>

                                    </div>
                                </div>
                            </td>
                        </tr>


                        <div class="modal fade admin-query" id="d{{$meeting->id}}">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">{{__('jitsi.Delete Class')}}</h4>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>

                                    <div class="modal-body">
                                        <div class="text-center">
                                            <h4>{{__('jitsi.You sure to delete ?')}}</h4>
                                        </div>

                                        <div class="mt-40 d-flex justify-content-between">
                                            <button type="button" class="primary-btn tr-bg"
                                                    data-dismiss="modal">{{__('jitsi.Cancel')}}</button>
                                            <form class="" action="{{ route('jitsi.meetings.destroy',$meeting->id) }}"
                                                  method="POST">
                                                @csrf
                                                @method('delete')
                                                <button class="primary-btn fix-gr-bg"
                                                        type="submit">{{__('jitsi.Delete')}}</button>
                                            </form>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
