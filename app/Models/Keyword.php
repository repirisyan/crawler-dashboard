<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keyword extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function temp_item()
    {
        return $this->hasMany(TempItem::class);
    }

    public function comodity()
    {
        return $this->belongsTo(Comodity::class);
    }

    public function getKeywords($request)
    {
        return $this->with('comodity:id,name')->when(isset($request['search']), function ($query) use ($request) {
            $query->where('name', 'like', '%'.$request['search'].'%');
        })->when(isset($request['comodity']), function ($query) use ($request) {
            $query->where('comodity_id', $request['comodity']);
        })->when(isset($request['status']), function ($query) use ($request) {
            $query->where('status', $request['status']);
        })->paginate($request['per_page'] ?? 15);
    }

    public function changeStatus($id, $status)
    {
        return $this->find($id)->update([
            'status' => ! $status,
            'updated_at' => now(),
        ]);
    }

    public function getKeyword($keyword_id)
    {
        return $this->where('id', $keyword_id)->first();
    }

    public function getAllKeyword($status = null)
    {
        return $this->with('comodity:id,name')->when($status != null, function ($query) use ($status) {
            $query->where('status', $status);
        })->get();
    }

    public function storeData($data)
    {
        return $this->create([
            'name' => $data['name'],
            'comodity_id' => $data['comodity_id'],
        ]);
    }

    public function updateData($data, $keyword_id)
    {
        return $this->find($keyword_id)->update([
            'name' => $data['name'],
            'comodity_id' => $data['comodity_id'],
        ]);
    }

    public function deleteData($keyword_id)
    {
        return $this->destroy($keyword_id);
    }
}
