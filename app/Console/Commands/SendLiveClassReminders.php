<?php

namespace App\Console\Commands;

use App\Notifications\GeneralNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use Modules\VirtualClass\Entities\VirtualClass;

class SendLiveClassReminders extends Command
{
    protected $signature = 'live-class:send-reminders';
    protected $description = 'Send a reminder 30 minutes before each live class';

    public function handle(): int
    {
        $timezone = Settings('active_time_zone') ?: config('app.timezone');
        $now = Carbon::now($timezone);

        VirtualClass::with(['course.user', 'course.enrollUsers', 'students'])
            ->whereBetween('start_date', [$now->toDateString(), $now->copy()->addDay()->toDateString()])
            ->get()
            ->each(function (VirtualClass $class) use ($now, $timezone) {
                if (!$class->start_date || !$class->time) {
                    return;
                }

                $startsAt = Carbon::parse($class->start_date . ' ' . $class->time, $timezone);
                $minutesUntilStart = $now->diffInMinutes($startsAt, false);

                if ($minutesUntilStart < 29 || $minutesUntilStart > 30) {
                    return;
                }

                $cacheKey = 'live-class-reminder:' . $class->id . ':' . $startsAt->format('YmdHi');
                if (!Cache::add($cacheKey, true, $startsAt->copy()->addHours(2))) {
                    return;
                }

                $students = $class->students->isNotEmpty()
                    ? $class->students
                    : $class->course->enrollUsers;
                $recipients = $students->push($class->course->user)->filter()->unique('id');

                Notification::send($recipients, new GeneralNotification([
                    'title' => 'تذكير بحصة مباشرة',
                    'body' => 'ستبدأ حصة «' . $class->title . '» بعد 30 دقيقة، في ' . $startsAt->format('h:i A') . '.',
                    'actionText' => 'عرض الحصة',
                    'actionURL' => url('/classes'),
                    'notification_type' => 'live_class_reminder',
                ]));
            });

        return self::SUCCESS;
    }
}
