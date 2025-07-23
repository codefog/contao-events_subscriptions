<?php

namespace Codefog\EventsSubscriptionsBundle\Subscription;

interface ExportAwareInterface
{
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
