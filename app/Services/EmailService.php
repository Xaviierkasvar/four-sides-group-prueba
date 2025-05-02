<?php

namespace App\Services;

use App\Models\Task;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class EmailService
{
    /**
     * Clave de API de EmailJS
     *
     * @var string
     */
    protected $apiKey;
    
    /**
     * ID del servicio de EmailJS
     *
     * @var string
     */
    protected $serviceId;
    
    /**
     * ID de la plantilla de EmailJS
     *
     * @var string
     */
    protected $templateId;
    
    /**
     * URL de la API de EmailJS
     *
     * @var string
     */
    protected $apiUrl = 'https://api.emailjs.com/api/v1.0/email/send';
    
    /**
     * Servicio para generar archivos ICS
     *
     * @var ICalendarService
     */
    protected $calendarService;
    
    /**
     * Constructor
     *
     * @param ICalendarService $calendarService
     */
    public function __construct(ICalendarService $calendarService)
    {
        $this->apiKey = config('services.emailjs.api_key');
        $this->serviceId = config('services.emailjs.service_id');
        $this->templateId = config('services.emailjs.template_id');
        $this->calendarService = $calendarService;
    }
    
    /**
     * Enviar invitación de tarea por email
     *
     * @param Task $task
     * @return array
     */
    public function sendTaskInvitation(Task $task)
    {
        try {
            // Cargar las relaciones necesarias
            $task->load(['machine.client', 'user']);
            
            // Verificar que el usuario tenga email
            if (!$task->user || !$task->user->email) {
                throw new Exception("El usuario responsable no tiene un correo electrónico válido.");
            }
            
            // Obtener los datos relacionados
            $machine = $task->machine;
            $client = $machine->client;
            $user = $task->user;
            
            // Obtener los textos de urgencia y estado
            $urgencyText = $task->getUrgencyTextAttribute();
            $statusText = $task->getStatusTextAttribute();
            
            // Preparar los datos exactamente igual que en tu ejemplo de Postman
            $templateParams = [
                'user_name' => $user->name,
                'user_email' => $user->email,
                'task_description' => $task->description,
                'task_start_date' => $task->start_date->format('d/m/Y H:i'),
                'task_end_date' => $task->end_date->format('d/m/Y H:i'),
                'task_urgency' => $urgencyText,
                'task_status' => $statusText,
                'machine_name' => $machine->name,
                'machine_serial' => $machine->serial,
                'client_name' => $client->client_name,
                'client_address' => $client->full_address ?? 'No disponible',
                'client_city' => $client->city ?? 'No disponible',
                'client_contact' => trim($client->contact_first_name . ' ' . $client->contact_last_name) ?: 'No disponible',
                'client_phone' => $client->contact_phone ?? 'No disponible',
                'client_email' => $client->contact_email ?? 'No disponible'
                // Los siguientes parámetros están comentados hasta que se active el plan premium
                // 'ics_file' => $icsBase64,
                // 'ics_filename' => 'tarea_' . $task->task_id . '.ics'
            ];
            
            // Registrar lo que estamos enviando para depuración
            Log::debug('Enviando solicitud a EmailJS', [
                'service_id' => $this->serviceId,
                'template_id' => $this->templateId,
                'user_id' => $this->apiKey,
                'template_params' => $templateParams
            ]);
            
            // Enviar el email exactamente como en Postman
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Origin' => 'https://www.emailjs.com',
                'Referer' => 'https://www.emailjs.com',
            ])->post($this->apiUrl, [
                'service_id' => $this->serviceId,
                'template_id' => $this->templateId,
                'user_id' => $this->apiKey,
                'template_params' => $templateParams
            ]);
            
            // Registrar la respuesta para depuración
            Log::debug('Respuesta de EmailJS', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            
            // Verificar la respuesta
            if ($response->successful()) {
                Log::info('Notificación de tarea enviada por email', [
                    'task_id' => $task->task_id,
                    'recipient' => $user->email
                ]);
                
                return [
                    'success' => true,
                    'message' => 'Notificación enviada correctamente a ' . $user->email
                ];
            } else {
                Log::error('Error al enviar notificación de tarea por email', [
                    'task_id' => $task->task_id,
                    'response' => $response->body()
                ]);
                
                return [
                    'success' => false,
                    'message' => 'Error al enviar la notificación: ' . $response->body()
                ];
            }
        } catch (Exception $e) {
            Log::error('Excepción al enviar notificación de tarea por email', [
                'task_id' => $task->task_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Error al enviar la notificación: ' . $e->getMessage()
            ];
        }
    }
}