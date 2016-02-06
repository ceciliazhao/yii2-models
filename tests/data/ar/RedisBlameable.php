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

/**
 * Description of RedisBlameable
 *
 * @author vistart <i@vistart.name>
 */
class RedisBlameable extends \vistart\Models\models\BaseRedisBlameableModel
{

    public $guidAttribute = false;
    public $idAttribute = 'alpha2';

    public static function primaryKey()
    {
        return [
            'alpha2',
        ];
    }
}
