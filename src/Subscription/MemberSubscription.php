<?php

namespace Codefog\EventsSubscriptionsBundle\Subscription;

use Codefog\EventsSubscriptionsBundle\EventConfig;
use Codefog\EventsSubscriptionsBundle\MemberConfig;
use Codefog\EventsSubscriptionsBundle\Model\SubscriptionModel;
use Codefog\HasteBundle\Form\Form;
use Codefog\HasteBundle\Formatter;
use Contao\FrontendUser;
use Contao\MemberModel;
use Contao\System;

class MemberSubscription extends AbstractSubscription implements ExportAwareInterface, NotificationAwareInterface
{
    /**
     * @var MemberModel
     */
    private $memberModel;

    /**
     * Set the member model
     *
     * @param MemberModel $memberModel
     */
    public function setMemberModel($memberModel)
    {
        $this->memberModel = $memberModel;
    }

    /**
     * Get the member model
     *
     * @return MemberModel|null
     */
    protected function getMemberModel()
    {
        if ($this->memberModel === null) {
            // Try to set from subscription model
            if ($this->subscriptionModel !== null) {
                $this->memberModel = $this->subscriptionModel->getMember();
            } elseif (System::getContainer()->get('contao.security.token_checker')->hasFrontendUser()) {
                // Try to get the currently logged in user
                $this->memberModel = MemberModel::findByPk(FrontendUser::getInstance()->id);
            }
        }

        return $this->memberModel;
    }

    /**
     * @inheritDoc
     */
    public function canSubscribe(EventConfig $event)
    {
        if (($memberModel = $this->getMemberModel()) === null) {
            return false;
        }

        $validator = $this->getSubscriptionValidator();

        if (!$validator->canSubscribe($event)) {
            return false;
        }

        $member = MemberConfig::create($memberModel->id);

        if ($validator->isMemberSubscribed($event, $member)
            || !$validator->validateMemberGroups($event, $member)
            || !$validator->validateMemberTotalLimit($member)
            || !$validator->validateMemberPeriodLimit($event, $member)
        ) {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function canUnsubscribe(EventConfig $event)
    {
        if (($memberModel = $this->getMemberModel()) === null) {
            return false;
        }

        $validator = $this->getSubscriptionValidator();

        if (!$validator->canUnsubscribe($event)) {
            return false;
        }

        $member = MemberConfig::create($memberModel->id);

        if (!$validator->isMemberSubscribed($event, $member)) {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function isSubscribed(EventConfig $event)
    {
        if (($memberModel = $this->getMemberModel()) === null) {
            return false;
        }

        return $this->getSubscriptionValidator()->isMemberSubscribed($event, MemberConfig::create($memberModel->id));
    }

    /**
     * @inheritDoc
     */
    public function writeToModel(EventConfig $event, SubscriptionModel $model)
    {
        if (($memberModel = $this->getMemberModel()) === null) {
            throw new \RuntimeException('The member model cannot be null');
        }

        parent::writeToModel($event, $model);

        $model->member = $memberModel->id;
    }

    /**
     * @inheritDoc
     */
    public function createForm(EventConfig $event)
    {
        if (!$this->canSubscribe($event) && !$this->canUnsubscribe($event)) {
            return false;
        }

        return $this->createBasicForm($event);
    }

    /**
     * @inheritDoc
     */
    public function processForm(Form $form, EventConfig $event)
    {
        $eventModel = $event->getEvent();

        // Subscribe user
        if ($this->canSubscribe($event)) {
            // Validate the number of participants
            if (!$this->validateNumberOfParticipants($form, $event)) {
                return;
            }

            $this->getSubscriber()->subscribe($event, $this);
            $this->throwRedirectException(
                'subscribe',
                $GLOBALS['TL_LANG']['MSC']['events_subscriptions.subscribeConfirmation'],
                $eventModel->id
            );
        }

        // Unsubscribe user
        if ($this->canUnsubscribe($event)) {
            $this->getSubscriber()->unsubscribe($event, $this);
            $this->throwRedirectException(
                'unsubscribe',
                $GLOBALS['TL_LANG']['MSC']['events_subscriptions.unsubscribeConfirmation'],
                $eventModel->id
            );
        }

        $this->throwRedirectException();
    }

    /**
     * @inheritDoc
     */
    public function setUnsubscribeCriteria(EventConfig $event, array &$columns, array &$values)
    {
        if (($member = $this->getMemberModel()) === null) {
            throw new \RuntimeException('There is no member model available');
        }

        $columns[] = 'member=?';
        $values[] = $member->id;
    }

    /**
     * @inheritDoc
     */
    public function getBackendLabel()
    {
        if (($member = $this->getMemberModel()) === null) {
            return '';
        }

        $label = sprintf(
            '%s %s <span style="padding-left:3px;color:#b3b3b3;">[%s – %s]</span> <span style="padding-left:3px;color:#b3b3b3;">[%s: %s]</span>',
            $member->firstname,
            $member->lastname,
            $member->username,
            $member->email,
            $GLOBALS['TL_LANG']['MSC']['events_subscriptions.numberOfParticipants'],
            $this->subscriptionModel->numberOfParticipants
        );

        if ($this->isOnWaitingList()) {
            $label = sprintf(
                '%s <strong class="tl_red">[%s]</strong>',
                $label,
                $GLOBALS['TL_LANG']['MSC']['events_subscriptions.onWaitingList']
            );
        }

        return $label;
    }

    /**
     * @inheritDoc
     */
    public function getFrontendLabel()
    {
        if (($member = $this->getMemberModel()) === null) {
            return '';
        }

        $label = sprintf('%s %s', $member->firstname, $member->lastname);

        if (isset($this->moduleData['cal_showParticipants']) && $this->moduleData['cal_showParticipants']) {
            $label = sprintf('%s (%s)', $label, $this->subscriptionModel->numberOfParticipants);
        }

        return $label;
    }

    /**
     * @inheritDoc
     */
    public function getExportColumns()
    {
        $headerFields = $GLOBALS['TL_LANG']['MSC']['events_subscriptions.memberExportHeaderFields'];

        return [
            'member_id'       => $headerFields['member_id'],
            'member_username' => $headerFields['member_username'],
        ];
    }

    /**
     * @inheritDoc
     */
    public function getExportRow()
    {
        if (($member = $this->getMemberModel()) === null) {
            return [];
        }

        return [
            'subscription_firstname' => $member->firstname,
            'subscription_lastname'  => $member->lastname,
            'subscription_email'     => $member->email,
            'subscription_numberOfParticipants' => $this->subscriptionModel->numberOfParticipants,
            'member_id'              => $member->id,
            'member_username'        => $member->username,
        ];
    }

    /**
     * @inheritDoc
     */
    public function getNotificationEmail()
    {
        if (($member = $this->getMemberModel()) === null) {
            return '';
        }

        return $member->email;
    }

    /**
     * @inheritDoc
     */
    public function getNotificationTokens()
    {
        if (($member = $this->getMemberModel()) === null) {
            return [];
        }

        $tokens = [
            'unsubscribe_link' => $this->generateUnsubscribeLink(),
        ];

        foreach ($member->row() as $k => $v) {
            $tokens['subscription_'.$k] = System::getContainer()->get(Formatter::class)->dcaValue($member::getTable(), $k, $v);
        }

        $tokens['subscription_numberOfParticipants'] = $this->subscriptionModel->numberOfParticipants;

        return $tokens;
    }
}
