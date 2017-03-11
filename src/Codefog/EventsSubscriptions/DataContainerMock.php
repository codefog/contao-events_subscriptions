<?php

namespace Codefog\EventsSubscriptions;

use Contao\DataContainer;
use Contao\Model;

class DataContainerMock extends DataContainer
{
    /**
     * DataContainerMock constructor.
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        parent::__construct();
        $this->id           = $model->id;
        $this->table        = $model::getTable();
        $this->activeRecord = $model;
    }

    /**
     * @inheritDoc
     */
    public function getPalette()
    {
        throw new \BadMethodCallException('This method is not available in DataContainer mock');
    }

    /**
     * @inheritDoc
     */
    protected function save($varValue)
    {
        throw new \BadMethodCallException('This method is not available in DataContainer mock');
    }
}
