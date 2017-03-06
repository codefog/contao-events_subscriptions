<?php

/**
 * events_subscriptions extension for Contao Open Source CMS
 *
 * @copyright Copyright (c) 2011-2017, Codefog
 * @author    Codefog <https://codefog.pl>
 * @license   http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link      http://github.com/codefog/contao-events_subscriptions
 */

namespace Codefog\EventsSubscriptions;

use Contao\Database;

class Upgrade
{
    /**
     * @var Database
     */
    private $db;

    /**
     * @var bool
     */
    private $migrateData = false;

    /**
     * Upgrade constructor.
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Run the upgrade
     */
    public function run()
    {
        $this->renameTables();
        $this->renameColumns();

        if ($this->migrateData) {
            $this->migrateData();
        }
    }

    /**
     * Rename the tables
     */
    private function renameTables()
    {
        if ($this->db->tableExists('tl_calendar_events_subscriptions')) {
            $this->migrateData = true;
            $this->db->query('RENAME TABLE `tl_calendar_events_subscriptions` TO `tl_calendar_events_subscription`');
        }
    }

    /**
     * Rename the columns
     */
    private function renameColumns()
    {
        if ($this->db->fieldExists('lastEmail', 'tl_calendar_events_subscription')) {
            $this->db->query(
                "ALTER TABLE `tl_calendar_events_subscriptions` CHANGE COLUMN `lastEmail` `lastReminder` int(10) unsigned NOT NULL default '0'"
            );
        }
    }

    /**
     * Migrate the data
     */
    private function migrateData()
    {
        // Enable subscription in all calendars
        $this->db->query('UPDATE tl_calendar SET subscription_enable=1');

        // Rename the frontend modules
        $this->db->query("UPDATE tl_module SET type='event_list_subscribe' WHERE type='eventlistsubscribe'");
        $this->db->query("UPDATE tl_module SET type='event_subscribe' WHERE type='eventsubscribe'");
        $this->db->query("UPDATE tl_module SET type='event_subscriptions' WHERE type='eventsubscriptions'");
    }
}
