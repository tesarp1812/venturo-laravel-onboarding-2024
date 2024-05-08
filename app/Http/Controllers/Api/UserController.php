<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\UserHelper;
use App\Http\Requests\User\CreateRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Resources\User\UserCollections;
use App\Http\Resources\User\UserResource;

class UserController extends Controller
{

    private $user;
    public function __construct()
    {
        $this->user = new UserHelper();
    }

    /**
     * Mengambil list user
     *
     * @author Wahyu Agung <wahyuagung26@email.com>
     */
    public function index(Request $request)
    {
        $filter = [
            'nama' => $request->nama ?? '',
            'email' => $request->email ?? '',
        ];
        $users = $this->user->getAll($filter, 5, $request->sort ?? '');

        return response()->success(new UserCollections($users['data']));

    }

    /**
     * Membuat data user baru & disimpan ke tabel m_user
     *
     * @author Wahyu Agung <wahyuagung26@email.com>
     */

    public function store(CreateRequest $request)
    { {
            /**
             * Menampilkan pesan error ketika validasi gagal
             * pengaturan validasi bisa dilihat pada class app/Http/request/User/CreateRequest
             */
            if (isset($request->validator) && $request->validator->fails()) {
                return response()->failed($request->validator->errors());
            }

            $payload = $request->only(['email', 'name', 'password', 'photo']);
            $users = $this->user->create($payload);

            if (!$users['status']) {
                return response()->failed($users['error']);
            }

            return response()->success(new UserResource($users));

        }
    }

    /**
     * Menampilkan user secara spesifik dari tabel m_user
     *
     * @author Wahyu Agung <wahyuagung26@email.com>
     */
    public function show($id)
    {
        $users = $this->user->getById($id);

        if (!($users['status'])) {
            return response()->failed(['Data user tidak ditemukan'], 404);
        }

        return response()->success(new UserResource($users));

    }

    /**
     * Mengubah data user di tabel m_user
     *
     * @author Wahyu Agung <wahyuagung26@email.com>
     */
    public function update(UpdateRequest $request)
    {
        /**
         * Menampilkan pesan error ketika validasi gagal
         * pengaturan validasi bisa dilihat pada class app/Http/request/User/UpdateRequest
         */
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['email', 'name', 'password', 'id', 'photo']);
        $users = $this->user->update($payload, $payload['id']);

        if (!$users['status']) {
            return response()->failed($users['error']);
        }

        return response()->success(new UserResource($users));

    }

    /**
     * Soft delete data user
     *
     * @author Wahyu Agung <wahyuagung26@email.com>
     * @param mixed $id
     */

    public function destroy($id)
    {
        $users = $this->user->delete($id);

        if (!$users) {
            return response()->failed(['Mohon maaf data pengguna tidak ditemukan']);
        }

        return response()->success($users);
    }
}
