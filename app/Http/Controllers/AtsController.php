<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobVacancy;
use App\Models\JobApplication;

class AtsController extends Controller
{
    public function jobs() {
        $jobs = JobVacancy::where('status', 'published')->get();
        return view('careers.jobs', compact('jobs'));
    }

    public function apply(Request $request) {
        $request->validate([
            'job_vacancy_id' => 'required|exists:job_vacancies,id',
            'applicant_name' => 'required|string',
            'email' => 'required|email',
            'phone_number' => 'required|string',
            'cv' => 'required|file|mimes:pdf|max:2048'
        ]);

        $cvPath = $request->file('cv')->store('cvs', 'public');

        JobApplication::create([
            'job_vacancy_id' => $request->job_vacancy_id,
            'applicant_name' => $request->applicant_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'cv_path' => $cvPath,
            'status' => 'submitted'
        ]);

        return back()->with('success', 'Lamaran berhasil dikirim');
    }
}