<?php
/**
 * Softnoesis
 * Copyright(C) 04/2023 Softnoesis <ideveloper1990@gmail.com>
 * @package Softnoesis_CancelOrder
 * @copyright Copyright(C) 2015 Softnoesis (ideveloper1990@gmail.com)
 * @author Softnoesis <ideveloper1990@gmail.com>
 */

namespace Softnoesis\CancelOrder\Controller\Adminhtml\Order;

/**
 * class Index
 */
class Index extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $ksResultPageFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $ksContext
     * @param \Magento\Framework\View\Result\PageFactory $ksResultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $ksContext,
        \Magento\Framework\View\Result\PageFactory $ksResultPageFactory
    ) {
        parent::__construct($ksContext);
        $this->ksResultPageFactory = $ksResultPageFactory;
    }

    /**
     * Mapped  Order request List page.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $ksResultPage = $this->ksResultPageFactory->create();
        $ksResultPage->setActiveMenu('Softnoesis_CancelOrder::ordercancel_list');
        $ksResultPage->getConfig()->getTitle()->prepend(__('Cancel Order Request'));
        return $ksResultPage;
    }

    /**
     * Check Order request Import Permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Softnoesis_CancelOrder::ordercancel_list');
    }
}
