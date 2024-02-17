<?php

declare(strict_types=1);

namespace app\Bitrix24;

use Generator;

/**
 * Trait Company
 * Methods for working with the company in Bitrix24.
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
trait Company
{
    /**
     * Returns a description of company fields, including custom fields.
     * 
     * Action: 'crm.company.fields'
     * @see:    https://training.bitrix24.com/rest_help/crm/company/crm_company_fields.php
     * 
     * @access public
     * @return array
     */
    public function getCompanyFields() : array {
        return $this->request('crm.company.fields');
    }

    /**
     * Get company by ID.
     * 
     * Action: 'crm.company.get'
     * @see:    https://training.bitrix24.com/rest_help/crm/company/crm_company_get.php
     * 
     * Action: 'crm.company.contact.items.get'
     * @see:    https://training.bitrix24.com/rest_help/crm/company/crm_company_contact_items_get.php
     * 
     * @param  int|string $companyID Company ID
     * @param  array      $with      List of related entities returned with the company [self::$WITH_CONTACTS]
     * 
     * @access public
     * @return array
     */
    public function getCompany(int|string $companyID, array $with = []) : array {
        $with = array_map('strtoupper', $with);

        if (empty($with)) {
            return $this->request(
                'crm.company.get', [
                    'id' => $companyID
                ]
            );
        }

        $commands = [
            'COMPANY' => $this->buildCommand(
                'crm.company.get', [
                    'id' => $companyID
                ]
            )
        ];

        // Related contacts
        if (in_array(self::$WITH_CONTACTS, $with)) {
            $commands[self::$WITH_CONTACTS] = $this->buildCommand(
                'crm.company.contact.items.get', [
                    'id' => $companyID
                ]
            );
        }

        $result = $this->batchRequest($commands, true);

        return $this->createResultWith($result, 'COMPANY', $with);
    }

    /**
     * Add company.
     * 
     * Action: 'crm.company.add'
     * @see    https://training.bitrix24.com/rest_help/crm/company/crm_company_add.php
     *
     * @param  array $fields Company field list
     * @param  array $params Parameters for company
     * 
     * @access public
     * @return int
     */
    public function addCompany(array $fields = [], array $params = []) : int {
        $result = $this->request(
            'crm.company.add', [
                'fields' => $fields,
                'params' => $params
            ]
        );

        return $result;
    }

    /**
     * Update company.
     * 
     * Action: 'crm.company.update'
     * @see:    https://training.bitrix24.com/rest_help/crm/company/crm_company_update.php
     * 
     * @param  int|string $companyID ID of company
     * @param  array      $fields    List of company fields
     * @param  array      $params    Parameters for company
     * 
     * @access public
     * @return int
     */
    public function updateCompany(
        int|string $companyID,
        array $fields = [],
        array $params = []
    ) : int {
        $result = $this->request(
            'crm.company.update', [
                'id'     => $companyID,
                'fields' => $fields,
                'params' => $params
            ]
        );

        return $result;
    }

    /**
     * Delete a company by ID.
     * 
     * Action: 'crm.company.delete'
     * @see:    https://training.bitrix24.com/rest_help/crm/company/crm_company_delete.php
     * 
     * @param  int|string $companyID ID of company
     * 
     * @access public
     * @return int
     */
    public function deleteCompany(int|string $companyID) : int {
        $result = $this->request(
            'crm.company.delete', [
                'id' => $companyID
            ]
        );

        return $result;
    }

    /**
     * Return all companies.
     * 
     * Action: 'crm.company.list'
     * @see    https://training.bitrix24.com/rest_help/crm/company/crm_company_list.php
     * 
     * @param  array $filter Filtering parameters
     * @param  array $select Selection parameters
     * @param  array $order  Sorting parameters
     * 
     * @access public
     * @return Generator
     */
    public function getCompanyList(
        array $filter = [],
        array $select = [],
        array $order = []
    ) : Generator {
        $params = [
            'order'  => $order,
            'filter' => $filter,
            'select' => $select
        ];

        return $this->getList('crm.company.list', $params);
    }

    /**
     * Returns all companies using the quick method.
     * 
     * Action: 'crm.company.list'
     * @see    https://dev.1c-bitrix.ru/rest_help/rest_sum/start.php
     * @see    https://training.bitrix24.com/rest_help/crm/company/crm_company_list.php
     * 
     * @param  array $filter Filtering parameters
     * @param  array $select Selection parameters
     * @param  array $order  Sorting parameters
     * 
     * @return Generator
     */
    public function fetchCompanyList(
        array $filter = [],
        array $select = [],
        array $order = []
    ) : Generator {
        $params = [
            'order'  => $order,
            'filter' => $filter,
            'select' => $select
        ];

        return $this->fetchList('crm.company.list', $params);
    }

    /**
     * Returns contacts associated with a company by using company ID
     * 
     * Action: 'crm.company.contact.items.get'
     * @see    https://training.bitrix24.com/rest_help/crm/company/crm_company_contact_items_get.php
     * 
     * @param  int|string $companyID ID of company
     * 
     * @access public
     * @return array
     */
    public function getCompanyContactItems($companyID) : array {
        $result = $this->request(
            'crm.company.contact.items.get', [
                'id' => $companyID
            ]
        );

        return $result;
    }

    /**
     * Set up contacts associated with a company by company ID
     * 
     * Action: 'crm.company.contact.items.set'
     * @see:    https://training.bitrix24.com/rest_help/crm/company/crm_company_contact_items_set.php
     * 
     * @param  int|string $companyID ID of company
     * @param  array      $contacts  Contacts array
     * 
     * @access public
     * @return array
     */
    public function setCompanyContactItems(int|string $companyID, array $contacts) : array {
        $result = $this->request(
            'crm.company.contact.items.set', [
                'id'    => $companyID,
                'items' => $contacts
            ]
        );

        return $result;
    }

    /**
     * Add companies in batch.
     * Returns array of company IDs.
     * 
     * Action: 'crm.company.add'
     * @see:    https://training.bitrix24.com/rest_help/crm/company/crm_company_add.php
     *
     * @param  array $companies Companies array
     * @param  array $params    Parameters for companies
     * 
     * @access public
     * @return array
     */
    public function addCompanies(array $companies = [], array $params = []) : array {
        // IDs of added companies
        $companyResults = [];

        while ($companiesChunk = array_splice($companies, 0, $this->batchSize)) {
            // Create array of commands to add companies
            $commandParams = [];

            foreach ($companiesChunk as $index => $company) {
                $commandParams[$index] = [
                    'fields' => $company,
                    'params' => $params
                ];
            }

            $commands      = $this->buildCommands('crm.company.add', $commandParams);
            $companyResult = $this->batchRequest($commands);

            // Comparing number of commands and the number of IDs in the response
            $sent     = count($commandParams);
            $received = count($companyResult);

            if ($received != $sent) {
                $jsonResponse = $this->toJSON($this->lastResponse);

                throw new Bitrix24APIException(
                    "Unable to add companies in batch ({$sent} / {$received}): {$jsonResponse}"
                );
            }

            $companyResults = array_merge($companyResults, $companyResult);
        }

        return $companyResults;
    }

    /**
     * Update companies in batch.
     * Return array of company IDs
     * 
     * Action: 'crm.company.update'
     * @see:    https://training.bitrix24.com/rest_help/crm/company/crm_company_update.php
     *
     * @param  array $companies Array of companies
     * @param  array $params    Parameters for companies
     * 
     * @access public
     * @return array
     */
    public function updateCompanies(array $companies = [], array $params = []) : array {
        // ID array of updated companies
        $companyResults = [];

        while ($companiesChunk = array_splice($companies, 0, $this->batchSize)) {
            $commandParams = [];

            foreach ($companiesChunk as $index => $company) {
                // Check if the ID field in the company is available for updating
                $companyID = $company['ID'] ?? null;

                if (empty($companyID)) {
                    $jsonCompany = $this->toJSON($company);

                    throw new Bitrix24APIException(
                        "The 'ID' field in company (index {$index}) on the update request is missing or empty: '{$jsonCompany}'"
                    );
                }

                $companyResults[]      = $companyID;
                $commandParams[$index] = [
                    'id'     => $companyID,
                    'fields' => $company,
                    'params' => $params
                ];
            }

            $commands = $this->buildCommands('crm.company.update', $commandParams);
            $result   = $this->batchRequest($commands);

            // Comparing number of teams and number of statuses in the response
            $sent     = count($commandParams);
            $received = count($result);

            if ($received != $sent) {
                $jsonResponse = $this->toJSON($this->lastResponse);

                throw new Bitrix24APIException(
                    "Cannot batch update companies ({$sent} / {$received}): {$jsonResponse}"
                );
            }
        }

        return $companyResults;
    }

    /**
     * Delete companies.
     * Return array of company IDs
     * 
     * Action: 'crm.company.delete'
     * @see:    https://training.bitrix24.com/rest_help/crm/company/crm_company_delete.php
     * 
     * @param  array $companyIDs Array of company IDs
     * 
     * @access public
     * @return array
     */
    public function deleteCompanies(array $companyIDs = []) : array {
        // IDs of deleted companies
        $companyResults = [];

        while ($companiesChunk = array_splice($companyIDs, 0, $this->batchSize)) {
            // Create array of commands to delete companies
            $commandParams = [];

            foreach ($companiesChunk as $index => $companyID) {
                $commandParams[$index] = ['id' => $companyID];
                $companyResults[]      = $companyID;
            }

            $commands      = $this->buildCommands('crm.company.delete', $commandParams);
            $companyResult = $this->batchRequest($commands);

            // Comparing number of teams and number of statuses in the response
            $sent     = count($commandParams);
            $received = count($companyResult);

            if ($received != $sent) {
                $jsonResponse = $this->toJSON($this->lastResponse);

                throw new Bitrix24APIException(
                    "Cannot delete companies in batch ({$sent} / {$received}): {$jsonResponse}"
                );
            }
        }

        return $companyResults;
    }
}
