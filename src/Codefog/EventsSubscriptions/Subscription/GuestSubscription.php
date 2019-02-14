<?php

namespace Codefog\EventsSubscriptions\Subscription;

use Codefog\EventsSubscriptions\EventConfig;
use Codefog\EventsSubscriptions\Model\SubscriptionModel;
use Haste\Form\Form;
use Haste\Util\ArrayPosition;

class GuestSubscription extends AbstractSubscription implements ExportAwareInterface, NotificationAwareInterface
{
    /**
     * @inheritDoc
     */
    public function canSubscribe(EventConfig $event)
    {
        return $this->getSubscriptionValidator()->canSubscribe($event);
    }

    /**
     * @inheritDoc
     */
    public function canUnsubscribe(EventConfig $event)
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function isSubscribed(EventConfig $event)
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function writeToModel(EventConfig $event, SubscriptionModel $model)
    {
        if (($form = $this->getForm($event)) === null) {
            return;
        }

        parent::writeToModel($event, $model);

        $model->firstname = $form->fetch('firstname');
        $model->lastname  = $form->fetch('lastname');
        $model->email     = $form->fetch('email');
    }

    /**
     * @inheritDoc
     */
    public function createForm(EventConfig $event)
    {
        if (FE_USER_LOGGED_IN || !$this->canSubscribe($event)) {
            return false;
        }

        $form = $this->createBasicForm($event)
            ->addFormField(
                'firstname',
                [
                    'label'     => &$GLOBALS['TL_LANG']['MSC']['events_subscriptions.guestForm.firstname'],
                    'inputType' => 'text',
                    'eval'      => ['mandatory' => true],
                ],
                ArrayPosition::before('enableReminders')
            )->addFormField(
                'lastname',
                [
                    'label'     => &$GLOBALS['TL_LANG']['MSC']['events_subscriptions.guestForm.lastname'],
                    'inputType' => 'text',
                    'eval'      => ['mandatory' => true],
                ],
                ArrayPosition::before('enableReminders')
            )->addFormField(
                'email',
                [
                    'label'     => &$GLOBALS['TL_LANG']['MSC']['events_subscriptions.guestForm.email'],
                    'inputType' => 'text',
                    'eval'      => ['mandatory' => true, 'rgxp' => 'email'],
                ],
                ArrayPosition::before('enableReminders')
            );

        return $form;
    }

    /**
     * @inheritDoc
     */
    public function processForm(Form $form, EventConfig $event)
    {
        if ($this->canSubscribe($event)) {
            $this->getSubscriber()->subscribe($event, $this);
            $this->throwRedirectException(
                'subscribe',
                $GLOBALS['TL_LANG']['MSC']['events_subscriptions.subscribeConfirmation'],
                $event->getEvent()->id
            );
        }

        $this->throwRedirectException();
    }

    /**
     * @inheritDoc
     */
    public function setUnsubscribeCriteria(EventConfig $event, array &$columns, array &$values)
    {
        $columns[] = 'unsubscribeToken=?';
        $values[] = $this->subscriptionModel->unsubscribeToken;
    }

    /**
     * @inheritDoc
     */
    public function getExportColumns()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getExportRow()
    {
        return [
            'subscription_firstname' => $this->subscriptionModel->firstname,
            'subscription_lastname'  => $this->subscriptionModel->lastname,
            'subscription_email'     => $this->subscriptionModel->email,
        ];
    }

    /**
     * @inheritDoc
     */
    public function getBackendLabel()
    {
        $label = sprintf(
            '%s %s <span style="padding-left:3px;color:#b3b3b3;">[%s]</span>',
            $this->subscriptionModel->firstname,
            $this->subscriptionModel->lastname,
            $this->subscriptionModel->email
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
        return sprintf('%s %s', $this->subscriptionModel->firstname, $this->subscriptionModel->lastname);
    }

    /**
     * @inheritDoc
     */
    public function getNotificationEmail()
    {
        return $this->subscriptionModel->email;
    }

    /**
     * @inheritDoc
     */
    public function getNotificationTokens()
    {
        return [
            'subscription_firstname' => $this->subscriptionModel->firstname,
            'subscription_lastname'  => $this->subscriptionModel->lastname,
            'subscription_email'     => $this->subscriptionModel->email,
            'unsubscribe_link'       => $this->generateUnsubscribeLink(),
        ];
    }
}
