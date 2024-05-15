<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\UserRoleModel;
use Illuminate\Http\Request;
use App\Http\Requests\Role\CreateRequest;
use App\Helpers\UserRoleHelper;
use App\Http\Requests\Role\UpdateRequest;
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

        return response()->success($role);
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


        return response()->success($role['data']);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Mengambil data role berdasarkan ID
        $role = $this->role->find($id);

        // Memeriksa apakah data ditemukan
        if (!$role) {
            return response()->json(['status' => false, 'message' => 'Role not found'], 404);
        }

        // Mengembalikan respons dengan data role
        return response()->json(['status' => true, 'data' => $role], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Mencari role berdasarkan ID
        $role = UserRoleModel::find($id);

        // Memeriksa apakah data ditemukan
        if (!$role) {
            return response()->json(['status' => false, 'message' => 'Role not found'], 404);
        }

        // Validasi input dari request
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'access' => 'required|string|max:255',
        ]);

        // Mengupdate role berdasarkan input dari request
        $role->name = $validatedData['name'];
        $role->access = $validatedData['access'];
        $role->save();

        // Mengembalikan respons dengan data role yang telah diupdate
        return response()->json(['status' => true, 'data' => $role], 200);
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
