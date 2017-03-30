<?php

namespace Codefog\EventsSubscriptions\Subscription;

use Codefog\EventsSubscriptions\EventConfig;
use Codefog\EventsSubscriptions\Model\SubscriptionModel;
use Haste\Form\Form;

interface SubscriptionInterface
{
    /**
     * Get the subscription model
     *
     * @return SubscriptionModel $model
     */
    public function getSubscriptionModel();

    /**
     * Set the subscription model
     *
     * @param SubscriptionModel $model
     */
    public function setSubscriptionModel(SubscriptionModel $model);

    /**
     * Return true if the user can subscribe
     *
     * @param EventConfig $event
     *
     * @return bool
     */
    public function canSubscribe(EventConfig $event);

    /**
     * Return true if the user can unsubscribe
     *
     * @param EventConfig $event
     *
     * @return bool
     */
    public function canUnsubscribe(EventConfig $event);

    /**
     * Return true if the user is subscribed
     *
     * @param EventConfig $event
     *
     * @return bool
     */
    public function isSubscribed(EventConfig $event);

    /**
     * Write the necessary data to model
     *
     * @param EventConfig       $event
     * @param SubscriptionModel $model
     */
    public function writeToModel(EventConfig $event, SubscriptionModel $model);

    /**
     * Get the subscribe/unsubscribe form
     *
     * @param EventConfig $event
     *
     * @return Form|null
     */
    public function getForm(EventConfig $event);

    /**
     * Process the subscribe/unsubscribe form
     *
     * @param Form        $form
     * @param EventConfig $event
     */
    public function processForm(Form $form, EventConfig $event);

    /**
     * Get the backend label
     *
     * @return string
     */
    public function getBackendLabel();

    /**
     * Get the frontend label
     *
     * @return string
     */
    public function getFrontendLabel();

    /**
     * Get the export columns
     *
     * @return array
     */
    public function getExportColumns();

    /**
     * Get the export row
     *
     * @return array
     */
    public function getExportRow();
}
