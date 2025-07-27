<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use OwenIt\Auditing\Contracts\Auditable;

class ProductSale extends Pivot implements Auditable
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

    protected $table = "product_sale";

    protected $fillable = [
        'sale_id',
        'product_id',
        'sale_quantity',
        'unit_price',
        'subtotal_price',
        'details'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id', 'id');
    }
}
