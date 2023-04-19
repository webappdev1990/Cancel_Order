<?php
/**
 * Softnoesis
 * Copyright(C) 04/2023 Softnoesis <ideveloper1990@gmail.com>
 * @package Softnoesis_CancelOrder
 * @copyright Copyright(C) 2015 Softnoesis (ideveloper1990@gmail.com)
 * @author Softnoesis <ideveloper1990@gmail.com>
 */

namespace Softnoesis\CancelOrder\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Order Cancel observer
 */
class OrderCancelAfter implements ObserverInterface
{
    /**
     * @var \Softnoesis\CancelOrder\Model\OrderCancel
     */
    protected $ksOrderCancelFactory;

    /**
     * @param \Softnoesis\CancelOrder\Model\OrderCancel $ksOrderCancelFactory
     */
    public function __construct(
        \Softnoesis\CancelOrder\Model\OrderCancelFactory $ksOrderCancelFactory
    ) {
        $this->ksOrderCancelFactory = $ksOrderCancelFactory;
    }

    /**
     *
     * @param \Magento\Framework\Event\Observer $ksObserver
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $ksObserver)
    {
        $ksOrderData = $ksObserver->getEvent()->getOrder();
        $ksOrderId = $ksOrderData->getId();
        $ksupdatedOrderStatus = ['status' => 'cancelled', 'state' => 'cancelled'];
        $ksOrderCancelCollection = $this->ksOrderCancelFactory->create()->getCollection()->addFieldToFilter('request_id', $ksOrderId);
        foreach ($ksOrderCancelCollection as $ksData) {
            $ksOrderCancel = $this->ksOrderCancelFactory->create()->load($ksData['Id']);
            $ksOrderCancel->addData($ksupdatedOrderStatus)->save();
        }
    }
}
