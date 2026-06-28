<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Demo;
use App\Models\Lead;
use Illuminate\Database\Seeder;

class LeadSeeder extends Seeder
{
    public function run(): void
    {
        $leads = [
            [
                'name' => 'Ana García',
                'business_name' => 'Makeup by Ana',
                'sector' => 'Maquillaje',
                'city' => 'Vigo',
                'source' => 'instagram',
                'status' => Lead::STATUS_BUDGET_SENT,
                'potential' => 'high',
                'contacted_at' => now()->startOfWeek()->toDateString(),
                'next_follow_up_at' => now()->addDay()->setTime(11, 0),
                'notes' => 'Le interesa web profesional con WhatsApp directo.',
            ],
            [
                'name' => 'Clínica Fisio Norte',
                'business_name' => 'Fisio Norte',
                'sector' => 'Fisioterapia',
                'city' => 'Ponferrada',
                'source' => 'instagram',
                'status' => Lead::STATUS_DEMO_REQUESTED,
                'potential' => 'high',
                'contacted_at' => now()->subDays(2)->toDateString(),
                'next_follow_up_at' => now()->addHours(5),
                'notes' => 'Dolor principal: explicar servicios y recuperar confianza desde Google/Instagram.',
            ],
            [
                'name' => 'Taller Motor Bierzo',
                'business_name' => 'Motor Bierzo',
                'sector' => 'Taller mecánico',
                'city' => 'Ponferrada',
                'source' => 'google',
                'status' => Lead::STATUS_RESPONDED,
                'potential' => 'medium',
                'contacted_at' => now()->subDays(1)->toDateString(),
                'next_follow_up_at' => now()->addDays(3)->setTime(10, 30),
                'notes' => 'Preguntar si valora hacerlo ahora o solo verlo como idea.',
            ],
        ];

        foreach ($leads as $leadData) {
            $lead = Lead::create($leadData);

            Activity::create([
                'lead_id' => $lead->id,
                'type' => Activity::TYPE_RESPONSE,
                'occurred_at' => now()->subDay(),
                'notes' => 'Respuesta de prueba para estadísticas.',
            ]);
        }

        $demoLead = Lead::where('business_name', 'Fisio Norte')->first();

        if ($demoLead) {
            Demo::create([
                'lead_id' => $demoLead->id,
                'title' => 'Demo para clínica de fisioterapia',
                'due_at' => now()->addDay()->setTime(18, 0),
                'status' => Demo::STATUS_PENDING,
                'priority' => 'high',
                'notes' => 'Hacer hero + metodología + casos de recuperación + CTA WhatsApp.',
            ]);
        }
    }
}
