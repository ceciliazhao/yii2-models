<?php

/**
 *  _   __ __ _____ _____ ___  ____  _____
 * | | / // // ___//_  _//   ||  __||_   _|
 * | |/ // /(__  )  / / / /| || |     | |
 * |___//_//____/  /_/ /_/ |_||_|     |_|
 * @link https://vistart.name/
 * @copyright Copyright (c) 2016 vistart
 * @license https://vistart.name/license/
 */

namespace vistart\Models\models;

use vistart\Models\queries\BaseMongoMessageQuery;
use vistart\Models\traits\MessageTrait;

/**
 * This model helps build message model stored in mongodb.
 * This model enables the following fields:
 * 
 * - idAttribute: _id
 * - createdAtAttribute: createdAt
 * - updatedAtAttribute: updatedAt
 * - ipTypeAttribute: ip_type
 * - ipAttribute1: ip_1
 * - ipAttribute2: ip_2
 * - ipAttribute3: ip_3
 * - ipAttribute5: ip_4
 * 
 * - contentAttribute: content
 * - createdByAttribute: user_guid
 * 
 * - otherGuidAttribute: other_guid
 * - attachmentAttribute: attachment
 * - receivedAtAttribute: received_at
 * - readAtAttribute: read_at
 * 
 * Property:
 * - expiredAt: 604800 // 7 days
 *
 * @version 2.0
 * @author vistart <i@vistart.name>
 */
abstract class BaseMongoMessageModel extends BaseMongoBlameableModel
{
    use MessageTrait;

    public $updatedByAttribute = false;
    public $expiredAt = 604800; // 7 days.

    public function init()
    {
        if (!is_string($this->queryClass)) {
            $this->queryClass = BaseMongoMessageQuery::className();
        }
        if ($this->skipInit) {
            return;
        }
        $this->initMessageEvents();
        parent::init();
    }
}
