<div class="tab-pane fade" id="images_tab">
    @php
        $profileAvatars = config('profile_avatars.items', []);
        $selectedAvatarKey = collect($profileAvatars)->search($user->image);
        $selectedAvatarKey = $selectedAvatarKey === false ? '' : $selectedAvatarKey;
    @endphp
    <style>
        .settings-avatar-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(58px, 1fr));
            gap: 10px;
            margin-top: 14px;
            max-width: 360px;
            margin-inline: auto;
        }

        .settings-avatar-item {
            width: 58px;
            height: 58px;
            border-radius: 50%;
            overflow: hidden;
            border: 2px solid #e8ebf3;
            background: #fff;
            padding: 0;
            cursor: pointer;
            transition: border-color .2s ease, box-shadow .2s ease;
        }

        .settings-avatar-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .settings-avatar-item.active {
            border-color: #2f8cff;
            box-shadow: 0 0 0 3px rgba(47, 140, 255, 0.22);
        }
    </style>
    <div class="row">
        <div class="col-12">

            <h3>{{__('profile.images')}}</h3>
            <hr>

            <form action="{{route('users.photo.update')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="profile-image-div text-center">
                            <p>{{ __('profile.select_profile_picture') }}</p>
                            <div class="profile-photo mx-auto position-relative overflow-hidden mt-3 mt-md-4">
                                <img id="profile_picture_show" class="h-100 object-fit-cover w-100"
                                     src="{{getProfileImage(@$user->image,$user->name)}}" alt="Profile Photo">
                                <label for="profile_picture_upload"
                                       class="overlay d-flex align-items-center justify-content-center fs-2 w-100 h-100 position-absolute top-0 left-0 bg-black bg-opacity-50 text-white"
                                       style="cursor:pointer;">
                                    <i class="fa fa-camera"></i>
                                </label>
                                <input type="file" class="d-none" name="profile_picture" id="profile_picture_upload"
                                       accept=".png, .jpg, .jpeg" onchange="previewProfilePicture(this)">
                            </div>
                            <input type="hidden" name="avatar_key" id="settings_avatar_key" value="{{ $selectedAvatarKey }}">
                            @if(count($profileAvatars))
                                <p class="mt-3 mb-1 text-muted" style="font-size:13px;">{{ __('common.Or choose from below') }}</p>
                                <div class="settings-avatar-grid">
                                    @foreach($profileAvatars as $avatarKey => $avatarPath)
                                        <button
                                            type="button"
                                            class="settings-avatar-item {{ $selectedAvatarKey === $avatarKey ? 'active' : '' }}"
                                            data-avatar-key="{{ $avatarKey }}"
                                            data-avatar-path="{{ asset($avatarPath) }}"
                                            title="{{ $avatarKey }}"
                                        >
                                            <img src="{{ asset($avatarPath) }}" alt="{{ $avatarKey }}">
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">

                        <div class="profile-image-div text-center w-100">
                            <p> {{__('common.Recommend size')}} (1920 x 500) px</p>
                            @if(auth()->check() && auth()->user()->role_id == 3)
                                <div class="cover-photo position-relative overflow-hidden mt-3 mt-md-4 rounded-2">
                                    <img class="h-100 object-fit-cover w-100"
                                         src="{{ getStudentDashboardBanner() }}"
                                         alt="Cover Photo">
                                </div>
                            @else
                                <label for="cover_photo"
                                       class="cover-photo position-relative overflow-hidden mt-3 mt-md-4 rounded-2">
                                    <img id="cover_photo_show" class="h-100 object-fit-cover w-100"
                                         src="{{@$user->userInfo->cover_photo? showImage(@$user->userInfo->cover_photo):showImage(null,'cover_photo')}}"
                                         alt="Cover Photo">
                                    <input type="file" class="d-none" name="cover_photo" id="cover_photo"
                                           accept=".png, .jpg, .jpeg">
                                    <span
                                        class="overlay d-flex align-items-center justify-content-center fs-2 w-100 h-100 position-absolute top-0 left-0 bg-black bg-opacity-50 text-white"><i
                                            class="fa fa-camera"></i></span>
                                </label>
                            @endif
                        </div>

                    </div>
                </div>

                <div class="row">

                    <div class="col-12 text-end">
                        <hr class="d-block">
                        <button class="theme_btn small_btn text-center" type="submit"><i
                                class="ti-check"></i> {{__('common.Save')}}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
