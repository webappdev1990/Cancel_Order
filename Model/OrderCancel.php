<?php
/**
 * Softnoesis
 * Copyright(C) 04/2023 Softnoesis <ideveloper1990@gmail.com>
 * @package Softnoesis_CancelOrder
 * @copyright Copyright(C) 2015 Softnoesis (ideveloper1990@gmail.com)
 * @author Softnoesis <ideveloper1990@gmail.com>
 */
namespace Softnoesis\CancelOrder\Model;

/**
 * class OrderCancel
 */
class OrderCancel extends \Magento\Framework\Model\AbstractModel
{
    //constructor
    public function _construct()
    {
        $this->_init(\Softnoesis\CancelOrder\Model\ResourceModel\OrderCancel::class);
    }
}
