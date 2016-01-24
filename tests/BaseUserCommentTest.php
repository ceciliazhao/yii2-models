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

use vistart\Models\tests\data\ar\User;
use vistart\Models\tests\data\ar\UserComment;

/**
 * Description of BaseUserCommentTest
 *
 * @author vistart <i@vistart.name>
 */
class BaseUserCommentTest extends TestCase
{

    private function prepareUser()
    {
        $user = new User(['password' => '123456']);
        $this->assertTrue($user->register());
        return $user;
    }

    private function prepareComment($user)
    {
        $comment = $user->create(UserComment::className(), ['content' => 'comment']);
        return $comment;
    }

    private function prepareSubComment($comment)
    {
        $sub = $comment->bear();
        $createdByAttribute = $sub->createdByAttribute;
        $sub->$createdByAttribute = $comment->$createdByAttribute;
        $sub->content = 'sub';
        return $sub;
    }

    public function testNew()
    {
        $user = $this->prepareUser();
        $comment = $this->prepareComment($user);
        $subComment = $this->prepareSubComment($comment);
        if ($result = $comment->save()) {
            $this->assertTrue($result);
        } else {
            var_dump($comment->errors);
            $this->fail();
        }
        if ($result = $subComment->save()) {
            $this->assertTrue($result);
        } else {
            var_dump($subComment->errors);
            $this->fail();
        }
        $this->assertTrue($user->deregister());
    }

    /**
     * @depends testNew
     */
    public function testDeleteParentCascade()
    {
        $user = $this->prepareUser();
        $comment = $this->prepareComment($user);
        $subComment = $this->prepareSubComment($comment);
        $comment->save();
        $subComment->save();
        if ($comment->delete()) {
            $query = UserComment::find()->id($subComment->id);
            $copy = clone $query;
            var_dump($copy->createCommand()->getRawSql());
            $sub = UserComment::find()->id($subComment->id)->one();
            $this->assertNull($sub);
        } else {
            var_dump($comment->errors);
            $this->fail();
        }
        $this->assertTrue($user->deregister());
    }
}
