<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class contact_histories extends Model
{
    public $timestamps = false;
    protected $table = 'contact_histories';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'contact_id',
        'receptionist',
        'content',
        'created_at',
        'updated_at',
    ];
}

