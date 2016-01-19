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

use vistart\Models\traits\AdditionalAccountTrait;

/**
 * Description of BaseAdditionalAccountModel
 *
 * @author vistart <i@vistart.name>
 */
abstract class BaseAdditionalAccountModel extends BaseBlameableModel
{

    use AdditionalAccountTrait;

    public $enableIP = false;
    public $confirmationAttribute = false;
    public $contentAttribute = false;
    public $descriptionAttribute = false;
    public $updatedByAttribute = false;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge($this->getAdditionalAccountRules(), parent::rules());
    }
}
