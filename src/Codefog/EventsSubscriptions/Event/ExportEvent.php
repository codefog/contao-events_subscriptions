<?php

/**
 * events_subscriptions extension for Contao Open Source CMS
 *
 * @copyright Copyright (c) 2011-2017, Codefog
 * @author    Codefog <https://codefog.pl>
 * @license   http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link      http://github.com/codefog/contao-events_subscriptions
 */

namespace Codefog\EventsSubscriptions\Event;

use Contao\Model\Collection;
use Haste\IO\Reader\ArrayReader;
use Haste\IO\Writer\AbstractWriter;

class ExportEvent
{
    /**
     * @var ArrayReader
     */
    private $reader;

    /**
     * @var AbstractWriter
     */
    private $writer;

    /**
     * @var Collection|null
     */
    private $subscriptions;

    /**
     * ExportEvent constructor.
     *
     * @param ArrayReader     $reader
     * @param AbstractWriter  $writer
     * @param Collection|null $subscriptions
     */
    public function __construct(ArrayReader $reader, AbstractWriter $writer, Collection $subscriptions = null)
    {
        $this->reader        = $reader;
        $this->writer        = $writer;
        $this->subscriptions = $subscriptions;
    }

    /**
     * @return ArrayReader
     */
    public function getReader()
    {
        return $this->reader;
    }

    /**
     * @param ArrayReader $reader
     */
    public function setReader(ArrayReader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @return AbstractWriter
     */
    public function getWriter()
    {
        return $this->writer;
    }

    /**
     * @param AbstractWriter $writer
     */
    public function setWriter(AbstractWriter $writer)
    {
        $this->writer = $writer;
    }

    /**
     * @return Collection|null
     */
    public function getSubscriptions()
    {
        return $this->subscriptions;
    }

    /**
     * @param Collection|null $subscriptions
     */
    public function setSubscriptions(Collection $subscriptions)
    {
        $this->subscriptions = $subscriptions;
    }
}
