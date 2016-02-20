<?php

/**
 *  _   __ __ _____ _____ ___  ____  _____
 * | | / // // ___//_  _//   ||  __||_   _|
 * | |/ // /(__  )  / / / /| || |     | |
 * |___//_//____/  /_/ /_/ |_||_|     |_|
 * @link http://vistart.name/
 * @copyright Copyright (c) 2016 vistart
 * @license http://vistart.name/license/
 */

namespace vistart\Models\models;

use vistart\Models\traits\MetaTrait;

/**
 * Description of BaseMetaModel
 *
 * @version 2.0
 * @author vistart <i@vistart.name>
 */
abstract class BaseMetaModel extends BaseBlameableModel
{
    use MetaTrait;

    public $idAttribute = 'key';
    public $idPreassigned = true;

    /**
     * Collation: utf8mb4_unicode_ci
     * MySQL 5.7 supports the length of key more than 767 bytes.
     * @var int 
     */
    public $idAttributeLength = 190;
    public $createdAtAttribute = false;
    public $updatedAtAttribute = false;
    public $enableIP = false;
    public $contentAttribute = 'value';
    public $updatedByAttribute = false;
    public $confirmationAttribute = false;

}
