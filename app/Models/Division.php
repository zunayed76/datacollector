<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory;
    // Allows us to use Division::create(['name' => 'Dhaka'])
    protected $fillable = ['name'];

    public function districts() 
    { 
        return $this->hasMany(District::class); 
    }
}
