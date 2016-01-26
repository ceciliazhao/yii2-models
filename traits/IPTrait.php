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

use vistart\Helpers\Ip;
use Yii;

/**
 * Entity features concerning IP address.
 * @property string|integer|null $ipAddress
 * @proeprty array $ipRules
 * @version 2.0
 * @author vistart <i@vistart.name>
 */
trait IPTrait
{

    /**
     * @var integer REQUIRED. Determine whether enabling the IP attributes and 
     * features, and IP address type if enabled.
     * @since 1.1
     * @version 2.0
     */
    public $enableIP = 0x03;

    /**
     * @var integer Disable IP address features.
     */
    public static $no_ip = 0x0;

    /**
     * @var integer Only accept IPv4 address.
     */
    public static $ipv4 = 0x1;

    /**
     * @var integer Only accept IPv6 address.
     */
    public static $ipv6 = 0x2;

    /**
     * @var integer Accept IPv4 and IPv6 address. Judge type of IP address
     * automatically.
     */
    public static $ip_all = 0x3;

    /**
     * @var string The attribute name that will receive the beginning 32 bits of
     * IPv6, or IPv4. The default value is 'ip_1'.
     */
    public $ipAttribute1 = 'ip_1';

    /**
     * @var string The attribute name that will receive the 33 - 64 bits of IPv6,
     * or 0 of IPv4. The default value is 'ip_2'.
     */
    public $ipAttribute2 = 'ip_2';

    /**
     * @var string The attribute name that will receive the 65 - 96 bits of IPv6,
     * or 0 of IPv4. The default value is 'ip_3'.
     */
    public $ipAttribute3 = 'ip_3';

    /**
     * @var string The attribute name that will receive the last 32 bits of IPv6,
     * or 0 of IPv4. The default value is 'ip_4'.
     */
    public $ipAttribute4 = 'ip_4';

    /**
     * @var string The attribute name that will receive the type of IP address.
     * The default value is 'ip_type'. If you assign $enableIP to $ip_all, this
     * attribute is required.
     */
    public $ipTypeAttribute = 'ip_type';

    /**
     * Attach `onInitGuidAttribute` event.
     * @param string $eventName
     */
    protected function attachInitIpEvent($eventName)
    {
        $this->on($eventName, [$this, 'onInitIpAddress']);
    }

    /**
     * Initialize ip attributes.
     * This method is ONLY used for being triggered by event. DO NOT call,
     * override or modify it directly, unless you know the consequences.
     * @param \yii\base\Event $event
     * @since 1.1
     */
    public function onInitIpAddress($event)
    {
        $sender = $event->sender;
        if ($sender->enableIP && empty($sender->ipAddress))
        {
            $sender->ipAddress = Yii::$app->request->userIP;
        }
    }

    /**
     * Return the IP address.
     * The IP address is converted from ipAttribute*.
     * If you disable($this->enableIP = false) the IP feature, this method will
     * return null, or return the significantly IP address(Colon hexadecimal of
     * IPv6 or Dotted decimal of IPv4).
     * @return string|integer|null
     */
    public function getIpAddress()
    {
        if (!$this->enableIP)
        {
            return null;
        }
        if ($this->enableIP & static::$ip_all)
        {
            $ipTypeAttribute = $this->ipTypeAttribute;
            if ($this->$ipTypeAttribute == Ip::IPv4)
            {
                return $this->getIpv4Address();
            }
            if ($this->$ipTypeAttribute == Ip::IPv6)
            {
                return $this->getIpv6Address();
            }
        } else
        if ($this->enableIP & static::$ipv4)
        {
            return $this->getIpv4Address();
        } else
        if ($this->enableIP & static::$ipv6)
        {
            return $this->getIpv6Address();
        }
        return null;
    }

    /**
     * Get the IPv4 address.
     * @return string
     */
    private function getIpv4Address()
    {
        $ipAttribute1 = $this->ipAttribute1;
        return Ip::long2ip($this->$ipAttribute1);
    }

    /**
     * Get the IPv6 address.
     * @return string
     */
    private function getIpv6Address()
    {
        $ipAttribute1 = $this->ipAttribute1;
        $ipAttribute2 = $this->ipAttribute2;
        $ipAttribute3 = $this->ipAttribute3;
        $ipAttribute4 = $this->ipAttribute4;
        return Ip::LongtoIPv6(Ip::populateIPv6([
                            $this->$ipAttribute1,
                            $this->$ipAttribute2,
                            $this->$ipAttribute3,
                            $this->$ipAttribute4
        ]));
    }

    /**
     * Convert the IP address to integer, and store it(them) to ipAttribute*.
     * If you disable($this->enableIP = false) the IP feature, this method will
     * be skipped(return null).
     * @param string $ipAddress the significantly IP address.
     * @return string|integer|null Integer when succeeded to convert.
     */
    public function setIpAddress($ipAddress)
    {
        if (!$ipAddress || !$this->enableIP)
        {
            return null;
        }
        $ipType = Ip::judgeIPtype($ipAddress);
        if ($ipType == Ip::IPv4 && $this->enableIP & static::$ipv4)
        {
            $ipAttribute1 = $this->ipAttribute1;
            $this->$ipAttribute1 = Ip::ip2long($ipAddress);
        } else
        if ($ipType == Ip::IPv6 && $this->enableIP & static::$ipv6)
        {
            $ips = Ip::splitIPv6(Ip::IPv6toLong($ipAddress));
            $ipAttribute1 = $this->ipAttribute1;
            $ipAttribute2 = $this->ipAttribute2;
            $ipAttribute3 = $this->ipAttribute3;
            $ipAttribute4 = $this->ipAttribute4;
            $this->$ipAttribute1 = bindec($ips[0]);
            $this->$ipAttribute2 = bindec($ips[1]);
            $this->$ipAttribute3 = bindec($ips[2]);
            $this->$ipAttribute4 = bindec($ips[3]);
        } else
        {
            return 0;
        }
        if ($this->enableIP & static::$ip_all)
        {
            $ipTypeAttribute = $this->ipTypeAttribute;
            $this->$ipTypeAttribute = $ipType;
        }
        return $ipType;
    }

    /**
     * Get the rules associated with ip attributes.
     * @return array
     */
    public function getIpRules()
    {
        $rules = [];
        if ($this->enableIP & static::$ipv6)
        {
            $rules = [
                [[$this->ipAttribute1,
                $this->ipAttribute2,
                $this->ipAttribute3,
                $this->ipAttribute4],
                    'number', 'integerOnly' => true, 'min' => 0
                ],
            ];
        }
        if ($this->enableIP & static::$ipv4)
        {
            $rules = [
                [[$this->ipAttribute1],
                    'number', 'integerOnly' => true, 'min' => 0
                ],
            ];
        }
        if ($this->enableIP & static::$ip_all)
        {
            $rules[] = [
                [$this->ipTypeAttribute], 'in', 'range' => [Ip::IPv4, Ip::IPv6],
            ];
        }
        return $rules;
    }
}
