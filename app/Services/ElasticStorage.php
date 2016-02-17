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
    public function lists($from=0, $size=10, $search=array())
    {
        $params['index'] = $this->index;
        $params['from']  = $from;
        $params['size']  = $size;
        $params['body']  = $search;
        return $this->client->search($params);
    }
}
