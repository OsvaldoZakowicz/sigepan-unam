<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use OwenIt\Auditing\Contracts\Auditable;

class PreOrderProvision extends Pivot implements Auditable
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

    // IMPORTANTE: Definir la clave primaria
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = true;

    protected $table = "pre_order_provision";

    protected $fillable = [
        'pre_order_id',
        'provision_id',
        'has_stock',
        'quantity',
        'alternative_quantity',
        'unit_price',
        'total_price',
    ];

    public function pre_order()
    {
        return $this->belongsTo(PreOrder::class, 'pre_order_id', 'id');
    }

    public function provision()
    {
        return $this->belongsTo(Provision::class, 'provision_id', 'id');
    }
}
