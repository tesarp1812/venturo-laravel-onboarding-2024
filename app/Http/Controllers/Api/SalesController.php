<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Sales\SalesHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
        return response()->success($sales['data']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
