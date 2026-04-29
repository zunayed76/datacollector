<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    use HasFactory;
    protected $fillable = [
        'submission_id', 'degree', 'institute', 'board', 
        'grade', 'passing_year', 'certificate'
    ];
    public function submission() { return $this->belongsTo(Submission::class); }
}
