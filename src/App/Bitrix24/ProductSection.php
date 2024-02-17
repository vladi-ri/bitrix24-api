<?php

declare(strict_types=1);

namespace app\Bitrix24;

use Generator;

/**
 * Trait ProductSection.
 * Methods for working with ProductSection in Bitrix24.
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
trait ProductSection
{
    /**
     * Return description of product section fields, including custom fields.
     * 
     * Action: 'crm.productsection.fields'
     * @see    https://training.bitrix24.com/rest_help/crm/product_section/crm_productsection_fields.php
     * 
     * @access public
     * @return array
     */
    public function getProductSectionFields() : array {
        return $this->request('crm.productsection.fields');
    }

    /**
     * Return product section by ID.
     * 
     * Action: 'crm.productsection.get'
     * @see    https://training.bitrix24.com/rest_help/crm/product_section/crm_productsection_get.php
     * 
     * @param  int|string $productSectionID ID раздела товаров
     * 
     * @access public
     * @return array
     */
    public function getProductSection(int|string $productSectionID) : array {
        $productSection = $this->request(
            'crm.productsection.get', [
                'id' => $productSectionID
            ]
        );

        return $productSection;
    }

    /**
     * Add product section.
     * 
     * Action: 'crm.productsection.add'
     * @see    https://training.bitrix24.com/rest_help/crm/product_section/crm_productsection_add.php
     * 
     * @param  array $fields List of product section fields
     * 
     * @access public
     * @return int
     */
    public function addProductSection(array $fields = []) : int {
        $result = $this->request(
            'crm.productsection.add', [
                'fields' => $fields
            ]
        );

        return $result;
    }

    /**
     * Update product section.
     * 
     * Action: 'crm.productsection.update'
     * @see    https://training.bitrix24.com/rest_help/crm/product_section/crm_productsection_update.php
     * 
     * @param  int|string $productSectionID Product section ID
     * @param  array      $fields           List of product section fields
     * 
     * @access public
     * @return int
     */
    public function updateProductSection($productSectionID, array $fields = []) : int {
        $result = $this->request(
            'crm.productsection.update', [
                'id'     => $productSectionID,
                'fields' => $fields
            ]
        );

        return $result;
    }

    /**
     * Delete product section by ID.
     * 
     * Action: 'crm.product.delete'
     * @see    https://training.bitrix24.com/rest_help/crm/products/crm_product_delete.php
     * 
     * @param  int|string $productSectionID Product section ID
     * 
     * @access public
     * @return int
     */
    public function deleteProductSection($productSectionID) : int {
        $result = $this->request(
            'crm.product.delete', [
                'id' => $productSectionID
            ]
        );

        return $result;
    }

    /**
     * Return all product sections.
     * 
     * Action: 'crm.productsection.list'
     * @see    https://training.bitrix24.com/rest_help/crm/product_section/crm_productsection_list.php
     * 
     * @param  array $filter Filtering parameters
     * @param  array $select Selection parameters
     * @param  array $order  Sorting parameters
     * 
     * @access public
     * @return Generator
     */
    public function getProductSectionList(
        array $filter = [],
        array $select = [],
        array $order = []
    ) : Generator {
        $params = [
            'order'  => $order,
            'filter' => $filter,
            'select' => $select
        ];

        return $this->getList('crm.productsection.list', $params);
    }

    /**
     * Return all product sections using the quick method.
     * 
     * Action: 'crm.productsection.list'
     * @see    https://training.bitrix24.com/rest_help/crm/product_section/crm_productsection_list.php
     * @see    https://dev.1c-bitrix.ru/rest_help/rest_sum/start.php
     * 
     * @param  array $filter Filtering parameters
     * @param  array $select Selection parameters
     * @param  array $order  Sorting parameters
     * 
     * @access public
     * @return Generator
     */
    public function fetchProductSectionList(
        array $filter = [],
        array $select = [],
        array $order = []
    ) : Generator {
        $params = [
            'order'  => $order,
            'filter' => $filter,
            'select' => $select
        ];

        return $this->fetchList('crm.productsection.list', $params);
    }

    /**
     * Adds product sections.
     * Return array of IDs of product sections
     * 
     * Action: crm.productsection.add
     * @see    https://training.bitrix24.com/rest_help/crm/product_section/crm_productsection_add.php
     * 
     * @param  array $productSections Array of product sections
     * @return array
     */
    public function addProductSections(array $productSections = []) : array {
        // IDs of added product sections
        $productSectionResults = [];

        while ($productSectionsChunk = array_splice($productSections, 0, $this->batchSize)) {
            $commandParams = [];

            foreach ($productSectionsChunk as $index => $productSection) {
                $commandParams[] = [ 'fields' => $productSection ];
            }

            $commands = $this->buildCommands('crm.productsection.add', $commandParams);
            $result   = $this->batchRequest($commands);

            $sent     = count($commandParams);
            $received = count($result);

            if ($received != $sent) {
                $jsonResponse = $this->toJSON($this->lastResponse);

                throw new Bitrix24APIException(
                    "Unable to add product sections ({$sent} / {$received}): {$jsonResponse}"
                );
            }

            $productSectionResults = array_merge($productSectionResults, $result);
        }

        return $productSectionResults;
    }

    /**
     * Update product sections.
     * Return array of IDs of product sections
     * 
     * Action: 'crm.productsection.update'
     * @see    https://training.bitrix24.com/rest_help/crm/product_section/crm_productsection_update.php
     * 
     * @param  array $productSections Array of product sections
     * 
     * @access public
     * @return array
     */
    public function updateProductSections(array $productSections = []) : array {
        // IDs of updated product sections
        $productSectionResults = [];

        while ($productSectionsChunk = array_splice($productSections, 0, $this->batchSize)) {
            $commandParams = [];

            foreach ($productSectionsChunk as $index => $productSection) {
                // Check if the ID field is available in the product for adding
                $productSectionID = $productSection['ID'] ?? null;

                if (empty($productSectionID)) {
                    $jsonProductSection = $this->toJSON($productSection);

                    throw new Bitrix24APIException(
                        "The 'ID' field in the product section (index {$index}) on the update is missing or empty: '{$jsonProductSection}'"
                    );
                }

                $productSectionResults[] = $productSectionID;

                $commandParams[]         = [
                    'id'     => $productSectionID,
                    'fields' => $productSection
                ];
            }

            $commands = $this->buildCommands('crm.productsection.update', $commandParams);
            $result   = $this->batchRequest($commands);

            $sent     = count($commandParams);
            $received = count($result);

            if ($received != $sent) {
                $jsonResponse = $this->toJSON($this->lastResponse);

                throw new Bitrix24APIException(
                    "Unable to update product section ({$sent} / {$received}): {$jsonResponse}"
                );
            }
        }

        return $productSectionResults;
    }

    /**
     * Delete product sections
     * 
     * Action: 'crm.productsection.delete'
     * @see    https://training.bitrix24.com/rest_help/crm/product_section/crm_productsection_delete.php
     * 
     * @param  array $productSectionIDs Array of product section IDs
     * 
     * @access public
     * @return array Массив Id разделов товаров
     */
    public function deleteProductSections(array $productSectionIDs = []) : array {
        // ID of deleted product sections
        $productSectionResults = [];

        while ($productSectionsChunk = array_splice($productSectionIDs, 0, $this->batchSize)) {
            $commandParams = [];

            foreach ($productSectionsChunk as $index => $productSectionID) {
                $commandParams[]         = ['id' => $productSectionID];
                $productSectionResults[] = $productSectionID;
            }

            $commands = $this->buildCommands('crm.productsection.delete', $commandParams);
            $result   = $this->batchRequest($commands);

            $sent     = count($commandParams);
            $received = count($result);

            if ($received != $sent) {
                $jsonResponse = $this->toJSON($this->lastResponse);

                throw new Bitrix24APIException(
                    "Unable to delete product sections ({$sent} / {$received}): {$jsonResponse}"
                );
            }
        }

        return $productSectionResults;
    }
}
