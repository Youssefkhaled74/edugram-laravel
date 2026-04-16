<div>
    @php
        $profileAvatars = config('profile_avatars.items', []);
        $selectedAvatarKey = collect($profileAvatars)->search($profile->image);
    @endphp

    <style>
        .profilepic {
            position: relative;
            width: 272px;
            height: 272px;
            border-radius: 50%;
            overflow: hidden;
            background-color: #111;
            margin: auto;
            margin-bottom: 15px;
        }

        @media only screen and (min-width: 1581px) and (max-width: 1880px) {
            .profilepic {
                width: 200px;
                height: 200px;
            }
        }

        @media only screen and (min-width: 1440px) and (max-width: 1580px) {
            .profilepic {
                width: 200px;
                height: 200px;
            }
        }

        @media only screen and (min-width: 1200px) and (max-width: 1439px) {
            .profilepic {
                width: 200px;
                height: 200px;
            }
        }

        @media only screen and (min-width: 992px) and (max-width: 1199px) {
            .profilepic {
                width: 200px;
                height: 200px;
            }
        }

        @media only screen and (min-width: 768px) and (max-width: 991px) {
            .profilepic {
                width: 160px;
                height: 160px;
            }
        }

        @media only screen and (max-width: 767px) {
            .profilepic {
                width: 150px;
                height: 150px;
            }
        }


        .profilepic__image {
            object-fit: cover;
        }

        .profilepic__image {
            width: 100%;
            height: 100%
        }

        .avatar-picker-section {
            max-width: 520px;
            margin: 0 auto;
        }

        .avatar-picker-title {
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }

        .avatar-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(58px, 1fr));
            gap: 10px;
            margin-bottom: 12px;
        }

        .avatar-item {
            border: 2px solid #e7e7e7;
            border-radius: 50%;
            width: 58px;
            height: 58px;
            overflow: hidden;
            background: #fff;
            cursor: pointer;
            padding: 0;
            transition: border-color .2s ease, transform .2s ease;
        }

        .avatar-item:hover {
            border-color: #3c9ee7;
            transform: translateY(-1px);
        }

        .avatar-item.active {
            border-color: #3c9ee7;
            box-shadow: 0 0 0 2px rgba(60, 158, 231, 0.2);
        }

        .avatar-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

    </style>

    <form action="" method="post" enctype="multipart/form-data">
        @csrf
        <input type="hidden" value="{{route('ajaxUploadProfilePic')}}" id="ajax-update-profile-image">
        <input type="hidden" value="{{url('/')}}" id="url">
        <input type="hidden" value="{{$selectedAvatarKey ?: ''}}" id="selected_avatar" name="selected_avatar">
        <input type="hidden" value="{{$selectedAvatarKey ?: ''}}" id="avatar_key">
        <div class="profilepic">
            <img class="profilepic__image" src="{{getProfileImage($profile->image,$profile->name)}}"
                 id="show_profile_image"
                 width="272" height="272" alt="Profibild"/>
            <i id="loading" class="fa fa-spinner fa-spin fa-3x fa-fw  site_image_spinner" style="display: none"></i>
        </div>

        @if(count($profileAvatars))
            <div class="avatar-picker-section text-center">
                <p class="avatar-picker-title mb-0">Choose Avatar</p>
                <div class="avatar-grid">
                    @foreach($profileAvatars as $avatarKey => $avatarPath)
                        <button
                            type="button"
                            class="avatar-item {{ $selectedAvatarKey === $avatarKey ? 'active' : '' }}"
                            data-avatar-key="{{$avatarKey}}"
                            data-avatar-path="{{asset($avatarPath)}}"
                            title="{{$avatarKey}}"
                        >
                            <img src="{{asset($avatarPath)}}" alt="{{$avatarKey}}">
                        </button>
                    @endforeach
                </div>
            </div>
        @endif
    </form>

</div>
