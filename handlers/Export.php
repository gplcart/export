<?php

/**
 * @package Exporter
 * @author Iurii Makukh
 * @copyright Copyright (c) 2017, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0+
 */

namespace gplcart\modules\export\handlers;

use gplcart\core\models\Product as ProductModel,
    gplcart\core\models\Price as PriceModel,
    gplcart\core\models\File as FileModel,
    gplcart\core\models\Store as StoreModel;

/**
 * Handler for Exporter module
 */
class Export
{

    /**
     * Product model instance
     * @var \gplcart\core\models\Product $product
     */
    protected $product;

    /**
     * Price model instance
     * @var \gplcart\core\models\Price $price
     */
    protected $price;

    /**
     * File model instance
     * @var \gplcart\core\models\File $file
     */
    protected $file;

    /**
     * Store model instance
     * @var \gplcart\core\models\Store $store
     */
    protected $store;

    /**
     * An array of the current job data
     * @var array
     */
    protected $job = array();

    /**
     * @param ProductModel $product
     * @param PriceModel $price
     * @param FileModel $file
     * @param StoreModel $store
     */
    public function __construct(ProductModel $product, PriceModel $price,
                                FileModel $file, StoreModel $store)
    {
        $this->file = $file;
        $this->price = $price;
        $this->store = $store;
        $this->product = $product;
    }

    /**
     * Processes one job iteration
     * @param array $job
     */
    public function process(array &$job)
    {
        $this->job = &$job;

        $options = $this->job['data']['options'];
        $options['limit'] = array($this->job['done'], $this->job['data']['limit']);

        $items = (array)$this->product->getList($options);

        foreach ($items as $product) {
            $data = $this->prepare($product);
            gplcart_file_csv($this->job['data']['file'], $data, $this->job['data']['delimiter']);
        }

        if (empty($items)) {
            $this->job['status'] = false;
            $this->job['done'] = $this->job['total'];
        } else {
            $this->job['done'] += count($items);
        }
    }

    /**
     * Prepares export data
     * @param array $product
     * @return array
     */
    protected function prepare(array $product)
    {
        $data = array();
        foreach ($this->job['data']['header'] as $key => $value) {
            $data[$key] = isset($product[$key]) ? $product[$key] : '';
        }

        $this->prepareImages($data, $product);
        $this->preparePrice($data, $product);
        $this->prepareImages($data, $product);

        return $data;
    }

    /**
     * Prepares prices
     * @param array $data
     * @param array $product
     */
    protected function preparePrice(array &$data, array $product)
    {
        if (isset($data['price'])) {
            $data['price'] = $this->price->decimal($data['price'], $product['currency']);
        }
    }

    /**
     * Prepares images
     * @param array $data
     * @param array $product
     * @return null|array
     */
    protected function prepareImages(array &$data, array $product)
    {
        if (!isset($data['images'])) {
            return null;
        }

        $images = $this->getImages($product);

        if (empty($images)) {
            return null;
        }

        $store = $this->store->get($product['store_id']);

        $paths = array();
        foreach ($images as $image) {
            if (isset($store['domain'])) {
                $path = $this->store->url($store);
                $paths[] = "$path/files/{$image['path']}";
            } else {
                $paths[] = $image['path'];
            }
        }

        $data['images'] = implode($this->job['data']['multiple'], $paths);
        return $data;
    }

    /**
     * Returns an array of product images
     * @param array $product
     * @return array
     */
    protected function getImages(array $product)
    {
        $options = array(
            'order' => 'asc',
            'sort' => 'weight',
            'file_type' => 'image',
            'entity' => 'product',
            'entity_id' => $product['product_id']
        );

        return (array)$this->file->getList($options);
    }

}
