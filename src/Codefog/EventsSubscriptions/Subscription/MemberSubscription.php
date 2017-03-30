<?php

namespace Codefog\EventsSubscriptions\Subscription;

use Codefog\EventsSubscriptions\EventConfig;
use Codefog\EventsSubscriptions\MemberConfig;
use Codefog\EventsSubscriptions\Model\SubscriptionModel;
use Contao\FrontendUser;
use Contao\MemberModel;
use Haste\Form\Form;
use Haste\Util\Format;

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
            } elseif (FE_USER_LOGGED_IN) {
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
    public function getBackendLabel()
    {
        if (($member = $this->getMemberModel()) === null) {
            return '';
        }

        return sprintf(
            '%s %s <span style="padding-left:3px;color:#b3b3b3;">[%s – %s]</span>',
            $member->firstname,
            $member->lastname,
            $member->username,
            $member->email
        );
    }

    /**
     * @inheritDoc
     */
    public function getFrontendLabel()
    {
        if (($member = $this->getMemberModel()) === null) {
            return '';
        }

        return sprintf('%s %s', $member->firstname, $member->lastname);
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

        $tokens = [];

        foreach ($member->row() as $k => $v) {
            $tokens['subscription_'.$k] = Format::dcaValue($member::getTable(), $k, $v);
        }

        return $tokens;
    }
}
