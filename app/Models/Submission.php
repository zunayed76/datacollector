<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Submission extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 
        'name', 
        'fathers_name', 
        'mothers_name', 
        'nid_number', 
        'nid_file'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function educations(): HasMany
    {
        return $this->hasMany(Education::class);
    }

    public function languages(): HasMany
    {
        return $this->hasMany(Language::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }
}