<?php

namespace Codefog\EventsSubscriptionsBundle\Backend;

use Codefog\EventsSubscriptionsBundle\EventConfig;
use Codefog\EventsSubscriptionsBundle\Model\SubscriptionModel;
use Codefog\EventsSubscriptionsBundle\NotificationCenter\NotificationType\EventsSubscriptionsEventType;
use Codefog\EventsSubscriptionsBundle\NotificationCenterHelper;
use Codefog\EventsSubscriptionsBundle\NotificationSender;
use Codefog\EventsSubscriptionsBundle\Services;
use Codefog\EventsSubscriptionsBundle\Subscription\NotificationAwareInterface;
use Codefog\HasteBundle\Formatter;
use Contao\Backend;
use Contao\BackendTemplate;
use Contao\CalendarEventsModel;
use Contao\Controller;
use Contao\Database;
use Contao\Date;
use Contao\Environment;
use Contao\Input;
use Contao\MemberGroupModel;
use Contao\Message;
use Contao\StringUtil;
use Contao\System;
use Contao\Widget;

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
        $memberGroups = $this->getMemberGroups($eventModel);

        // Process the form
        if (Input::post('FORM_SUBMIT') === $formSubmit) {
            $this->processForm($eventModel, $memberGroups);
        }

        return $this->createTemplate($eventModel, $formSubmit, $memberGroups)->parse();
    }

    /**
     * Create the template
     *
     * @param CalendarEventsModel $eventModel
     * @param string              $formSubmit
     * @param array               $memberGroups
     *
     * @return BackendTemplate
     */
    protected function createTemplate(CalendarEventsModel $eventModel, $formSubmit, array $memberGroups = [])
    {
        $eventData = [];

        // Format the event data
        foreach ($eventModel->row() as $k => $v) {
            $eventData[$k] = System::getContainer()->get(Formatter::class)->dcaValue($eventModel::getTable(), $k, $v);;
        }

        $template = new BackendTemplate('be_events_subscriptions_notification');
        $template->backUrl = Backend::getReferer();
        $template->message = Message::generate();
        $template->event = $eventData;
        $template->eventRaw = $eventModel->row();
        $template->action = Environment::get('request');
        $template->formSubmit = $formSubmit;
        $template->lastNotificationDate = ($eventModel->subscription_lastNotificationSent) ? System::getContainer()->get(Formatter::class)->datim($eventModel->subscription_lastNotificationSent) : null;

        $template->notification = new $GLOBALS['BE_FFL']['select'](Widget::getAttributesFromDca([
            'label' => &$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['notification.notification'],
            'options' => $this->getNotifications(),
            'eval' => ['mandatory' => true, 'includeBlankOption' => true],
        ], 'notification', Input::post('notification')));

        $template->subscribableMemberGroups = new $GLOBALS['BE_FFL']['checkbox'](Widget::getAttributesFromDca([
            'label' => &$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['notification.subscribableMemberGroups'],
            'options' => $memberGroups['subscribable'],
            'eval' => ['multiple' => true],
        ], 'subscribable-member-groups', Input::post('subscribable-member-groups')));

        if (count($memberGroups['other']) > 0) {
            $template->otherMemberGroups = new $GLOBALS['BE_FFL']['checkbox'](Widget::getAttributesFromDca([
                'label' => &$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['notification.otherMemberGroups'],
                'options' => $memberGroups['other'],
                'eval' => ['multiple' => true],
            ], 'other-member-groups', Input::post('other-member-groups')));
        }

        return $template;
    }

    /**
     * Process the form
     *
     * @param CalendarEventsModel $eventModel
     * @param array $memberGroups
     */
    protected function processForm(CalendarEventsModel $eventModel, array $memberGroups = [])
    {
        if (!($notificationId = (int) Input::post('notification')) || !array_key_exists($notificationId, $this->getNotifications())) {
            Controller::reload();
        }

        if (isset($_POST['action_subscribers'])) {
            $count = $this->handleSubscribersAction($eventModel, $notificationId);
        } elseif (isset($_POST['action_groups'])) {
            $count = $this->handleGroupsAction($eventModel, $notificationId, $memberGroups);
        }

        // Update the last notification time
        $eventModel->subscription_lastNotificationSent = time();
        $eventModel->save();

        Message::addConfirmation(sprintf($GLOBALS['TL_LANG']['tl_calendar_events_subscription']['notification.confirmation'], $count));
        Controller::redirect(Backend::getReferer());
    }

    /**
     * Send notification to all subscribers of the event.
     */
    protected function handleSubscribersAction(CalendarEventsModel $eventModel, int $notificationId)
    {
        $subscriptionModels = SubscriptionModel::findBy('pid', $eventModel->id);

        if ($subscriptionModels === null) {
            Message::addError($GLOBALS['TL_LANG']['tl_calendar_events_subscription']['notification.noRecipients']);
            Controller::reload();
        }

        $count = 0;
        $basicTokens = $this->notificationSender->getBasicTokens($eventModel);
        $factory = Services::getSubscriptionFactory();

        /** @var SubscriptionModel $subscriptionModel */
        foreach ($subscriptionModels as $subscriptionModel) {
            $subscription = $factory->createFromModel($subscriptionModel);

            if (!($subscription instanceof NotificationAwareInterface)) {
                continue;
            }

            $tokens = array_merge($basicTokens, $subscription->getNotificationTokens());
            $tokens['recipient_email'] = $subscription->getNotificationEmail();

            $count += System::getContainer()->get(NotificationCenterHelper::class)->sendNotification($notificationId, $tokens);
        }

        return $count;
    }

    /**
     * Send notification to unsubscribed members of selected groups.
     */
    protected function handleGroupsAction(CalendarEventsModel $eventModel, int $notificationId, array $memberGroups)
    {
        $memberGroupIds = [];

        // Assign subscribable member groups
        if (is_array($subscribableMemberGroupIds = Input::post('subscribable-member-groups')) && count($subscribableMemberGroupIds) > 0) {
            $memberGroupIds = array_merge($memberGroupIds, array_intersect(array_map('\intval', $subscribableMemberGroupIds), array_keys($memberGroups['subscribable'])));
        }

        // Assign subscribable other groups
        if (is_array($otherMemberGroupIds = Input::post('other-member-groups')) && count($otherMemberGroupIds) > 0) {
            $memberGroupIds = array_merge($memberGroupIds, array_intersect(array_map('\intval', $otherMemberGroupIds), array_keys($memberGroups['other'])));
        }

        if (count($memberGroupIds) === 0) {
            Message::addError($GLOBALS['TL_LANG']['tl_calendar_events_subscription']['notification.noRecipients']);
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

        $basicTokens = $this->notificationSender->getBasicTokens($eventModel);
        $count = 0;

        foreach ($members as $member) {
            $tokens = array_merge($basicTokens, $this->notificationSender->getTokens($member, 'tl_member', 'member_'));
            $tokens['recipient_email'] = $member['email'];

            $count += System::getContainer()->get(NotificationCenterHelper::class)->sendNotification($notificationId, $tokens, $member->language);
        }

        return $count;
    }

    /**
     * Get the member groups
     *
     * @param CalendarEventsModel $eventModel
     *
     * @return array
     */
    protected function getMemberGroups(CalendarEventsModel $eventModel)
    {
        $groups = ['subscribable' => [], 'other' => []];
        $eventConfig = new EventConfig($eventModel->getRelated('pid'), $eventModel);
        $subscribableGroups = $eventConfig->hasMemberGroupsLimit() ? $eventConfig->getMemberGroups() : null;

        if (($models = MemberGroupModel::findAllActive(['order' => 'name'])) !== null) {
            /** @var MemberGroupModel $model */
            foreach ($models as $model) {
                $key = 'subscribable';

                if (isset($subscribableGroups) && !in_array($model->id, $subscribableGroups)) {
                    $key = 'other';
                }

                $groups[$key][(int) $model->id] = $model->name;
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
        return System::getContainer()->get(NotificationCenterHelper::class)->getNotificationsByType(EventsSubscriptionsEventType::NAME);
    }
}
