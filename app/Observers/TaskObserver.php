<?php

namespace App\Observers;

use App\Models\Task;
use App\Services\EmailService;
use Illuminate\Support\Facades\Log;

class TaskObserver
{
    /**
     * Servicio de email
     *
     * @var EmailService
     */
    protected $emailService;
    
    /**
     * Constructor
     *
     * @param EmailService $emailService
     */
    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }
    
    /**
     * Handle the Task "created" event.
     *
     * @param  \App\Models\Task  $task
     * @return void
     */
    public function created(Task $task)
    {
        try {
            // No enviar invitaciones durante el seeding o en consola
            if (app()->runningInConsole()) {
                return;
            }
            
            // Enviar invitación por email
            $result = $this->emailService->sendTaskInvitation($task);
            
            if ($result['success']) {
                Log::info('Invitación de tarea enviada automáticamente', [
                    'task_id' => $task->task_id
                ]);
            } else {
                Log::warning('No se pudo enviar la invitación de tarea automáticamente', [
                    'task_id' => $task->task_id,
                    'reason' => $result['message']
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error al procesar envío de invitación en TaskObserver', [
                'task_id' => $task->task_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Handle the Task "updated" event.
     *
     * @param  \App\Models\Task  $task
     * @return void
     */
    public function updated(Task $task)
    {
        try {
            // No enviar invitaciones durante ejecuciones en consola
            if (app()->runningInConsole()) {
                return;
            }
            
            // Comprobar si cambió algo relevante para el calendario
            $relevantFieldsChanged = $task->isDirty([
                'description',
                'start_date',
                'end_date',
                'status',
                'urgency',
                'responsible',
                'machine_id'
            ]);
            
            // Solo enviar actualización si cambiaron campos relevantes
            if ($relevantFieldsChanged) {
                $result = $this->emailService->sendTaskInvitation($task);
                
                if ($result['success']) {
                    Log::info('Actualización de tarea enviada automáticamente', [
                        'task_id' => $task->task_id
                    ]);
                } else {
                    Log::warning('No se pudo enviar la actualización de tarea automáticamente', [
                        'task_id' => $task->task_id,
                        'reason' => $result['message']
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error al procesar actualización de invitación en TaskObserver', [
                'task_id' => $task->task_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}