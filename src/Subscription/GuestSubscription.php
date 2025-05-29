<?php

namespace Codefog\EventsSubscriptionsBundle\Subscription;

use Codefog\EventsSubscriptionsBundle\EventConfig;
use Codefog\EventsSubscriptionsBundle\Model\SubscriptionModel;
use Codefog\HasteBundle\Form\Form;
use Codefog\HasteBundle\Util\ArrayPosition;
use Contao\System;

class GuestSubscription extends AbstractSubscription implements ExportAwareInterface,  NotificationAwareInterface
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
        if (System::getContainer()->get('contao.security.token_checker')->hasFrontendUser() || !$this->canSubscribe($event)) {
            return false;
        }

        $form = $this->createBasicForm($event);
        $position = null;

        // Determine the fields position
        if ($form->hasFormField('enableReminders')) {
            $position = ArrayPosition::before('enableReminders');
        }

        // Determine the fields position
        if ($form->hasFormField('numberOfParticipants')) {
            $position = ArrayPosition::before('numberOfParticipants');
        }

        $form
            ->addFormField(
                'firstname',
                [
                    'label'     => &$GLOBALS['TL_LANG']['MSC']['events_subscriptions.guestForm.firstname'],
                    'inputType' => 'text',
                    'eval'      => ['mandatory' => true],
                ],
                $position
            )->addFormField(
                'lastname',
                [
                    'label'     => &$GLOBALS['TL_LANG']['MSC']['events_subscriptions.guestForm.lastname'],
                    'inputType' => 'text',
                    'eval'      => ['mandatory' => true],
                ],
                $position
            )->addFormField(
                'email',
                [
                    'label'     => &$GLOBALS['TL_LANG']['MSC']['events_subscriptions.guestForm.email'],
                    'inputType' => 'text',
                    'eval'      => ['mandatory' => true, 'rgxp' => 'email'],
                ],
                $position
            )->addFormField(
                'captcha_' . $event->getEvent()->id,
                [
                    'label'     => &$GLOBALS['TL_LANG']['MSC']['securityQuestion'],
                    'inputType' => 'captcha',
                    'eval'      => ['mandatory' => true],
                ]
            );

        return $form;
    }

    /**
     * @inheritDoc
     */
    public function processForm(Form $form, EventConfig $event)
    {
        if ($this->canSubscribe($event)) {
            // Validate the number of participants
            if (!$this->validateNumberOfParticipants($form, $event)) {
                return;
            }

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
            'subscription_numberOfParticipants' => $this->subscriptionModel->numberOfParticipants,
        ];
    }

    /**
     * @inheritDoc
     */
    public function getBackendLabel()
    {
        $label = sprintf(
            '%s %s <span style="padding-left:3px;color:#b3b3b3;">[%s]</span> <span style="padding-left:3px;color:#b3b3b3;">[%s: %s]</span>',
            $this->subscriptionModel->firstname,
            $this->subscriptionModel->lastname,
            $this->subscriptionModel->email,
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
        $label = sprintf('%s %s', $this->subscriptionModel->firstname, $this->subscriptionModel->lastname);

        if (isset($this->moduleData['cal_showParticipants']) && $this->moduleData['cal_showParticipants']) {
            $label = sprintf('%s (%s)', $label, $this->subscriptionModel->numberOfParticipants);
        }

        return $label;
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
            'subscription_numberOfParticipants' => $this->subscriptionModel->numberOfParticipants,
            'unsubscribe_link'       => $this->generateUnsubscribeLink(),
        ];
    }
}
