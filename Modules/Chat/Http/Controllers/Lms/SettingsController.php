<?php


namespace Modules\Chat\Http\Controllers\Lms;


use App\Events\ClassTeacherGetAllStudent;
use App\Events\CreateClassGroupChat;
use App\Events\OneToOneConnection;
use App\SmAssignClassTeacher;
use App\SmAssignSubject;
use App\SmClassTeacher;
use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\CourseSetting\Entities\Course;

class SettingsController extends Controller
{
    public function chatSettings(Request $request){
        if (demoCheck()) {
            return redirect()->back();
        }
        try {
            UpdateGeneralSetting('chat_file_limit', $request->file_upload_limit);
            UpdateGeneralSetting('chat_can_upload_file' , $request->can_upload_file);
            UpdateGeneralSetting('chat_file_limit', $request->file_upload_limit);

            Toastr::success('Settings successfully updated!');
            return redirect()->back();
        }catch (\Exception $e){
            Toastr::error('Oops! Something went wrong!');
            return redirect()->back();
        }
    }

    public function chatPermissionStore(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        try {
            UpdateGeneralSetting('chat_everyone_to_everyone', $request->everyone_to_everyone);
            UpdateGeneralSetting('chat_admin_can_chat_without_invitation', $request->admin_can_chat_without_invitation);
            UpdateGeneralSetting('chat_open', $request->open_chat_system);

            Toastr::success('Settings successfully updated!','Success');
            return redirect()->back();
        }catch (\Exception $e){
            Toastr::error($e->getMessage());
            return redirect()->back();
        }
    }

    public function generate($type)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        try {
            $courses = Course::whereNull('quiz_id', )->get();

            foreach ($courses as $assignCourses){
                $instructor = User::find($assignCourses->user_id);
                foreach ($assignCourses->enrollUsers as $student){
                    event(new OneToOneConnection($instructor, $student,$assignCourses));
                }
            }

            UpdateGeneralSetting('chat_generate', 'generated');

            Toastr::success('Data successfully populated!');
            return redirect()->back();
        }catch (\Exception $exception){
            Toastr::error($exception->getMessage());
            return redirect()->back();
        }
    }
}
