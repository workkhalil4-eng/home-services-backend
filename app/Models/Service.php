<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    /** @use HasFactory<\Database\Factories\ServiceFactory> */
    use HasFactory;

    protected $fillable = ['category_id', 'name', 'base_price', 'is_fixed_price', 'description', 'is_active'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
