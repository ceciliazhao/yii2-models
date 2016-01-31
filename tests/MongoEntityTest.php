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

namespace vistart\Models\tests;

use vistart\Models\tests\data\ar\MongoEntity;

/**
 * Description of MongoEntityTest
 *
 * @author vistart <i@vistart.name>
 */
class MongoEntityTest extends MongoTestCase
{

    /**
     * @group mongo
     */
    public function testNew()
    {
        $entity = new MongoEntity();
        $this->assertTrue($entity->save());
        $query = MongoEntity::find()->guid($entity->guid);
        $query1 = clone $query;
        $this->assertInstanceOf(MongoEntity::className(), $query1->one());
        $this->assertEquals(1, $entity->delete());
    }
}
