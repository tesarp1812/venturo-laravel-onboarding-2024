<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\UserHelper;
use App\Http\Requests\User\CreateRequest;
use App\Http\Requests\User\UpdateRequest;
use Illuminate\Http\UploadedFile;

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

        return response()->success($users['data']);
    }

    /**
     * Membuat data user baru & disimpan ke tabel m_user
     *phpph
     * @author Wahyu Agung <wahyuagung26@email.com>
     */

    public function store(CreateRequest $request)
    { {
        // dd($request->all());

        
            /**
             * Menampilkan pesan error ketika validasi gagal
             * pengaturan validasi bisa dilihat pada class app/Http/request/User/CreateRequest
             */
            if (isset($request->validator) && $request->validator->fails()) {
                return response()->failed($request->validator->errors());
            }

            $payload = $request->only(['email', 'name', 'password', 'photo']);
            $user = $this->user->create($payload);

            if (!$user['status']) {
                return response()->failed($user['error']);
            }
            

            return response()->success($user['data']);
        }
    }

    /**
     * Menampilkan user secara spesifik dari tabel m_user
     *
     * @author Wahyu Agung <wahyuagung26@email.com>
     */
    public function show($id)
    {
        $user = $this->user->getById($id);

        if (!($user['status'])) {
            return response()->failed(['Data user tidak ditemukan'], 404);
        }

        return response()->success($user['data']);
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


        // upload photo

        // Decode the base64 string
        $decodedImage = base64_decode($request->image);
        $fileName = uniqid() . '.jpg';

        // Create a temporary file
        $tempFilePath = public_path('uploads/foto-user/' . $fileName);
        file_put_contents($tempFilePath, $decodedImage);

        $payload = $request->only(['email', 'name', 'password', 'id', 'photo']);
        $payload['photo'] = $fileName;
        $user = $this->user->update($payload, $payload['id']);

        if (!$user['status']) {
            return response()->failed($user['error']);
        }

        return response()->success($user['data']);
    }

    /**
     * Soft delete data user
     *
     * @author Wahyu Agung <wahyuagung26@email.com>
     * @param mixed $id
     */

    public function destroy($id)
    {
        $user = $this->user->delete($id);

        if (!$user) {
            return response()->failed(['Mohon maaf data pengguna tidak ditemukan']);
        }

        return response()->success($user);
    }
}


