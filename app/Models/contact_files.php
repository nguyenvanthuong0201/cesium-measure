<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class contact_files extends Model
{
    public $timestamps = false;
    protected $table = 'contact_files';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'contact_id',
        'name_file',
        'created_at',
        'updated_at',
    ];
}

