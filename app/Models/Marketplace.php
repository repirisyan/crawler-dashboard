<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marketplace extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function getAllData()
    {
        return $this->all();
    }

    public function getActiveMarketplace()
    {
        return $this->select('id', 'name')->where('maintenance', false)->get();
    }

    public function getCrawlingMarketplace()
    {
        return $this->select('id', 'name')->where(['maintenance' => false, 'status' => false])->get();
    }

    public function getStatus($id)
    {
        return $this->where('id', $id)->value('status');
    }

    public function toggleMaintenance($status, $id)
    {
        return $this->find($id)->update([
            'maintenance' => $status,
            'updated_at' => now(),
        ]);
    }

    public function changeStatus($id, $status)
    {
        return $this->find($id)->update([
            'status' => $status,
        ]);
    }
}
