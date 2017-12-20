<?php

/**
 * events_subscriptions extension for Contao Open Source CMS
 *
 * Copyright (C) 2013 Codefog
 *
 * @package events_subscriptions
 * @author  Codefog <http://codefog.pl>
 * @author  Kamil Kuzminski <kamil.kuzminski@codefog.pl>
 * @license LGPL
 */


/**
 * Table tl_calendar_events_subscriptions
 */
$GLOBALS['TL_DCA']['tl_calendar_events_subscriptions'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'ptable'                      => 'tl_calendar_events',
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary',
				'pid' => 'index',
				'member' => 'index'
			)
		)
	),

	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 4,
			'disableGrouping'         => true,
			'headerFields'            => array('title', 'startDate', 'startTime', 'endDate', 'endTime', 'published'),
			'child_record_callback'   => array('tl_calendar_events_subscriptions', 'listMembers')
		),
		'global_operations' => array
		(
			'all' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'                => 'act=select',
				'class'               => 'header_edit_all',
				'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="e"'
			)
		),
		'operations' => array
		(
			'edit' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_calendar_events_subscriptions']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif'
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_calendar_events_subscriptions']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_calendar_events_subscriptions']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif'
			)
		)
	),

	// Palettes
	'palettes' => array
	(
		'default'                     => '{member_legend},member,addedBy'
	),

	// Fields
	'fields' => array
	(
		'id' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL auto_increment"
		),
		'pid' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'tstamp' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'member' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_calendar_events_subscriptions']['member'],
			'exclude'                 => true,
			'inputType'               => 'select',
			'options_callback'        => array('tl_calendar_events_subscriptions', 'getMembers'),
			'eval'                    => array('mandatory'=>true, 'includeBlankOption'=>true, 'chosen'=>true, 'tl_class'=>'w50'),
			'save_callback' => array
			(
				array('tl_calendar_events_subscriptions', 'checkIfAlreadyExists')
			),
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'addedBy' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_calendar_events_subscriptions']['addedBy'],
			'default'                 => \Contao\BackendUser::getInstance()->id,
            'exclude'                 => true,
			'inputType'               => 'select',
			'foreignKey'              => 'tl_user.name',
			'eval'                    => array('mandatory'=>true, 'includeBlankOption'=>true, 'tl_class'=>'w50'),
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'lastEmail' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		)
	)
);


/**
 * Provide miscellaneous methods that are used by the data configuration array.
 */
class tl_calendar_events_subscriptions extends \Backend
{

	/**
	 * List all subscribed members
	 * @param array
	 * @return array
	 */
	public function listMembers($arrRow)
	{
		$objMember = $this->Database->prepare("SELECT * FROM tl_member WHERE id=?")->limit(1)->execute($arrRow['member']);
		return '<div>' . $objMember->firstname . ' ' . $objMember->lastname . ' <span style="color:#b3b3b3;padding-left:3px;">[' . $objMember->username . ' - ' . $objMember->email . ']</span></div>';
	}


	/**
	 * Get all members and return them as array
	 * @return array
	 */
	public function getMembers()
	{
		$arrMembers = array();
		$objMembers = $this->Database->execute("SELECT * FROM tl_member");

		while ($objMembers->next())
		{
			$arrMembers[$objMembers->id] = $objMembers->firstname . ' ' . $objMembers->lastname . ' (' . $objMembers->username . ')';
		}

		return $arrMembers;
	}


	/**
	 * Check if the member is already subscribed to the event
	 * @param mixed
	 * @param object
	 * @return mixed
	 * @throws Exception
	 */
	public function checkIfAlreadyExists($varValue, \DataContainer $dc)
	{
		if ($varValue)
		{
			$this->import('EventsSubscriptions');

			// Throw an error
			if (!$this->EventsSubscriptions->checkSubscription($dc->activeRecord->pid, $varValue))
			{
				throw new \Exception(sprintf($GLOBALS['TL_LANG']['ERR']['memberAlreadySubscribed'], $varValue));
			}
		}

		return $varValue;
	}
}
