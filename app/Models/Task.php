<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    /**
     * La clave primaria asociada con la tabla.
     *
     * @var string
     */
    protected $primaryKey = 'task_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'machine_id',
        'description',
        'responsible',
        'start_date',
        'end_date',
        'status',
        'urgency',
        'notes',
        'completion_date',
        'created_by',
        'updated_by'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'datetime',
        'end_date'   => 'datetime',
        'completion_date' => 'datetime',
    ];

    /**
     * Get the machine that this task belongs to.
     */
    public function machine(): BelongsTo
    {
        return $this->belongsTo(Machine::class, 'machine_id', 'machine_id');
    }

    /**
     * Get the user responsible for this task.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible');
    }
    
    /**
     * Get the status in Spanish.
     */
    public function getStatusTextAttribute()
    {
        $statusMap = [
            'pending'     => 'Pendiente',
            'in_progress' => 'En Progreso',
            'completed'   => 'Completada',
            'cancelled'   => 'Cancelada'
        ];
        
        return $statusMap[$this->status] ?? $this->status;
    }
    
    /**
     * Get the urgency in Spanish.
     */
    public function getUrgencyTextAttribute()
    {
        $urgencyMap = [
            'low'      => 'Baja',
            'medium'   => 'Media',
            'high'     => 'Alta',
            'critical' => 'Crítica'
        ];
        
        return $urgencyMap[$this->urgency] ?? $this->urgency;
    }
    
    /**
     * Get the urgency badge class.
     */
    public function getUrgencyBadgeAttribute()
    {
        $badgeMap = [
            'low'      => 'bg-success',
            'medium'   => 'bg-info',
            'high'     => 'bg-warning',
            'critical' => 'bg-danger'
        ];
        
        return $badgeMap[$this->urgency] ?? 'bg-secondary';
    }
    
    /**
     * Get the status badge class.
     */
    public function getStatusBadgeAttribute()
    {
        $badgeMap = [
            'pending'     => 'bg-warning',
            'in_progress' => 'bg-info',
            'completed'   => 'bg-success',
            'cancelled'   => 'bg-secondary'
        ];
        
        return $badgeMap[$this->status] ?? 'bg-secondary';
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = auth()->id();
        });

        static::updating(function ($model) {
            $model->updated_by = auth()->id();
        });
    }
}