<?php

namespace App\Models;

use App\Http\Traits\Uuid;
use App\Repository\CrudInterface;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRoleModel extends Model implements CrudInterface 
{
    use HasFactory;
    use Uuid;
    use SoftDeletes; // Use SoftDeletes library
    protected $table = "m_user_roles";
    protected $fillable = [
        'name',
        'access'
    ];
    public $timestamp = true;

    public function getAll(array $filter, int $itemPerPage = 0, string $sort = '')
    {
        $query = $this->query();

        if (!empty($filter['name'])) {
            $query->where('name', 'LIKE', '%' . $filter['name'] . '%');
        }

        $sort = $sort ?: 'id DESC';
        $query->orderByRaw($sort);

        if ($itemPerPage > 0) {
            return $query->paginate($itemPerPage)->appends('sort', $sort);
        } else {
            return $query->get();
        }
    }

    public function getById(string $id)
    {
        return $this->find($id);
    }

    public function store(array $payload)
    {
        return $this->create($payload);
    }

    public function edit(array $payload, string $id)
    {
        return $this->find($id)->update($payload);
    }

    public function drop(string $id)
    {
        return $this->find($id)->delete();
    }
}
