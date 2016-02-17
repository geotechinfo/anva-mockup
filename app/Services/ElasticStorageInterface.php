<?php
namespace App\Services;

interface ElasticStorageInterface
{

    /**
     * Set index type key
     *
     * @param $type
     * @return mixed
     */
    public function setType($type);
    
    /**
     * Get item list
     * @param $searchBody
     * @return mixed
     */
    public function lists($from=0, $size=10, $search=array());
}
