<?php

namespace Codefog\EventsSubscriptions\EventListener;

use Contao\Database;
use Contao\DataContainer;
use Contao\StringUtil;

class MemberListener
{
    /**
     * Delete all related event subscriptions.
     */
    public function onDeleteCallback(DataContainer $dc, $undoId)
    {
        $db = Database::getInstance();
        $subscriptions = $db
            ->prepare('SELECT * FROM tl_calendar_events_subscription WHERE type=? AND member=?')
            ->execute('member', $dc->id)
        ;

        // Store the subscriptions in the tl_undo, so they can be restored with member
        if ($subscriptions->numRows) {
            $undo = $db
                ->prepare('SELECT data FROM tl_undo WHERE id=?')
                ->limit(1)
                ->execute($undoId)
            ;

            if ($undo->numRows) {
                $undoData = StringUtil::deserialize($undo->data);

                if (is_array($undoData)) {
                    $undoData['tl_calendar_events_subscription'] = $subscriptions->fetchAllAssoc();

                    $db
                        ->prepare('UPDATE tl_undo SET data=? WHERE id=?')
                        ->execute(serialize($undoData), $undoId)
                    ;
                }
            }
        }

        $db
            ->prepare('DELETE FROM tl_calendar_events_subscription WHERE type=? AND member=?')
            ->execute('member', $dc->id)
        ;
    }
}
