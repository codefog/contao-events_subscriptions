<?php

/**
 * events_subscriptions extension for Contao Open Source CMS
 *
 * @copyright Copyright (c) 2011-2017, Codefog
 * @author    Codefog <https://codefog.pl>
 * @license   http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link      http://github.com/codefog/contao-events_subscriptions
 */

namespace Codefog\EventsSubscriptions\DataContainer;

use Codefog\EventsSubscriptions\Services;
use Contao\Backend;
use Contao\BackendUser;
use Contao\CalendarEventsModel;
use Contao\CalendarModel;
use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\DataContainer;
use Contao\Image;
use Contao\Input;
use Contao\System;
use Haste\Dca\PaletteManipulator;

class EventsContainer
{
    /**
     * Extend the palette if necessary
     *
     * @param DataContainer $dc
     */
    public function extendPalette(DataContainer $dc)
    {
        if (!$dc->id) {
            return;
        }

        // Get the current pid
        if (Input::get('act') === 'edit') {
            $pid = CalendarEventsModel::findByPk($dc->id)->pid;
        } else {
            $pid = $dc->id;
        }

        // Return if the subscription is not enabled
        if (!CalendarModel::findByPk($pid)->subscription_enable) {
            return;
        }

        PaletteManipulator::create()
            ->addLegend('subscription_legend', 'title_legend', \Haste\Dca\PaletteManipulator::POSITION_AFTER, true)
            ->addField('subscription_override', 'subscription_legend', \Haste\Dca\PaletteManipulator::POSITION_APPEND)
            ->applyToPalette('default', 'tl_calendar_events');
    }

    /**
     * Check the permission
     */
    public function checkPermission()
    {
        try {
            System::importStatic('tl_calendar_events')->checkPermission();
        } catch (AccessDeniedException $e) {
            // Catch the exception and return if this is the subscriptions notification controller
            if (Input::get('key') === 'subscriptions_notification') {
                $user = BackendUser::getInstance();

                if (empty($user->calendars) || !\is_array($user->calendars)) {
                    $root = [0];
                } else {
                    $root = $user->calendars;
                }

                // Return if the calendar is allowed
                if (\in_array(CalendarEventsModel::findByPk(Input::get('id'))->pid, $root)) {
                    return;
                }
            }

            throw $e;
        }
    }

    /**
     * Get the "subscriptions overview" button
     *
     * @param string $href
     * @param string $label
     * @param string $title
     * @param string $class
     * @param string $attributes
     *
     * @return string
     */
    public function getSubscriptionsOverviewButton($href, $label, $title, $class, $attributes)
    {
        if (($calendarModel = CalendarModel::findByPk(CURRENT_ID)) === null || !$calendarModel->subscription_enable) {
            return '';
        }

        return sprintf(
            '<a href="%s" class="%s" title="%s"%s>%s</a> ',
            str_replace('&amp;table=tl_calendar_events', '', Backend::addToUrl($href.'&amp;id='.CURRENT_ID)),
            $class,
            specialchars($title),
            $attributes,
            $label
        );
    }

    /**
     * Get the "subscriptions export" button
     *
     * @return string
     */
    public function getSubscriptionsExportButton($href, $label, $title, $class, $attributes)
    {
        if (($calendarModel = CalendarModel::findByPk(CURRENT_ID)) === null || !$calendarModel->subscription_enable) {
            return '';
        }

        return sprintf(
            '<a href="%s" class="%s" title="%s"%s>%s</a> ',
            str_replace('&amp;table=tl_calendar_events', '', Backend::addToUrl($href.'&amp;id='.CURRENT_ID)),
            $class,
            specialchars($title),
            $attributes,
            $label
        );
    }

    /**
     * Get the "notifications" button
     *
     * @param array  $row
     * @param string $href
     * @param string $label
     * @param string $title
     * @param string $icon
     * @param string $attributes
     *
     * @return string
     */
    public function getNotificationsButton(array $row, $href, $label, $title, $icon, $attributes)
    {
        if (!$this->isSubscriptionEnabled($row['id'])) {
            return '';
        }

        return sprintf(
            '<a href="%s" title="%s"%s>%s</a> ',
            Backend::addToUrl($href.'&amp;id='.$row['id']),
            specialchars($title),
            $attributes,
            Image::getHtml($icon, $label)
        );
    }

    /**
     * Get the "subscriptions" button
     *
     * @param array  $row
     * @param string $href
     * @param string $label
     * @param string $title
     * @param string $icon
     * @param string $attributes
     *
     * @return string
     */
    public function getSubscriptionsButton(array $row, $href, $label, $title, $icon, $attributes)
    {
        if (!$this->isSubscriptionEnabled($row['id'])) {
            return '';
        }

        return sprintf(
            '<a href="%s" title="%s"%s>%s</a> ',
            Backend::addToUrl($href.'&amp;id='.$row['id']),
            specialchars($title),
            $attributes,
            Image::getHtml($icon, $label)
        );
    }

    /**
     * Get the types
     *
     * @return array
     */
    public function getTypes()
    {
        return Services::getSubscriptionFactory()->getAll();
    }

    /**
     * Return true if the subscription is enabled
     *
     * @param int $id
     *
     * @return bool
     */
    private function isSubscriptionEnabled($id)
    {
        return Services::getEventConfigFactory()->create($id)->canSubscribe();
    }
}
