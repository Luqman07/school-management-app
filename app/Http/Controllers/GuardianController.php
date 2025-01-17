<?php

namespace App\Http\Controllers;

use App\Models\Guardian;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GuardianController extends Controller
{
    /**
     * Show edit guardian page
     *
     * @param  Guardian $guardian
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function edit(Guardian $guardian)
    {
        return view('guardian.edit', compact(['guardian']));
    }

    /**
     * Update guardian
     *
     * @param  Guardian $guardian
     * @param  Request $request
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function update(Guardian $guardian, Request $request)
    {

        $data = $request->validate([
            'title' => ['required', 'max:30', 'string'],
            'first_name' => ['required', 'max:30', 'string'],
            'last_name' => ['required', 'max:30', 'string'],
            'email' => ['required', 'string', 'email:rfc,dns', Rule::unique('guardians')->ignore($guardian)],
            'phone' => ['required', 'string', 'between:10,15', Rule::unique('guardians')->ignore($guardian)],
            'occupation' => ['required', 'string'],
            'address' => ['required']
        ]);

        $guardian = $guardian->update($data);

        $guardian = Guardian::where('phone', $request->phone)->first();

        return redirect()->route('guardian.edit', ['guardian' => $guardian])->with('success', 'Guardian updated!');
    }
    
    /**
     * Change student's guardian
     *
     * @param  Student $student
     * @param  Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeGuardian(Student $student, Request $request)
    {
        $data = $request->validate([
            'guardian' => ['required', 'string']
        ]);

        $currentGuardian = $student->guardian;
        $newGuardian = Guardian::where('email', $data['guardian'])->first();

        if ($currentGuardian->id == $newGuardian->id) return back()->with('error', "The selected guardian is already the student's guardian");

        $student->guardian_id = $newGuardian->id;
        $student->save();

        // Delete current guardian if it no longer has any children
        if (count($currentGuardian->children) < 1) $currentGuardian->delete();

        return back()->with('success', "Guardian changed!");
    }
}
