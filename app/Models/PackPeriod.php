<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use OwenIt\Auditing\Contracts\Auditable;

class PackPeriod extends Pivot implements Auditable
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

    protected $table = "pack_period";

    protected $fillable = [
        'period_id',
        'pack_id',
        'quantity',
    ];

    public function period()
    {
        return $this->belongsTo(RequestForQuotationPeriod::class, 'period_id', 'id');
    }

    public function pack()
    {
        return $this->belongsTo(Pack::class, 'pack_id', 'id');
    }
}
