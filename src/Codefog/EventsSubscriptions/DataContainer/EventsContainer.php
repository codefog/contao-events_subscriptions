<?php

namespace Codefog\EventsSubscriptions\DataContainer;

use Codefog\EventsSubscriptions\EventConfig;
use Contao\Backend;
use Contao\DataContainer;
use Contao\Image;

class EventsContainer
{
    /**
     * Extend the palette if necessary
     *
     * @param DataContainer $dc
     */
    public function extendPalette(DataContainer $dc)
    {
        if (!$dc->id || !$this->isSubscriptionEnabled($dc->id)) {
            return;
        }

        $GLOBALS['TL_DCA']['tl_calendar_events']['palettes']['default'] = str_replace(
            'author;',
            'author;{subscription_legend:hide},subscription_override;',
            $GLOBALS['TL_DCA']['tl_calendar_events']['palettes']['default']
        );
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
     * Return true if the subscription is enabled
     *
     * @param int $id
     *
     * @return bool
     */
    private function isSubscriptionEnabled($id)
    {
        try {
            new EventConfig($id);
        } catch (\InvalidArgumentException $e) {
            return false;
        }

        return true;
    }
}
