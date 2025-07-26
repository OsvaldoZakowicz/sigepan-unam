<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use OwenIt\Auditing\Contracts\Auditable;

class PackQuotation extends Pivot implements Auditable
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

    protected $table = "pack_quotation";

    protected $fillable = [
        'quotation_id',
        'pack_id',
        'has_stock',
        'quantity',
        'unit_price',
        'total_price'
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class, 'quotation_id', 'id');
    }

    public function pack()
    {
        return $this->belongsTo(Pack::class, 'pack_id', 'id');
    }
}
