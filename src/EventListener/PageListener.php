<?php

/**
 * events_subscriptions extension for Contao Open Source CMS
 *
 * @copyright Copyright (c) 2011-2017, Codefog
 * @author    Codefog <https://codefog.pl>
 * @license   http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link      http://github.com/codefog/contao-events_subscriptions
 */

namespace Codefog\EventsSubscriptionsBundle\EventListener;

use Codefog\EventsSubscriptionsBundle\Model\SubscriptionModel;
use Codefog\EventsSubscriptionsBundle\Services;
use Codefog\HasteBundle\UrlParser;
use Contao\Controller;
use Contao\Input;
use Contao\PageModel;
use Contao\System;

class PageListener
{
    /**
     * On generate the page
     */
    public function onGeneratePage()
    {
        if ($token = Input::get('event_unsubscribe')) {
            $this->unsubscribe($token);
        }
    }

    /**
     * Unsubscribe the user
     *
     * @param string $token
     */
    private function unsubscribe($token)
    {
        if (($subscriptionModel = SubscriptionModel::findOneBy('unsubscribeToken', $token)) !== null) {
            $config = Services::getEventConfigFactory()->create($subscriptionModel->pid);
            $calendar = $config->getCalendar();
            $subscription = Services::getSubscriptionFactory()->createFromModel($subscriptionModel);

            // Unsubscribe
            Services::getSubscriber()->unsubscribe($config, $subscription);

            // Redirect if we have a special page for that
            if ($calendar->subscription_unsubscribeLinkPage && ($page = PageModel::findPublishedById($calendar->subscription_unsubscribeLinkPage)) !== null) {
                Controller::redirect($page->getFrontendUrl());
            }
        }

        // Simply reload the page without token parameter
        Controller::redirect(System::getContainer()->get(UrlParser::class)->removeQueryString(['event_unsubscribe']));
    }
}
