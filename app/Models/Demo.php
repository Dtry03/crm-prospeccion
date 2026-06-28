<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Demo extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_DONE = 'done';
    public const STATUS_SENT = 'sent';

    protected $fillable = [
        'lead_id',
        'title',
        'due_at',
        'status',
        'priority',
        'demo_url',
        'video_sent',
        'notes',
    ];

    protected $casts = [
        'due_at' => 'datetime',
        'video_sent' => 'boolean',
    ];

    public static function statusOptions(): array
    {
        return [
            self::STATUS_PENDING => 'Pendiente',
            self::STATUS_IN_PROGRESS => 'En proceso',
            self::STATUS_DONE => 'Hecha',
            self::STATUS_SENT => 'Enviada',
        ];
    }

    public static function priorityOptions(): array
    {
        return [
            'low' => 'Baja',
            'medium' => 'Media',
            'high' => 'Alta',
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    protected static function booted(): void
    {
        static::created(function (Demo $demo): void {
            if ($demo->lead === null) {
                return;
            }

            $demo->lead->updateQuietly([
                'status' => Lead::STATUS_DEMO_PENDING,
            ]);
        });

        static::updated(function (Demo $demo): void {
            if (! $demo->wasChanged('status') || $demo->status !== self::STATUS_SENT || $demo->lead === null) {
                return;
            }

            $demo->lead->update([
                'status' => Lead::STATUS_DEMO_SENT,
            ]);
        });
    }
}
