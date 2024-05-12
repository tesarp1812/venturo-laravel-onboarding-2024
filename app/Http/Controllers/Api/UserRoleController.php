<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\UserRoleModel;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{

    private $role;
    public function __construct()
    {
        $this->role = new UserRoleModel();
    }


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Mendapatkan input dari request
        $filter = [
            'name' => $request->input('name') // asumsi filter berdasarkan nama
        ];

        // Mengambil data roles berdasarkan filter
        $role = $this->role->getAll($filter);

        // Mengembalikan respons dengan data roles
        return response()->json(['status' => true, 'data' => $role], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input dari request
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'access' => 'required|string|max:255',
        ]);

        // Membuat role baru berdasarkan input dari request
        $role = $this->role->create([
            'name' => $validatedData['name'],
            'access' => $validatedData['access'],
        ]);

        // Mengembalikan respons dengan data role yang baru dibuat
        return response()->json(['status' => true, 'data' => $role], 201);
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
