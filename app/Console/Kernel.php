<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\Course;
use App\Models\Attendance;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(function () {
            $courses = Course::whereNotNull('first_attendance_time')
                             ->whereRaw('first_attendance_time <= now() - interval \'10 minutes\'')
                             ->get();

            foreach ($courses as $course) {
                $students = $course->students;

                foreach ($students as $student) {
                    $attendance = Attendance::where('course_id', $course->id)
                                            ->where('student_id', $student->id)
                                            ->whereDate('date', now()->toDateString())
                                            ->first();

                    if (!$attendance) {
                        Attendance::create([
                            'course_id' => $course->id,
                            'student_id' => $student->id,
                            'date' => now(),
                            'present' => false,
                            'created_by' => 1, // O el ID del usuario que hace la acción
                            'updated_by' => 1, // O el ID del usuario que hace la acción
                        ]);

                        // Aquí puedes agregar la lógica para enviar el correo
                        // Mail::to($student->email)->send(new AttendanceNotification($course, $student));
                    }
                }
            }
        })->everyMinute();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
