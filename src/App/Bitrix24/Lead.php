<?php

declare(strict_types=1);

namespace app\Bitrix24;

use Generator;

/**
 * Trait Lead
 * Methods for working with lead in Bitrix24.
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
trait Lead
{
    /**
     * Return a description of the fields of the lead, including custom fields.
     * 
     * Action: 'crm.lead.fields'
     * @see    https://training.bitrix24.com/rest_help/crm/leads/crm_lead_fields.php
     * @see    https://dev.1c-bitrix.ru/rest_help/crm/leads/crm_lead_fields.php
     * 
     * @access public
     * @return array
     */
    public function getLeadFields() : array {
        return $this->request('crm.lead.fields');
    }

    /**
     * Return a lead by ID.
     * 
     * Action: 'crm.lead.get'
     * @see    https://training.bitrix24.com/rest_help/crm/leads/crm_lead_get.php
     * 
     * Action: 'crm.lead.productrows.get'
     * @see    https://training.bitrix24.com/rest_help/crm/leads/crm_lead_productrows_get.php
     * 
     * @param  int|string $leadID Lead ID
     * @param  array      $with   List of related entities returned with the lead [self::$WITH_PRODUCTS]
     * 
     * @access public
     * @return array
     */
    public function getLead(int|string $leadID, array $with = []) : array {
        $with = array_map('strtoupper', $with);

        if (empty($with)) {
            return $this->request(
                'crm.lead.get', [
                    'id' => $leadID
                ]
            );
        }

        $commands = [
            'LEAD' => $this->buildCommand(
                'crm.lead.get', [
                    'id' => $leadID
                ]
            )
        ];

        // Related products
        if (in_array(self::$WITH_PRODUCTS, $with)) {
            $commands[self::$WITH_PRODUCTS] = $this->buildCommand(
                'crm.lead.productrows.get', [
                    'id' => $leadID
                ]
            );
        }

        $result = $this->batchRequest($commands, true);

        return $this->createResultWith($result, 'LEAD', $with);
    }

    /**
     * Add a lead.
     * 
     * Action: 'crm.lead.add'
     * @see    https://training.bitrix24.com/rest_help/crm/leads/crm_lead_add.php
     * 
     * @param  array $fields Список полей лида
     * @param  array $params Параметры для лида
     * 
     * @access public
     * @return int
     */
    public function addLead(array $fields = [], array $params = []) : int {
        $result = $this->request(
            'crm.lead.add', [
                'fields' => $fields,
                'params' => $params
            ]
        );

        return $result;
    }

    /**
     * Update a lead
     * 
     * Action: 'crm.lead.update'
     * @see    https://training.bitrix24.com/rest_help/crm/leads/crm_lead_update.php
     * 
     * @param  int|string $leadID Lead ID
     * @param  array      $fields An array in format array("field"=>"value"[, ...])
     *                            containing values of the contact fields that need to be updated.
     * @param  array      $params Parameters for a lead
     * 
     * @access public
     * @return int
     */
    public function updateLead(
        int|string $leadID,
        array $fields = [],
        array $params = []
    ) : int {
        $result = $this->request(
            'crm.lead.update', [
                'id'     => $leadID,
                'fields' => $fields,
                'params' => $params
            ]
        );

        return $result;
    }

    /**
     * Delete a lead by ID.
     * 
     * Action: 'crm.lead.delete'
     * @see    https://training.bitrix24.com/rest_help/crm/leads/crm_lead_delete.php
     * 
     * @param  int|string $leadID Lead ID
     * 
     * @access public
     * @return int
     */
    public function deleteLead(int|string $leadID) : int {
        $result = $this->request(
            'crm.lead.delete', [
                'id' => $leadID
            ]
        );

        return $result;
    }

    /**
     * Return all leads.
     * 
     * Action: 'crm.lead.list'
     * @see    https://training.bitrix24.com/rest_help/crm/leads/crm_lead_list.php
     * @see    https://dev.1c-bitrix.ru/rest_help/crm/leads/crm_lead_list.php
     * 
     * @param  array $filter Filtering parameters
     * @param  array $select Selection parameters
     * @param  array $order  Sorting parameters
     * 
     * @access public
     * @return Generator
     */
    public function getLeadList(
        array $filter = [],
        array $select = [],
        array $order = []
    ) : Generator {
        $params = [
            'order'  => $order,
            'filter' => $filter,
            'select' => $select
        ];

        return $this->getList('crm.lead.list', $params);
    }

    /**
     * Return all leads using the quick method.
     * 
     * Action: 'crm.lead.list'
     * @see    https://training.bitrix24.com/rest_help/crm/leads/crm_lead_list.php
     * @see    https://dev.1c-bitrix.ru/rest_help/crm/leads/crm_lead_list.php
     * 
     * @param  array $filter Filtering parameters
     * @param  array $select Selection parameters
     * @param  array $order  Sorting parameters
     * 
     * @return Generator
     */
    public function fetchLeadList(
        array $filter = [],
        array $select = [],
        array $order = []
    ) : Generator {
        $params = [
            'order'  => $order,
            'filter' => $filter,
            'select' => $select
        ];

        return $this->fetchList('crm.lead.list', $params);
    }

    /**
     * Return product items associated with a lead by lead ID.
     * 
     * Action: 'crm.lead.productrows.get'
     * @see    https://training.bitrix24.com/rest_help/crm/leads/crm_lead_productrows_get.php
     * 
     * @param  int|string $leadID Lead ID
     * 
     * @access public
     * @return array
     */
    public function getLeadProductRows(int|string $leadID) : array {
        $result = $this->request(
            'crm.lead.productrows.get', [
                'id' => $leadID
            ]
        );

        return $result;
    }

    /**
     * Set product items associated with a lead by lead ID.
     * 
     * Action: 'crm.lead.productrows.set'
     * @see    https://training.bitrix24.com/rest_help/crm/leads/crm_lead_productrows_set.php
     * 
     * @param  int|string $leadID   Lead ID
     * @param  array      $products Array of products
     * 
     * @access public
     * @return array
     */
    public function setLeadProductRows($leadId, array $products) : array {
        $result = $this->request(
            'crm.lead.productrows.set', [
                'id'   => $leadID,
                'rows' => $products
            ]
        );

        return $result;
    }
}
