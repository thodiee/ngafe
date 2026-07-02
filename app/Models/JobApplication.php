<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class JobApplication extends Model {
    protected $fillable = ['job_vacancy_id', 'applicant_name', 'email', 'phone_number', 'cv_path', 'status'];
    public function jobVacancy() { return $this->belongsTo(JobVacancy::class); }
}