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

namespace vistart\Models\tests\data\ar;

/**
 * Description of MultiDomainsManager
 *
 * @author vistart <i@vistart.name>
 */
class MultiDomainsManager extends \vistart\Models\components\MultiDomainsManager
{

    /**
     * @var string the base domain.
     */
    public $baseDomain = 'yii2-models.vistart';
    public $currentDomain = '';

    /**
     * <Sub Domain Name> => [
     *     'component' => <URL Manager Component Configuration Array>,
     *     'scheme' => 'http'(default) or 'https',
     * ]
     * @var array 
     */
    public $subDomains = [
        '' => [
            'component' => [
                'class' => 'yii\web\UrlManager', // `class` could be ignored as it is `yii\web\UrlManager`.
                'enablePrettyUrl' => true,
                'showScriptName' => false,
                'suffix' => '.html',
            ],
            'scheme' => 'http', // `scheme` could be ignored as it is 'http'.
        ],
        'my' => [
            'component' => [
                'enablePrettyUrl' => true,
                'showScriptName' => false,
                'suffix' => '.html',
                'rules' => [
                    'posts/<year:\d{4}>/<category>' => 'post/index',
                    'posts' => 'post/index',
                    'post/<id:\d+>' => 'post/view',
                ],
            ],
            'scheme' => 'https',
        ],
        'login' => [
            'component' => [
                'enablePrettyUrl' => true,
                'showScriptName' => false,
                'rules' => [
                    '' => 'site/login',
                    'logout' => 'site/logout',
                ],
            ],
            'scheme' => 'https',
        ],
        'm' => [
        ],
        'mh' => [
            'component' => [
                'enablePrettyUrl' => true,
            ],
        ],
    ];

}
