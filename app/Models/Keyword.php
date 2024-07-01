<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keyword extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function temp_item(){
        return $this->hasMany(TempItem::class);
    }

    public function search_list(){
        return $this->hasMany(SearchList::class);
    }

    public function getKeywords($search = null){
        return $this->when($search != null, function($query) use ($search){
            $query->where('keyword','like','%'.$search.'%');
        })->simplePaginate(15);
    }

    public function getKeyword($keyword_id){
        return $this->where('id',$keyword_id)->first();
    }

    public function getAllKeyword(){
        return $this->select('id','name')->get();
    }

    public function storeData($data){
        return $this->create([
            'name' => $data['name']
        ]);
    }

    public function updateData($data,$keyword_id){
        return $this->find($keyword_id)->update([
            'name' => $data['name']
        ]);
    }

    public function deleteData($keyword_id){
        return $this->destroy($keyword_id);
    }

}
