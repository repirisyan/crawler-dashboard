<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupervisionList extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function getAllData()
    {
        return $this->simplePaginate(15);
    }

    public function getData($id)
    {
        return $this->where('id', $id)->first();
    }

    public function storeData($data)
    {
        return $this->create([
            'name' => $data['name'],
        ]);
    }

    public function updateData($id, $data)
    {
        return $this->find($id)->update([
            'name' => $data['name'],
        ]);
    }

    public function deleteData($id)
    {
        return $this->destroy($id);
    }
}
