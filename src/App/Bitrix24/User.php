<?php

declare(strict_types=1);

namespace app\Bitrix24;

use Generator;

/**
 * Trait User.
 * Methods for working with User in Bitrix24.
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
trait User
{
    /**
     * Return a list of the user's field names
     * 
     * Action: 'user.fields'
     * @see    https://training.bitrix24.com/rest_help/users/user_fields.php
     * 
     * @access public
     * @return array
     */
    public function getUserFields() : array {
        return $this->request('user.fields');
    }

    /**
     * Return user by ID.
     * 
     * Action: 'user.get'
     * @see    https://training.bitrix24.com/rest_help/users/user_get.php
     * 
     * @param  int|string $userID User ID
     * 
     * @access public
     * @return array|null
     */
    public function getUser(int|string $userID) : array|null {
        $result = $this->request('user.get', [
            'ID' => $userID
        ]);

        $user = array_shift($result);

        return $user;
    }

    /**
     * Return all users.
     * 
     * Action: 'user.get'
     * @see    https://training.bitrix24.com/rest_help/users/user_get.php
     * 
     * @param  array  $filter    Filtering parameters
     * @param  string $order     Sorting direction
     * @param  string $sort      The field by which the results are sorted
     * @param  bool   $adminMode Admin mode key
     * 
     * @access public
     * @return Generator
     */
    public function getUsers(
        array $filter = [],
        string $order = 'ASC',
        string $sort = '',
        bool $adminMode = false
    ) : Generator {

        $params = [
            'FILTER'     => $filter,
            'order'      => $order,
            'sort'       => $sort,
            'ADMIN_MODE' => $adminMode
        ];

        return $this->getList('user.get', $params);
    }
}
