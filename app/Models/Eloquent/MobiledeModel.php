<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;

class MobiledeModel extends Model
{
    use HasFactory;  //, SoftDeletes;

    public function MobiledeModels()
    {
        //return $this->belongsTo(CarBrand::class);
        return $this->hasMany(CarBrandModel::class);
    }
}
