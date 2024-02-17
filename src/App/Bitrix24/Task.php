<?php

declare(strict_types=1);

namespace app\Bitrix24;

use Generator;

/**
 * Trait Task
 * Methods for working with tasks in Bitrix24.
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
trait Task
{
    /**
     * Return a list of task field names
     * 
     * Action: 'tasks.task.getFields'
     * @see    https://training.bitrix24.com/rest_help/tasks/task/tasks/tasks_task_getFields.php
     * 
     * @access public
     * @return array
     */
    public function getTaskFields() : array {
        return $this->request('tasks.task.getFields');
    }

    /**
     * Return task by ID.
     * 
     * Action: 'tasks.task.get'
     * @see    https://training.bitrix24.com/rest_help/tasks/task/tasks/tasks_task_get.php
     * 
     * @param  int|string $taskID Task ID
     * @param  array      $select Selection parameters
     * 
     * @access public
     * @return array|null
     */
    public function getTask(int|string $taskID, array $select = []) : array|null {
        $task = $this->request(
            'tasks.task.get', [
                'taskId' => $taskID,
                'select' => $select
            ]
        );

        return $task;
    }

    /**
     * Return all tasks.
     * 
     * Action: 'tasks.task.list'
     * @see    https://training.bitrix24.com/rest_help/tasks/task/tasks/tasks_task_list.php
     * @see    https://dev.1c-bitrix.ru/rest_help/tasks/task/tasks/tasks_task_list.php
     * 
     * @param  array $filter Filtering parameters
     * @param  array $select Selection parameters
     * @param  array $order  Sorting parameters
     * 
     * @access public
     * @return Generator
     */
    public function getTaskList(
        array $filter = [],
        array $select = [],
        array $order = []
    ) : Generator {
        $params = [
            'order'  => $order,
            'filter' => $filter,
            'select' => $select
        ];

        return $this->getList('tasks.task.list', $params);
    }

    /**
     * Add task.
     * 
     * Action: 'tasks.task.add'
     * @see    https://training.bitrix24.com/rest_help/tasks/task/tasks/tasks_task_add.php
     * 
     * @param  array $fields List of task fields
     * 
     * @access public
     * @return int
     */
    public function addTask(array $fields = []) : int {
        $result = $this->request(
            'tasks.task.add', [
                'fields' => $fields
            ]
        );

        return $result;
    }

    /**
     * Add tasks.
     * 
     * Action: 'tasks.task.add'
     * @see    https://training.bitrix24.com/rest_help/tasks/task/tasks/tasks_task_add.php
     * 
     * @param  array $companies Array of task parameters
     * 
     * @access public
     * @return array Массив id активностей
     */
    public function addTasks(array $tasks = []) : array {
        // IDs of added tasks
        $taskResults = [];

        while ($tasksChunk = array_splice($tasks, 0, $this->batchSize)) {
            // Create an array of commands to add tasks
            $commandParams = [];

            foreach ($tasksChunk as $index => $task) {
                $commandParams[$index] = [
                    'fields' => $task
                ];
            }

            $commands   = $this->buildCommands('tasks.task.add', $commandParams);
            $taskResult = $this->batchRequest($commands);

            // Comparing number of commands and number of IDs in the response
            $sent     = count($commandParams);
            $received = count($taskResult);

            if ($received != $sent) {
                $jsonResponse = $this->toJSON($this->lastResponse);

                throw new Bitrix24APIException(
                    "Unable to add tasks ({$sent} / {$received}): {$jsonResponse}"
                );
            }

            $taskResults = array_merge($taskResults, $taskResult);
        }

        return $taskResults;
    }
}
