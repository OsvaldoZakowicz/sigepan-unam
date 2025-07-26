<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use OwenIt\Auditing\Contracts\Auditable;

class PeriodProvision extends Pivot implements Auditable
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

    protected $table = "period_provision";

    protected $fillable = [
        'period_id',
        'provision_id',
        'quantity',
    ];

    public function period()
    {
        return $this->belongsTo(RequestForQuotationPeriod::class, 'period_id', 'id');
    }

    public function provision()
    {
        return $this->belongsTo(Provision::class, 'provision_id', 'id');
    }
}
