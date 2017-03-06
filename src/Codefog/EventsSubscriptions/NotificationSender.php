<?php

/**
 * events_subscriptions extension for Contao Open Source CMS
 *
 * @copyright Copyright (c) 2011-2017, Codefog
 * @author    Codefog <https://codefog.pl>
 * @license   http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link      http://github.com/codefog/contao-events_subscriptions
 */

namespace Codefog\EventsSubscriptions;

use Codefog\EventsSubscriptions\Model\SubscriptionModel;
use Contao\CalendarModel;
use Contao\Config;
use Contao\Model\Collection;
use Haste\Util\Format;
use NotificationCenter\Model\Notification;

class NotificationSender
{
    /**
     * Send the notification
     *
     * @param Notification      $notification
     * @param SubscriptionModel $subscription
     */
    public function send(Notification $notification, SubscriptionModel $subscription)
    {
        $tokens = $this->generateTokens($subscription);
        $notification->send($tokens, $tokens['member_language']);
    }

    /**
     * Send the notification by type
     *
     * @param string            $type
     * @param SubscriptionModel $subscription
     */
    public function sendByType($type, SubscriptionModel $subscription)
    {
        if (($models = Notification::findBy('type', $type)) === null) {
            return;
        }

        /**
         * @var Collection   $models
         * @var Notification $model
         */
        foreach ($models as $model) {
            $this->send($model, $subscription);
        }
    }

    /**
     * Generate the tokens
     *
     * @param SubscriptionModel $subscription
     *
     * @return array
     */
    private function generateTokens(SubscriptionModel $subscription)
    {
        $tokens = ['admin_email' => $GLOBALS['TL_ADMIN_EMAIL'] ?: Config::get('adminEmail')];

        // Generate event tokens
        if (($event = $subscription->getEvent()) !== null) {
            foreach ($event->row() as $k => $v) {
                $tokens['event_'.$k] = Format::dcaValue($event::getTable(), $k, $v);
            }

            // Generate calendar tokens
            if (($calendar = CalendarModel::findByPk($event->pid)) !== null) {
                foreach ($calendar->row() as $k => $v) {
                    $tokens['calendar_'.$k] = Format::dcaValue($calendar::getTable(), $k, $v);
                }
            }
        }

        // Generate member tokens
        if (($member = $subscription->getMember()) !== null) {
            foreach ($member->row() as $k => $v) {
                $tokens['member_'.$k] = Format::dcaValue($member::getTable(), $k, $v);
            }
        }

        return $tokens;
    }
}
