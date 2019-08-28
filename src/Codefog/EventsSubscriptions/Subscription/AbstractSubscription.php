<?php

namespace Codefog\EventsSubscriptions\Subscription;

use Codefog\EventsSubscriptions\EventConfig;
use Codefog\EventsSubscriptions\Exception\RedirectException;
use Codefog\EventsSubscriptions\Model\SubscriptionModel;
use Codefog\EventsSubscriptions\Services;
use Codefog\EventsSubscriptions\Subscriber;
use Codefog\EventsSubscriptions\SubscriptionValidator;
use Contao\Environment;
use Contao\Input;
use Haste\Form\Form;

abstract class AbstractSubscription implements SubscriptionInterface
{
    /**
     * @var Form
     */
    protected $form;

    /**
     * @var SubscriptionModel
     */
    protected $subscriptionModel;

    /**
     * @return SubscriptionModel
     */
    public function getSubscriptionModel()
    {
        return $this->subscriptionModel;
    }

    /**
     * @inheritDoc
     */
    public function setSubscriptionModel(SubscriptionModel $model)
    {
        $this->subscriptionModel = $model;
    }

    /**
     * Get the subscriber
     *
     * @return Subscriber
     */
    protected function getSubscriber()
    {
        return Services::getSubscriber();
    }

    /**
     * Get the subscription validator
     *
     * @return SubscriptionValidator
     */
    protected function getSubscriptionValidator()
    {
        return Services::getSubscriptionValidator();
    }

    /**
     * @inheritDoc
     */
    public function getForm(EventConfig $event)
    {
        if ($this->form === null) {
            $this->form = $this->createForm($event);
        }

        // Tried to create the form but apparently it's not to be displayed
        if ($this->form === false) {
            return null;
        }

        return $this->form;
    }

    /**
     * @inheritDoc
     */
    public function isOnWaitingList()
    {
        if (($subscriptionModel = $this->getSubscriptionModel()) === null) {
            throw new \BadMethodCallException('The subscription model does not exist');
        }

        $event = Services::getEventConfigFactory()->create($subscriptionModel->getEvent()->id);

        if (!$event->hasWaitingList() || !($max = $event->getMaximumSubscriptions())) {
            return null;
        }

        $total = SubscriptionModel::countBy(
            ['pid=? AND dateCreated<? AND id!=?'],
            [$event->getEvent()->id, $subscriptionModel->dateCreated, $subscriptionModel->id]
        );

        return $total >= $max;
    }

    /**
     * @inheritDoc
     */
    public function setUnsubscribeCriteria(EventConfig $event, array &$columns, array &$values)
    {
        // Backwards compatibility
    }

    /**
     * @inheritDoc
     */
    public function writeToModel(EventConfig $event, SubscriptionModel $model)
    {
        if (($form = $this->getForm($event)) === null) {
            return;
        }

        // Disable reminder if they are turned on and the user explicitly does not request them
        if ($event->hasReminders() && !$this->form->fetch('enableReminders')) {
            $model->disableReminders = 1;
        }
    }

    /**
     * Create the basic form
     *
     * @param EventConfig $event
     *
     * @return Form|null
     */
    protected function createBasicForm(EventConfig $event)
    {
        $form = new Form(
            sprintf(
                'event-subscription-%s-%s',
                Services::getSubscriptionFactory()->getType(get_called_class()),
                $event->getEvent()->id
            ),
            'POST',
            function ($haste) {
                return Input::post('FORM_SUBMIT') === $haste->getFormId();
            }
        );

        $form->addContaoHiddenFields();

        if ($event->hasReminders()) {
            $form->addFormField('enableReminders', [
                'inputType' => 'checkbox',
                'default' => 1,
                'options' => [1 => &$GLOBALS['TL_LANG']['MSC']['events_subscriptions.enableReminders']],
            ]);
        }

        return $form;
    }

    /**
     * Throw the redirect exception
     *
     * @param string $page
     * @param string $message
     * @param int    $eventId
     *
     * @throws RedirectException
     */
    protected function throwRedirectException($page = null, $message = null, $eventId = null)
    {
        $exception = new RedirectException($message);
        $exception->setPage($page);
        $exception->setEventId($eventId);

        throw $exception;
    }

    /**
     * Generate the unsubscribe link
     *
     * @return null|string
     */
    protected function generateUnsubscribeLink()
    {
        if (!$this->subscriptionModel->unsubscribeToken) {
            return null;
        }

        return Environment::get('base') . '?event_unsubscribe=' . $this->subscriptionModel->unsubscribeToken;
    }

    /**
     * Create the form
     *
     * @param EventConfig $event
     *
     * @return Form|bool
     */
    abstract protected function createForm(EventConfig $event);
}
