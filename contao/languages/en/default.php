<?php

/**
 * events_subscriptions extension for Contao Open Source CMS
 *
 * @copyright Copyright (c) 2011-2017, Codefog
 * @author    Codefog <https://codefog.pl>
 * @license   http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link      http://github.com/codefog/contao-events_subscriptions
 */

/**
 * Miscellaneous
 */
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.subscribe']               = 'Subscribe';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.subscribeWaitingList']    = 'Subscribe to a waiting list';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.unsubscribe']             = 'Unsubscribe';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.unsubscribeWaitingList']  = 'Unsubscribe from a waiting list';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.subscribeConfirmation']   = 'You have subscribed to the event.';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.unsubscribeConfirmation'] = 'You have unsubscribed from the event.';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.subscribeNotAllowed']     = 'It is no longer possible to subscribe to this event.';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.unsubscribeNotAllowed']   = 'It is no longer possible to unsubscribe from this event.';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.onWaitingList']           = 'waiting list';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.enableReminders']         = 'Send me reminders for this event';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.canSubscribeUntil']       = 'You can subscribe until: %s';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.canUnsubscribeUntil']     = 'You can unsubscribe until: %s';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.numberOfParticipants']    = 'Number of participants';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.numberOfParticipantsLimit'] = 'You can subscribe up to %d participants to this event.';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.numberOfParticipantsExceeded'] = 'Unfortunately, you cannot subscribe because the maximum number of participants in this event is %d.';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.numberOfParticipantsExceededWaitingList'] = 'Unfortunately, you cannot subscribe because the maximum number of participants in this event is %d, with an extra waiting list of %d slots.';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.numberOfParticipantsSubscribeToWaitingList'] = 'You can only be subscribed to the waiting list of this event because the maximum number of participants is %d. Please re-submit the form to subscribe to the waiting list.';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.guestForm.firstname']     = 'First name';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.guestForm.lastname']      = 'Last name';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.guestForm.email']         = 'E-mail address';

/**
 * Errors
 */
$GLOBALS['TL_LANG']['ERR']['events_subscriptions.memberAlreadySubscribed'] = 'Member ID %s is already subscribed to this event!';

/**
 * Export
 */
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.exportHeaderFields'] = [
    'event_id'                 => 'Event ID',
    'event_title'              => 'Event title',
    'event_start'              => 'Event start',
    'event_end'                => 'Event end',
    'subscription_date'        => 'Subscription date',
    'subscription_type'        => 'Subscription type',
    'subscription_waitingList' => 'Waiting list',
    'subscription_firstname'   => 'Firstname',
    'subscription_lastname'    => 'Lastname',
    'subscription_email'       => 'E-mail address',
    'subscription_numberOfParticipants' => 'Number of participants',
];

$GLOBALS['TL_LANG']['MSC']['events_subscriptions.memberExportHeaderFields'] = [
    'member_id'       => 'Member ID',
    'member_username' => 'Member username',
];
