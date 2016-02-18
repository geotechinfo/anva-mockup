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
        try {
            $result = $this->elasticStorage->lists();
            $products = $result['hits']['hits'];

            $response = [];
            foreach ($products as $product) {
                $response[] = $product['_source'];
            }
            return $this->listResponse($response);
        } catch (\Exception $e) {
            return $this->errorResponse();
        }
    }
    
    public function search(Request $request)
    {   
        // Search parameters
        /*
        nameText => Your search text for product name, default: blank(without filter);
        priceMin => Minimum product price, default: blank(without filter);
        priceMax => Maximum product price, default: blank(without filter);
        operator => Operator between nameText and priceRange, possible values: and|or, default: and;
        */      
        $nameText = $request->get("nameText");
        $priceMin = $request->get("priceMin");
        $priceMax = $request->get("priceMax");
        $operator = $request->get("operator");
        
        // Search normalizations
        $nameText = (strlen($nameText)>0) ? $nameText : "";
        $priceMin = (strlen($priceMin)>0) ? $priceMin : null;
        $priceMax = (strlen($priceMax)>0) ? $priceMax : null;
        $oprtator = (strlen($operator)>0) ? $wildcard : "and";
        
        // Search validations
        if(!is_null($priceMin) && !(filter_var($priceMin, FILTER_VALIDATE_FLOAT) && $priceMin > 0)){
            return $this->errorResponse("Minimum price must be a positive float value!");
        }
        if(!is_null($priceMax) && !(filter_var($priceMax, FILTER_VALIDATE_FLOAT) && $priceMax > 0)){
            return $this->errorResponse("Maximum price must be a positive float but greater than 0!");
        }
        if(!is_null($priceMin) && !is_null($priceMax) && $priceMin>$priceMax){
            return $this->errorResponse("Minimum price should be less than maximum price!");
        }
        if(!in_array($oprtator, array("or", "and"))){
            return $this->errorResponse("You should provice a valid operator, e.g. and/or!");
        }
        
        // Sort parameters
        /*
        sortField => Sort field name, possible values: id|price, default: id;
        sortOrder => Sort field order, possible values: asc|desc, default: desc;
        */
        $sortField = $request->get("sortField");
        $sortOrder = $request->get("sortOrder");
        
        // Sort normalizations
        echo $sortField = (strlen($sortField)>0) ? $sortField : "id";
        echo $sortOrder = (strlen($sortOrder)>0) ? $sortOrder : "desc";
        
        // Sort validations
        if(!in_array($sortField, array("id", "price"))){
            return $this->errorResponse("You should use id/price field for sort items!");
        }
        if(!in_array($sortOrder, array("asc", "desc"))){
            return $this->errorResponse("You should use asc/desc for field sorting order!");
        }
        
        // Paginate parameters
        /*
        itemFrom => Start from item no, default: 0;
        listSize => Total item to be displayed, default: 10
        */
        $itemFrom = $request->get("itemFrom");
        $listSize = $request->get("listSize");
        
        // Paginate normalizations
        $itemFrom = (strlen($itemFrom)>0) ? $itemFrom : 0;
        $listSize = (strlen($listSize)>0) ? $listSize : 10;
        
        // Paginate validations
        if(!filter_var($itemFrom, FILTER_VALIDATE_INT) || $itemFrom < 0){
            return $this->errorResponse("Start item for pagination should be a positive integer!");
        }
        if(!filter_var($listSize, FILTER_VALIDATE_INT) || $listSize < 1){
            return $this->errorResponse("You should provide positive integer but not 0 for list size!");
        }
        
        // Search query
        $search = array(
            "query"=>array(
                "filtered"=>array(
                    "filter"=>array(
                        $oprtator=>array(
                            array(
                                "query"=>array(
                                    "wildcard" => array(
                                        "name"=> array(
                                            "value"=>"*".$nameText."*"
                                        )
                                    )
                                )
                            ),
                            array(
                                "range"=>array(
                                    "price"=>array(
                                        "gte"=>$priceMin,
                                        "lt"=>$priceMax
                                    )
                                )
                            )
                        )
                    )
                )
            )
        );
        
        try {
            $result = $this->elasticStorage->lists($search, array($sortField=>$sortOrder), $itemFrom, $listSize);
            $products = $result['hits']['hits'];

            $response = [];
            foreach ($products as $product) {
                $response[] = $product['_source'];
            }
            return $this->listResponse($response);
        } catch (\Exception $e) {
            return $this->errorResponse("Something went wrong!");
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

    protected function errorResponse($message)
    {
        $response = [
            'code' => 500,
            'status' => 'error',
            'message' => $message
        ];
        return response()->json($response, $response['code']);
    }
}
