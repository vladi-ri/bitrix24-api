<?php

declare(strict_types=1);

namespace app\Bitrix24;

use Generator;

/**
 * Trait Product
 * Methods for working with products in Bitrix24.
 * 
 * @author    vladi-ri
 * @copyright 2024 vladi-ri
 * @see       https://github.com/vladi-ri/bitrix24-api
 * @license   OpenSource
 *
 * @version 1.0.0
 * 
 * v1.0.0 (17.02.2024) Introduce Bitrix24API PHP project
 */
trait Product
{
    /**
     * Return a description of product fields, including custom fields.
     * 
     * Action: 'crm.product.fields'
     * @see    https://training.bitrix24.com/rest_help/crm/products/crm_product_fields.php
     * 
     * @access public
     * @return array
     */
    public function getProductFields() : array {
        return $this->request('crm.product.fields');
    }

    /**
     * Get product by ID.
     * 
     * Action: 'crm.product.get'
     * @see    https://training.bitrix24.com/rest_help/crm/products/crm_product_get.php
     * 
     * @param  int|string $productID Product ID
     * 
     * @access public
     * @return array
     */
    public function getProduct(int|string $productID) : array {
        $product = $this->request(
            'crm.product.get', [
                'id' => $productID
            ]
        );

        return $product;
    }

    /**
     * Add a product.
     * 
     * Action: 'crm.product.add'
     * @see    https://training.bitrix24.com/rest_help/crm/products/crm_product_add.php
     * 
     * @param  array $fields List of product fields
     * 
     * @access public
     * @return int
     */
    public function addProduct(array $fields = []) : int {
        $result = $this->request(
            'crm.product.add', [
                'fields' => $fields
            ]
        );

        return $result;
    }

    /**
     * Update a product.
     * 
     * Action: 'crm.product.update'
     * @see    https://training.bitrix24.com/rest_help/crm/products/crm_product_update.php
     * 
     * @param  int|string $productID Product ID
     * @param  array      $fields    Список полей товара
     * 
     * @access public
     * @return int
     */
    public function updateProduct($productID, array $fields = []) : int {
        $result = $this->request(
            'crm.product.update', [
                'id'     => $productID,
                'fields' => $fields
            ]
        );

        return $result;
    }

    /**
     * Delete product by ID.
     * 
     * Action: 'crm.product.delete'
     * @see    https://training.bitrix24.com/rest_help/crm/products/crm_product_delete.php
     * 
     * @param  string|int $productID Product ID
     * 
     * @access public
     * @return array
     */
    public function deleteProduct(string|int $productID) : array {
        $result = $this->request(
            'crm.product.delete', [
                'id' => $productID
            ]
        );

        return $result;
    }

    /**
     * Return all products.
     * 
     * Action: 'crm.product.list'
     * @see    https://training.bitrix24.com/rest_help/crm/products/crm_product_list.php
     * 
     * @param  array $filter Filtering parameters
     * @param  array $order  Ordering parameters
     * @param  array $select Selection parameters
     * 
     * @access public
     * @return Generator
     */
    public function getProductList(
        array $filter = [],
        array $select = [ '*', 'PROPERTY_*' ],
        array $order = []
    ): Generator {
        $params = [
            'order'  => $order,
            'filter' => $filter,
            'select' => $select
        ];

        return $this->getList('crm.product.list', $params);
    }

    /**
     * Return all products using the quick method.
     * 
     * Action: 'crm.product.list'
     * @see    https://training.bitrix24.com/rest_help/crm/products/crm_product_list.php
     * @see    https://dev.1c-bitrix.ru/rest_help/rest_sum/start.php
     * 
     * @param  array $filter Filtering parameters
     * @param  array $order  Ordering parameters
     * @param  array $select Selection parameters
     * 
     * @access public
     * @return Generator
     */
    public function fetchProductList(
        array $filter = [],
        array $select = ['*', 'PROPERTY_*'],
        array $order = []
    ) : Generator {
        $params = [
            'order'  => $order,
            'filter' => $filter,
            'select' => $select
        ];

        return $this->fetchList('crm.product.list', $params);
    }

    /**
     * Add products.
     * Return array of product IDs.
     * 
     * Action: 'crm.product.add'
     * @see    https://training.bitrix24.com/rest_help/crm/products/crm_product_add.php
     * 
     * @param  array $products Products array - Product section 'SECTION_ID'
     * 
     * @access public
     * @return array
     */
    public function addProducts(array $products = []) : array {
        // IDs of added products
        $productResults = [];

        while ($productsChunk = array_splice($products, 0, $this->batchSize)) {
            $commandParams = [];

            foreach ($productsChunk as $index => $product) {
                $commandParams[] = [ 'fields' => $product ];
            }

            $commands = $this->buildCommands('crm.product.add', $commandParams);
            $result   = $this->batchRequest($commands);

            $sent     = count($commandParams);
            $received = count($result);

            if ($received != $sent) {
                $jsonResponse = $this->toJSON($this->lastResponse);

                throw new Bitrix24APIException(
                    "Unable to add products ({$sent} / {$received}): {$jsonResponse}"
                );
            }

            $productResults = array_merge($productResults, $result);
        }

        return $productResults;
    }

    /**
     * Update products.
     * Return array of product IDs.
     * 
     * Action: 'crm.product.update'
     * @see    https://training.bitrix24.com/rest_help/crm/products/crm_product_update.php
     * 
     * @param  array $products Products array
     * 
     * @access public
     * @return array
     */
    public function updateProducts(array $products = []) : array {
        // IDs of updated products
        $productResults = [];

        while ($productsChunk = array_splice($products, 0, $this->batchSize)) {
            $commandParams = [];

            foreach ($productsChunk as $index => $product) {
                // Check if the ID field is available in the product for adding
                $productID = $product['ID'] ?? null;

                if (empty($productID)) {
                    $jsonProduct = $this->toJSON($product);

                    throw new Bitrix24APIException(
                        "The 'ID' field in the product (index {$index}) on the update is missing or empty: '{$jsonProduct}'"
                    );
                }

                $productResults[] = $productID;

                $commandParams[] = [
                    'id'     => $productID,
                    'fields' => $product
                ];
            }

            $commands = $this->buildCommands('crm.product.update', $commandParams);
            $result   = $this->batchRequest($commands);

            $sent     = count($commandParams);
            $received = count($result);

            if ($received != $sent) {
                $jsonResponse = $this->toJSON($this->lastResponse);

                throw new Bitrix24APIException(
                    "Unable to update products ({$sent} / {$received}): {$jsonResponse}"
                );
            }
        }

        return $productResults;
    }

    /**
     * Delete products.
     * Return array of product IDs
     * 
     * @param  array $productIDs Array of product IDs
     * 
     * @access public
     * @return array
     */
    public function deleteProducts(array $productIDs = []) : array {
        // IDs of deleted items
        $productResults = [];

        while ($productsChunk = array_splice($productIDs, 0, $this->batchSize)) {
            $commandParams = [];

            foreach ($productsChunk as $index => $productID) {
                $commandParams[]  = ['id' => $productID];
                $productResults[] = $productID;
            }

            $commands = $this->buildCommands('crm.product.delete', $commandParams);
            $result   = $this->batchRequest($commands);

            $sent     = count($commandParams);
            $received = count($result);

            if ($received != $sent) {
                $jsonResponse = $this->toJSON($this->lastResponse);

                throw new Bitrix24APIException(
                    "Unable to delete products ({$sent} / {$received}): {$jsonResponse}"
                );
            }
        }

        return $productResults;
    }
}
