<?php

namespace App\Models;

use App\Http\Traits\Uuid;
use App\Repository\CrudInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesModel extends Model implements CrudInterface
{
    use HasFactory;
    use Uuid;
    use SoftDeletes; // Use SoftDeletes library
    public $timestamps = true;
    protected $fillable = [
        'm_customer_id',
        'date'
    ];
    protected $table = 't_sales';

    public function customer()
    {
        return $this->hasOne(CustomerModel::class, 'id', 'm_customer_id');
    }
    
    public function details()
    {
        return $this->hasMany(SalesDetailModel::class, 't_sales_id');
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

        if (!empty($filter['name'])) {
            $user->where('name', 'LIKE', '%' . $filter['name'] . '%');
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


    public function getSalesByCategory($startDate, $endDate, $category = '')
    {
        $sales = $this->query()->with([
            'details.product' => function ($query) use ($category) {
                if (!empty($category)) {
                    $query->where('m_product_category_id', $category);
                }
            },
            'details',
            'details.product.category',
        ]);

        if (!empty($startDate) && !empty($endDate)) {
            $sales->whereRaw('date >= "' . $startDate . ' 00:00:01" and date <= "' . $endDate . ' 23:59:59"');
        }
        // dd($sales);

        return $sales->orderByDesc('date')->get();
        // return $sales->orderByDesc('date')->limit(2)->get();
    }


    public function getSales($startDate, $endDate, $category = '')
    {
        $sales = $this->query()->with([
            'details.product' => function ($query) use ($category) {
                if (!empty($category)) {
                    $query->where('m_product_category_id', $category);
                }
            },
            'customer',
            'details',
            'details.product.category',
            'details.productDetails',
        ]);

        if (!empty($startDate) && !empty($endDate)) {
            $sales->whereRaw('date >= "' . $startDate . ' 00:00:01" and date <= "' . $endDate . ' 23:59:59"');
        }
        // dd($sales);

        return $sales->orderByDesc('date')->get();
        // return $sales->orderByDesc('date')->limit(2)->get();
    }

    public function getSalesByCustomers($startDate, $endDate, $category = '')
    {
        $sales = $this->query()->with([
            'details.product' => function ($query) use ($category) {
                if (!empty($category)) {
                    $query->where('m_product_category_id', $category);
                }
            },
            'details',
            'details.product.category',
            'details.productDetails',
            'customer'
        ]);

        if (!empty($startDate) && !empty($endDate)) {
            $sales->whereRaw('date >= "' . $startDate . ' 00:00:01" and date <= "' . $endDate . ' 23:59:59"');
        }
        // dd($sales);

        return $sales->orderByDesc('date')->get();
        // return $sales->orderByDesc('date')->limit(2)->get();
    }

    public function get($startDate, $endDate, $categoryId = '')
    {
        $this->startDate = $startDate;
        $this->endDate   = $endDate;

        $sales = $this->sales->getSalesByCategory($startDate, $endDate, $categoryId);

        return [
            'status'     => true,
            'data'       => $this->reformatReport($sales, $startDate, $endDate),
            'dates'          => array_values($this->dates),
            'total_per_date' => array_values($this->totalPerDate),
            'grand_total'    => $this->total
        ];
    }
}
