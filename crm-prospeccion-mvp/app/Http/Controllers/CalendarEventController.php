<?php

namespace App\Http\Controllers;

use App\Filament\Resources\DemoResource;
use App\Filament\Resources\LeadResource;
use App\Models\Demo;
use App\Models\Lead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CalendarEventController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $start = $request->date('start')?->startOfDay() ?? now()->subMonth()->startOfDay();
        $end = $request->date('end')?->endOfDay() ?? now()->addMonth()->endOfDay();

        $events = collect()
            ->merge($this->demoEvents($start, $end))
            ->merge($this->followUpEvents($start, $end))
            ->values();

        return response()->json($events);
    }

    private function demoEvents(mixed $start, mixed $end): Collection
    {
        return Demo::query()
            ->with('lead')
            ->whereBetween('due_at', [$start, $end])
            ->get()
            ->map(function (Demo $demo): array {
                $clientName = $demo->lead?->name ?? 'Cliente sin nombre';
                $status = Demo::statusOptions()[$demo->status] ?? $demo->status;

                return [
                    'id' => 'demo-' . $demo->id,
                    'title' => 'Demo: ' . $clientName . ' · ' . $status,
                    'start' => $demo->due_at->toIso8601String(),
                    'url' => DemoResource::getUrl('edit', ['record' => $demo]),
                    'backgroundColor' => match ($demo->status) {
                        Demo::STATUS_PENDING => '#f59e0b',
                        Demo::STATUS_IN_PROGRESS => '#0ea5e9',
                        Demo::STATUS_DONE => '#6366f1',
                        Demo::STATUS_SENT => '#22c55e',
                        default => '#64748b',
                    },
                    'borderColor' => 'transparent',
                ];
            });
    }

    private function followUpEvents(mixed $start, mixed $end): Collection
    {
        return Lead::query()
            ->whereNotNull('next_follow_up_at')
            ->whereBetween('next_follow_up_at', [$start, $end])
            ->get()
            ->map(function (Lead $lead): array {
                return [
                    'id' => 'followup-' . $lead->id,
                    'title' => 'Seguimiento: ' . $lead->name,
                    'start' => $lead->next_follow_up_at->toIso8601String(),
                    'url' => LeadResource::getUrl('edit', ['record' => $lead]),
                    'backgroundColor' => $lead->next_follow_up_at->isPast() ? '#ef4444' : '#8b5cf6',
                    'borderColor' => 'transparent',
                ];
            });
    }
}
