<?php

namespace App\Filament\Widgets;

use App\Models\Activity;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class WeeklyStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $start = now()->startOfWeek();
        $end = now()->endOfWeek();

        $contacts = $this->count(Activity::TYPE_CONTACT, $start, $end);
        $responses = $this->count(Activity::TYPE_RESPONSE, $start, $end);
        $demoRequests = $this->count(Activity::TYPE_DEMO_REQUESTED, $start, $end);
        $budgets = $this->count(Activity::TYPE_BUDGET_SENT, $start, $end);
        $wins = $this->count(Activity::TYPE_WON, $start, $end);

        $responseRate = $this->percentage($responses, $contacts);
        $demoRate = $this->percentage($demoRequests, max($responses, 1));
        $closeRate = $this->percentage($wins, max($budgets, 1));

        return [
            Stat::make('Contactos esta semana', $contacts)
                ->description('Desde el lunes')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('gray'),

            Stat::make('Respuestas', $responses)
                ->description($responseRate . '% sobre contactos')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('info'),

            Stat::make('Demos pedidas', $demoRequests)
                ->description($demoRate . '% sobre respuestas')
                ->descriptionIcon('heroicon-m-computer-desktop')
                ->color('warning'),

            Stat::make('Presupuestos', $budgets)
                ->description('Enviados esta semana')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),

            Stat::make('Ventas', $wins)
                ->description($closeRate . '% sobre presupuestos')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
        ];
    }

    private function count(string $type, mixed $start, mixed $end): int
    {
        return Activity::query()
            ->where('type', $type)
            ->whereBetween('occurred_at', [$start, $end])
            ->count();
    }

    private function percentage(int $part, int $total): int
    {
        if ($total <= 0) {
            return 0;
        }

        return (int) round(($part / $total) * 100);
    }
}
