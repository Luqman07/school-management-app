<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUserIsStudentClassroomTeacherOrUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth('teacher')->check()) {

            //if request teacher doesn't have a classroom
            if (is_null($request->user()->branchClassroom)) abort(403);

            //check if authenticated teacher is the classteacher of the classroom to be viewed
            if (!$request->user()->branchClassroom?->id === $request->route('student')->branchClassroom?->id) abort(403);
        }

        return $next($request);
    }
}
