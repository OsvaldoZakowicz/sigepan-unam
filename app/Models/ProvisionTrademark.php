<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProvisionTrademark extends Model
{
    use HasFactory;

    protected $fillable = [
      'provision_trademark_name',
      'provision_trademark_is_editable',
    ];
}
