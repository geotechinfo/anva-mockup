<?php
namespace App\Services;

interface ProductStorage
{

    /**
     * Set index type key
     *
     * @param $type
     * @return mixed
     */
    public function setType($type);

    /**
     * Index an item
     *
     * @param $type
     * @param $id
     * @param array $body
     * @return mixed
     */
    public function index($id, array $body, $type = null);

    /**
     * Get an item
     * @param $type
     * @param $id
     * @return mixed
     */
    public function get($id, $type = null);

    /**
     * Search for items
     * @param $type
     * @param $searchBody
     * @return mixed
     */
    public function search($searchBody, $type = null);

    /**
     * Delete an item
     *
     * @param $type
     * @param $id
     * @return mixed
     */
    public function delete($id, $type = null);

    /**
     * Mapping
     *
     * @param $params
     */
    public function mapping($params);

    /**
     * Delete index
     *
     * @param $params
     */
    public function deleteMapping($params);

    /**
     * Delete index
     *
     * @param $params
     * @return mixed
     */
    public function deleteIndex($params);
}
