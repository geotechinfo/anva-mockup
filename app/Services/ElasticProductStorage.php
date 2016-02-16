<?php
namespace App\Services;

use Elasticsearch\Client;

class ElasticProductStorage implements ProductStorage
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
     * Index an item
     *
     * @param $type
     * @param $id
     * @param array $body
     * @return mixed
     */
    public function index($id, array $body, $type = null)
    {
        $params = array();
        $params['body']  = $body;
        $params['index'] = $this->index;
        $params['type']  = is_null($type) ? $this->type : $type;
        $params['id']    = $id;
        return $this->client->index($params);
    }

    /**
     * Get an item
     * @param $type
     * @param $id
     * @return mixed
     */
    public function get($id, $type = null)
    {
        $params = array();
        $params['index'] = $this->index;
        $params['type']  = is_null($type) ? $this->type : $type;
        $params['id']    = $id;
        return $this->client->get($params);
    }

    /**
     * Search for items
     * @param $type
     * @param $searchBody
     * @return mixed
     */
    public function search($searchBody, $type = null)
    {
        $params['index'] = $this->index;
        $params['type']  = is_null($type) ? $this->type : $type;
        $params['body']  = $searchBody;
        return $this->client->search($params);
    }

    /**
     * Delete an item
     *
     * @param $type
     * @param $id
     * @return mixed
     */
    public function delete($id, $type = null)
    {
        $params = array();
        $params['index'] = $this->index;
        $params['type']  = is_null($type) ? $this->type : $type;
        $params['id'] = $id;
        return $this->client->delete($params);
    }

    /**
     * Mapping for indices
     *
     * @param $params
     * @return mixed
     */
    public function mapping($params)
    {
       return $this->client->indices()->create($params);
    }

    /**
     * Delete mapping
     *
     * @param $params
     * @return mixed
     */
    public function deleteMapping($params)
    {
        return $this->client->indices()->deleteMapping($params);
    }

    /**
     * Delete index
     *
     * @param $params
     * @return mixed
     */
    public function deleteIndex($params)
    {
        return $this->client->indices()->delete($params);
    }
}
