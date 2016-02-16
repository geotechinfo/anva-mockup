<?php
namespace App\Http\Controllers;
use Laravel\Lumen\Routing\Controller as BaseController;

use App\Services\ProductStorage;
use Illuminate\Http\Request;
use Symfony\Component\Config\Definition\Exception\Exception;

class ProductController extends BaseController
{
    const PRODUCTSTORAGE_TYPE = 'product';
    private $productStorage;

    /**
     * Construct
     * @param ProductStorage $productStorage
     */
    public function __construct(ProductStorage $productStorage)
    {
        $this->productStorage = $productStorage;
        $this->productStorage->setType(self::PRODUCTSTORAGE_TYPE);
    }

    public function index(Request $request)
    {
        $searchParams = array();
        try {
            $result = $this->productStorage->search($searchParams);
            $tasks = $result['hits']['hits'];

            $response = [];
            foreach ($tasks as $task) {
                $response[] = $task['_source'];
            }
            return $this->listResponse($response);
        } catch (\Exception $e) {
            return $this->errorResponse();
        }


    }

    private function listResponse($data)
    {
        $response = [
            'code' => 200,
            'status' => 'success',
            'data' => $data
        ];
        return response()->json($response, $response['code']);
    }

    protected function errorResponse()
    {
        $response = [
            'code' => 500,
            'status' => 'error',
            'message' => 'Can not process request'
        ];
        return response()->json($response, $response['code']);
    }
}
