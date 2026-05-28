<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\belongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'order_number',
        'status_id',
        'client_id',
    ];

   public function client(): belongsTo
   {
       return $this->belongsTo(Client::class);
   }

   public function details(): HasMany
   {
       return $this->hasMany(OrderDetail::class);
   }
}
