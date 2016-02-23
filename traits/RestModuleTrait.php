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

namespace vistart\Models\traits;

/**
 * This trait is used for building module with RESTful API.
 *
 * @version 2.0
 * @author vistart <i@vistart.name>
 */
trait RestModuleTrait
{

    /**
     * This event will move response data into array which contains `success` and
     * `data` elements, and replace status code with 200.
     * @param \yii\base\Event $event
     */
    protected function responseBeforeSend($event)
    {
        $response = $event->sender;
        /* @var $response \yii\web\Response */
        if ($response->data !== null) {
            // Clear the 'type' property in all responses.
            if (isset($response->data['type'])) {
                unset($response->data['type']);
            }
            $response->data = [
                'success' => $response->isSuccessful,
                'data' => $response->data
            ];
            $response->statusCode = 200;
        }
    }

    /**
     * replace '_' with '/'.
     * @param string $route
     * @return string 
     */
    public static function transRouteToName($route)
    {
        return str_replace('/', '_', $route);
    }

    /**
     * Get API name.
     * @param string $route
     * @return string
     */
    public static function getApiName($route)
    {
        return 'api_' . self::transRouteToName($route);
    }

    public static function getApiRateLimiterName($route)
    {
        return static::getApiName($route) . '_ratelimiter';
    }
}
