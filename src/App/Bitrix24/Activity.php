<?php

declare(strict_types=1);

namespace app\Bitrix24;

/**
 * Trait Activity
 * Methods for working with cases (activities) in Bitrix24.
 *
 * @author    vladi-ri
 * @copyright 2024 vladi-ri
 * @see       https://github.com/vladi-ri/bitrix24-api
 * @license   OpenSource
 *
 * @version 0.9
 *
 * v1.0.0 (17.02.2024) Introduce Bitrix24API PHP project
 */
trait Activity
{
    /**
     * Returns activity fields
     * 
     * Action: 'crm.activity.fields'
     * See:    https://training.bitrix24.com/rest_help/crm/rest_activity/crm_activity_fields.php
     *
     * @access public
     * @return array
     */
    public function getActivityFields() : array {
        return $this->request('crm.activity.fields');
    }

    /**
     * Returns the activity by ID
     * 
     * Action: 'crm.activity.get'
     * See:    https://training.bitrix24.com/rest_help/crm/rest_activity/crm_activity_get.php
     *
     * @param int|string $activityID ID of activity
     * 
     * @access public
     * @return array|null
     */
    public function getActivity(int|string $activityID) : array|null {
        $activity = $this->request(
            'crm.activity.get', [
                'id' => $activityID
            ]
        );

        return $activity;
    }

    /**
     * Add activity
     * 
     * Action: 'crm.activity.add'
     * See:    https://training.bitrix24.com/rest_help/crm/rest_activity/crm_activity_add.php
     * 
     * @param array $fields Список полей активности
     * 
     * @access public
     * @return int
     */
    public function addActivity(array $fields = []) : int {
        $result = $this->request(
            'crm.activity.add', [
                'fields' => $fields
            ]
        );

        return $result;
    }

    /**
     * Add activities in batch
     * 
     * Action: 'crm.activity.add'
     * See:    https://training.bitrix24.com/rest_help/crm/rest_activity/crm_activity_add.php
     *
     * @param  array $activities Array of activity parameters
     * 
     * @access public
     * @return array Array of activity IDs
     */
    public function addActivities(array $activities = []) : array {
        // IDs of added activities
        $activityResults = [];

        while ($activitiesChunk = array_splice($activities, 0, $this->batchSize)) {
            // Form an array of commands for adding activities
            $commandParams = [];

            foreach ($activitiesChunk as $index => $activity) {
                $commandParams[ $index ] = [
                    'fields' => $activity
                ];
            }

            $commands       = $this->buildCommands('crm.activity.add', $commandParams);
            $activityResult = $this->batchRequest($commands);

            // Comparing the number of commands and the number of IDs in the response
            $sent     = count($commandParams);
            $received = count($activityResult);

            if ($received != $sent) {
                $jsonResponse = $this->toJSON($this->lastResponse);

                throw new Bitrix24APIException(
                    "Unable to batch add activities ({$sent} / {$received}): {$jsonResponse}"
                );
            }

            $activityResults = array_merge($activityResults, $activityResult);
        }

        return $activityResults;
    }
}
