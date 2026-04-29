<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;
    protected $fillable = [
        'submission_id', 'type', 'division_id', 'district_id', 'thana_id', 'union', 'location_details'
    ];

    public function submission() { return $this->belongsTo(Submission::class); }

    public function division() { return $this->belongsTo(Division::class); }
    public function district() { return $this->belongsTo(District::class); }
    public function thana()    { return $this->belongsTo(Thana::class); }
}
