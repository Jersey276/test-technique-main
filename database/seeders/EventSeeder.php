<?php

namespace Database\Seeders;

use App\Models\Event;
use DateTime;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!Event::exists()) {
            Event::factory()->count(100)->create();
        } else {

            foreach (Event::all() as $event) {
                $event->update([
                    'ends_at' => (new DateTime($event->starts_at))->modify('+'.rand(1, 720).' hours'),
                ]);
            }
        }
    }
}
