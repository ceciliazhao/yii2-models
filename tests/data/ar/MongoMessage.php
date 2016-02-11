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

namespace vistart\Models\tests\data\ar;

use vistart\Models\models\BaseMongoMessageModel;

/**
 * Description of MongoMessage
 *
 * @author vistart <i@vistart.name>
 */
class MongoMessage extends BaseMongoMessageModel
{
    public static function collectionName()
    {
        return ['yii2-models', 'message'];
    }
}
