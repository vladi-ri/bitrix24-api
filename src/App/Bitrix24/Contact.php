<?php

declare(strict_types=1);

namespace app\Bitrix24;

use Generator;

/**
 * Trait Contact
 * Methods for working with a contact in Bitrix24.
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
trait Contact
{
    /**
     * Return a description of the contact's fields, including custom fields.
     * 
     * Action: 'crm.contact.fields'
     * @see    https://training.bitrix24.com/rest_help/crm/contacts/crm_contact_fields.php
     * 
     * @access public
     * @return array
     */
    public function getContactFields() : array {
        return $this->request('crm.contact.fields');
    }

    /**
     * Return a contact by ID.
     * 
     * Action: 'crm.contact.get'
     * @see    https://training.bitrix24.com/rest_help/crm/contacts/crm_contact_get.php
     * 
     * Action: 'crm.contact.company.items.get'
     * @see    https://training.bitrix24.com/rest_help/crm/contacts/crm_contact_company_items_get.php
     * 
     * @param  int|string $contactID Contact ID
     * @param  array      $with      List of related entities returned with the contact [self::$WITH_COMPANIES]
     * 
     * @access public
     * @return array
     */
    public function getContact(int|string $contactID, array $with = []) : array {
        $with = array_map('strtoupper', $with);

        if (empty($with)) {
            return $this->request(
                'crm.contact.get', [
                    'id' => $contactID
                ]
            );
        }

        $commands = [
            'CONTACT' => $this->buildCommand('crm.contact.get', [
                'id' => $contactID
            ])
        ];

        // Related companies
        if (in_array(self::$WITH_COMPANIES, $with)) {
            $commands[self::$WITH_COMPANIES] = $this->buildCommand(
                'crm.contact.company.items.get', [
                    'id' => $contactID
                ]
            );
        }

        $result = $this->batchRequest($commands, true);

        return $this->createResultWith($result, 'CONTACT', $with);
    }

    /**
     * Return contacts by phone number.
     * 
     * Action: 'crm.contact.list'
     * @see    https://training.bitrix24.com/rest_help/crm/contacts/crm_contact_list.php
     * 
     * @param  int|string $phone  Phone number
     * @param  array      $select Selection parameters
     * 
     * @access public
     * @return array
     */
    public function getContactsByPhone(int|string $phone, array $select = []) : array {
        return $this->request(
            'crm.contact.list', [
                'filter' => ['PHONE' => $phone],
                'select' => $select
            ]
        );
    }

    /**
     * Add contact.
     * 
     * Action: 'crm.contact.add'
     * @see    https://training.bitrix24.com/rest_help/crm/contacts/crm_contact_add.php
     * 
     * @param  array $fields List of contact fields
     * @param  array $params Parameters for contact
     * 
     * @access public
     * @return int
     */
    public function addContact(array $fields = [], array $params = []) : int {
        $result = $this->request(
            'crm.contact.add', [
                'fields' => $fields,
                'params' => $params
            ]
        );

        return $result;
    }

    /**
     * Update contact.
     * 
     * Action: 'crm.contact.update'
     * @see    https://training.bitrix24.com/rest_help/crm/contacts/crm_contact_update.php
     * 
     * @param  string|int $contactID ID of contact
     * @param  array      $fields    List of contact fields
     * @param  array      $params    List of contact parameters
     * @return int
     */
    public function updateContact(
        string|int $contactID,
        array $fields = [],
        array $params = []
    ) : int {
        $result = $this->request(
            'crm.contact.update', [
                'id'     => $contactID,
                'fields' => $fields,
                'params' => $params
            ]
        );

        return $result;
    }

    /**
     * Delete contact by ID.
     * 
     * Action: 'crm.contact.delete'
     * @see    https://training.bitrix24.com/rest_help/crm/contacts/crm_contact_delete.php
     *
     * @param  int|string $contactID ID of contact
     * 
     * @access public
     * @return int
     */
    public function deleteContact($contactID) : int {
        $result = $this->request(
            'crm.contact.delete', [
                'id' => $contactID
            ]
        );

        return $result;
    }

    /**
     * Return all contacts.
     * 
     * Action: 'crm.contact.list'
     * @see    https://training.bitrix24.com/rest_help/crm/contacts/crm_contact_list.php
     * 
     * @param  array $filter Filtering parameters
     * @param  array $select Selection parameters
     * @param  array $order  Sorting parameters
     * 
     * @access public
     * @return Generator
     */
    public function getContactList(
        array $filter = [],
        array $select = [],
        array $order = []
    ) : Generator {
        $params = [
            'order'  => $order,
            'filter' => $filter,
            'select' => $select
        ];

        return $this->getList('crm.contact.list', $params);
    }

    /**
     * Returns all contacts using the quick method.
     * 
     * Action: 'crm.contact.list'
     * @see    https://training.bitrix24.com/rest_help/crm/contacts/crm_contact_list.php
     * @see    https://dev.1c-bitrix.ru/rest_help/rest_sum/start.php
     * 
     * @param  array $filter Filtering parameters
     * @param  array $order  Sorting parameters
     * @param  array $select Selection parameters
     * 
     * @access public
     * @return Generator
     */
    public function fetchContactList(
        array $filter = [],
        array $select = [],
        array $order = []
    ) : Generator {
        $params = [
            'order'  => $order,
            'filter' => $filter,
            'select' => $select
        ];

        return $this->fetchList('crm.contact.list', $params);
    }

    /**
     * Returns the companies associated with the contact by contact ID.
     * 
     * Action: 'crm.contact.company.items.get'
     * @see    https://training.bitrix24.com/rest_help/crm/contacts/crm_contact_company_items_get.php
     * 
     * @param  int|string $contactID ID of contact
     * 
     * @access public
     * @return array
     */
    public function getContactCompanyItems(int|string $contactID) : array {
        $result = $this->request(
            'crm.contact.company.items.get', [
                'id' => $contactID
            ]
        );

        return $result;
    }

    /**
     * Sets the companies associated with the contact by contact ID.
     * 
     * Action: 'crm.contact.company.items.set'
     * @see    https://training.bitrix24.com/rest_help/crm/contacts/crm_contact_company_items_set.php
     * 
     * @param  int|string $contactID ID of contact
     * @param  array      $companies Array of companies
     * 
     * @access public
     * @return array
     */
    public function setContactCompanyItems(int|string $contactID, array $companies) : array {
        $result = $this->request(
            'crm.contact.company.items.set', [
                'id'    => $contactID,
                'items' => $companies
            ]
        );

        return $result;
    }

    /**
     * Add contacts in batch.
     * Returns array of Contact IDs.
     * 
     * Action: 'crm.contact.add'
     * @see    https://training.bitrix24.com/rest_help/crm/contacts/crm_contact_add.php
     * 
     * @param  array $contacts Contacts array (fields of related entities ['COMPANY_ID'])
     * @param  array $params   Setting parameters for contact
     * 
     * @return array
     */
    public function addContacts(array $contacts = [], array $params = []) : array {
        // IDs of contacts added
        $contactResults = [];

        while ($contactsChunk = array_splice($contacts, 0, $this->batchSize)) {
            $commandParams = [];

            foreach ($contactsChunk as $index => $contact) {
                $commandParams[ $index ] = [
                    'fields' => $contact,
                    'params' => $params
                ];
            }

            $commands = $this->buildCommands('crm.contact.add', $commandParams);
            $result   = $this->batchRequest($commands);

            // Comparing number of commands and number of IDs in the response
            $sent     = count($commandParams);
            $received = count($result);

            if ($received != $sent) {
                $jsonResponse = $this->toJSON($this->lastResponse);

                throw new Bitrix24APIException(
                    "Cannot add contacts in batch ({$sent} / {$received}): {$jsonResponse}"
                );
            }

            $contactResults = array_merge($contactResults, $result);
        }

        return $contactResults;
    }

    /**
     * Update contacts in batch.
     * Returns array of Contact IDs.
     * 
     * Action: 'crm.contact.update'
     * @see    https://training.bitrix24.com/rest_help/crm/contacts/crm_contact_update.php
     * 
     * @param  array $contacts Contacts array (fields of related entities ['COMPANY_ID'])
     * @param  array $params   Setting parameters for contact
     * 
     * @access public
     * @return array
     */
    public function updateContacts(array $contacts = [], array $params = []) : array {
        // IDs of updated contacts
        $contactResults = [];

        while ($contactsChunk = array_splice($contacts, 0, $this->batchSize)) {
            $commandParams = [];

            foreach ($contactsChunk as $index => $contact) {
                // Check if the ID field in the contact is available for updating
                $contactID = $contact['ID'] ?? null;

                if (empty($contactID)) {
                    $jsonContact = $this->toJSON($contact);

                    throw new Bitrix24APIException(
                        "The 'ID' field in the contact (index {$index}) on the update is missing or empty: '{$jsonContact}'"
                    );
                }

                $contactResults[]      = $contactID;

                $commandParams[$index] = [
                    'id'     => $contactID,
                    'fields' => $contact,
                    'params' => $params
                ];
            }

            $commands = $this->buildCommands('crm.contact.update', $commandParams);
            $result   = $this->batchRequest($commands);

            // Comparing number of teams and number of statuses in the response
            $sent     = count($commandParams);
            $received = count($result);

            if ($received != $sent) {
                $jsonResponse = $this->toJSON($this->lastResponse);

                throw new Bitrix24APIException(
                    "Unable to update contacts in batch ({$sent} / {$received}): {$jsonResponse}"
                );
            }
        }

        return $contactResults;
    }

    /**
     * Delete contacts in batch.
     * Return array of contact IDs.
     * 
     * Action: 'crm.contact.delete'
     * @see    https://training.bitrix24.com/rest_help/crm/contacts/crm_contact_delete.php
     * 
     * @param  array $contactIDs Array of contact IDs
     * 
     * @access public
     * @return array
     */
    public function deleteContacts(array $contactIDs = []) : array {
        // IDs of deleted contacts
        $contactResults = [];

        while ($contactsChunk = array_splice($contactIDs, 0, $this->batchSize)) {
            $commandParams = [];

            foreach ($contactsChunk as $index => $contactID) {
                $commandParams[$index] = ['id' => $contactID];
                $contactResults[]      = $contactID;
            }

            $commands = $this->buildCommands('crm.contact.delete', $commandParams);
            $result   = $this->batchRequest($commands);

            // Comparing number of teams and number of statuses in the response
            $sent     = count($commandParams);
            $received = count($result);

            if ($received != $sent) {
                $jsonResponse = $this->toJSON($this->lastResponse);

                throw new Bitrix24APIException(
                    "Unable to delete contacts in a batch ({$sent} / {$received}): {$jsonResponse}"
                );
            }
        }

        return $contactResults;
    }
}
