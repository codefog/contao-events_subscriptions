<?php

/**
 * events_subscriptions extension for Contao Open Source CMS
 *
 * @copyright Copyright (c) 2011-2017, Codefog
 * @author    Codefog <https://codefog.pl>
 * @license   http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link      http://github.com/codefog/contao-events_subscriptions
 */

namespace Codefog\EventsSubscriptionsBundle;

class FlashMessage
{
    /**
     * Session key
     * @var string
     */
    private $sessionKey = 'event-subscribe-message';

    /**
     * Set the message
     *
     * @param string $message
     * @param string $id
     */
    public function set($message, $id)
    {
        $_SESSION[$this->sessionKey][$id] = $message;
    }

    /**
     * Puke the message
     *
     * @param string $id
     *
     * @return string|null
     */
    public function puke($id)
    {
        $message = null;

        if (isset($_SESSION[$this->sessionKey][$id])) {
            $message = $_SESSION[$this->sessionKey][$id];
            unset($_SESSION[$this->sessionKey][$id]);
        }

        return $message;
    }
}
