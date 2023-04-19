<?php
/**
 * Softnoesis
 * Copyright(C) 04/2023 Softnoesis <ideveloper1990@gmail.com>
 * @package Softnoesis_CancelOrder
 * @copyright Copyright(C) 2015 Softnoesis (ideveloper1990@gmail.com)
 * @author Softnoesis <ideveloper1990@gmail.com>
 */
namespace Softnoesis\CancelOrder\Controller\Index;

class Config extends \Magento\Framework\App\Action\Action
{
    /**
     * @var Data
     */
    protected $ksHelperData;

    /**
     * @var Softnoesis\CancelOrder\Model\OrderCancelFactory
     */
    protected $ksOrderCancelFactory;

    /**
     * @param Context $ksContext
     * @param Data $ksHelperData
     * @param Softnoesis\CancelOrder\Model\OrderCancelFactory $orderCancel
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $ksContext,
        \Softnoesis\CancelOrder\Helper\Data $ksHelperData,
        \Softnoesis\CancelOrder\Model\OrderCancelFactory $ksOrderCancelFactory
    ) {
        $this->ksHelperData = $ksHelperData;
        $this->ksOrderCancelFactory = $ksOrderCancelFactory;
        return parent::__construct($ksContext);
    }


    /**
     * Method to save request and send mail to admin.
     */
    public function execute()
    {
        $ksOrderCancel = $this->ksOrderCancelFactory->create();
        $ksData = $this->getRequest()->getPost();

        $ksOrderCancel->setData('order_cancel_reason', $ksData['reason']);
        $ksOrderCancel->setData('status', $ksData['status']);
        $ksOrderCancel->setData('state', $ksData['state']);
        $ksOrderCancel->setData('request_id', $ksData['entityId']);
        $ksOrderCancel->setData('customer_email', $this->ksHelperData->getCustomerData()->getEmail());
        $ksOrderCancel->setData('customer_name', $this->ksHelperData->getCustomerData()->getName());

        try {
            $ksOrderCancel->save();
            $this->ksHelperData->sendMail($ksData, 'request');
            $this->messageManager->addSuccessMessage(__('Your order cancel request is sent.This may take some time.'));
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage(__('Sorry request has been sent before. Please wait, this may take some time.'));
        }
    }
}
