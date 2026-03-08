<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <script src='{{$setting->jitsi_server??''}}external_api.js' type="text/javascript"></script>
</head>
<body>
<div id="meeting" style="width: 98vw;height: 95vh;overflow:hidden;"></div>
<script type="text/javascript">
    @php
        if ($meeting->instructor_id==\Illuminate\Support\Facades\Auth::user()->id){
        $userType='Instructor';
    }else{
        $userType="Student";
    }
    @endphp
    const domain = '{{getDomainName($setting->jitsi_server??'')}}';
    const options = {
        roomName: {{$meeting->meeting_id}},
        width: "100%",
        height: "100%",
        disableInviteFunctions: false,
        enableWelcomePage: false,
        parentNode: document.querySelector('#meeting'),
        onload: function () {
        }, userInfo: {
            email: '{{Auth::user()->email}}',
            displayName: '{{Auth::user()->name}}' + ' | ' + '{{$userType}}'
        }
    };
    const api = new JitsiMeetExternalAPI(domain, options);
    api.executeCommand('subject', ' {{$meeting->topic}}');
    api.executeCommand('avatarUrl', '{{getProfileImage(Auth::user()->image,Auth::user()->name)}}');

</script>
</body>
</html>
