<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'staff_id','staff_name',
        'name', 'description', 'date', 
        'time', 'sessions','card_image', 'venue', 'registered' ,
        'max_participant', 'resource_person', 
        'department', 'category',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'create_at', 'updated_at',
    ];
}
