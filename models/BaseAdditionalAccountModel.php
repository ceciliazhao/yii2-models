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
 * This abstract class helps you build additional account class.
 *
 * Default settings:
 * - enable GUID.
 * - enable ID, random string, with 8 characters.
 * - enable IP, accept all IP address.
 * - enable createdAtAttribute.
 * - enable content, and its rule is integer.
 * - enable confirmation, but confirm code.
 * - enable description.
 * the content attribute is used for recording the login-type of account, e.g. ID
 * , email or any other format.
 * the content type attribute is used for recording the account source, e.g. register
 * from self, or any other account provider.
 * @version 2.0
 * @author vistart <i@vistart.name>
 */
abstract class BaseAdditionalAccountModel extends BaseBlameableModel
{
    use AdditionalAccountTrait;

    public $idAttributeLength = 8;
    public $updatedByAttribute = false;
    public $contentAttribute = 'content'; // Account type, types defined by yourself.
    public $contentAttributeRule = ['integer', 'min' => 0];
    public $contentTypeAttribute = 'source';  // Where did this account origin from, defined by yourself.
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
