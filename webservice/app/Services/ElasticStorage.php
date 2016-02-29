<?php
namespace App\Services;

use Elasticsearch\Client;

class ElasticStorage implements ElasticStorageInterface
{

    private $client;
    private $index;
    private $type;


    public function __construct()
    {
        $params = [
            'hosts' => [ENV('ELASTIC_HOST')],
            'logging' => ENV('ELASTIC_LOGGING'),
        ];
        $this->client = new Client($params);
        $this->index = ENV('ELASTIC_INDEX');
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Get item list
     * @param $searchBody
     * @return mixed
     */    
    public function lists($search=array(), $sort=array("id"=>"desc"), $from=0, $size=50)
    {
        $params['index'] = $this->index;
        $params['from']  = $from;
        $params['size']  = $size;        
        $params['body']  = array();
        
        $params['body'] = (count($search)>0) ? array_merge($params['body'], $search) : $params['body'];
        $params['body'] = array_merge($params['body'], array("sort"=>$sort));

        return $this->client->search($params);
    }
}
