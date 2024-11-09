<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProvisionTrademark extends Model
{
  use HasFactory;

  protected $fillable = [
    'provision_trademark_name',
    'provision_trademark_is_editable',
  ];

  // * una marca esta presente en muchos suministros
  public function provisions(): HasMany
  {
    return $this->hasMany(Provision::class, 'provision_trademark_id', 'id');
  }
}
