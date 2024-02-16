<?php

declare(strict_types=1);

namespace app\Bitrix24;

use Generator;

/**
 * Trait Catalog
 * Methods for working with product catalogue in Bitrix24.
 *
 * @author    vladi-ri
 * @copyright 2024 vladi-ri
 * @see       https://github.com/vladi-ri/bitrix24-api-php
 * @license   OpenSource
 *
 * @version 1.0.0
 *
 * v1.0.0 (16.02.2024) Introduce Bitrix24API PHP project
 */
trait Catalog
{
    /**
     * Returns the description of the product catalogue fields.
     * 
     * Action: 'crm.catalog.fields'
     * @see:    https://training.bitrix24.com/rest_help/crm/catalog/crm_catalog_fields.php
     * 
     * @access public
     * @return array
     */
    public function getCatalogFields() : array {
        return $this->request('crm.catalog.fields');
    }

    /**
     * Return all product catalogues.
     *
     * Action: 'crm.catalog.list'
     * @see    https://dev.1c-bitrix.ru/rest_help/crm/catalog/crm_catalog_list.php
     * 
     * @param  array $filter Filter parameters
     * @param  array $select Selection parameter
     * @param  array $order  Sorting parameter
     * 
     * @access public
     * @return Generator
     */
    public function getCatalogList(
        array $filter = [],
        array $select = [],
        array $order = []
    ) : Generator {
        $params = [
            'order'  => $order,
            'filter' => $filter,
            'select' => $select
        ];

        return $this->getList('crm.catalog.list', $params);
    }

    /**
     * Returns all product catalogues using the quick method.
     * 
     * Action: 'crm.catalog.list'
     * @see    https://dev.1c-bitrix.ru/rest_help/rest_sum/start.php
     * @see    https://dev.1c-bitrix.ru/rest_help/crm/catalog/crm_catalog_list.php
     * 
     * @param  array $filter Filtering parameters
     * @param  array $select Selection parameters
     * @param  array $order  Sorting parameters
     * 
     * @access public
     * @return Generator
     */
    public function fetchCatalogList(
        array $filter = [],
        array $select = [],
        array $order = []
    ) : Generator {
        $params = [
            'order'  => $order,
            'filter' => $filter,
            'select' => $select
        ];

        return $this->fetchList('crm.catalog.list', $params);
    }
}
