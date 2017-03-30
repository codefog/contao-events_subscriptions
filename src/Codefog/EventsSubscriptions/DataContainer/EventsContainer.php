<?php

/**
 * events_subscriptions extension for Contao Open Source CMS
 *
 * @copyright Copyright (c) 2011-2017, Codefog
 * @author    Codefog <https://codefog.pl>
 * @license   http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link      http://github.com/codefog/contao-events_subscriptions
 */

namespace Codefog\EventsSubscriptions\DataContainer;

use Codefog\EventsSubscriptions\EventConfig;
use Codefog\EventsSubscriptions\Services;
use Contao\Backend;
use Contao\DataContainer;
use Contao\Image;
use Haste\Dca\PaletteManipulator;

class EventsContainer
{
    /**
     * Extend the palette if necessary
     *
     * @param DataContainer $dc
     */
    public function extendPalette(DataContainer $dc)
    {
        if (!$dc->id || CURRENT_ID === $dc->id) {
            return;
        }

        // Return if the subscription is not enabled
        if (!$this->isSubscriptionEnabled($dc->id)) {
            return;
        }

        PaletteManipulator::create()
            ->addLegend('subscription_legend', 'title_legend', \Haste\Dca\PaletteManipulator::POSITION_AFTER, true)
            ->addField('subscription_override', 'subscription_legend', \Haste\Dca\PaletteManipulator::POSITION_APPEND)
            ->applyToPalette('default', 'tl_calendar_events');
    }

    /**
     * Get the "subscriptions" button
     *
     * @param array  $row
     * @param string $href
     * @param string $label
     * @param string $title
     * @param string $icon
     * @param string $attributes
     *
     * @return string
     */
    public function getSubscriptionsButton(array $row, $href, $label, $title, $icon, $attributes)
    {
        if (!$this->isSubscriptionEnabled($row['id'])) {
            return '';
        }

        return sprintf(
            '<a href="%s" title="%s"%s>%s</a> ',
            Backend::addToUrl($href.'&amp;id='.$row['id']),
            specialchars($title),
            $attributes,
            Image::getHtml($icon, $label)
        );
    }

    /**
     * Get the types
     *
     * @return array
     */
    public function getTypes()
    {
        return Services::getSubscriptionFactory()->getAll();
    }

    /**
     * Return true if the subscription is enabled
     *
     * @param int $id
     *
     * @return bool
     */
    private function isSubscriptionEnabled($id)
    {
        $config = EventConfig::create($id);

        return $config->canSubscribe();
    }
}
