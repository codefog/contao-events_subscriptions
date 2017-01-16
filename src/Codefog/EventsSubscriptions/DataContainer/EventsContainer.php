<?php

namespace Codefog\EventsSubscriptions\DataContainer;

use Contao\Database;
use Contao\DataContainer;

class EventsContainer
{
    /**
     * Extend the palette if necessary
     *
     * @param DataContainer $dc
     */
    public function extendPalette(DataContainer $dc)
    {
        if (!$dc->id) {
            return;
        }

        $parent = Database::getInstance()->prepare(
            "SELECT subscription_reminders FROM tl_calendar WHERE id=(SELECT pid FROM tl_calendar_events WHERE id=?)"
        )
            ->limit(1)
            ->execute($dc->id);

        if (!$parent->subscription_reminders) {
            return;
        }

        $GLOBALS['TL_DCA']['tl_calendar_events']['palettes']['default'] = str_replace(
            'author;',
            'author;{subscription_legend:hide},subscription_maximum;',
            $GLOBALS['TL_DCA']['tl_calendar_events']['palettes']['default']
        );
    }
}
