<?php

namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\Fee;
use App\Models\Period;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class FeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = $this->allRecords();

        foreach ($data['classrooms'] as $classroom) {
            foreach ($data['periods'] as $period) {
                $record = Fee::where('classroom_id', $classroom->id)->where('period_id', $period->id);

                if ($record->exists()) continue;

                Fee::create([
                    'amount' => mt_rand(10000, 100000),
                    'classroom_id' => $classroom->id,
                    'period_id' => $period->id
                ]);
            }
        }
    }

    private function allRecords()
    {
        $period = Period::first();
        $classroom = Classroom::first();

        //if any of the required values are empty seed their tables
        if (!$period) Artisan::call('db:seed', ['--class' => 'PeriodSeeder']);
        if (!$classroom) Artisan::call('db:seed', ['--class' => 'ClassroomSeeder']);

        $periods = Period::all();
        $classrooms = Classroom::all();

        return [
            'periods' => $periods,
            'classrooms' => $classrooms,
        ];
    }
}
