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

    public $idAttributeLength = 8;
    public $updatedByAttribute = false;
    public $contentAttribute = 'content'; // 账户类型，具体类型请自定义。
    public $contentAttributeRule = ['integer', 'min' => 0];
    public $contentTypeAttribute = 'source';  // 账户来源
    public $contentTypes = [
        0 => 'self',
        1 => 'third-party',
    ];
    public $confirmationAttribute = 'confirmed';
    public $confirmCodeAttribute = false;
    public $descriptionAttribute = 'description';

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge($this->getAdditionalAccountRules(), parent::rules());
    }
}
