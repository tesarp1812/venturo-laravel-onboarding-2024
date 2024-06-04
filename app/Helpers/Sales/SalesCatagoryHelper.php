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
            foreach ($sales['customer'] as $detail) {


                $date                   = date('Y-m-d', strtotime($sales['date']));
                $testId                 = $detail['id'];
                

                $salesDetail[$testId] = [
                    
                    'id'    => $testId,
                    
                ];

                
            }
        }

        return $this->convertNumericKey($salesDetail);
    }

    private function convertNumericKey($salesDetail)
    {
        $indexSales = 0;

        foreach ($salesDetail as $sales) {
            $list[$indexSales] = [
                'category_id'    => $sales['category_id'],
                'test'           => $sales['id'],
                'category_name'  => $sales['category_name'],
                'category_total' => $sales['category_total']
            ];

            $indexProducts = 0;
            foreach ($sales['products'] as $product) {
                $list[$indexSales]['products'][$indexProducts] = [
                    'product_id'         => $product['product_id'],
                    'product_name'       => $product['product_name'],
                    'transactions'       => array_values($product['transactions']),
                    'transactions_total' => $product['transactions_total']
                ];

                $indexProducts++;
            }

            $indexSales++;
        }

        unset($salesDetail);

        return $list ?? [];
    }


    public function get($startDate, $endDate, $categoryId = '')
    {
        $this->startDate = $startDate;
        $this->endDate   = $endDate;

        $sales = $this->sales->getSalesByCategory($startDate, $endDate, $categoryId);

        return [
            'status'     => true,
            'data'       => $this->reformatReport($sales, $startDate, $endDate),
            'dates'          => array_values($this->dates),
            'total_per_date' => array_values($this->totalPerDate),
            'grand_total'    => $this->total
        ];
    }
}
