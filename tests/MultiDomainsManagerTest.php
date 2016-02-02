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

use vistart\Models\tests\data\ar\MultiDomainsManager;
use Yii;

/**
 * 
 * @author vistart <i@vistart.name>
 */
class MultiDomainsManagerTest extends TestCase
{

    /**
     * @group md
     */
    public function testInit()
    {
        //UserEmail::deleteAll();
    }

    /**
     * @depends testInit
     * @group md
     */
    public function testNew()
    {
        $MultiDomainsManager = \Yii::$app->multiDomainsManager;
        $urlManager = $MultiDomainsManager->current;
        $myUrlManager = $MultiDomainsManager->get('my');
        $miUrlManager = $MultiDomainsManager->get('mi');
        $this->assertNull($miUrlManager);
        $mUrlManager = $MultiDomainsManager->get('m');
        $this->assertNull($mUrlManager);
        $mhUrlManager = $MultiDomainsManager->get('mh');
        $this->assertNotNull($mhUrlManager);
        $loginUrlManager = $MultiDomainsManager->get('login');
        $this->assertEquals('/site/index.html', $urlManager->createUrl('/site/index'));
        $this->assertEquals('/posts.html', $myUrlManager->createUrl('/post/index'));
        $this->assertEquals('/', $loginUrlManager->createUrl('/site/login'));
        $this->assertEquals('/logout', $loginUrlManager->createUrl('/site/logout'));
    }

    /**
     * @depends testNew
     * @group md
     */
    public function testSSO()
    {
        $sso = \Yii::$app->user;
        \Yii::$app->request->hostInfo = 'vistart.name';
        \Yii::$app->request->url = '/';
        $sso->ssoDomain = 'login';
        $sso->loginUrl = '';
        $this->assertInstanceOf(\yii\web\Response::className(), $sso->loginRequired());
        $sso->loginUrl = null;
        try {
            $sso->loginRequired();
            $this->fail();
        } catch (\yii\web\ForbiddenHttpException $ex) {
            $this->assertEquals(Yii::t('yii', 'Login Required'), $ex->getMessage());
        }
        $sso->loginUrl = 'sso/login';
        \Yii::$app->requestedRoute = 'sso/login';
        try {
            $sso->loginRequired();
            $this->fail();
        } catch (\yii\web\ForbiddenHttpException $ex) {
            $this->assertEquals(Yii::t('yii', 'Login Required'), $ex->getMessage());
        }
        
        $sso->loginUrl = '';
        $sso->multiDomainsManagerId = '';
        $this->assertInstanceOf(\yii\web\Response::className(), $sso->loginRequired());

        $sso->multiDomainsManagerId = null;
        $this->assertInstanceOf(\yii\web\Response::className(), $sso->loginRequired());
    }
}
