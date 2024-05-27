<?php

namespace App\Helpers\Sales;

use App\Helpers\Venturo;
use App\Http\Traits\Uuid;
use App\Models\SalesDetailModel;
use App\Models\SalesModel;
use Carbon\Carbon;

class SalesHelper extends Venturo
{
    use Uuid;
    private $sales;
    private $salesDetail;

    public function __construct()
    {
        $this->sales = new SalesModel();
        $this->salesDetail = new SalesDetailModel();
    }

    public function create(array $payload): array
    {
        try {

            $this->beginTransaction();

            $sales = $this->sales->store($payload);

            $this->insertUpdateDetail($payload['details'] ?? [], $sales->id);

            $this->commitTransaction();

            return [
                'status' => true,
                'data' => $sales
            ];
        } catch (\Throwable $th) {
            $this->rollbackTransaction();

            return [
                'status' => false,
                'error' => $th->getMessage()
            ];
        }
    }

    public function delete(int $salesId)
    {
        try {
            $this->beginTransaction();

            $this->sales->drop($salesId);

            $this->salesDetail->dropBysalesId($salesId);

            $this->commitTransaction();

            return [
                'status' => true,
                'data' => $salesId
            ];
        } catch (\Throwable $th) {
            $this->rollbackTransaction();

            return [
                'status' => false,
                'error' => $th->getMessage()
            ];
        }
    }

    public function getAll(array $filter, int $itemPerPage = 0, string $sort = '')
    {
        $sales = $this->sales->getAll($filter, $itemPerPage, $sort);

        return [
            'status' => true,
            'data' => $sales
        ];
    }


    public function getById(string $id): array
    {
        $sales = $this->sales->getById($id);
        if (empty($sales)) {
            return [
                'status' => false,
                'data' => null
            ];
        }

        return [
            'status' => true,
            'data' => $sales
        ];
    }

    public function update(array $payload): array
    {
        try {

            $this->beginTransaction();

            $this->sales->edit($payload, $payload['id']);

            $this->insertUpdateDetail($payload['details'] ?? [], $payload['id']);
            $this->deleteDetail($payload['details_deleted'] ?? []);

            $sales = $this->getById($payload['id']);
            $this->commitTransaction();

            return [
                'status' => true,
                'data' => $sales['data']
            ];
        } catch (\Throwable $th) {
            $this->rollbackTransaction();

            return [
                'status' => false,
                'error' => $th->getMessage()
            ];
        }
    }


    private function deleteDetail(array $details)
    {
        if (empty($details)) {
            return false;
        }


        foreach ($details as $val) {
            $this->salesDetail->delete($val['id']);
        }
    }

    private function insertUpdateDetail(array $details, string $productId)
    {
        if (empty($details)) {
            return false;
        }

        foreach ($details as $val) {
            // Insert
            if (isset($val['is_added']) && $val['is_added']) {
                $val['t_sales_id'] = $productId;
                $val['date'] = Carbon::now(); 
                $this->salesDetail->store($val);
            }

            // Update
            if (isset($val['is_updated']) && $val['is_updated']) {
                $this->salesDetail->edit($val, $val['id']);
            }
        }
    }
}
