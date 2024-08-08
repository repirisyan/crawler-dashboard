<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function keyword()
    {
        return $this->belongsTo(Keyword::class);
    }

    public function marketplace()
    {
        return $this->belongsTo(Marketplace::class);
    }

    public function getData($request)
    {
        return $this->with(['marketplace:id,name', 'keyword:id,name,comodity_id,sub_comodity,second_level_sub_comodity,third_level_sub_comodity', 'keyword.comodity:id,name'])
            ->when($request['search'] != null, function ($query) use ($request) {
                // If both search and marketplace_id are provided, add both conditions
                if ($request['comodities'] != null) {
                    $query->where(function ($query) use ($request) {
                        $query->whereHas('keyword', function ($query) use ($request) {
                            $query->whereIn('comodity_id', $request['comodities']);
                        });
                    });
                }
                if ($request['date'] != null) {
                    $query->where(function ($query) use ($request) {
                        $query->whereDate('created_at', $request['date']);
                    });
                }
                if ($request['marketplaces'] != null) {
                    $query->where(function ($query) use ($request) {
                        $query->whereIn('marketplace_id', $request['marketplaces']);
                    });
                }
                $query->where(function ($query) use ($request) {
                    $query->where('title', 'like', '%'.$request['search'].'%')
                        ->orWhere('seller', 'like', '%'.$request['search'].'%')
                        ->orWhere('location', 'like', '%'.$request['search'].'%');
                });
            })
            ->when($request['marketplaces'] != null && $request['search'] == null, function ($query) use ($request) {
                // If only marketplace_id is provided, add the condition
                $query->whereIn('marketplace_id', $request['marketplaces']);
            })
            ->when($request['comodities'] != null && $request['search'] == null, function ($query) use ($request) {
                // If only marketplace_id is provided, add the condition
                $query->where(function ($query) use ($request) {
                    $query->whereHas('keyword', function ($query) use ($request) {
                        $query->whereIn('comodity_id', $request['comodities']);
                    });
                });
            })
            ->when($request['date'] != null && $request['search'] == null, function ($query) use ($request) {
                // If only marketplace_id is provided, add the condition
                $query->whereDate('created_at', $request['date']);
            })
            ->simplePaginate($request['per_page'] ?? 15);
    }

    public function getTotalItem()
    {
        return $this->count();
    }

    public function truncate()
    {
        return $this->truncate();
    }
}
