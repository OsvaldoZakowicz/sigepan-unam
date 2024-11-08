<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProvisionType extends Model
{
    use HasFactory;

    protected $fillable = [
      'provision_type_name',
      'provision_type_short_description',
      'provision_type_is_editable'
    ];
}
