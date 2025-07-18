<?php

namespace Codefog\EventsSubscriptionsBundle\Subscription;

use Codefog\EventsSubscriptionsBundle\EventConfig;
use Codefog\EventsSubscriptionsBundle\Exception\RedirectException;
use Codefog\EventsSubscriptionsBundle\Model\SubscriptionModel;
use Codefog\EventsSubscriptionsBundle\Services;
use Codefog\EventsSubscriptionsBundle\Subscriber;
use Codefog\EventsSubscriptionsBundle\SubscriptionValidator;
use Codefog\HasteBundle\Form\Form;
use Contao\Database;
use Contao\Environment;
use Contao\Input;

abstract class AbstractSubscription implements FrontendDataInterface, ModuleDataAwareInterface, SubscriptionInterface
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
     * @var array
     */
    protected $moduleData = [];

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
     * @inheritDoc
     */
    public function setModuleData(array $moduleData)
    {
        $this->moduleData = $moduleData;
    }

    /**
     * @inheritDoc
     */
    public function getFrontendData()
    {
        if ($this->subscriptionModel === null) {
            return [];
        }

        return $this->subscriptionModel->row();
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

        $total = Database::getInstance()
            ->prepare('SELECT SUM(numberOfParticipants) AS total FROM tl_calendar_events_subscription WHERE pid=? AND dateCreated<? AND id!=?')
            ->execute($event->getEvent()->id, $subscriptionModel->dateCreated, $subscriptionModel->id)
            ->total
        ;

        return ($total + $subscriptionModel->numberOfParticipants) > $max;
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
        if (($form = $this->getForm($event)) === null || !$form->isSubmitted()) {
            return;
        }

        // Disable reminder if they are turned on and the user explicitly does not request them
        if ($event->hasReminders() && !$this->form->fetch('enableReminders')) {
            $model->disableReminders = 1;
        }

        // Set the number of participants
        if ($event->canProvideNumberOfParticipants()) {
            $model->numberOfParticipants = $form->fetch('numberOfParticipants');
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

        if ($event->canProvideNumberOfParticipants()) {
            $form->addFormField('numberOfParticipants', [
                'label' => &$GLOBALS['TL_LANG']['MSC']['events_subscriptions.numberOfParticipants'],
                'default' => 1,
                'inputType' => 'text',
                'eval' => ['mandatory' => true, 'rgxp' => 'natural', 'minval' => 1],
            ]);
        }

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
     * Validate the number of participants.
     *
     * @param Form $form
     * @param EventConfig $event
     *
     * @return bool
     */
    protected function validateNumberOfParticipants(Form $form, EventConfig $event)
    {
        if (!$event->canProvideNumberOfParticipants()) {
            return true;
        }

        $numberOfParticipants = (int) $form->fetch('numberOfParticipants');
        $subscriptionValidator = $this->getSubscriptionValidator();

        if (!$subscriptionValidator->validateNumberOfParticipantsLimit($event, $numberOfParticipants)) {
            $this->throwRedirectException(
                null,
                sprintf($GLOBALS['TL_LANG']['MSC']['events_subscriptions.numberOfParticipantsLimit'], $event->getNumberOfParticipantsLimit()),
                $event->getEvent()->id,
            );
        }

        if ($subscriptionValidator->validateMaximumSubscriptions($event, $numberOfParticipants, true)) {
            return true;
        }

        // If waiting list is enabled and subscription to it can be made, notify the user first
        if ($event->hasWaitingList() && $subscriptionValidator->validateMaximumSubscriptions($event, $numberOfParticipants)) {
            // Return true if the user confirmed the subscription
            if (isset($_POST['waitingList'])) {
                return true;
            }

            Services::getFlashMessage()->set(
                sprintf($GLOBALS['TL_LANG']['MSC']['events_subscriptions.numberOfParticipantsSubscribeToWaitingList'], $event->getMaximumSubscriptions()),
                $event->getEvent()->id
            );

            // Preserve the form data
            $formData = $form->fetchAll();

            // Add a hidden confirmation field
            $form->addFormField('waitingList', ['inputType' => 'hidden', 'value' => '1']);

            // Restore the form data as it is be reset after modifying the form fields
            foreach ($formData as $k => $v) {
                $form->getWidget($k)->value = $v;
            }

            return false;
        }

        // Subscription is not possible at all
        if ($event->hasWaitingList()) {
            $message = sprintf($GLOBALS['TL_LANG']['MSC']['events_subscriptions.numberOfParticipantsExceededWaitingList'], $event->getMaximumSubscriptions(), $event->getWaitingListLimit());
        } else {
            $message = sprintf($GLOBALS['TL_LANG']['MSC']['events_subscriptions.numberOfParticipantsExceeded'], $event->getMaximumSubscriptions());
        }

        $this->throwRedirectException(null, $message, $event->getEvent()->id);
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
