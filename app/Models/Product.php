<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Seller;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'price',
        'min_order',
        'unit',
        'category_id',
        'city',
        'banner_image',
        'user_id', // Make sure this is included
    ];

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relationship with Seller
    public function seller()
    {
        return $this->hasOne(Seller::class, 'user_id', 'user_id');
    }
}
