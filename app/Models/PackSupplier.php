<?php

namespace App\Models;

use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PackSupplier extends Pivot implements Auditable
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

    protected $table = "pack_supplier";

    protected $fillable = [
        'supplier_id',
        'pack_id',
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

    public function pack(): BelongsTo
    {
        return $this->belongsTo(Pack::class, 'pack_id', 'id');
    }
}
