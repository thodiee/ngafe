<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class JobVacancy extends Model {
    protected $fillable = ['title', 'description', 'requirements', 'status'];
    public function applications() { return $this->hasMany(JobApplication::class); }
}