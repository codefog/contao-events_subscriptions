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
 * Fields
 */
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['type']         = [
    'Type',
    'Please choose the subscription type.',
];
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['addedBy']      = [
    'Added by',
    'Here you can choose who subscribed this member.',
];
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['disableReminders'] = [
    'Disable reminders',
    'Disable the event reminders for this subscriber.',
];
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['sendNotification'] = [
    'Send a notification',
    'Send a subscribe notification to the participant.',
];
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['numberOfParticipants'] = [
    'Number of participants',
    'Here you can enter the number of participants.',
];
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['member']       = [
    'Member',
    'Please choose the member you want to subscribe to the event.',
];
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['firstname']    = ['First name', 'Please enter the first name.'];
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['lastname']     = ['Lastname', 'Please enter the last name.'];
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['email']        = [
    'E-mail address',
    'Please enter the e-mail address.',
];
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['dateCreated']  = ['Date created'];
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['lastReminder'] = ['Last reminder sent'];
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['unsubscribeToken'] = ['Unsubscribe token'];

/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['type_legend']   = 'Subscription settings';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['guest_legend']  = 'Guest details';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['member_legend'] = 'Member details';

/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['new']    = [
    'New subscription',
    'Create a new subscriptions',
];
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['newFromMemberGroup']    = [
    'New from member group',
    'Create multiple subscriptions from member group',
];
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['show']   = [
    'Subscription details',
    'Show details of subscription ID %s',
];
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['edit']   = [
    'Edit subscription',
    'Edit subscription ID %s',
];
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['delete'] = [
    'Delete subscription',
    'Delete subscription ID %s',
];
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['export'] = ['Export', 'Export the subscriptions.'];

/**
 * Reference
 */
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['typeRef'] = [
    'guest'  => 'Guest',
    'member' => 'Member',
];

/**
 * Miscellaneous
 */
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['summary']    = 'There are %s subscription(s) to this event.';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['summaryMax'] = 'There are %s subscription(s) of %s possible to this event.';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['activeMembers'] = 'Active';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['inactiveMembers'] = 'Inactive';

/**
 * Export
 */
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['export.headline'] = 'Export subscriptions';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['export.explanation'] = 'You are about to export the subscriptions data for the event:';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['export.count'] = 'Number of the subscriptions to be exported:';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['export.excelFormatHint'] = 'To enable export in Excel format please install the <strong>phpoffice/phpspreadsheet</strong> package. Alternatively you can install the deprecated <strong>phpoffice/phpexcel</strong> package.';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['export.csv'] = 'Export as CSV';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['export.excel'] = 'Export as Excel';

// Backend breadcrumb
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['subscriptions_export'] = [null, 'Export subscriptions of event ID %s'];

/**
 * New from member group
 */
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['newFromMemberGroup.headline'] = 'Create subscriptions from member group';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['newFromMemberGroup.explanation'] = 'Here you can batch create multiple member subscriptions by selecting the member groups for the event:';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['newFromMemberGroup.subscribableMemberGroups'] = 'Member groups allowed to subscribe';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['newFromMemberGroup.otherMemberGroups'] = 'Other member groups';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['newFromMemberGroup.noMembers'] = 'There are no active members to subscribe.';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['newFromMemberGroup.memberStatus'] = 'Member status';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['newFromMemberGroup.sendNotification'] = 'Send a notification';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['newFromMemberGroup.submit'] = 'Create subscriptions';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['newFromMemberGroup.confirmation'] = '%s members subscribed to this event successfully!';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['newFromMemberGroup.memberStatusRef'] = [
    'all' => 'All members',
    'active' => 'Only active members',
    'preActive' => 'Active + pre-active members (with activation date in the future)',
];

// Backend breadcrumb
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['subscriptions_newFromMemberGroup'] = [null, 'Create subscriptions from member group for event ID %s'];

/**
 * Notification
 */
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['notification.headline'] = 'Event notifications';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['notification.explanation'] = 'Here you can send a notification to selected member groups for the event:';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['notification.notification'] = 'Notification';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['notification.notificationExplanation'] = 'Please choose the notification that will be sent.';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['notification.subscribersHeadline'] = 'Subscribers of the event';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['notification.subscribersExplanation'] = 'Send a notification to all subscribers of the current event. The notification will be sent to both guest and member subscriptions.';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['notification.groupsHeadline'] = 'Not subscribed members';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['notification.groupsExplanation'] = 'Send a notification to members of the selected groups that haven\'t subscribed to the event yet.';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['notification.subscribableMemberGroups'] = 'Member groups allowed to subscribe';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['notification.otherMemberGroups'] = 'Other member groups';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['notification.submit'] = 'Send a notification';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['notification.noRecipients'] = 'There are no active members to send this notification to.';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['notification.confirmation'] = '%s notifications sent successfully!';
$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['notification.lastNotificationDate'] = 'Last notification sent at %s.';
