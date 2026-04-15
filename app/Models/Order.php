<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\Scope;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_number',
        'customer_id',
        'coupon_id',
        'subtotal',
        'discount_amount',
        'shipping_cost',
        'tax_amount',
        'total',
        'shipping_full_name',
        'shipping_phone',
        'shipping_address_line_1',
        'shipping_address_line_2',
        'shipping_city',
        'shipping_state',
        'shipping_postal_code',
        'shipping_country',
        'payment_method',
        'payment_status',
        'transaction_id',
        'status',
        'tracking_number',
        'customer_notes',
        'admin_notes',
    ];
 /**
     * Scope to filter by status
     */
    #[Scope]
    protected function ofStatus(Builder $query, string $status): void
    {
        $query->where('status', $status);
    }

    /**
     * Scope to filter by payment status
     */
    #[Scope]
    protected function paymentStatus(Builder $query, string $status): void
    {
        $query->where('payment_status', $status);
    }

    /**
     * Scope to only pending orders
     */
    #[Scope]
    protected function pending(Builder $query): void
    {
        $query->where('status', 'pending');
    }

    /**
     * Scope to only processing orders
     */
    #[Scope]
    protected function processing(Builder $query): void
    {
        $query->where('status', 'processing');
    }

    /**
     * Scope to only shipped orders
     */
    #[Scope]
    protected function shipped(Builder $query): void
    {
        $query->where('status', 'shipped');
    }

    /**
     * Scope to only delivered orders
     */
    #[Scope]
    protected function delivered(Builder $query): void
    {
        $query->where('status', 'delivered');
    }

    //relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistories()
    {
        return $this->hasMany(OrderStatusHistory::class)->orderBy('created_at', 'desc');
    }

    //helper methods
     public function getShippingAddressAttribute()
    {
        return implode(', ', array_filter([
            $this->shipping_address_line_1,
            $this->shipping_address_line_2,
            $this->shipping_city,
            $this->shipping_state,
            $this->shipping_postal_code,
            $this->shipping_country,
        ]));
    }

    public function updateStatus($newStatus, $notes = null, $userId = null)
    {
        $this->update(['status' => $newStatus]);

        $this->statusHistories()->create([
            'status' => $newStatus,
            'notes' => $notes,
            'user_id' => $userId,
        ]);
    }

    protected static function boot(){
        parent::boot();

        static::creating(function ($order){
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-'. strtoupper(uniqid());
            }
        });

        static::created(function($order){
            $order->statusHistories()->create([
                'status' => $order->status,
                'notes' => 'Order created'
            ]);
            //order confirmation email
        });
    }

}
