<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\Classroom;
use App\Models\Period;
use App\Models\Result;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ResultTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_store_results()
    {
        $this->withoutExceptionHandling();
        //create an academic session for the test
        Period::factory()->create(['active' => true]);

        $user = User::factory()->create();
        $student = Student::factory()->create();
        $subject = Subject::factory()->create();

        $response = $this->actingAs($user)->post(route('result.store', ['student' => $student]), [
            'ca' => mt_rand(0, 40),
            'exam' => mt_rand(0, 60),
            'subject' => $subject->name
        ]);

        $response->assertStatus(302);
    }

    public function test_user_can_store_results_with_one_assessment()
    {

        Period::factory()->create(['active' => true]);

        $user = User::factory()->create();
        $student = Student::factory()->create();
        $subject = Subject::factory()->create();

        $response = $this->actingAs($user)->post(route('result.store', ['student' => $student]), [
            'ca' => mt_rand(0, 40),
            'subject' => $subject->name
        ]);

        $response->assertStatus(302);
    }

    public function test_result_create_method()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $student = Student::factory()->create();

        //seed a current acaaemic session
        Period::factory()->create(['active' => true]);

        $response = $this->actingAs($user)->get(route('result.create', ['student' => $student]));

        $response->assertStatus(200);
    }

    public function test_result_edit_method()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $result = Result::factory()->create();
        $response = $this->actingAs($user)->get(route('result.edit', ['result' => $result]));

        $response->assertStatus(200);
    }

    public function test_result_destroy_method()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $result = Result::factory()->create();
        $response = $this->actingAs($user)->delete(route('result.destroy', ['result' => $result]));
        $response->assertStatus(302)->assertSessionHas('success');
    }

    public function test_show_performance_report_method()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create(['is_hos' => true]);
        $result = Result::factory()->create();

        //seed a subject to the classroom
        $classroom = $result->student->classroom;
        $subject = Subject::factory()->create();
        $data = [$subject->id => ['academic_session_id' => $result->period->academicSession->id]];
        $classroom->subjects()->attach($data);

        $response = $this->actingAs($user)->get(route('result.show.performance', ['student' => $result->student, 'periodSlug' => $result->period->slug]));
        $response->assertStatus(200);
    }

    public function test_result_update_method()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $result = Result::factory()->create();
        session(['resultsPage' => route('result.edit', ['result' => $result])]);
        $response = $this->actingAs($user)->patch(route('result.update', ['result' => $result]), [
            'ca' => mt_rand(0, 40),
            'exam' => mt_rand(0, 60)
        ]);

        $response->assertStatus(302)->assertSessionHas('success');
    }

    public function test_classroom_performance_report_email()
    {
        // $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $classroom = Classroom::factory()->create();
        $students = Student::factory()->times(10)->create(['classroom_id' => $classroom->id]);
        $period = Period::factory()->create(['active' => true]);
        $subject = Subject::factory()->create();

        $students->map(
            function ($student) use ($classroom, $period, $subject) {
                $ca = mt_rand(0, 40);
                $exam = mt_rand(0, 60);
                Result::create([
                    'subject_id' => $subject->id,
                    'classroom_id' => $classroom->id,
                    'student_id' => $student->id,
                    'period_id' => $period->id,
                    'ca' => $ca,
                    'exam' => $exam,
                    'total' => $exam + $ca,
                ]);
            }
        );
        $response = $this->actingAs($user)->get(route('email.class.performace.report', ['classroom' => $classroom->id]));
        $response->assertStatus(302);
    }
}
