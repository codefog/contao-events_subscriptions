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
use Contao\Controller;
use Contao\Model;
use Contao\Model\Collection;
use Contao\System;
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
            $tokens = array_merge($tokens, $this->getModelTokens($event, 'event_'));

            // Generate calendar tokens
            if (($calendar = CalendarModel::findByPk($event->pid)) !== null) {
                $tokens = array_merge($tokens, $this->getModelTokens($calendar, 'calendar_'));
            }
        }

        // Generate member tokens
        if (($member = $subscription->getMember()) !== null) {
            $tokens = array_merge($tokens, $this->getModelTokens($member, 'member_'));
        }

        return $tokens;
    }

    /**
     * Get the model tokens
     *
     * @param Model  $model
     * @param string $prefix
     *
     * @return array
     */
    private function getModelTokens(Model $model, $prefix)
    {
        $tokens = [];
        $dc     = new DataContainerMock($model);
        $table  = $model::getTable();

        // Load the data container
        Controller::loadDataContainer($table);

        foreach ($model->row() as $k => $v) {
            $field = $GLOBALS['TL_DCA'][$table]['fields'][$k];

            // Avoid the potential SQL error
            if ($field['foreignKey'] && !$v) {
                $v = 0;
            }

            $tokens[$prefix.$k] = Format::dcaValue($table, $k, $v, $dc);
        }

        return $tokens;
    }
}
