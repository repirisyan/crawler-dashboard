<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comodity extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function getComodities($request)
    {
        return $this->when(isset($request['search']), function ($query) use ($request) {
            $query->where('name', 'like', '%'.$request['search'].'%');
        })->paginate($request['per_page'] ?? 15);
    }

    public function getAllComodity()
    {
        return $this->all();
    }

    public function getComodity($id)
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

    public function destroyData($id)
    {
        return $this->destroy($id);
    }
}
