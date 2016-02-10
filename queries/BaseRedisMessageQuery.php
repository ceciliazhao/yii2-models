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

namespace vistart\Models\queries;

use vistart\Models\traits\MessageQueryTrait;

/**
 * Description of BaseRedisMessageQuery
 *
 * @author vistart <i@vistart.name>
 */
class BaseRedisMessageQuery extends BaseRedisBlameableQuery
{
    use MessageQueryTrait;
}
