<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Course;
use App\Models\Attendance;
use App\Models\Alert;
use App\Models\Notification;
use App\Mail\AttendanceAlertEmail;
use Illuminate\Support\Facades\Mail;

class CheckAttendance extends Command
{
    protected $signature = 'attendance:check {courseId}';
    protected $description = 'Check and send attendance alerts for a specific course';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $courseId = $this->argument('courseId');
        $course = Course::findOrFail($courseId);

        $this->checkAlerts($course);
    }

    private function checkAlerts($course)
    {
        foreach ($course->students as $student) {
            $totalAbsences = Attendance::where('course_id', $course->id)
                                       ->where('student_id', $student->id)
                                       ->where('present', 0)
                                       ->count();

            $totalHours = $course->hours;
            $hoursPerClass = $course->hours_for_classes;
            $totalClasses = $totalHours / $hoursPerClass;
            $absencePercentage = ($totalAbsences / $totalClasses) * 100;

            $absencePercentage = (int) $absencePercentage;

            $alerts = Alert::all();
            foreach ($alerts as $alert) {
                if ($absencePercentage >= $alert->percentage_of_hours) {
                    $this->sendAlert($student, $course, $alert->message, $alert->percentage_of_hours);
                }
            }
        }
    }

    private function sendAlert($student, $course, $message, $percentage_of_hours)
    {
        $existingNotification = Notification::where('user_id', $student->id)
                                            ->where('course_id', $course->id)
                                            ->where('absence_percentage', $percentage_of_hours)
                                            ->exists();

        if (!$existingNotification) {
            $studentEmail = $student->email;
            Mail::to($studentEmail)->send(new AttendanceAlertEmail($message));

            $teacher = $course->teachers()->first();
            if ($teacher) {
                $teacherEmail = $teacher->email;
                Mail::to($teacherEmail)->send(new AttendanceAlertEmail($message));
            }

            Notification::create([
                'user_id' => $student->id,
                'role_id' => $student->role_id,
                'course_id' => $course->id,
                'absence_percentage' => $percentage_of_hours,
                'message' => $message,
                'date_sent' => now(),
                'created_by' => 1,
                'updated_by' => 1,
            ]);
        }
    }
}
