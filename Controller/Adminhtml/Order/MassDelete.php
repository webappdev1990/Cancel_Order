<?php
/**
 * Softnoesis
 * Copyright(C) 04/2023 Softnoesis <ideveloper1990@gmail.com>
 * @package Softnoesis_WholesaleInquiry
 * @copyright Copyright(C) 2015 Softnoesis (ideveloper1990@gmail.com)
 * @author Softnoesis <ideveloper1990@gmail.com>
 */
namespace Softnoesis\CancelOrder\Controller\Adminhtml\Order;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use Softnoesis\CancelOrder\Model\ResourceModel\OrderCancel\CollectionFactory;

class MassDelete extends \Magento\Backend\App\Action
{
    protected $filter;

    protected $collectionFactory;

    public function __construct(Context $context, Filter $filter, CollectionFactory $collectionFactory)
    {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();

        foreach ($collection as $record) {
            $record->delete();
        }

        $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $collectionSize));
        
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
