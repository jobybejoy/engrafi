<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
   
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'register_number', 'name', 'email','phone_number','degree','department','address' ,'year'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at' , 'updated_at'
    ];
}
