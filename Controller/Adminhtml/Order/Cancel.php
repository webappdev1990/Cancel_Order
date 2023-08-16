<?php
/**
 * Softnoesis
 * Copyright(C) 04/2023 Softnoesis <ideveloper1990@gmail.com>
 * @package Softnoesis_CancelOrder
 * @copyright Copyright(C) 2015 Softnoesis (ideveloper1990@gmail.com)
 * @author Softnoesis <ideveloper1990@gmail.com>
 */
namespace Softnoesis\CancelOrder\Controller\Adminhtml\Order;

use Magento\Framework\Controller\ResultFactory;
use Softnoesis\CancelOrder\Model\OrderCancelFactory;
use Magento\Sales\Model\Order\ItemFactory;

/**
 * class Cancel
 */
class Cancel extends \Magento\Backend\App\Action
{
    /**
     * @var Softnoesis\CancelOrder\Helper\Data
     */
    protected $ksHelperData;

    /**
     * @var \Softnoesis\CancelOrder\Model\OrderCancelFactory
     */
    private $ksOrderCancelFactory;

    /**
     * @var \Magento\Sales\Api\OrderManagementInterface
     */
    private $ksOrderManagement;

    /**
     * @param \Magento\Backend\App\Action\Context $ksContext
     * @param \Softnoesis\CancelOrder\Model\OrderCancelFactory $ksOrderCancelFactory
     * @param Softnoesis\CancelOrder\Helper\Data $ksHelperData
     * @param OrderManagementInterface $orderManagement
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $ksContext,
        \Softnoesis\CancelOrder\Model\OrderCancelFactory $ksOrderCancelFactory,
        \Softnoesis\CancelOrder\Helper\Data $ksHelperData,
        \Magento\Sales\Api\OrderManagementInterface $ksOrderManagement
    ) {
        parent::__construct($ksContext);
        $this->ksOrderCancelFactory = $ksOrderCancelFactory;
        $this->ksOrderManagement = $ksOrderManagement;
        $this->ksHelperData = $ksHelperData;
    }

    /**
     * Mapped CancelOrder List page.
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $ksResultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $ksResultRedirect->setUrl($this->_redirect->getRefererUrl());
        $ksOrderId = (int)$this->getRequest()->getParam('id');
        try {
            $ksOrdercancel = $this->ksOrderCancelFactory->create()->load($ksOrderId, 'request_id');
            $ksoldStatus = $ksOrdercancel->getData('status');
            $this->ksOrderManagement->cancel($ksOrderId);
            $updatedOrder  = ['status' => 'cancelled', 'state' => 'cancelled'];
            $ksOrdercancel->addData($updatedOrder);
            try {
                $ksOrdercancel->save();
                $ksData  = ['entityId' => $ksOrderId, 'reason' => $ksOrdercancel->getData('order_cancel_reason'), 'customer_name' =>  $ksOrdercancel->getData('customer_name'), 'customer_email' => $ksOrdercancel->getData('customer_email')];
                if ($ksoldStatus != 'cancelled') {
                    $this->ksHelperData->sendMail($ksData, 'confirmation');
                    $this->messageManager->addSuccessMessage(__('Order Cancelled for the selected request.'));
                } else {
                    $this->messageManager->addSuccessMessage(__('Order for selected request already being cancelled.'));
                }
            } catch (\Exception $ee) {
                $this->messageManager->addErrorMessage(__('Sorry record not updated in request table.'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Sorry record not updated.'));
        }
        return $ksResultRedirect;
    }

    /**
     * Check Order request Import Permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Softnoesis_CancelOrder::order_cancel');
    }
}
