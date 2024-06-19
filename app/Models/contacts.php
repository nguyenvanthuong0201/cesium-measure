<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class contacts extends Model
{
    public $timestamps = false;
    protected $table = 'contacts';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'no_contact',
        'reporter_address',
        'reporter_name',
        'reporter_tel',
        'reception_department_id',
        'receptionist',
        'address',
        'subject',
        'contact_way_id',
        'contact_type_id',
        'contact_category_id',
        'contact_category_detail_id',
        'content',
        'lat',
        'lon',
    ];
}

