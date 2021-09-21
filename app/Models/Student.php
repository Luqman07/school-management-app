<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * Guardian relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function guardian()
    {
        return $this->belongsTo(Guardian::class);
    }

    /**
     * Classroom relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    /**
     * Result relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function results()
    {
        return $this->hasMany(Result::class);
    }

    /**
     * Pd relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pds()
    {
        return $this->hasMany(PD::class);
    }

    /**
     * AD relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ads()
    {
        return $this->hasMany(AD::class);
    }

    /**
     * Attendance relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * teacher remarks relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function teacherRemarks()
    {
        return $this->hasMany(TeacherRemark::class);
    }
    
    /**
     * Find student
     *
     * @param  string $admission_no
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function findStudent($admission_no)
    {

        $student = Student::where('admission_no', $admission_no);
        if (!$student->exists()) {
            abort(404);
        }

        return $student;
    }

    /**
     * Check if student is active
     *
     * @return boolean
     */
    public function isActive()
    {
        return $this->is_active == true;
    }

    /**
     * Get all students that have not graduated and are active
     *
     * @return mixed $students
     */
    public static function getAllStudents()
    {
        $students = Student::whereNull('graduated_at')->where('is_active', true)->get();
        return $students;
    }
    
    /**
     * Get Inactive Students
     *
     * @return mixed $students
     */
    public static function getInactiveStudents() 
    {
        $students = Student::where('is_active', false)->get();
        return $students;
    }

    /**
     * Get Alumni
     *
     * @return mixed $alumni
     */
    public static function getAlumni()
    {
        $alumni = Student::whereNotNull('graduated_at')->get();
        return $alumni;
    }


    /**
     * Check if student can graduate
     * 
     * Only Students in the highest class can graduate
     *
     * @return boolean
     */
    public function canGraduate()
    {
        $classRank = $this->classroom->rank;
        $highestClassRank = Classroom::max('rank');

        return $classRank == $highestClassRank;
    }


    /**
     * check if student is an alumni
     *
     * @return bool
     */
    public function hasGraduated()
    {
        return $this->graduated_at !== null;
    }
}
