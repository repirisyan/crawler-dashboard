<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchList extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function comodity()
    {
        return $this->belongsTo(Comodity::class);
    }

    public function keyword()
    {
        return $this->belongsTo(Keyword::class);
    }

    public function getDatas()
    {
        return $this->with(['keyword:id,name', 'comodity:id,name'])->simplePaginate(15);
    }

    public function getAllData()
    {
        return $this->with(['keyword:id,name'])->select('id', 'keyword_id', 'comodity_id')->get();
    }

    public function storeData($data)
    {
        return $this->create([
            'keyword_id' => $data['keyword_id'],
            'comodity_id' => $data['comodity_id'],
        ]);
    }

    public function getData($id)
    {
        return $this->where('id', $id)->first();
    }

    public function updateData($data, $id)
    {
        return $this->find($id)->update([
            'keyword_id' => $data['keyword_id'],
            'comodity_id' => $data['comodity_id'],
        ]);
    }

    public function deleteData($id)
    {
        return $this->destroy($id);
    }
}
