<?php

namespace Codefog\EventsSubscriptions\EventListener;

use Contao\Config;
use Contao\Database;
use Contao\Date;
use Contao\System;

class CronListener
{
    /**
     * Members cache
     * @var array
     */
    private static $arrMembers = [];

    /**
     * Execute the tasks on ohurly job
     */
    public function onHourlyJob()
    {
        $this->sendEmailReminders();
    }

    /**
     * Send the e-mail reminders
     */
    private function sendEmailReminders()
    {
        $db           = Database::getInstance();
        $now          = mktime(date('H'), 0, 0, 1, 1, 1970);
        $objCalendars = $db->execute(
            "SELECT * FROM tl_calendar WHERE subscription_reminders=1 AND ((subscription_time >= $now) AND (subscription_time <= $now + 3600))"
        );

        // Return if there are no calendars with subscriptions
        if (!$objCalendars->numRows) {
            return;
        }

        $intReminders = 0;
        $objToday     = new \Date();

        // Send the e-mails
        while ($objCalendars->next()) {
            $arrDays = array_map('intval', trimsplit(',', $objCalendars->subscription_days));

            if (empty($arrDays)) {
                continue;
            }

            $arrWhere = array();

            // Bulid a WHERE statement
            foreach ($arrDays as $intDay) {
                $objDateEvent = new \Date(strtotime('+'.$intDay.' days'));
                $arrWhere[]   = "((e.startTime BETWEEN ".$objDateEvent->dayBegin." AND ".$objDateEvent->dayEnd.") AND ((es.lastEmail = 0) OR (es.lastEmail NOT BETWEEN ".$objToday->dayBegin." AND ".$objToday->dayEnd.")))";
            }

            $objSubscriptions = $db->prepare(
                "SELECT e.*, es.member FROM tl_calendar_events_subscriptions es JOIN tl_calendar_events e ON e.id=es.pid WHERE e.pid=?".(!empty($arrWhere) ? " AND (".implode(
                        " OR ",
                        $arrWhere
                    ).")" : "")
            )
                ->execute($objCalendars->id);

            // Continue if there are no subscriptions to send
            if (!$objSubscriptions->numRows) {
                continue;
            }

            $arrWildcards = $this->generateWildcards($objCalendars->row(), 'calendar');

            while ($objSubscriptions->next()) {
                // Get the member if it is not in cache
                if (!isset(self::$arrMembers[$objSubscriptions->member])) {
                    $objMember = $db->prepare("SELECT * FROM tl_member WHERE id=? AND email!=''")
                        ->limit(1)
                        ->execute($objSubscriptions->member);

                    // Continue if member was not found
                    if (!$objMember->numRows) {
                        continue;
                    }

                    self::$arrMembers[$objSubscriptions->member] = $objMember->row();
                }

                $arrWildcards = array_merge(
                    $arrWildcards,
                    $this->generateWildcards($objSubscriptions->row(), 'event'),
                    $this->generateWildcards(self::$arrMembers[$objSubscriptions->member], 'member')
                );

                // Generate an e-mail
                $objEmail = new \Email();

                $objEmail->from     = $GLOBALS['TL_ADMIN_EMAIL'];
                $objEmail->fromName = $GLOBALS['TL_ADMIN_NAME'];
                $objEmail->subject  = \String::parseSimpleTokens($objCalendars->subscription_subject, $arrWildcards);
                $objEmail->text     = \String::parseSimpleTokens($objCalendars->subscription_message, $arrWildcards);

                // Send an e-mail
                if ($objEmail->sendTo(self::$arrMembers[$objSubscriptions->member]['email'])) {
                    $intReminders++;
                    $db->prepare(
                        "UPDATE tl_calendar_events_subscriptions SET lastEmail=? WHERE pid=? AND member=?"
                    )->execute(time(), $objSubscriptions->id, $objSubscriptions->member);
                }
            }
        }

        System::log(
            'A total number of '.$intReminders.' event reminders have been sent',
            'EventsSubscriptions sendEmailReminders()',
            TL_INFO
        );
    }

    /**
     * Generate the wildcards and return them as array
     *
     * @param array
     * @param string
     *
     * @return array
     */
    protected function generateWildcards($arrData, $strPrefix = '')
    {
        if (!is_array($arrData)) {
            return array();
        }

        $arrReturn = $arrData;

        foreach ($arrData as $k => $v) {
            switch ($k) {
                case 'startTime':
                case 'endTime':
                    $v = Date::parse(Config::get('timeFormat'), $v);
                    break;

                case 'startDate':
                case 'endDate':
                    $v = Date::parse(Config::get('dateFormat'), $v);
                    break;
            }

            // Add prefix, if any
            if ($strPrefix != '') {
                unset($arrReturn[$k]);
                $k = $strPrefix.'_'.$k;
            }

            $arrReturn[$k] = $v;
        }

        return $arrReturn;
    }
}
