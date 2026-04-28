<div class="d-flex align-content-center justify-content-between gap-5 flex-wrap">
    @if(!Settings('hide_social_share_btn') =='1')
        <div class="social_btns blog_social_btns">
            <a target="_blank"
               href="https://www.facebook.com/sharer/sharer.php?u={{URL::current()}}"
               class="social_btn fb_text"> <i class="fab fa-facebook-f"></i></a>
            <a target="_blank"
               href="https://x.com/intent/tweet?text={{$blog->title}}&amp;url={{URL::current()}}"
               class="social_btn twitter_text"> <svg
                    class="x-twitter-icon" viewBox="0 0 24 24" aria-hidden="true"
                    focusable="false" width="1em" height="1em"
                    style="vertical-align: -0.125em;">
                    <path
                        d="M4 3h5.3l4.2 5.7L18.7 3H22l-6.8 9 6.9 9H16.8l-4.5-6.1L7.1 21H3.7l7.2-9.4L4 3z"
                        fill="currentColor"/>
                </svg></a>
            <a target="_blank"
               href="https://pinterest.com/pin/create/link/?url={{URL::current()}}&amp;description={{$blog->title}}"
               class="social_btn pinterest_text"> <i
                    class="fab fa-pinterest-p"></i></a>
            <a target="_blank"
               href="https://www.linkedin.com/shareArticle?mini=true&amp;url={{URL::current()}}&amp;title={{$blog->title}}&amp;summary={{$blog->title}}"
               class="social_btn linkedin_text"> <i
                    class="fab fa-linkedin-in"></i></a>
        </div>
    @endif

    <div class="like_btn d-flex align-items-center gap-2 ">
        <span> {{$blog->likers_count}}</span>
        @if(auth()->check())
            <a href="{{route('blogs.toggleLike',$blog->id)}}">
                <i class="fa fa-heart fa-2x {{$blog->isLikedBy(auth()->user())?'':'text-danger'}}"></i>
            </a>
        @else
            <a href="#">
                <i class="fa fa-heart fa-2x"></i>
            </a>
        @endif

    </div>
</div>
