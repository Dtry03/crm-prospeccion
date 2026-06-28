<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    use HasFactory;

    public const STATUS_CONTACTED = 'contacted';
    public const STATUS_RESPONDED = 'responded';
    public const STATUS_DEMO_REQUESTED = 'demo_requested';
    public const STATUS_DEMO_PENDING = 'demo_pending';
    public const STATUS_DEMO_SENT = 'demo_sent';
    public const STATUS_BUDGET_SENT = 'budget_sent';
    public const STATUS_FOLLOW_UP = 'follow_up';
    public const STATUS_WON = 'won';
    public const STATUS_LOST = 'lost';
    public const STATUS_NO_RESPONSE = 'no_response';

    protected $fillable = [
        'name',
        'business_name',
        'sector',
        'city',
        'source',
        'contact_url',
        'phone',
        'email',
        'status',
        'potential',
        'contacted_at',
        'next_follow_up_at',
        'notes',
    ];

    protected $casts = [
        'contacted_at' => 'date',
        'next_follow_up_at' => 'datetime',
    ];

    public static function statusOptions(): array
    {
        return [
            self::STATUS_CONTACTED => 'Contactado',
            self::STATUS_RESPONDED => 'Respondió',
            self::STATUS_DEMO_REQUESTED => 'Pidió demo',
            self::STATUS_DEMO_PENDING => 'Demo pendiente',
            self::STATUS_DEMO_SENT => 'Demo enviada',
            self::STATUS_BUDGET_SENT => 'Presupuesto enviado',
            self::STATUS_FOLLOW_UP => 'Seguimiento',
            self::STATUS_WON => 'Cerrado ganado',
            self::STATUS_LOST => 'Cerrado perdido',
            self::STATUS_NO_RESPONSE => 'No responde',
        ];
    }

    public static function sourceOptions(): array
    {
        return [
            'instagram' => 'Instagram',
            'google' => 'Google',
            'manual' => 'Manual',
            'referral' => 'Recomendación',
            'other' => 'Otro',
        ];
    }

    public static function potentialOptions(): array
    {
        return [
            'low' => 'Bajo',
            'medium' => 'Medio',
            'high' => 'Alto',
        ];
    }

    public function demos(): HasMany
    {
        return $this->hasMany(Demo::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    protected static function booted(): void
    {
        static::created(function (Lead $lead): void {
            Activity::create([
                'lead_id' => $lead->id,
                'type' => Activity::TYPE_CONTACT,
                'occurred_at' => $lead->contacted_at?->startOfDay() ?? now(),
                'notes' => 'Contacto registrado al crear el cliente.',
            ]);
        });

        static::updated(function (Lead $lead): void {
            if (! $lead->wasChanged('status')) {
                return;
            }

            $map = [
                self::STATUS_RESPONDED => Activity::TYPE_RESPONSE,
                self::STATUS_DEMO_REQUESTED => Activity::TYPE_DEMO_REQUESTED,
                self::STATUS_DEMO_SENT => Activity::TYPE_DEMO_SENT,
                self::STATUS_BUDGET_SENT => Activity::TYPE_BUDGET_SENT,
                self::STATUS_FOLLOW_UP => Activity::TYPE_FOLLOW_UP,
                self::STATUS_WON => Activity::TYPE_WON,
                self::STATUS_LOST => Activity::TYPE_LOST,
            ];

            $activityType = $map[$lead->status] ?? null;

            if ($activityType === null) {
                return;
            }

            Activity::create([
                'lead_id' => $lead->id,
                'type' => $activityType,
                'occurred_at' => now(),
                'notes' => 'Actividad automática por cambio de estado a: ' . (self::statusOptions()[$lead->status] ?? $lead->status),
            ]);
        });
    }
}
