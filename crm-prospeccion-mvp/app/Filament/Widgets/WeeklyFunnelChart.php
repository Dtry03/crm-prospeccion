<?php

namespace App\Filament\Widgets;

use App\Models\Activity;
use Filament\Widgets\ChartWidget;

class WeeklyFunnelChart extends ChartWidget
{
    protected static ?string $heading = 'Embudo semanal';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $start = now()->startOfWeek();
        $end = now()->endOfWeek();

        return [
            'datasets' => [
                [
                    'label' => 'Cantidad',
                    'data' => [
                        $this->count(Activity::TYPE_CONTACT, $start, $end),
                        $this->count(Activity::TYPE_RESPONSE, $start, $end),
                        $this->count(Activity::TYPE_DEMO_REQUESTED, $start, $end),
                        $this->count(Activity::TYPE_BUDGET_SENT, $start, $end),
                        $this->count(Activity::TYPE_WON, $start, $end),
                    ],
                ],
            ],
            'labels' => [
                'Contactos',
                'Respuestas',
                'Demos',
                'Presupuestos',
                'Ventas',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    private function count(string $type, mixed $start, mixed $end): int
    {
        return Activity::query()
            ->where('type', $type)
            ->whereBetween('occurred_at', [$start, $end])
            ->count();
    }
}
