<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\UserRoleModel;
use Illuminate\Http\Request;
use App\Http\Requests\Role\CreateRequest;
use App\Helpers\UserRoleHelper;
use App\Http\Requests\Role\UpdateRequest;
use App\Http\Resources\UserRole\UserRoleCollections;
use App\Http\Resources\UserRole\UserRoleResource;
use PHPOpenSourceSaver\JWTAuth\Payload;

class UserRoleController extends Controller
{

    private $role;
    public function __construct()
    {
        $this->role = new UserRoleHelper();
    }


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filter = [
            'name' => $request->nama ?? ''
        ];
        $role = $this->role->getAll($filter, 5, $request->sort ?? '');
        //dd($role['data']);
        return response()->success(new UserRoleCollections($role['data']));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateRequest $request)
    {
        //dd($request->all());

        /**
         * Menampilkan pesan error ketika validasi gagal
         * pengaturan validasi bisa dilihat pada class app/Http/request/role/CreateRequest
         */
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }


        $payload = $request->only(['name', 'access']);

        $role = $this->role->create($payload);

        if (!$role['status']) {
            return response()->failed($role['error']);
        }


        return response()->success(new UserRoleResource($role['data']));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $role = $this->role->getById($id);

        if (!($role['status'])) {
            return response()->failed(['Data role tidak ditemukan'], 404);
        }

        return response()->success(new UserRoleResource($role['data']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request)
    {
        /**
         * Menampilkan pesan error ketika validasi gagal
         * pengaturan validasi bisa dilihat pada class app/Http/request/role/UpdateRequest
         */
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }
        
        $payload = $request->only(['name', 'access','id']);
        $role = $this->role->update($payload, $payload['id']);
        //dd($role);

        if (!$role['status']) {
            return response()->failed($role['error']);
        }

        return response()->success(new UserRoleResource($role['data']));
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Mencari role berdasarkan ID
        $role = UserRoleModel::find($id);

        // Memeriksa apakah data ditemukan
        if (!$role) {
            return response()->json(['status' => false, 'message' => 'Role not found'], 404);
        }

        // Menghapus role dari database
        $role->delete();

        // Mengembalikan respons sukses
        return response()->json(['status' => true, 'message' => 'Role deleted successfully'], 200);
    }
}
