<?php

declare(strict_types=1);

namespace app\Bitrix24;

use Generator;

/**
 * Trait Deal
 * Methods for working with a deal in Bitrix24.
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
trait Deal
{
    /**
     * Return a description of the deal fields, including custom fields.
     * 
     * Action: 'crm.deal.fields'
     * @see    https://training.bitrix24.com/rest_help/crm/deals/crm_deal_fields.php
     * 
     * @access public
     * @return array
     */
    public function getDealFields() : array {
        return $this->request('crm.deal.fields');
    }

    /**
     * Return a deal by ID.
     * 
     * Action: 'crm.deal.get'
     * @see    https://training.bitrix24.com/rest_help/crm/deals/crm_deal_get.php
     * 
     * Action: 'crm.deal.productrows.get'
     * @see    https://training.bitrix24.com/rest_help/crm/deals/crm_deal_productrows_get.php
     * 
     * Action: 'crm.deal.contact.items.get'
     * @see    https://training.bitrix24.com/rest_help/crm/deals/crm_deal_contact_items_get.php
     * 
     * @param  int|string $dealID ID of Deal
     * @param  array      $with   List of related entities returned with the deal [self::$WITH_CONTACTS, self::$WITH_PRODUCTS]
     * 
     * @access public
     * @return array
     */
    public function getDeal(int|string $dealID, array $with = []) : array {
        $with = array_map('strtoupper', $with);

        if (empty($with)) {
            return $this->request(
                'crm.deal.get', [
                    'id' => $dealID
                ]
            );
        }

        $commands = [
            'DEAL' => $this->buildCommand('crm.deal.get', [
                'id' => $dealID
            ])
        ];

        // Related Products
        if (in_array(self::$WITH_PRODUCTS, $with)) {
            $commands[self::$WITH_PRODUCTS] = $this->buildCommand('crm.deal.productrows.get', [
                'id' => $dealID
            ]);
        }

        // Related contacts
        if (in_array(self::$WITH_CONTACTS, $with)) {
            $commands[self::$WITH_CONTACTS] = $this->buildCommand('crm.deal.contact.items.get', [
                'id' => $dealID
            ]);
        }

        $result = $this->batchRequest($commands, true);

        return $this->createResultWith($result, 'DEAL', $with);
    }

    /**
     * Add a Deal.
     * 
     * Action: 'crm.deal.add'
     * @see    https://training.bitrix24.com/rest_help/crm/deals/crm_deal_add.php
     * 
     * @param  array $fields List of Deal fields
     * @param  array $params Parameters for Deal
     * 
     * @access public
     * @return int
     */
    public function addDeal(array $fields = [], array $params = []) : int {
        $result = $this->request(
            'crm.deal.add', [
                'fields' => $fields,
                'params' => $params
            ]
        );

        return $result;
    }

    /**
     * Update a Deal.
     * 
     * Action: 'crm.deal.update'
     * @see    https://training.bitrix24.com/rest_help/crm/deals/crm_deal_update.php
     * 
     * @param  int|string $dealID Deal ID
     * @param  array      $fields List of Deal fields
     * @param  array      $params Parameters for Deal
     * 
     * @access public
     * @return int
     */
    public function updateDeal(
        int|string $dealID,
        array $fields = [],
        array $params = []
    ) : int {
        $result = $this->request(
            'crm.deal.update', [
                'id'     => $dealID,
                'fields' => $fields,
                'params' => $params
            ]
        );

        return $result;
    }

    /**
     * Delete Deal by ID.
     * 
     * Action: 'crm.deal.delete'
     * @see    https://training.bitrix24.com/rest_help/crm/deals/crm_deal_delete.php
     * 
     * @param  int|string $dealID ID of Deal
     * 
     * @access public
     * @return int
     */
    public function deleteDeal(int|string $dealID) : int|string {
        $result = $this->request(
            'crm.deal.delete', [
                'id' => $dealID
            ]
        );

        return $result;
    }

    /**
     * Get all Deals.
     * 
     * Action: 'crm.deal.list'
     * @see    https://training.bitrix24.com/rest_help/crm/deals/crm_deal_list.php
     * @see    https://dev.1c-bitrix.ru/rest_help/crm/cdeals/crm_deal_list.php
     * 
     * @param  array $filter Filtering parameters
     * @param  array $select Selection parameters
     * @param  array $order  Sorting parameters
     * 
     * @access public
     * @return Generator
     */
    public function getDealList(
        array $filter = [],
        array $select = [],
        array $order = []
    ) : Generator {
        $params = [
            'order'  => $order,
            'filter' => $filter,
            'select' => $select
        ];

        return $this->getList('crm.deal.list', $params);
    }

    /**
     * Return all Deals using the fast method.
     * 
     * Action: 'crm.deal.list'
     * @see    https://training.bitrix24.com/rest_help/crm/deals/crm_deal_list.php
     * @see    https://dev.1c-bitrix.ru/rest_help/crm/cdeals/crm_deal_list.php
     * @see    https://dev.1c-bitrix.ru/rest_help/rest_sum/start.php
     * 
     * @param  array $filter Filtering parameters
     * @param  array $select Selection parameters
     * @param  array $order  Sorting parameters
     * 
     * @access public
     * @return Generator
     */
    public function fetchDealList(
        array $filter = [],
        array $select = [],
        array $order = []
    ) : Generator {
        $params = [
            'order'  => $order,
            'filter' => $filter,
            'select' => $select
        ];

        return $this->fetchList('crm.deal.list', $params);
    }

    /**
     * Return contacts associated with a Deal by Deal ID.
     * 
     * Action: 'crm.deal.contact.items.get'
     * @see    https://training.bitrix24.com/rest_help/crm/deals/crm_deal_contact_items_get.php
     * 
     * @param  int|string $dealID ID сделки
     * 
     * @access public
     * @return array
     */
    public function getDealContactItems(int|string $dealID) : array {
        $result = $this->request(
            'crm.deal.contact.items.get', [
                'id' => $dealID
            ]
        );

        return $result;
    }

    /**
     * Set contacts associated with the Deal by Deal ID
     * 
     * Action: 'crm.deal.contact.items.set'
     * @see    https://training.bitrix24.com/rest_help/crm/deals/crm_deal_contact_items_set.php
     * 
     * @param  int|string $dealID   Deal ID
     * @param  array      $contacts Array of contacts
     * 
     * @access public
     * @return array
     */
    public function setDealContactItems(int|string $dealID, array $contacts) : array {
        $result = $this->request(
            'crm.deal.contact.items.set', [
                'id'    => $dealID,
                'items' => $contacts
            ]
        );

        return $result;
    }

    /**
     * Return products associated with Deal by Deal ID.
     * 
     * Action: crm.deal.productrows.get
     * @see    https://training.bitrix24.com/rest_help/crm/deals/crm_deal_productrows_get.php
     * 
     * @param  int|string $dealID ID of Deal
     * 
     * @access public
     * @return array
     */
    public function getDealProductRows(int|string $dealID) : array {
        $result = $this->request(
            'crm.deal.productrows.get', [
                'id' => $dealID#
            ]
        );

        return $result;
    }

    /**
     * Set the products associated with the Deal by Deal ID.
     * 
     * Action: 'crm.deal.productrows.set'
     * @see    https://training.bitrix24.com/rest_help/crm/deals/crm_deal_productrows_set.php
     * 
     * @param  int|string $dealID   ID of Deal
     * @param  array      $products Array of products
     * 
     * @access public
     * @return array
     */
    public function setDealProductRows(int|string $dealID, array $products) : array {
        $result = $this->request(
            'crm.deal.productrows.set', [
                'id'   => $dealID,
                'rows' => $products
            ]
        );

        return $result;
    }

    /**
     * Add Deals with assiciated products.
     * Return array of Deal IDs
     * 
     * Action: 'crm.deal.add'
     * @see    https://training.bitrix24.com/rest_help/crm/deals/crm_deal_add.php
     * 
     * Action: 'crm.deal.productrows.set'
     * @see    https://training.bitrix24.com/rest_help/crm/deals/crm_deal_productrows_set.php
     * 
     * @param  array $deals  Deal array (Array of associated entities ['COMPANY_ID', 'CONTACT_ID', 'PRODUCTS'])
     * @param  array $params Deal parameters
     * 
     * @access public
     * @return array
     */
    public function addDeals(array $deals = [], array $params = []) : array {
        // IDs of created deals
        $dealResults = [];

        while ($dealsChunk = array_splice($deals, 0, $this->batchSize)) {
            // Create an array of commands to add deals
            $commandParams = [];

            foreach ($dealsChunk as $index => $deal) {
                $commandParams[$index] = [
                    'fields' => $deal,
                    'params' => $params
                ];
            }

            $commands   = $this->buildCommands('crm.deal.add', $commandParams);
            $dealResult = $this->batchRequest($commands);

            // Comparing number of commands and number of created deals in the response
            $sent     = count($commandParams);
            $received = count($dealResult);

            if ($received != $sent) {
                $jsonResponse = $this->toJSON($this->lastResponse);

                throw new Bitrix24APIException(
                    "It is not possible to batch add deals ({$sent} / {$received}): {$jsonResponse}"
                );
            }

            // Create an array of commands to add products to deals
            $commandParams = [];

            foreach ($dealsChunk as $index => $deal) {
                // Skip transactions without PRODUCTS field or without products
                if (
                    !isset($deal['PRODUCTS'])
                    || !is_array($deal['PRODUCTS'])
                    || count($deal['PRODUCTS']) == 0
                ) {
                    continue;
                }

                $commandParams[$index] = [
                    'id'   => $dealResult[$index],
                    'rows' => $deal['PRODUCTS']
                ];
            }

            $commands      = $this->buildCommands('crm.deal.productrows.set', $commandParams);
            $productResult = $this->batchRequest($commands);

            // Comparing number of deals and number of statuses in the response
            $sent     = count($commandParams);
            $received = count($productResult);

            if ($received != $sent) {
                $jsonResponse = $this->toJSON($this->lastResponse);

                throw new Bitrix24APIException(
                    "Cannot batch add items to deals ({$sent} / {$received}): {$jsonResponse}"
                );
            }

            // Save Id of created deals
            $dealResults = array_merge($dealResults, $dealResult);
        }

        return $dealResults;
    }

    /**
     * Update deals with product items.
     * Return array of Deal IDs.
     * 
     * Action: 'crm.deal.update'
     * @see    https://training.bitrix24.com/rest_help/crm/deals/crm_deal_update.php
     * 
     * Action: 'crm.deal.productrows.set'
     * @see    https://training.bitrix24.com/rest_help/crm/deals/crm_deal_productrows_set.php
     * 
     * @param  array $deals Transaction array (fields of related entities ['COMPANY_ID', 'CONTACT_ID', 'PRODUCTS'])
     *                      If a deal has many contacts, and the CONTACT_ID field is passed in the deal update request,
     *                      only 1 contact remains in the deal after the update, or 0 contacts if CONTACT_ID is empty.
     *
     * @param  array $params Parameters of Deal
     * 
     * @access public
     * @return array
     */
    public function updateDeals(array $deals = [], array $params = []) : array {
        // IDs of updated deals
        $dealResults = [];

        while ($dealsChunk = array_splice($deals, 0, $this->batchSize)) {
            // Create an array of commands to update deals
            $commandParams = [];

            foreach ($dealsChunk as $index => $deal) {
                // Check if the ID field in the deal is available for updating
                $dealID = $deal['ID'] ?? null;

                if (empty($dealID)) {
                    $jsonDeal = $this->toJSON($deal);

                    throw new Bitrix24APIException(
                        "The 'ID' field in the deal (index {$index}) on the update is missing or empty: '{$jsonDeal}'"
                    );
                }

                $dealResults[] = $dealID;

                $commandParams[$index] = [
                    'id'     => $dealID,
                    'fields' => $deal,
                    'params' => $params
                ];
            }

            $commands   = $this->buildCommands('crm.deal.update', $commandParams);
            $dealResult = $this->batchRequest($commands);

            // Compare number of Deals and number of successful statuses in the response
            $sent     = count($commandParams);
            $received = count($dealResult);

            if ($received != $sent) {
                $jsonResponse = $this->toJSON($this->lastResponse);

                throw new Bitrix24APIException(
                    "Unable to update deals ({$sent} / {$received}): {$jsonResponse}"
                );
            }

            // Create an array of commands to update products in deals
            $commandParams = [];

            foreach ($dealsChunk as $index => $deal) {
                // Skip transactions without PRODUCTS field
                if (
                    !isset($deal['PRODUCTS'])
                    || !is_array($deal['PRODUCTS'])
                ) {
                    continue;
                }

                // If the deal has no products, delete the products in the existing deal
                $products = count($deal['PRODUCTS']) ? $deal['PRODUCTS'] : [];

                $commandParams[$index] = [
                    'id'   => $deal['ID'],
                    'rows' => $products
                ];
            }

            $commands      = $this->buildCommands('crm.deal.productrows.set', $commandParams);
            $productResult = $this->batchRequest($commands);

            // Comparing number of deals and number of statuses in the response
            $sent     = count($commandParams);
            $received = count($productResult);

            if ($received != $sent) {
                $jsonResponse = $this->toJSON($this->lastResponse);

                throw new Bitrix24APIException(
                    "Unable to update items in deals ({$sent} / {$received}): {$jsonResponse}"
                );
            }
        }

        return $dealResults;
    }

    /**
     * Delete Deals.
     * Return array of Deal IDs.
     * 
     * Action: 'crm.deal.delete'
     * @see    https://training.bitrix24.com/rest_help/crm/deals/crm_deal_delete.php
     * 
     * @param  array $dealIDs Array of Deal IDs
     * 
     * @access public
     * @return array
     */
    public function deleteDeals(array $dealIDs = []) : array {
        // IDs of deleted deals
        $dealResults = [];

        while ($dealsChunk = array_splice($dealIDs, 0, $this->batchSize)) {
            // Create an array of commands to delete deals
            $commandParams = [];

            foreach ($dealsChunk as $index => $dealID) {
                $commandParams[$index] = ['id' => $dealID];
                $dealResults[]         = $dealID;
            }

            $commands   = $this->buildCommands('crm.deal.delete', $commandParams);
            $dealResult = $this->batchRequest($commands);

            // Compare number of deals and number of successful statuses in the response
            $sent     = count($commandParams);
            $received = count($dealResult);

            if ($received != $sent) {
                $jsonResponse = $this->toJSON($this->lastResponse);

                throw new Bitrix24APIException(
                    "Unable to delete transactions ({$sent} / {$received}): {$jsonResponse}"
                );
            }
        }

        return $dealResults;
    }

    /**
     * Sets the file to a NOT multiple user field of type file (file cannot be deleted).
     * 
     * @see    https://dev.1c-bitrix.ru/rest_help/crm/cases/edit/form_lead_with_files.php
     * 
     * @param  int|string $dealID           Deal ID
     * @param  int|string $userFieldID      ID of NOT multiple custom field in a transaction ('UF_CRM_XXXXXXXXXX')
     * @param  string     $fileName         File name
     * @param  string     $fileContent      Raw file data
     * @param  bool       $isBase64FileData Is raw file data base64 encoded?
     * 
     * @access public
     * @return int
     */
    public function setDealFile(
        int|string $dealID,
        int|string $userFieldID,
        string $fileName,
        string $fileContent,
        bool $isBase64FileData = true
    ) : int {
        if (!$isBase64FileData) {
            $fileContent = base64_encode($fileContent);
        }

        $fields = [
            $userFieldID => [
                'fileData' => [
                    $fileName, $fileContent
                ]
            ]
        ];

        $result = $this->updateDeal($dealID, $fields);

        return $result;
    }

    /**
     * Set files to a multiple custom file type field (files can be deleted)
     * 
     * @see    https://dev.1c-bitrix.ru/rest_help/crm/cases/edit/form_lead_with_files.php
     * 
     * @param  int|string $dealID           Deal ID
     * @param  int|string $userFieldID      ID of multiple custom field in a deal ('UF_CRM_XXXXXXXXXX')
     * @param  array      $files            Array of file parameters ([[<filename>, <raw file data> ], ...])
     *                                      (empty array to delete all files).
     * @param  bool       $isBase64FileData Is raw file data base64 encoded?
     * 
     * @access public
     * @return int
     */
    public function setDealFiles(
        int|string $dealID,
        int|string $userFieldID,
        array $files = [],
        bool $isBase64FileData = true
    ) : int {
        $userFieldValue = [];

        foreach ($files as $file) {
            if (!$isBase64FileData) {
                $file[1] = base64_encode($file[1]);
            }

            $userFieldValue[] = [
                'fileData' => $file
            ];
        }

        // If deleting all files
        if (!count($userFieldValue)) {
            $userFieldValue = '';
        }

        $fields = [
            $userFieldID => $userFieldValue
        ];

        $result = $this->updateDeal($dealID, $fields);

        return $result;
    }
}
