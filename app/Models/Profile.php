<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
  use HasFactory;

  protected $fillable = [
    'first_name',
    'last_name',
    'dni',
    'birthdate',
    'phone_number',
    'gender_id',
    'user_id'
  ];
}
