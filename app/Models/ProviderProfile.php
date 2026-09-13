<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderProfile extends Model
{
    /** @use HasFactory<\Database\Factories\ProviderProfileFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'is_available',
        'max_travel_distance',
        'id_card_image',
        'certificate_image',
        'kyc_status',
        'latitude',
        'longitude',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
}
