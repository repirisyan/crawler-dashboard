<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supervision extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function marketplace()
    {
        return $this->belongsTo(Marketplace::class);
    }

    public function keyword()
    {
        return $this->belongsTo(Keyword::class);
    }

    public function getDatas($request)
    {
        return $this->with(['keyword:id,name,comodity_id,sub_comodity,second_level_sub_comodity,third_level_sub_comodity', 'marketplace:id,name', 'keyword.comodity:id,name'])->when($request['search'] != null, function ($query) use ($request) {
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

            if ($request['status'] != null) {
                $query->where(function ($query) use ($request) {
                    $query->where('status', $request['status']);
                });
            }
            $query->where(function ($query) use ($request) {
                $query->where('name', 'like', '%'.$request['search'].'%')
                    ->orWhere('seller', 'like', '%'.$request['search'].'%')
                    ->orWhere('location', 'like', '%'.$request['search'].'%');
            });
        })->when($request['status'] != null && $request['search'] == null, function ($query) use ($request) {
            $query->where('status', $request['status']);
        })->when($request['marketplaces'] != null && $request['search'] == null, function ($query) use ($request) {
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
            })->when($request['date'] != null && $request['search'] == null, function ($query) use ($request) {
            // If only marketplace_id is provided, add the condition
            $query->whereDate('created_at', $request['date']);
        })->simplePaginate($request['entries'] ?? 15);
    }

    public function getData($supervision_id)
    {
        return $this->where('id', $supervision_id)->first();
    }

    public function getTotalItem()
    {
        return $this->count();
    }

    public function storeData($request)
    {
        return $this->create([
            'name' => $request['title'],
            'link' => $request['link'],
            'image' => $request['image'],
            'keyword_id' => $request['keyword_id'],
            'price' => $request['price'],
            'rating' => $request['rating'],
            'description' => $request['description'],
            'sold' => $request['sold'],
            'marketplace_id' => $request['marketplace_id'],
            'seller' => $request['seller'],
            'location' => $request['location'],
        ]);
    }

    public function destroyData($supervision_id)
    {
        return $this->destroy($supervision_id);
    }
}
