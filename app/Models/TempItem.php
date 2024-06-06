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

    public function getData($request){
        return $this->with('marketplace:id,name')->when($request['search'] != null, function($query) use ($request){
            $query->where('title','like','%'.$request['search'].'%')->orWhere('seller','like','%'.$request['search'].'%')->orWhere('location','like','%'.$request['search'].'%');
        })->when($request['marketplace_id'] != null, function($query) use ($request){
            $query->where('marketplace_id',$request['marketplace_id']);
        })->paginate($request['entries'] ?? 15);
    }

    public function truncate(){
        return $this->truncate();
    }
}
