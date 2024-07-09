<?php

namespace Codefog\EventsSubscriptions\Backend;

use Codefog\EventsSubscriptions\EventConfig;
use Codefog\EventsSubscriptions\Services;
use Codefog\EventsSubscriptions\Subscription\MemberSubscription;
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
use Contao\MemberModel;
use Contao\Message;
use Contao\StringUtil;
use Contao\System;
use Contao\Widget;

class NewFromMemberGroupController
{
    /**
     * Run the controller
     *
     * @return string
     */
    public function run()
    {
        if (Input::get('key') !== 'subscriptions_newFromMemberGroup' || ($eventModel = CalendarEventsModel::findByPk(Input::get('id'))) === null) {
            Controller::redirect('contao?act=error');
        }

        System::loadLanguageFile('tl_calendar_events_subscription');

        $formSubmit = 'events-subscriptions-new-from-member-group';
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
    protected function createTemplate(CalendarEventsModel $eventModel, $formSubmit, array $memberGroups)
    {
        $eventData = [];

        // Format the event data
        foreach ($eventModel->row() as $k => $v) {
            $eventData[$k] = System::getContainer()->get(Formatter::class)->dcaValue($eventModel::getTable(), $k, $v);
        }

        $template = new BackendTemplate('be_events_subscriptions_new_from_member_group');
        $template->backUrl = Backend::getReferer();
        $template->message = Message::generate();
        $template->event = $eventData;
        $template->eventRaw = $eventModel->row();
        $template->action = Environment::get('request');
        $template->formSubmit = $formSubmit;

        $template->subscribableMemberGroups = new $GLOBALS['BE_FFL']['checkbox'](Widget::getAttributesFromDca([
            'label' => &$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['newFromMemberGroup.subscribableMemberGroups'],
            'options' => $memberGroups['subscribable'],
            'eval' => ['multiple' => true],
        ], 'subscribable-member-groups', Input::post('subscribable-member-groups')));

        if (count($memberGroups['other']) > 0) {
            $template->otherMemberGroups = new $GLOBALS['BE_FFL']['checkbox'](Widget::getAttributesFromDca([
                'label' => &$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['newFromMemberGroup.otherMemberGroups'],
                'options' => $memberGroups['other'],
                'eval' => ['multiple' => true],
            ], 'other-member-groups', Input::post('other-member-groups')));
        }

        $template->memberStatus = new $GLOBALS['BE_FFL']['select'](Widget::getAttributesFromDca([
            'label' => &$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['newFromMemberGroup.memberStatus'],
            'options' => ['all', 'active', 'preActive'],
            'reference' => &$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['newFromMemberGroup.memberStatusRef'],
        ], 'member-status', Input::post('member-status')));

        $template->sendNotification = new $GLOBALS['BE_FFL']['checkbox'](Widget::getAttributesFromDca([
            'options' => [1 => &$GLOBALS['TL_LANG']['tl_calendar_events_subscription']['newFromMemberGroup.sendNotification']],
        ], 'send-notification', Input::post('send-notification')));

        return $template;
    }

    /**
     * Process the form
     *
     * @param CalendarEventsModel $eventModel
     * @param array               $memberGroups
     */
    protected function processForm(CalendarEventsModel $eventModel, array $memberGroups)
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
            Message::addError($GLOBALS['TL_LANG']['tl_calendar_events_subscription']['newFromMemberGroup.noMembers']);
            Controller::reload();
        }

        $where = "";
        $values = [];

        switch (Input::post('member-status')) {
            case 'active':
                $time = Date::floorToMinute();
                $where = "login=? AND (start='' OR start<=?) AND (stop='' OR stop>?) AND disable=''";
                $values[] = 1;
                $values[] = $time;
                $values[] = $time + 60;
                break;
            case 'preActive':
                $time = Date::floorToMinute();
                $where = "login=? AND (stop='' OR stop>?) AND disable=''";
                $values[] = 1;
                $values[] = $time + 60;
                break;
        }

        $members = [];
        $memberRecords = Database::getInstance()
            ->prepare("SELECT * FROM tl_member WHERE id NOT IN (SELECT member FROM tl_calendar_events_subscription WHERE pid=?)" . ($where ? (" AND " . $where) : ''))
            ->execute(array_merge([$eventModel->id], $values))
        ;

        // Get only the members that belong to selected groups
        while ($memberRecords->next()) {
            if (count(array_intersect($memberGroupIds, StringUtil::deserialize($memberRecords->groups, true))) > 0) {
                $members[] = $memberRecords->row();
            }
        }

        // No recipients
        if (count($members) === 0) {
            Message::addInfo($GLOBALS['TL_LANG']['tl_calendar_events_subscription']['newFromMemberGroup.noMembers']);
            Controller::reload();
        }

        $count = 0;
        $factory = Services::getSubscriptionFactory();
        $subscriber = Services::getSubscriber();

        $eventConfig = new EventConfig($eventModel->getRelated('pid'), $eventModel);
        $eventConfig->setExtras(['notification' => (bool) Input::post('send-notification')]);

        // Create the subscriptions
        foreach ($members as $member) {
            $memberModel = new MemberModel();
            $memberModel->setRow($member);

            try {
                /** @var MemberSubscription $subscription */
                $subscription = $factory->create('member');
            } catch (\InvalidArgumentException $e) {
                continue;
            }

            $subscription->setMemberModel($memberModel);

            $subscriber->subscribe($eventConfig, $subscription);

            $count++;
        }

        Message::addConfirmation(sprintf($GLOBALS['TL_LANG']['tl_calendar_events_subscription']['newFromMemberGroup.confirmation'], $count));
        Controller::redirect(Backend::getReferer());
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
}
