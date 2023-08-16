<?php
/**
 * Softnoesis
 * Copyright(C) 04/2023 Softnoesis <ideveloper1990@gmail.com>
 * @package Softnoesis_CancelOrder
 * @copyright Copyright(C) 2015 Softnoesis (ideveloper1990@gmail.com)
 * @author Softnoesis <ideveloper1990@gmail.com>
 */

 namespace Softnoesis\CancelOrder\Model\ResourceModel;

 use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * class OrderCancel
 */
class OrderCancel extends AbstractDb
{
    //Initialising table.
    public function _construct()
    {
        $this->_Init('Softnoesisorder_cancel_request', 'Id');
    }
}
