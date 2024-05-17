<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Hash;
use PHPUnit\Event\Code\Throwable;
use App\Http\Traits\Uuid;
use App\Models\ProductDetailModel;
use App\Models\ProductModel;

class ProductHelper extends Venturo
{
    const PRODUCT_PHOTO_DIRECTORY = 'foto-produk';
    private $product;
    private $productDetail;

    public function __construct()
    {
        $this->product = new ProductModel();
        $this->productDetail = new ProductDetailModel();
    }


    private function uploadAndGetPayload(array $payload)
    {
        if (!empty($payload['photo'])) {
            $fileName = $this->generateFileName($payload['photo'], 'PRODUCT_' . date('Ymdhis'));
            $photo = $payload['photo']->storeAs(self::PRODUCT_PHOTO_DIRECTORY, $fileName, 'public');
            $payload['photo'] = $photo;
        } else {
            unset($payload['photo']);
        }

        return $payload;
    }

    public function create(array $payload): array
    {
        try {
            $payload = $this->uploadAndGetPayload($payload);

            $this->beginTransaction();

            $product = $this->product->store($payload);

            $this->insertUpdateDetail($payload['details'] ?? [], $product->id);

            $this->commitTransaction();

            return [
                'status' => true,
                'data' => $product
            ];
        } catch (\Throwable $th) {
            $this->rollbackTransaction();

            return [
                'status' => false,
                'error' => $th->getMessage()
            ];
        }
    }


    public function delete(int $productId)
    {
        try {
            $this->beginTransaction();

            $this->product->drop($productId);

            $this->productDetail->dropByProductId($productId);

            $this->commitTransaction();

            return [
                'status' => true,
                'data' => $productId
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
        $categories = $this->product->getAll($filter, $itemPerPage, $sort);

        return [
            'status' => true,
            'data' => $categories
        ];
    }


    public function getById(string $id): array
    {
        $product = $this->product->getById($id);
        if (empty($product)) {
            return [
                'status' => false,
                'data' => null
            ];
        }

        return [
            'status' => true,
            'data' => $product
        ];
    }

    public function update(array $payload): array
    {
        try {
            $payload = $this->uploadAndGetPayload($payload);

            $this->beginTransaction();

            $this->product->edit($payload, $payload['id']);

            $this->insertUpdateDetail($payload['details'] ?? [], $payload['id']);
            $this->deleteDetail($payload['details_deleted'] ?? []);

            $product = $this->getById($payload['id']);
            $this->commitTransaction();

            return [
                'status' => true,
                'data' => $product['data']
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
            $this->productDetail->delete($val['id']);
        }
    }

    private function insertUpdateDetail(array $details, int $productId)
    {
        if (empty($details)) {
            return false;
        }

        foreach ($details as $val) {
            // Insert
            if (isset($val['is_added']) && $val['is_added']) {
                $val['m_product_id'] = $productId;
                $this->productDetail->store($val);
            }

            // Update
            if (isset($val['is_updated']) && $val['is_updated']) {
                $this->productDetail->edit($val, $val['id']);
            }
        }
    }
}
