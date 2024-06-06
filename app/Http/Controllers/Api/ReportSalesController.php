<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Report\SalesCatagoryHelper;
use App\Helpers\Report\SalesCustomersHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportSalesController extends Controller
{
    private $salesCategory;
    private $salesCustomers;
    public function __construct()
    {
        $this->salesCategory = new SalesCatagoryHelper();
        $this->salesCustomers = new SalesCustomersHelper();
    }

    public function viewSalesCategories(Request $request)
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
    }

    public function viewSalesCustomers(Request $request)
    {
        $startDate     = $request->start_date ?? null;
        $endDate       = $request->end_date ?? null;
        $categoryId    = $request->category_id ?? null;
        $isExportExcel = $request->is_export_excel ?? null;
        
        $sales = $this->salesCustomers->get($startDate, $endDate, $categoryId);
        // dd($sales);getSalesByCategory


        return response()->success($sales['data'], '', [
            'dates'          => $sales['dates'] ?? [],
            'total_per_date' => $sales['total_per_date'] ?? [],
            'grand_total'    => $sales['grand_total'] ?? 0
        ]);
        // return response()->json(['message' => 'Hello, World!']);
    }
}
