<?php

namespace App\Models;

use App\Repository\CrudInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesDetailModel extends Model implements CrudInterface
{
    use HasFactory;
    use SoftDeletes; // Use SoftDeletes library
    public $timestamps = true;
    protected $fillable = [
        't_sales_id',
        'm_product_id',
        'm_product_detail_id',
        'total_item',
        'price'
    ];
    protected $table = 'm_product_detail';

    public function drop(string $id)
    {
        return $this->find($id)->delete();
    }
 
    public function edit(array $payload, string $id)
    {
        return $this->find($id)->update($payload);
    }
 
    public function getAll(array $filter, int $itemPerPage = 0, string $sort = '')
    {
        $user = $this->query();
 
        if (!empty($filter['t_sales_id'])) {
            $user->where('t_sales_id', 'LIKE', '%' . $filter['t_sales_id'] . '%');
        }
 
        $sort = $sort ?: 'id DESC';
        $user->orderByRaw($sort);
        $itemPerPage = ($itemPerPage > 0) ? $itemPerPage : false;
 
        return $user->paginate($itemPerPage)->appends('sort', $sort);
    }
 
    public function getById(string $id)
    {
        return $this->find($id);
    }
 
    public function store(array $payload)
    {
        return $this->create($payload);
    }
}
