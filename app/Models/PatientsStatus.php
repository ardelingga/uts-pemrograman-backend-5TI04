<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientsStatus extends Model
{
    use HasFactory;
    protected $fillable = ['patient_id', 'status_id', 'date_in', 'date_out'];
}
