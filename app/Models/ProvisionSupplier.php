<?php

namespace App\Models;

use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ProvisionSupplier extends Pivot implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    /**
     * Eventos que deben ser auditados
     */
    protected $auditEvents = [
        'created',
        'updated', 
        'deleted',
    ];

    protected $table = "provision_supplier";

    protected $fillable = [
        'supplier_id',
        'provision_id',
        'price'
    ];

    // IMPORTANTE: Definir la clave primaria
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = true;

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }

    public function provision(): BelongsTo
    {
        return $this->belongsTo(Provision::class, 'provision_id', 'id');
    }

}
