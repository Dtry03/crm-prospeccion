<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{
    use HasFactory;

    public const TYPE_CONTACT = 'contact';
    public const TYPE_RESPONSE = 'response';
    public const TYPE_DEMO_REQUESTED = 'demo_requested';
    public const TYPE_DEMO_SENT = 'demo_sent';
    public const TYPE_BUDGET_SENT = 'budget_sent';
    public const TYPE_WON = 'won';
    public const TYPE_LOST = 'lost';
    public const TYPE_FOLLOW_UP = 'follow_up';

    protected $fillable = [
        'lead_id',
        'type',
        'occurred_at',
        'notes',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
    ];

    public static function typeOptions(): array
    {
        return [
            self::TYPE_CONTACT => 'Contacto hecho',
            self::TYPE_RESPONSE => 'Respondió',
            self::TYPE_DEMO_REQUESTED => 'Pidió demo',
            self::TYPE_DEMO_SENT => 'Demo enviada',
            self::TYPE_BUDGET_SENT => 'Presupuesto enviado',
            self::TYPE_WON => 'Compra / cerrado',
            self::TYPE_LOST => 'Perdido',
            self::TYPE_FOLLOW_UP => 'Seguimiento',
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }
}
