<?php

declare(strict_types=1);

namespace app\Bitrix24;

use Generator;

/**
 * Trait ProductRow
 * Methods for working with product rows in Bitrix24.
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
trait ProductRow
{
    /**
     * Return the description of product item fields.
     * 
     * Action: 'crm.productrow.fields'
     * @see    https://training.bitrix24.com/rest_help/crm/productrow_old/crm_productrow_fields.php
     * @deprecated
     * 
     * @access public
     * @return array
     */
    public function getProductRowFields() : array {
        return $this->request('crm.productrow.fields');
    }
}
