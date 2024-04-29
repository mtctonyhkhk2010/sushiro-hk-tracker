<?php

namespace App\Models;

use App\Enums\StoreStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;
use Spatie\Translatable\HasTranslations;

class Store extends Model
{
    use HasTranslations;
    use HasSpatial;

    public $translatable = ['name'];

    protected $guarded = [

    ];

    protected $casts = [
        'location' => Point::class,
        'status' => StoreStatus::class
    ];

}
