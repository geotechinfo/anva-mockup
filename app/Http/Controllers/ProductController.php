<?php
namespace App\Http\Controllers;
use Laravel\Lumen\Routing\Controller as BaseController;

use App\Services\ElasticStorage;
use Illuminate\Http\Request;

class ProductController extends BaseController
{
    const STORAGE_TYPE = 'product';
    private $elasticStorage;

    /**
     * Construct
     * @param ElasticStorage $elasticStorage
     */
    public function __construct(ElasticStorage $elasticStorage)
    {
        $this->elasticStorage = $elasticStorage;
        $this->elasticStorage->setType(self::STORAGE_TYPE);
    }

    public function index(Request $request)
    {
        return "Please use product/list(GET) or product/search(POST) request!";
    }
    
    public function lists(Request $request)
    {
        $searchParams = array();
        try {
            $result = $this->elasticStorage->lists();
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
