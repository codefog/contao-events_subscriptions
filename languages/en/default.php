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
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.login']                   = 'Login to subscribe';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.subscribe']               = 'Subscribe';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.unsubscribe']             = 'Unsubscribe';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.subscribeConfirmation']   = 'You have subscribed to the event.';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.unsubscribeConfirmation'] = 'You have unsubscribed from the event.';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.subscribeNotAllowed']     = 'It is no longer possible to subscribe to this event.';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.unsubscribeNotAllowed']   = 'It is no longer possible to unsubscribe from this event.';

/**
 * Errors
 */
$GLOBALS['TL_LANG']['ERR']['events_subscriptions.memberAlreadySubscribed'] = 'Member ID %s is already subscribed to this event!';

/* Export */
$GLOBALS['TL_LANG']['ERR']['events_subscriptions.exportHeaderFields'] = [
    'Event ID',
    'Event title',
    'Event start',
    'Event end',
    'Member ID',
    'Member firstname',
    'Member lastname',
    'Member e-mail',
    'Member username',
];
