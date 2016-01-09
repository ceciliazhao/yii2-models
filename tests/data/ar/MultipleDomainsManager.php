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
 * Description of MultipleDomainsManager
 *
 * @author i
 */
class MultipleDomainsManager extends \vistart\Models\models\MultipleDomainsManager {
    /**
     * @var string the base domain.
     */
    public $baseDomain = 'yii2-models.vistart';

    /**
     * <Sub Domain Name> => [
     *     'component' => <URL Manager Component Configuration Array>,
     *     'schema' => 'http'(default) or 'https',
     * ]
     * @var array 
     */
    public $subDomains = [
        'my' => [
            'component' => [
                'class' => 'yii\web\UrlManager',
                'enablePrettyUrl' => true,
                'showScriptName' => false,
                'suffix' => '.html',
                'rules' => [
                    [
                        'pattern' => 'login',
                        'route' => 'site/login',
                        'suffix' => '',
                    ],
                ],
            ],
            'schema' => 'https',
        ],
    ];
}
