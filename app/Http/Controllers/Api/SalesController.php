<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Sales\SalesCatagoryHelper;
use App\Helpers\Sales\SalesHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Sales\SalesRequest;
use App\Http\Resources\Sales\SalesCollection;
use App\Http\Resources\Sales\SalesResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{

    private $sales;
    private $salesCategory;
    public function __construct()
    {
        $this->sales = new SalesHelper();
        $this->salesCategory = new SalesCatagoryHelper();
    }


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filter = [
            'name' => $request->name ?? '',
            'm_customer_id' => $request->m_customer_id ?? '',
            'date' => $request->date ?? '',
        ];
        $sales = $this->sales->getAll($filter, $request->per_page ?? 25, $request->sort ?? '');
        //dd($sales);
        // return response()->success($sales['data']);
        return response()->success(new SalesCollection($sales['data']));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SalesRequest $request)
    {
        // dd($request->all());

        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(["m_customer_id", "date", 'product_detail']);
        $sales = $this->sales->create($payload);
        // dd($payload['product_detail']);

        if (!$sales['status']) {
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

    public function viewSales(Request $request)
    {
        $startDate     = $request->start_date ?? null;
        $endDate       = $request->end_date ?? null;
        $categoryId    = $request->category_id ?? null;
        $isExportExcel = $request->is_export_excel ?? null;

        $sales = $this->salesCategory->get($startDate, $endDate, $categoryId);
        // dd($sales);


        return response()->success($sales['data'], '', [
            'dates'          => $sales['dates'] ?? [],
            'total_per_date' => $sales['total_per_date'] ?? [],
            'grand_total'    => $sales['grand_total'] ?? 0
        ]);
        // return response()->json(['message' => 'Hello, World!']);
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
