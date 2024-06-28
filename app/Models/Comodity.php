<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comodity extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function temp_item(){
        return $this->hasMany(TempItem::class);
    }

    public function getComodities($search = null){
        return $this->when($search != null, function($query) use ($search){
            $query->where('name','like','%'.$search.'%');
        })->simplePaginate(5);
    }

    public function getAllComodity(){
        return $this->all();
    }

    public function getComodity($id){
        return $this->where('id',$id)->first();
    }

    public function storeData($data){
        return $this->create([
            'name' => $data['name']
        ]);
    }

    public function updateData($id,$data){
        return $this->find($id)->update([
            'name' => $data['name']
        ]);
    }

    public function destroyData($id){
        return $this->destroy($id);
    }
}
