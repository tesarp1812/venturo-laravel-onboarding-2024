<?php

namespace App\Helpers;

use App\Models\UserRoleModel;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Event\Code\Throwable;
use App\Http\Traits\Uuid;

class UserRoleHelper extends Venturo
{
    use Uuid;
    private $roleModel;
    public function __construct()
    {
        $this->roleModel = new UserRoleModel();
    }

    /**
     * Mengambil data user dari tabel m_user
     *
     * @author Wahyu Agung <wahyuagung26@gmail.com>
     *
     * @param  array $filter
     * $filter['nama'] = string
     * $filter['email'] = string
     * @param integer $itemPerPage jumlah data yang ditampilkan, kosongi jika ingin menampilkan semua data
     * @param string $sort nama kolom untuk melakukan sorting mysql beserta tipenya DESC / ASC
     *
     * @return object
     */
    public function getAll(array $filter, int $itemPerPage = 0, string $sort = ''): array
    {
        $users = $this->userModel->getAll($filter, $itemPerPage, $sort);

        return [
            'status' => true,
            'data' => $users
        ];
    }

    /**
     * Mengambil 1 data user dari tabel m_user
     *
     * @param integer $id id dari tabel m_user
     *
     * @return array
     */
    public function getById(string $id): array
    {
        $user = $this->userModel->getById($id);
        if (empty($user)) {
            return [
                'status' => false,
                'data' => null
            ];
        }

        return [
            'status' => true,
            'data' => $user
        ];
    }

    
    /**
     * method untuk menginput data baru ke tabel m_user
     *
     * @author Wahyu Agung <wahyuagung26@email.com>
     *
     * @param array $payload
     *              $payload['name'] = string
     *              $payload['access] = string
     *
     * @return array
     */
    public function create(array $payload): array
    {
        try {
            $role = $this->roleModel->store($payload);

            return [
                'status' => true,
                'data' => $role
            ];
        } catch (\Throwable $th) {
            return [
                'status' => false,
                'error' => $th->getMessage()
            ];
        }
    }

    /**
     * method untuk mengubah user pada tabel m_user
     *
     * @author Wahyu Agung <wahyuagung26@email.com>
     *
     * @param array $payload
     *                       $payload['nama'] = string
     *                       $payload['email] = string
     *                       $payload['password] = string
     *
     * @return array
     */
    public function update(array $payload, string $id): array
    {
        try {
            if (isset($payload['password']) && !empty($payload['password'])) {
                $payload['password'] = Hash::make($payload['password']) ?: '';
            } else {
                unset($payload['password']);
            }

            // $payload = $this->uploadGetPayload($payload);
            $this->userModel->edit($payload, $id);

            $user = $this->getById($id);
            return [
                'status' => true,
                'data' => $user['data']
            ];
        } catch (\Throwable $th) {
            return [
                'status' => false,
                'error' => $th->getMessage()
            ];
        }
    }


    /**
     * Menghapus data user dengan sistem "Soft Delete"
     * yaitu mengisi kolom deleted_at agar data tsb tidak
     * keselect waktu menggunakan Query
     *
     * @param  integer $id id dari tabel m_user
     *
     * @return bool
     */
    public function delete(string $id): bool
    {
        try {
            $this->userModel->drop($id);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}
