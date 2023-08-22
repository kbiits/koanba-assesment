<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Order.
 *
 * @package namespace App\Models;
 */
class Order extends Model implements Transformable
{
    use TransformableTrait, HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'orderId';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customerId',
        'customerName',
        'amount',
        'quality',
        'productId',
        'productName',
        'orderDate',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customerId', 'customerId');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'productId', 'productId');
    }
}
