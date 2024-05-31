<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function marketplace(){
        return $this->belongsTo(Marketplace::class);
    }

    public function province(){
        return $this->belongsTo(Province::class);
    }
}
