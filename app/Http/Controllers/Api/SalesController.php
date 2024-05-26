<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Sales\SalesHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Sales\SalesRequest;
use App\Http\Resources\Sales\SalesCollection;
use App\Models\SalesDetailModel;
use App\Models\SalesModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\TryCatch;

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
    {
        try {
            $validatedData = $request->validated();
    
            DB::beginTransaction();
    
            $sale = SalesModel::create([
                'm_customer_id' => $validatedData['m_customer_id'],
                'date' => now()->toDateString(),
            ]);
    
            foreach ($validatedData['product_detail'] as $detail) {
                SalesDetailModel::create([
                    't_sales_id' => $sale->id,
                    'm_product_id' => $detail['m_product_id'],
                    'm_product_detail_id' => $detail['m_product_detail_id'],
                    'total_item' => $detail['total_item'],
                    'price' => $detail['price'],
                ]);
            }
    
            DB::commit();
            return response()->json(['message' => 'Sale created successfully'], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Failed to create sale: ' . $e->getMessage()], 500);
        }
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
