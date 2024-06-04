<?php

namespace App\Models;

use App\Http\Traits\Uuid;
use App\Repository\CrudInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesDetailModel extends Model implements CrudInterface
{
    use HasFactory;
    use Uuid;
    use SoftDeletes; // Use SoftDeletes library
    public $timestamps = true;
    protected $fillable = [
        't_sales_id',
        'm_product_id',
        'm_product_detail_id',
        'total_item',
        'price'
    ];
    protected $table = 't_sales_detail';

    public function product()
    {
        return $this->hasOne(ProductModel::class, 'id', 'm_product_id');
    }

    public function productDetails()
    {
        return $this->hasOne(ProductDetailModel::class, 'id', 'm_product_detail_id');
    }

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

        if (!empty($filter['type'])) {
            $user->where('type', 'LIKE', '%' . $filter['type'] . '%');
        }

        if (!empty($filter['m_product_id'])) {
            $user->where('m_product_id', 'LIKE', '%' . $filter['m_product_id'] . '%');
        }

        $sort = $sort ?: 'm_product_category.index ASC';
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
