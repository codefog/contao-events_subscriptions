<?php

namespace Codefog\EventsSubscriptions\Backend;

use Codefog\EventsSubscriptions\NotificationSender;
use Codefog\EventsSubscriptions\Services;
use Contao\Backend;
use Contao\BackendTemplate;
use Contao\CalendarEventsModel;
use Contao\CalendarModel;
use Contao\Config;
use Contao\Controller;
use Contao\Database;
use Contao\Date;
use Contao\Environment;
use Contao\Events;
use Contao\Input;
use Contao\MemberGroupModel;
use Contao\Message;
use Contao\StringUtil;
use Contao\System;
use Contao\Widget;
use Haste\Util\Format;
use NotificationCenter\Model\Notification;

class NotificationController
{
    /**
     * @var NotificationSender
     */
    protected $notificationSender;

    /**
     * ExportController constructor.
     */
    public function __construct()
    {
        $this->notificationSender = Services::getNotificationSender();
    }

    /**
     * Run the controller
     *
     * @return string
     */
    public function run()
    {
        if (Input::get('key') !== 'subscriptions_notification'
            || ($eventModel = CalendarEventsModel::findByPk(Input::get('id'))) === null
        ) {
            Controller::redirect('contao/main.php?act=error');
        }

        System::loadLanguageFile('tl_calendar_events_subscription');

        $formSubmit = 'events-subscriptions-notification';

        // Process the form
        if (Input::post('FORM_SUBMIT') === $formSubmit) {
            $this->processForm($eventModel);
        }

        return $this->createTemplate($eventModel, $formSubmit)->parse();
    }

    /**
     * Create the template
     *
     * @param CalendarEventsModel $eventModel
     * @param string              $formSubmit
     *
     * @return BackendTemplate
     */
    protected function createTemplate(CalendarEventsModel $eventModel, $formSubmit)
    {
        $eventData = [];

        // Format the event data
        foreach ($eventModel->row() as $k => $v) {
            $eventData[$k] = Format::dcaValue($eventModel::getTable(), $k, $v);;
        }

        $template = new BackendTemplate('be_events_subscriptions_notification');
        $template->backUrl = Backend::getReferer();
        $template->message = Message::generate();
        $template->event = $eventData;
        $template->eventRaw = $eventModel->row();
        $template->action = Environment::get('request');
        $template->formSubmit = $formSubmit;
        $template->lastNotificationDate = ($eventModel->subscription_lastNotificationSent) ? Format::datim($eventModel->subscription_lastNotificationSent) : null;

        $template->notification = new $GLOBALS['BE_FFL']['select'](Widget::getAttributesFromDca([
            'label' => &$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['notification.notification'],
            'options' => $this->getNotifications(),
            'eval' => ['mandatory' => true, 'includeBlankOption' => true],
        ], 'notification', Input::post('notification')));

        $template->memberGroups = new $GLOBALS['BE_FFL']['checkbox'](Widget::getAttributesFromDca([
            'label' => &$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['notification.memberGroups'],
            'options' => $this->getMemberGroups(),
            'eval' => ['mandatory' => true, 'multiple' => true],
        ], 'member-groups', Input::post('member-groups')));

        return $template;
    }

    /**
     * Process the form
     *
     * @param CalendarEventsModel $eventModel
     */
    protected function processForm(CalendarEventsModel $eventModel)
    {
        if (!($notificationId = Input::post('notification')) || !($memberGroupIds = Input::post('member-groups')) || !is_array($memberGroupIds) || count($memberGroupIds) === 0) {
            Controller::reload();
        }

        $notificationId = (int) $notificationId;
        $memberGroupIds = array_intersect(array_map('\intval', $memberGroupIds), array_keys($this->getMemberGroups()));

        // Validate submitted data
        if (!array_key_exists($notificationId, $this->getNotifications()) || count($memberGroupIds) === 0 || ($notification = Notification::findByPk($notificationId)) === null) {
            Controller::reload();
        }

        $members = [];
        $time = Date::floorToMinute();
        $memberRecords = Database::getInstance()
            ->prepare("SELECT * FROM tl_member WHERE login=? AND (start='' OR start<=?) AND (stop='' OR stop>?) AND disable='' AND id NOT IN (SELECT member FROM tl_calendar_events_subscription WHERE pid=?)")
            ->execute(1, $time, $time + 60, $eventModel->id)
        ;

        // Get only the members that belong to selected groups
        while ($memberRecords->next()) {
            if (count(array_intersect($memberGroupIds, StringUtil::deserialize($memberRecords->groups, true))) > 0) {
                $members[] = $memberRecords->row();
            }
        }

        // No recipients
        if (count($members) === 0) {
            Message::addInfo($GLOBALS['TL_LANG']['tl_calendar_events_subscription']['notification.noRecipients']);
            Controller::reload();
        }

        $basicTokens = [
            'admin_email' => $GLOBALS['TL_ADMIN_EMAIL'] ?: Config::get('adminEmail'),
        ];

        // Generate event tokens
        $basicTokens = array_merge($basicTokens, $this->notificationSender->getModelTokens($eventModel, 'event_'));
        $basicTokens['event_link'] = Events::generateEventUrl($eventModel, true);

        // Generate calendar tokens
        if (($calendar = CalendarModel::findByPk($eventModel->pid)) !== null) {
            $basicTokens = array_merge($basicTokens, $this->notificationSender->getModelTokens($calendar, 'calendar_'));
        }

        $count = 0;

        foreach ($members as $member) {
            $tokens = array_merge($basicTokens, $this->notificationSender->getTokens($member, 'tl_member', 'member_'));
            $tokens['recipient_email'] = $member['email'];

            /** @var Notification $notification */
            $count += count(array_filter($notification->send($tokens, $member->language)));
        }

        // Update the last notification time
        $eventModel->subscription_lastNotificationSent = time();
        $eventModel->save();

        Message::addConfirmation(sprintf($GLOBALS['TL_LANG']['tl_calendar_events_subscription']['notification.confirmation'], $count));
        Controller::redirect(Backend::getReferer());
    }

    /**
     * Get the member groups
     *
     * @return array
     */
    protected function getMemberGroups()
    {
        $groups = [];

        if (($models = MemberGroupModel::findAllActive(['order' => 'name'])) !== null) {
            /** @var MemberGroupModel $model */
            foreach ($models as $model) {
                $groups[(int) $model->id] = $model->name;
            }
        }

        return $groups;
    }

    /**
     * Get the notifications
     *
     * @return array
     */
    protected function getNotifications()
    {
        $notifications = [];

        if (($models = Notification::findBy('type', 'events_subscription_event', ['order' => 'title'])) !== null) {
            /** @var Notification $model */
            foreach ($models as $model) {
                $notifications[(int) $model->id] = $model->title;
            }
        }

        return $notifications;
    }
}
