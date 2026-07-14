$('#select_country').select2();

function uploadAvatarByKey(avatarKey) {
    if (!avatarKey) {
        return;
    }

    var form_data = new FormData();
    var token = $("input[name=_token]").val();
    var submit_url = $('#ajax-update-profile-image').val();
    var url = $('#url').val();

    form_data.append('avatar_key', avatarKey);
    form_data.append('_token', token);

    $('#loading').css('display', 'block');

    $.ajax({
        url: submit_url,
        data: form_data,
        type: 'POST',
        contentType: false,
        processData: false,
        timeout: 30000,
        success: function (data) {
            if (data && !data.fail) {
                $('#show_profile_image').attr('src', data);
                var header_image = 'background-image: url(' + data + ')';
                $('.studentProfileThumb').attr('style', header_image);
            } else if (data && data.fail) {
                $('#show_profile_image').attr('src', url + '/public/demo/user/admin.jpg');
                toastr.error(data.errors['file'], 'Error Alert', {
                    timeOut: 5000
                });
            }
            $('#loading').css('display', 'none');
        },
        error: function (xhr) {
            alert(xhr.responseText);
            $('#show_profile_image').attr('src', url + '/public/demo/user/admin.jpg');
            $('#loading').css('display', 'none');
        }
    });
}

$(document).on('click', '.avatar-item', function () {
    var avatarKey = $(this).data('avatar-key');
    var avatarPath = $(this).data('avatar-path');

    $('.avatar-item').removeClass('active');
    $(this).addClass('active');

    $('#selected_avatar').val(avatarKey);
    $('#avatar_key').val(avatarKey);
    $('#profile_form_avatar_key').val(avatarKey);

    if (avatarPath) {
        $('#show_profile_image').attr('src', avatarPath);
    }

    uploadAvatarByKey(avatarKey);
});
