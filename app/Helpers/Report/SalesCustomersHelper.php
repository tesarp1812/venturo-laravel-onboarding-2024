<?php

namespace App\Helpers\Report;

use App\Helpers\Venturo;
use App\Models\SalesModel;
use DateInterval;
use DatePeriod;
use DateTime;

class SalesCustomersHelper extends Venturo
{
    private $sales;
    private $startDate;
    private $endDate;
    private $total;
    private $totalPerDate = [];
    private $dates = [];
    public function __construct()
    {
        $this->sales = new SalesModel();
    }

    private function getPeriode()
    {
        $begin = new DateTime($this->startDate);
        $end   = new DateTime($this->endDate);
        $end   = $end->modify('+1 day');

        $interval = DateInterval::createFromDateString('1 day');
        $period   = new DatePeriod($begin, $interval, $end);

        foreach ($period as $dt) {
            $date         = $dt->format('Y-m-d');
            $dates[$date] = [
                'date_transaction' => $date,
                'total_sales'      => 0,
            ];

            $this->setDefaultTotal($date);
            $this->setSelectedDate($date);
        }

        return $dates ?? [];
    }

    private function setDefaultTotal(string $date)
    {
        $this->totalPerDate[$date] = 0;
    }

    private function setSelectedDate(string $date)
    {
        $this->dates[] = $date;
    }

    private function reformatReport($list)
    {
        $list        = $list->toArray();
        $periods     = $this->getPeriode();
        $salesDetail = [];
        // dd($list);
        foreach ($list as $sales) {
            $customerId = $sales['m_customer_id'];
            $customerName = $sales['customer']['name'];
            $customerTotal = 0;
            $transactions = [];
    
            foreach ($sales['details'] as $detail) {
                // Skip if relation to product is not found
                if (empty($detail['product'])) {
                    continue;
                }
                $dateTransactions = $sales['date'];
                $totalDetailProductPrice = ($detail['product']['price'] + $detail['product_details']['price']) * $detail['total_item'];
    
                // Accumulate total sales for the customer
                $customerTotal += $totalDetailProductPrice;
    
                $transactions[] = [
                    'date' => $dateTransactions,
                    'total_sales' => $totalDetailProductPrice
                ];
            }
    
            // Aggregate total sales for each customer
            $salesDetail[$customerId] = [
                'customer_id' => $customerId,
                'customer_name' => $customerName,
                'total' => $customerTotal,
                'transaction' => $transactions
            ];
        }
    
        // return $salesDetail;
        return $this->convertNumericKey($salesDetail);
    }

    private function convertNumericKey($salesDetail)
{
    $numericSalesDetail = [];
    $indexSales = 0;
    
    foreach ($salesDetail as $sales) {
        $numericSalesDetail[$indexSales] = [
            'category_id' => $sales['customer_id'],
            'category_name' => $sales['customer_name'],
            'category_total' => $sales['total'],
            'products' => []
        ];
        
        $indexProducts = 0;
        foreach ($sales['transaction'] as $transaction) {
            $numericSalesDetail[$indexSales]['products'][$indexProducts] = [
                'product_id' => '',
                'product_name' => '',
                'transactions' => array_values($transaction),
                'transactions_total' => $transaction['total_sales']
            ];
            $indexProducts++;
        }
        
        $indexSales++;
    }
    
    return $numericSalesDetail;
}


    public function get($startDate, $endDate, $categoryId = '')
    {
        $this->startDate = $startDate;
        $this->endDate   = $endDate;

        $sales = $this->sales->getSalesByCustomers($startDate, $endDate, $categoryId);

        return [
            'status'     => true,
            'data'       => $this->reformatReport($sales, $startDate, $endDate),
            'dates'          => array_values($this->dates),
            'total_per_date' => array_values($this->totalPerDate),
            'grand_total'    => $this->total
        ];
    }
}
