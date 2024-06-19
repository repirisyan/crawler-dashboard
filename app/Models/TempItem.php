<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function marketplace()
    {
        return $this->belongsTo(Marketplace::class);
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function getData($request)
    {
        return $this->with('marketplace:id,name')
            ->when($request['search'] != null, function ($query) use ($request) {
                // If both search and marketplace_id are provided, add both conditions
                if ($request['marketplace_id'] != null) {
                    $query->where(function ($query) use ($request) {
                        $query->where('marketplace_id', $request['marketplace_id'])
                            ->where(function ($query) use ($request) {
                                $query->where('title', 'like', '%'.$request['search'].'%')
                                    ->orWhere('seller', 'like', '%'.$request['search'].'%')
                                    ->orWhere('location', 'like', '%'.$request['search'].'%');
                            });
                    });
                } else {
                    // If only search is provided, apply the search conditions
                    $query->where(function ($query) use ($request) {
                        $query->where('title', 'like', '%'.$request['search'].'%')
                            ->orWhere('seller', 'like', '%'.$request['search'].'%')
                            ->orWhere('location', 'like', '%'.$request['search'].'%');
                    });
                }
            })
            ->when($request['marketplace_id'] != null && $request['search'] == null, function ($query) use ($request) {
                // If only marketplace_id is provided, add the condition
                $query->where('marketplace_id', $request['marketplace_id']);
            })
            ->where('user_id', auth()->user()->id)
            ->paginate($request['entries'] ?? 15);
    }

    public function truncate()
    {
        return $this->truncate();
    }
}
