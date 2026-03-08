<?php

namespace Modules\Jitsi\Http\Controllers;

use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\CourseSetting\Entities\Course;
use Modules\Jitsi\Entities\JitsiMeeting;
use Modules\Jitsi\Entities\JitsiMeetingUser;
use Modules\Jitsi\Entities\JitsiSetting;
use Modules\VirtualClass\Entities\VirtualClass;

class JitsiMeetingController extends Controller
{
    public function index()
    {

        $data['user'] = Auth::user();
        $data['instructors'] = User::select('id', 'name')->whereIn('role_id', [1, 2])->get();
        $data['classes'] = VirtualClass::select('id', 'title')->where('host', 'Jitsi')->latest()->get();
        $data['meetings'] = JitsiMeeting::orderBy('id', 'DESC')->get();
        $data['setting'] = JitsiSetting::getData();
        return view('jitsi::meeting.meeting', $data);
    }


    public function store(Request $request)
    {
        $topic = $request->get('topic');
        $description = $request->get('description');
        $instructor_id = $request->get('instructor_id');
        $class_id = $request->get('class_id');
        $date = $request->get('date');
        $time = $request->get('time');
        $datetime = $date . " " . $time;
        $datetime = strtotime($datetime);

        $request->validate([
            'instructor_id' => 'required',
            'class_id' => 'required',
            'topic' => 'required',
            'date' => 'required',
            'time' => 'required',

        ]);

        try {
            $local_meeting = JitsiMeeting::create([
                'meeting_id' => date('ymdhmi'),
                'instructor_id' => $instructor_id,
                'class_id' => $class_id,
                'topic' => $topic,
                'date' => $date,
                'time' => $time,
                'datetime' => $datetime,
                'description' => $description,
                'created_by' => Auth::user()->id,
            ]);

            $user = new JitsiMeetingUser();
            $user->meeting_id = $local_meeting->id;
            $user->user_id = $instructor_id;
            $user->save();

            Toastr::success('Class updated successful', 'Success');
            return redirect()->route('jitsi.meetings');
        } catch (\Exception $e) {
            Toastr::error($e->getMessage(), trans('common.Failed'));
            return redirect()->back();
        }
    }

    public function classStore($data)
    {
        $topic = $data['topic'];
        $description = $data['description'];
        $instructor_id = $data['instructor_id'];
        $meeting_id = $data['jitsi_meeting_id'];
        $duration = $data['duration'];
        $class_id = $data['class_id'];
        $date = $data['date'];
        $time = $data['time'];
        $datetime = $date . " " . $time;
        $datetime = strtotime($datetime);
        if (empty($meeting_id)) {
            $meeting_id = date('ymdhmi');
        }

        try {
            $local_meeting = JitsiMeeting::create([
                'meeting_id' => $meeting_id,
                'instructor_id' => $instructor_id,
                'class_id' => $class_id,
                'duration' => $duration,
                'topic' => $topic,
                'date' => $date,
                'time' => $time,
                'datetime' => $datetime,
                'description' => $description,
                'created_by' => Auth::user()->id,
            ]);


            $user = new JitsiMeetingUser();
            $user->meeting_id = $local_meeting->id;
            $user->user_id = $instructor_id;
            $user->save();


            if ($local_meeting) {
                $result['message'] = '';
                $result['type'] = true;
                return $result;
            } else {
                $result['message'] = '';
                $result['type'] = false;
            }
        } catch (\Exception $e) {
            $result['message'] = $e->getMessage();
            $result['type'] = false;
            return $result;
        }
    }

    public function show(int $id)
    {
        $meeting = JitsiMeeting::findOrFail($id);
        $setting = JitsiSetting::getData();
        return view('jitsi::meeting.start', compact('meeting', 'setting'));
    }

    public function edit(int $id)
    {

        $data['user'] = Auth::user();
        $data['editdata'] = JitsiMeeting::findOrFail($id);
        $data['meetings'] = JitsiMeeting::orderBy('id', 'DESC')->get();
        $data['instructors'] = User::select('id', 'name')->whereIn('role_id', [1, 2])->get();
        $data['classes'] = VirtualClass::select('id', 'title')->where('host', 'Jitsi')->latest()->get();
        $data['setting'] = JitsiSetting::getData();
        return view('jitsi::meeting.meeting', $data);
    }

    public function update(Request $request, int $id)
    {
        $topic = $request->get('topic');
        $description = $request->get('description');
        $instructor_id = $request->get('instructor_id');
        $class_id = $request->get('class_id');
        $date = $request->get('date');
        $time = $request->get('time');
        $datetime = $date . " " . $time;
        $datetime = strtotime($datetime);
        $request->validate([
            'instructor_id' => 'required',
            'class_id' => 'required',
            'topic' => 'required',
            'date' => 'required',
            'time' => 'required',
        ]);

        JitsiMeeting::updateOrCreate([
            'id' => $id
        ], [
            'topic' => $topic,
            'description' => $description,
            'date' => $date,
            'time' => $time,
            'instructor_id' => $instructor_id,
            'class_id' => $class_id,
            'datetime' => $datetime,
        ]);


        Toastr::success('Class updated successful', 'Success');
        return redirect()->route('jitsi.meetings');
    }

    public function destroy(int $id)
    {
        $meeting = JitsiMeeting::findOrFail($id);
        JitsiMeetingUser::where('meeting_id', $meeting->id)->delete();
        $meeting->delete();
        Toastr::success('Class Delete successful', 'Success');
        return redirect()->route('jitsi.meetings');
    }

    public function meetingStart($course_id, $meeting_id)
    {
        $course = Course::findOrFail($course_id);
        if (Auth::check() && $course->isLoginUserEnrolled) {

            $meeting = JitsiMeeting::where('meeting_id', $meeting_id)->first();
            $setting = JitsiSetting::getData();
            return view('jitsi::meeting.start', compact('meeting', 'setting'));

        } else {
            Toastr::error('Access Failed!', 'Failed');
            return redirect()->back();
        }
    }
}
