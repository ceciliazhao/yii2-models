<?php

/*
 *  _   __ __ _____ _____ ___  ____  _____
 * | | / // // ___//_  _//   ||  __||_   _|
 * | |/ // /(__  )  / / / /| || |     | |
 * |___//_//____/  /_/ /_/ |_||_|     |_|
 * @link http://vistart.name/
 * @copyright Copyright (c) 2016 vistart
 * @license http://vistart.name/license/
 */

namespace vistart\Models\tests;

use vistart\Models\tests\data\ar\User;
use vistart\Models\tests\data\ar\UserRelation;

/**
 * Description of UserRelationTest
 *
 * @author vistart <i@vistart.name>
 */
class BaseUserRelationTest extends TestCase {

    private function prepareModels() {
        $user = new User(['password' => '123456']);
        $other_user = new User(['password' => '123456']);
        $this->assertTrue($user->register());
        $this->assertTrue($other_user->register());
        return [$user, $other_user];
    }

    private function destroyModels($users = []) {
        foreach ($users as $user) {
            $this->assertTrue($user->deregister());
        }
    }

    private function prepareRelationModels($user, $other) {
        $initRelation = UserRelation::buildNoInitModel();
        $relation = $user->createModel(UserRelation::className(), [$initRelation->otherGuidAttribute => $other->guid]);
        if ($relation->save()) {
            $this->assertTrue(true);
            $opposite = UserRelation::findOneOppositeRelation($user, $other);
            $this->assertInstanceOf(UserRelation::className(), $opposite);
            $opposites = UserRelation::findAllOppositeRelations($user, $other);
            $this->assertEquals(1, count($opposites));
        } else {
            $this->assertTrue(false);
            var_dump($relation->errors);
        }
        return [$relation, $opposite];
    }

    public function testNew() {
        $users = $this->prepareModels();
        $user = $users[0];
        $other = $users[1];
        $relations = $this->prepareRelationModels($user, $other);
        $this->destroyModels($users);
        echo __METHOD__ . ":Done!\n";
    }

    public function testRemoveOne() {
        echo __METHOD__ . ":Start!\n";
        $users = $this->prepareModels();
        $user = $users[0];
        $other = $users[1];
        echo "Initiator:" . $user->guid . "\n";
        echo "Recipient:" . $other->guid . "\n";

        $this->prepareRelationModels($user, $other);
        UserRelation::removeOneRelation($user, $other);
        $relations = UserRelation::findAllRelations($user, $other);
        $this->assertEmpty($relations);
        $opposites = UserRelation::findAllOppositeRelations($user, $other);
        $this->assertEmpty($opposites);

        $relations = $this->prepareRelationModels($user, $other);
        $this->assertEquals(1, $relations[0]->remove());
        $relations = UserRelation::findAllRelations($user, $other);
        $this->assertEmpty($relations);
        $opposites = UserRelation::findAllOppositeRelations($user, $other);
        $this->assertEmpty($opposites);

        $this->destroyModels($users);
        echo __METHOD__ . ":Done!\n";
    }

    /**
     * @depends testNew
     */
    public function testDeregisterOne() {
        $users = $this->prepareModels();
        $user = $users[0];
        $other = $users[1];
        $this->prepareRelationModels($user, $other);
        if ($user->deregister()) {
            $this->assertTrue(true);
            $relations = UserRelation::findAllRelations($user, $other);
            $this->assertEmpty($relations);
            $opposites = UserRelation::findAllOppositeRelations($user, $other);
            $this->assertEmpty($opposites);
            $this->assertTrue($other->deregister());
        } else {
            $this->assertTrue(false);
            var_dump($user->errors);
        }
        echo __METHOD__ . ":Done!\n";
    }

}
