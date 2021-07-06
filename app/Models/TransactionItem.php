<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionItem extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'users_id',
        'products_id',
        'transactions_id',
        'quantity'
    ];

    public function user(){
        return $this->belongsTo(User::class,'users_id','id');
    }
    public function transaction(){
        return $this->belongsTo(Transaction::class,'transactions_id','id');
    }
    public function product(){
        return $this->belongsTo(Product::class,'products_id','id');
    }
}
