<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class facilities extends Model
{
    public $timestamps = false;
    protected $table = 'facilities';
    protected $primaryKey = 'id';
    protected $fillable = [
        "id",
        "office_id",
        "road_type_id",
        "road_no",
        "name",
        "label",
        "attribute",
        "x",
        "y",
        "z",
        "lat",
        "lon",
        "created_at",
        "updated_at",
        "facility_type_id"
    ];
}

