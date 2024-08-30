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

        $dates = []; // Inisialisasi variabel $dates
        foreach ($period as $dt) {
            $date = $dt->format('Y-m-d');
            $dates[$date] = [
                'date_transaction' => $date,
                'total_sales'      => 0,
            ];

            $this->setDefaultTotal($date);
            $this->setSelectedDate($date);
        }

        return $dates ?? []; // Mengembalikan array kosong jika $dates adalah null
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
        $list = $list->toArray();
        $periods = $this->getPeriode();
        $salesDetail = [];

        // Pastikan $list adalah array
        if (!is_array($list)) {
            $list = []; // Atau tangani sesuai kebutuhan
        }

        foreach ($list as $sales) {
            $customerId = $sales['m_customer_id'] ?? null;
            $customerName = $sales['customer']['name'] ?? 'Unknown';
            $customerTotal = 0;
            $transactions = [];

            foreach ($sales['details'] as $detail) {
                if (empty($detail['product'])) {
                    continue;
                }
                $dateTransactions = $sales['date'] ?? 'Unknown';
                $totalDetailProductPrice = ($detail['product']['price'] ?? 0 + $detail['product_details']['price'] ?? 0) * ($detail['total_item'] ?? 0);

                $customerTotal += $totalDetailProductPrice;

                $transactions[] = [
                    'date' => $dateTransactions,
                    'total_sales' => $totalDetailProductPrice
                ];
            }

            $salesDetail[$customerId] = [
                'customer_id' => $customerId,
                'customer_name' => $customerName,
                'total' => $customerTotal,
                'transaction' => $transactions
            ];
        }

        return $this->convertNumericKey($salesDetail);
    }

    private function convertNumericKey($salesDetail)
    {
        // Pastikan $salesDetail adalah array
        if (!is_array($salesDetail)) {
            $salesDetail = []; // Atau tangani sesuai kebutuhan
        }

        $numericSalesDetail = [];
        $indexSales = 0;

        foreach ($salesDetail as $sales) {
            // Tambahkan pemeriksaan untuk memastikan semua elemen ada
            if (!isset($sales['customer_id']) || !isset($sales['customer_name']) || !isset($sales['total']) || !isset($sales['transaction'])) {
                continue; // Lewati elemen yang tidak lengkap
            }

            $numericSalesDetail[$indexSales] = [
                'category_id' => $sales['customer_id'],
                'category_name' => $sales['customer_name'],
                'category_total' => $sales['total'],
                'products' => []
            ];

            $indexProducts = 0;
            foreach ($sales['transaction'] as $transaction) {
                // Tambahkan pemeriksaan untuk memastikan transaksi memiliki total_sales
                if (!isset($transaction['total_sales'])) {
                    continue; // Lewati transaksi yang tidak lengkap
                }

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
            'data'       => $this->reformatReport($sales),
            'dates'      => array_values($this->dates),
            'total_per_date' => array_values($this->totalPerDate),
            'grand_total'    => $this->total
        ];
    }
}
