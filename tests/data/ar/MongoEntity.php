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

namespace vistart\Models\tests\data\ar;

/**
 * Description of MongoEntity
 *
 * @author vistart <i@vistart.name>
 */
class MongoEntity extends \vistart\Models\models\BaseMongoEntityModel
{

    public static function collectionName()
    {
        return ['yii2-models', 'entity'];
    }
}
