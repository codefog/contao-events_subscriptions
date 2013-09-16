<?php

/**
 * events_subscriptions extension for Contao Open Source CMS
 *
 * Copyright (C) 2013 Codefog Ltd
 *
 * @package events_subscriptions
 * @author  Codefog Ltd <http://codefog.pl>
 * @author  Kamil Kuzminski <kamil.kuzminski@codefog.pl>
 * @license LGPL
 */

namespace EventsSubscriptions;


/**
 * Provide methods to handle event subscriptions
 */
class EventsSubscriptions extends \Frontend
{

	/**
	 * Members cache
	 * @var array
	 */
	private static $arrMembers = array();


	/**
	 * Subscribe the member and return true on success, false otherwise
	 * @param integer
	 * @param integer
	 * @return boolean
	 */
	public function subscribeMember($intEvent, $intMember)
	{
		if ($this->checkSubscription($intMember, $intMember))
		{
			$insertId = $this->Database->prepare("INSERT INTO tl_calendar_events_subscriptions (tstamp, pid, member) VALUES (?, ?, ?)")
									   ->execute(time(), $intEvent, $intMember)
									   ->insertId;

			if ($insertId)
			{
				return true;
			}
		}

		return false;
	}


	/**
	 * Unsubscribe the member and return true on success, false otherwise
	 * @param integer
	 * @param integer
	 * @return boolean
	 */
	public function unsubscribeMember($intEvent, $intMember)
	{
		if (!$this->checkSubscription($intEvent, $intMember))
		{
			$affectedRows = $this->Database->prepare("DELETE FROM tl_calendar_events_subscriptions WHERE pid=? AND member=?")
										   ->execute($intEvent, $intMember)
										   ->affectedRows;

			if ($affectedRows)
			{
				return true;
			}
		}

		return false;
	}


	/**
	 * Return false if the member is already subscribed, true otherwise
	 * @param integer
	 * @param integer
	 * @return boolean
	 */
	public function checkSubscription($intEvent, $intMember)
	{
		$objSubscription = $this->Database->prepare("SELECT id FROM tl_calendar_events_subscriptions WHERE pid=? AND member=?")
										  ->limit(1)
										  ->execute($intEvent, $intMember);

		return $objSubscription->numRows ? false : true;
	}


	/**
	 * Send the e-mail reminders
	 */
	public function sendEmailReminders()
	{
		$now = mktime(date('H'), 0, 0, 1, 1, 1970);
		$objCalendars = $this->Database->execute("SELECT * FROM tl_calendar WHERE subscription_reminders=1 AND ((subscription_time >= $now) AND (subscription_time <= $now + 3600))");

		// Return if there are no calendars with subscriptions
		if (!$objCalendars->numRows)
		{
			return;
		}

		$intReminders = 0;
		$objToday = new \Date();

		// Send the e-mails
		while ($objCalendars->next())
		{
			$arrDays = array_map('intval', trimsplit(',', $objCalendars->subscription_days));

			if (empty($arrDays))
			{
				continue;
			}

			$arrWhere = array();

			// Bulid a WHERE statement
			foreach ($arrDays as $intDay)
			{
				$objDateEvent = new \Date(strtotime('+'.$intDay.' days'));
				$arrWhere[] = "((e.startTime BETWEEN " . $objDateEvent->dayBegin . " AND " . $objDateEvent->dayEnd . ") AND ((es.lastEmail = 0) OR (es.lastEmail NOT BETWEEN " . $objToday->dayBegin . " AND " . $objToday->dayEnd . ")))";
			}

			$objSubscriptions = $this->Database->prepare("SELECT e.*, es.member FROM tl_calendar_events_subscriptions es JOIN tl_calendar_events e ON e.id=es.pid WHERE e.pid=?" . (!empty($arrWhere) ? " AND (".implode(" OR ", $arrWhere).")" : ""))
											   ->execute($objCalendars->id);

			// Continue if there are no subscriptions to send
			if (!$objSubscriptions->numRows)
			{
				continue;
			}

			$arrWildcards = $this->generateWildcards($objCalendars->row(), 'calendar');

			while ($objSubscriptions->next())
			{
				// Get the member if it is not in cache
				if (!isset(self::$arrMembers[$objSubscriptions->member]))
				{
					$objMember = $this->Database->prepare("SELECT * FROM tl_member WHERE id=? AND email!=''")
												->limit(1)
												->execute($objSubscriptions->member);

					// Continue if member was not found
					if (!$objMember->numRows)
					{
						continue;
					}

					self::$arrMembers[$objSubscriptions->member] = $objMember->row();
				}

				$arrWildcards = array_merge($arrWildcards, $this->generateWildcards($objSubscriptions->row(), 'event'), $this->generateWildcards(self::$arrMembers[$objSubscriptions->member], 'member'));

				// Generate an e-mail
				$objEmail = new \Email();

				$objEmail->from = $GLOBALS['TL_ADMIN_EMAIL'];
				$objEmail->fromName = $GLOBALS['TL_ADMIN_NAME'];
				$objEmail->subject = \String::parseSimpleTokens($objCalendars->subscription_title, $arrWildcards);
				$objEmail->text = \String::parseSimpleTokens($objCalendars->subscription_message, $arrWildcards);

				// Send an e-mail
				if ($objEmail->sendTo(self::$arrMembers[$objSubscriptions->member]['email']))
				{
					$intReminders++;
					$this->Database->prepare("UPDATE tl_calendar_events_subscriptions SET lastEmail=? WHERE pid=? AND member=?")->execute(time(), $objSubscriptions->id, $objSubscriptions->member);
				}
			}
		}

		self::log('A total number of ' . $intReminders . ' event reminders have been sent', 'EventsSubscriptions sendEmailReminders()', TL_INFO);
	}


	/**
	 * Generate the wildcards and return them as array
	 * @param array
	 * @param string
	 * @return array
	 */
	protected function generateWildcards($arrData, $strPrefix='')
	{
		if (!is_array($arrData))
		{
			return array();
		}

		$arrReturn = $arrData;

		foreach ($arrData as $k=>$v)
		{
			switch ($k)
			{
				case 'startTime':
				case 'endTime':
					$v = $this->parseDate($GLOBALS['TL_CONFIG']['timeFormat'], $v);
					break;

				case 'startDate':
				case 'endDate':
					$v = $this->parseDate($GLOBALS['TL_CONFIG']['dateFormat'], $v);
					break;
			}

			// Add prefix, if any
			if ($strPrefix != '')
			{
				unset($arrReturn[$k]);
				$k = $strPrefix . '.' . $k;
			}

			$arrReturn[$k] = $v;
		}

		return $arrReturn;
	}
}
