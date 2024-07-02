<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marketplace extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function temp_item()
    {
        return $this->hasMany(TempItem::class);
    }

    public function supervision()
    {
        return $this->hasMany(Supervision::class);
    }

    public function getAllData()
    {
        return $this->all();
    }

    public function getActiveMarketplace()
    {
        return $this->select('id', 'name')->where('maintenance', false)->get();
    }

    public function getStatus($id)
    {
        return $this->where('id', $id)->value('status');
    }

    public function changeStatus($id, $status)
    {
        return $this->find($id)->update([
            'status' => $status,
        ]);
    }
}
