<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;
    protected $fillable = ['submission_id', 'language_name', 'proficiency_level'];
    public function submission() { return $this->belongsTo(Submission::class); }
}
