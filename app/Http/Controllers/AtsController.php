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
        $validated = $request->validate([
            'job_vacancy_id' => 'required|exists:job_vacancies,id',
            'applicant_name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'phone_number' => 'required|string|max:25',
            'cv' => 'required|file|mimes:pdf|mimetypes:application/pdf|max:2048'
        ]);

        $fileName = \Illuminate\Support\Str::random(40) . '.pdf';
        $cvPath = $request->file('cv')->storeAs('cvs', $fileName, 'local');

        JobApplication::create([
            'job_vacancy_id' => $validated['job_vacancy_id'],
            'applicant_name' => strip_tags(trim($validated['applicant_name'])),
            'email' => strtolower(trim($validated['email'])),
            'phone_number' => strip_tags(trim($validated['phone_number'])),
            'cv_path' => $cvPath,
            'status' => 'submitted'
        ]);

        return back()->with('success', 'Lamaran berhasil dikirim');
    }
}