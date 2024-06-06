<?php

namespace App\Helpers\Sales;

use App\Helpers\Venturo;
use App\Models\SalesModel;
use DateInterval;
use DatePeriod;
use DateTime;

class SalesCatagoryHelper extends Venturo
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
            $transactionId = $sales['id'];
            $customerId = $sales['m_customer_id'];
            $customerName = $sales['customer']['name'];
            $transactionDate = $sales['date'];
            $transactionTotal = 0;
            $products = [];

            foreach ($sales['details'] as $detail) {
                // Skip if relation to product is not found
                if (empty($detail['product'])) {
                    continue;
                }
                $productId = $detail['product']['id'];
                $productName = $detail['product']['name'];
                $productPrice = $detail['product']['price'];
                $detailProductId = $detail['product_details']['id'];
                $detailProductType = $detail['product_details']['type'];
                $detailProductDescription = $detail['product_details']['description'];
                $detailProductPrice = $detail['product_details']['price'];
                $detailTotalitem = $detail['total_item'];
                $TotalDetailProductPrice = ($detail['product']['price'] + $detail['product_details']['price']) * $detail['total_item'];
                $detailProduct = [
                    [
                        'detail_id' => $detailProductId,
                        'detail_type' => $detailProductType,
                        'detail_description' => $detailProductDescription,
                        'detail_price' => $detailProductPrice,
                        'total_item' => $detailTotalitem,
                        'price' => $TotalDetailProductPrice,
                    ]
                ];
                // Menambahkan TotalDetailProductPrice ke transactionTotal
                $transactionTotal += $TotalDetailProductPrice;

                $products[] = [
                    'product_id' => $productId,
                    'product_name' => $productName,
                    'product_price' => $productPrice,
                    'detail_product' => $detailProduct
                ];
            }

            $salesDetail[] = [
                'transactions_id' => $transactionId,
                'customer_id' => $customerId,
                'customer_name' => $customerName,
                'date_transaction' => $transactionDate,
                'transaction_total' => $transactionTotal,
                'products' => $products,

            ];
        }

        // return ($salesDetail);
        return $this->convertNumericKey($salesDetail);
    }

    private function convertNumericKey($salesDetail)
    {
        $indexSales = 0;
    
        foreach ($salesDetail as $sales) {
            $list[$indexSales] = [
                'transactions_id' => $sales['transactions_id'],
                'customer_id' => $sales['customer_id'],
                'customer_name' => $sales['customer_name'],
                'date_transaction' => $sales['date_transaction'],
                'transaction_total' => $sales['transaction_total'],
                'products' => $sales['products'],
            ];
    
            $indexSales++;
        }
    
        unset($salesDetail);
    
        return $list ?? [];
    }


    public function get($startDate, $endDate, $categoryId = '')
    {
        $this->startDate = $startDate;
        $this->endDate   = $endDate;

        $sales = $this->sales->getSales($startDate, $endDate, $categoryId);

        return [
            'status'     => true,
            'data'       => $this->reformatReport($sales, $startDate, $endDate),
            'dates'          => array_values($this->dates),
            'total_per_date' => array_values($this->totalPerDate),
            'grand_total'    => $this->total
        ];
    }
}
