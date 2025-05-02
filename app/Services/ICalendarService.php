<?php

namespace App\Services;

use App\Models\Task;
use Illuminate\Support\Str;

class ICalendarService
{
    /**
     * Generar un archivo ICS para una tarea
     *
     * @param Task $task
     * @return string
     */
    public function generateICS(Task $task)
    {
        // Cargar las relaciones necesarias
        $task->load(['machine.client', 'user']);
        
        // Obtener datos relacionados
        $machine = $task->machine;
        $client = $machine->client;
        $user = $task->user;
        
        // Formatear las fechas en formato UTC para ICS
        $startDate = $task->start_date->format('Ymd\THis\Z');
        $endDate = $task->end_date->format('Ymd\THis\Z');
        
        // Crear un identificador único para el evento
        $uid = (string) Str::uuid();
        
        // Formatear la descripción con todos los detalles
        $description = $this->buildEventDescription($task, $machine, $client);
        
        // Formatear la ubicación
        $location = '';
        if ($client && $client->full_address) {
            $location = $client->full_address;
            if ($client->city) {
                $location .= ", {$client->city}";
            }
        }
        
        // Formatear en texto seguro para ICS (escapar caracteres)
        $summary = $this->escapeString("Tarea: {$task->description}");
        $description = $this->escapeString($description);
        $location = $this->escapeString($location);
        
        // Construir el archivo ICS
        $ics = "BEGIN:VCALENDAR\r\n";
        $ics .= "VERSION:2.0\r\n";
        $ics .= "PRODID:-//FCMAQUINAS//TAREA//ES\r\n";
        $ics .= "CALSCALE:GREGORIAN\r\n";
        $ics .= "METHOD:REQUEST\r\n"; // REQUEST para invitaciones, PUBLISH para solo información
        
        // Evento
        $ics .= "BEGIN:VEVENT\r\n";
        $ics .= "UID:{$uid}\r\n";
        $ics .= "DTSTAMP:" . gmdate('Ymd\THis\Z') . "\r\n";
        $ics .= "DTSTART:{$startDate}\r\n";
        $ics .= "DTEND:{$endDate}\r\n";
        $ics .= "SUMMARY:{$summary}\r\n";
        
        // Solo añadir estos campos si tienen contenido
        if (!empty($description)) {
            $ics .= "DESCRIPTION:{$description}\r\n";
        }
        if (!empty($location)) {
            $ics .= "LOCATION:{$location}\r\n";
        }
        
        // Prioridad basada en la urgencia de la tarea
        switch ($task->urgency) {
            case 'critical':
                $ics .= "PRIORITY:1\r\n"; // Alta prioridad
                break;
            case 'high':
                $ics .= "PRIORITY:3\r\n"; // Prioridad media-alta
                break;
            case 'medium':
                $ics .= "PRIORITY:5\r\n"; // Prioridad media
                break;
            default:
                $ics .= "PRIORITY:9\r\n"; // Baja prioridad
                break;
        }
        
        // Estado basado en el estado de la tarea
        switch ($task->status) {
            case 'completed':
                $ics .= "STATUS:COMPLETED\r\n";
                break;
            case 'cancelled':
                $ics .= "STATUS:CANCELLED\r\n";
                break;
            case 'in_progress':
                $ics .= "STATUS:IN-PROCESS\r\n";
                break;
            default:
                $ics .= "STATUS:CONFIRMED\r\n";
                break;
        }
        
        // Alarma/Recordatorio (24 horas antes)
        $ics .= "BEGIN:VALARM\r\n";
        $ics .= "ACTION:DISPLAY\r\n";
        $ics .= "DESCRIPTION:Recordatorio\r\n";
        $ics .= "TRIGGER:-P1D\r\n"; // 1 día antes
        $ics .= "END:VALARM\r\n";
        
        $ics .= "END:VEVENT\r\n";
        $ics .= "END:VCALENDAR\r\n";
        
        return $ics;
    }
    
    /**
     * Construir la descripción detallada del evento
     *
     * @param Task $task
     * @param Machine $machine
     * @param Client $client
     * @return string
     */
    private function buildEventDescription($task, $machine, $client)
    {
        $urgencyText = $task->getUrgencyTextAttribute();
        $statusText = $task->getStatusTextAttribute();
        
        $description = "DETALLES DE LA TAREA:\n";
        $description .= "------------------------\n";
        $description .= "Descripción: {$task->description}\n";
        $description .= "Prioridad: {$urgencyText}\n";
        $description .= "Estado: {$statusText}\n";
        
        if (!empty($task->notes)) {
            $description .= "Notas: {$task->notes}\n";
        }
        
        $description .= "\nDATOS DEL EQUIPO:\n";
        $description .= "------------------------\n";
        $description .= "Equipo: {$machine->name}\n";
        $description .= "Serial: {$machine->serial}\n";
        
        $description .= "\nDATOS DEL CLIENTE:\n";
        $description .= "------------------------\n";
        $description .= "Cliente: {$client->client_name}\n";
        
        if (!empty($client->full_address)) {
            $description .= "Dirección: {$client->full_address}\n";
        }
        
        if (!empty($client->city)) {
            $description .= "Ciudad: {$client->city}\n";
        }
        
        if (!empty($client->contact_phone)) {
            $description .= "Teléfono: {$client->contact_phone}\n";
        }
        
        if (!empty($client->contact_email)) {
            $description .= "Email: {$client->contact_email}\n";
        }
        
        if (!empty($client->contact_first_name) || !empty($client->contact_last_name)) {
            $contactName = trim("{$client->contact_first_name} {$client->contact_last_name}");
            $description .= "Contacto: {$contactName}\n";
        }
        
        return $description;
    }
    
    /**
     * Escapar caracteres para el formato ICS
     *
     * @param string $string
     * @return string
     */
    private function escapeString($string)
    {
        // Reemplazar caracteres especiales y saltos de línea según la especificación iCalendar
        $string = str_replace("\n", "\\n", $string);
        $string = str_replace(",", "\\,", $string);
        $string = str_replace(";", "\\;", $string);
        $string = str_replace("\\", "\\\\", $string);
        
        // Envolver texto largo según especificación (75 caracteres por línea)
        $string = wordwrap($string, 75, "\r\n ", true);
        
        return $string;
    }
}