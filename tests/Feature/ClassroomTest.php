<?php

namespace Tests\Feature;

use App\Models\Classroom;
use App\Models\Period;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Database\Seeders\SubjectSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ClassroomTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_classroom_index_method()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('classroom.index'));
        $response->assertStatus(200);
    }

    public function test_classroom_can_be_stored()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post(route('classroom.store'), [
            'name' => $this->faker->word
        ]);
        $response->assertStatus(302)->assertSessionHas('success')->assertSessionHasNoErrors();
    }

    public function test_classroom_edit_method()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $classroom = Classroom::factory()->create();
        $response = $this->actingAs($user)->get(route('classroom.edit', ['classroom' => $classroom]));
        $response->assertStatus(200);
    }

    public function test_classroom_update_method()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $this->seed('ClassroomSeeder');
        $classrooms = Classroom::all();
        $classroom = $classrooms->random();
        $maxRank = $classrooms->max('rank');
        $minRank = $classrooms->min('rank');
        $rank = mt_rand($minRank, $maxRank);
        $response = $this->actingAs($user)->patch(route('classroom.update', ['classroom' => $classroom]), [
            'name' => $this->faker->word,
            'rank' => $rank
        ]);
        $response->assertStatus(302)->assertSessionHas('success')->assertSessionHasNoErrors();
    }

    public function test_user_can_delete_a_classroom()
    {
        $user = User::factory()->create();
        $classroom = Classroom::factory()->create();
        $response = $this->actingAs($user)->delete(route('classroom.destroy', ['classroom' => $classroom]));
        $response->assertStatus(302)->assertSessionHas('success');
    }

    public function test_user_cannot_delete_a_classroom_with_relations()
    {
        $user = User::factory()->create();
        $classroom = Classroom::factory()->create();
        Student::factory()->create(['classroom_id' => $classroom->id]);
        $response = $this->actingAs($user)->delete(route('classroom.destroy', ['classroom' => $classroom]));
        $response->assertStatus(302)->assertSessionHas('error');
    }

    public function test_classroom_subjects_can_be_updated()
    {
        $user = User::factory()->create();
        $subjects = $this->generateTestSubjects();
        $classroom = Classroom::factory()->create();

        Period::factory()->create(['active' => true]);
        $response = $this->actingAs($user)->post(route('classroom.update.subjects', ['classroom' => $classroom]), [
            'subjects' => $subjects
        ]);

        $response->assertStatus(302)->assertSessionHas('success');
    }

    public function test_classroom_subjects_can_be_set()
    {
        $classroom = Classroom::factory()->create();
        $user = User::factory()->create();
        Period::factory()->create(['active' => true]);
        $response = $this->actingAs($user)->get(route('classroom.set.subjects', ['classroom' => $classroom]));

        $response->assertStatus(200);
    }

    public function test_user_can_promote_students()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        Artisan::call('db:seed', ['--class' => 'ClassroomSeeder']);
        $classroom = Classroom::first();
        $students = Student::factory()->times(5)->create(['classroom_id' => $classroom->id])->pluck('id');
        $response = $this->actingAs($user)->post(
            route('classroom.promote.students', ['classroom' => $classroom]),
            ['students' => $students]

        );
        $response->assertStatus(302)->assertSessionHas('success');
    }

    public function test_user_can_demote_students()
    {
        $user = User::factory()->create();
        Artisan::call('db:seed', ['--class' => 'ClassroomSeeder']);
        $classroom = Classroom::where('rank', 3)->first();
        $students = Student::factory()->times(5)->create(['classroom_id' => $classroom->id])->pluck('id');
        $response = $this->actingAs($user)->post(
            route('classroom.demote.students', ['classroom' => $classroom]),
            ['students' => $students]

        );
        $response->assertStatus(302)->assertSessionHas('success');
    }

    private function generateTestSubjects()
    {
        $subjects = Subject::pluck('name')->all();

        //if subject table is empty run subjectSeeder
        if (sizeof($subjects) < 1) {
            $this->seed(SubjectSeeder::class);
            $subjects = Subject::pluck('name')->all();
        }

        $selectedSubjects = Arr::random($subjects, 5);
        return $selectedSubjects;
    }
}
