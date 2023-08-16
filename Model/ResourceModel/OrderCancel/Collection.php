<?php
/**
 * Softnoesis
 * Copyright(C) 04/2023 Softnoesis <ideveloper1990@gmail.com>
 * @package Softnoesis_CancelOrder
 * @copyright Copyright(C) 2015 Softnoesis (ideveloper1990@gmail.com)
 * @author Softnoesis <ideveloper1990@gmail.com>
 */

namespace Softnoesis\CancelOrder\Model\ResourceModel\OrderCancel;

/**
 * class Collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'Id';
    /**
     * Define resource model.
     */
    protected function _construct()
    {
        $this->_init(
            'Softnoesis\CancelOrder\Model\OrderCancel',
            'Softnoesis\CancelOrder\Model\ResourceModel\OrderCancel'
        );
    }
}
