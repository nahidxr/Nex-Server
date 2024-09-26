<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scorm extends Model {

    use HasFactory;

    protected $table = 'scorm';
    protected $fillable = [
        'resource_type',
        'resource_id',
    ];
}
