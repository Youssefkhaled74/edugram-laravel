@if(isset($result))
    <div class="row">
        @foreach($result as $instructor)

            <div class="col-md-6 col-lg-4 col-xl-3">
                <div class="single_instractor mb_30">
                    <div class="thumb">
                        <a href="{{route('instructorDetails',[$instructor->id,Str::slug($instructor->name,'-')])}}">
                            <img src="{{getProfileImage($instructor->image,$instructor->name)}}" alt="">
                        </a>
                        @if(!Settings('hide_social_share_btn') =='1')
                            <ul class="instructor_social-links">
                                <li><a href="{{$instructor->facebook}}"><i class="fab fa-facebook-f"></i></a></li>
                                <li><a href="{{$instructor->twitter}}"><svg class="x-twitter-icon"
                                                                             viewBox="0 0 24 24" aria-hidden="true"
                                                                             focusable="false" width="1em" height="1em"
                                                                             style="vertical-align: -0.125em;">
                                        <path
                                            d="M4 3h5.3l4.2 5.7L18.7 3H22l-6.8 9 6.9 9H16.8l-4.5-6.1L7.1 21H3.7l7.2-9.4L4 3z"
                                            fill="currentColor"/>
                                    </svg></a></li>
                                <li><a href="{{$instructor->linkedin}}"><i class="fab fa-linkedin-in"></i></a></li>
                                <li><a href="{{@$instructor->instagram}}"><i class="fab fa-instagram"></i></a></li>
                            </ul>
                        @endif
                    </div>
                    <a href="{{route('instructorDetails',[$instructor->id,Str::slug($instructor->name,'-')])}}">
                        <h3 class="member-name">{{$instructor->name}}</h3></a>
                    <div class="designation">{{$instructor->headline}}</div>
                </div>
            </div>
        @endforeach
    </div>
    @if(isset($has_pagination))
        <div class="row">
            @if ($result->hasPages())
                <div class="pagination-wrapper">
                    {{ $result->links(theme('partials._new_pagination')) }}
                </div>
            @endif
        </div>
    @endif
@endif
