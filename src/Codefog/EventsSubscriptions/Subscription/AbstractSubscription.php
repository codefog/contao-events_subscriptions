<?php

namespace Codefog\EventsSubscriptions\Subscription;

use Codefog\EventsSubscriptions\EventConfig;
use Codefog\EventsSubscriptions\Exception\RedirectException;
use Codefog\EventsSubscriptions\Model\SubscriptionModel;
use Codefog\EventsSubscriptions\Services;
use Codefog\EventsSubscriptions\Subscriber;
use Codefog\EventsSubscriptions\SubscriptionValidator;
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
     * Create the form
     *
     * @param EventConfig $event
     *
     * @return Form|bool
     */
    abstract protected function createForm(EventConfig $event);
}
