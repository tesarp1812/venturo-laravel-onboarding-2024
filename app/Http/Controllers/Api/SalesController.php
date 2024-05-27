<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Sales\SalesHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Sales\SalesRequest;
use App\Http\Resources\Sales\SalesResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{

    private $sales;
    public function __construct()
    {
        $this->sales = new SalesHelper();
    }


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filter = [
            'name' => $request->name ?? ''
        ];
        $sales = $this->sales->getAll($filter, $request->per_page ?? 25, $request->sort ?? '');
        //dd($sales);
        return response()->success(new SalesCollection($sales['data']));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SalesRequest $request)
    public function store(SalesRequest $request)
    {
        // dd($request->all());

        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only([
            "m_customer_id",
            "date",
            'details',
        ]);
        // dd($payload);
        $sales = $this->sales->create($payload);
        
        if(!$sales['status']){
            return response()->failed($sales['error']);
        }
        

        return response()->success($sales['data'], 'sales berhasil');

    }
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $sales = $this->sales->getById($id);
        
        // dd();
        if (!($sales['status'])) {
            return response()->failed(['Data sales tidak ditemukan'], 404);
        }

        return response()->success(new SalesResource($sales['data']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
