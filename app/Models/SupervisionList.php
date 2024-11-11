<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupervisionList extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function getSupervisionList($request)
    {
        return $this->when(isset($request['search']), function ($query) use ($request) {
            $query->where('name', 'like', '%'.$request['search'].'%');
        })->when(isset($request['status']), function ($query) use ($request) {
            $query->where('status', $request['status']);
        })->paginate($request['per_page'] ?? 15);
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

    public function changeStatus($id, $status)
    {
        return $this->find($id)->update([
            'status' => ! $status,
            'updated_at' => now(),
        ]);
    }

    public function updateData($id, $data)
    {
        return $this->find($id)->update([
            'name' => $data['name'],
            'category' => $data['category'],
        ]);
    }

    public function deleteData($id)
    {
        return $this->destroy($id);
    }
}
