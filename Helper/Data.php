<?php
/**
 * Softnoesis
 * Copyright(C) 04/2023 Softnoesis <ideveloper1990@gmail.com>
 * @package Softnoesis_CancelOrder
 * @copyright Copyright(C) 2015 Softnoesis (ideveloper1990@gmail.com)
 * @author Softnoesis <ideveloper1990@gmail.com>
 */

namespace Softnoesis\CancelOrder\Helper;

use Psr\Log\LoggerInterface;
use Magento\Framework\App\Area;
use Magento\Backend\Model\Auth\Session;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;

/**
 * class Data
 */
class Data extends AbstractHelper
{
    /**
     * system config paths
     */
    public const XML_PATH_EMAIL_RECIPIENT = 'trans_email/ident_';
    public const XML_PATH_CANCELORDER = 'Cancelorder/';

    /**
     * @var StateInterface
     */
    private $ksInlineTranslation;

    /**
     * @var TransportBuilder
     */
    private $ksTransportBuilder;

    /**
     * @var ScopeConfigInterface
     */
    protected $ksScopeConfig;

    /**
     * @var StoreManagerInterface
     */
    private $ksStoreManager;

    /**
     * @var ordecancelFactory
     */
    protected $OrderCancelFactory;

    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    protected $ksCustomerSession;

    /**
     * @var LoggerInterface
     */
    private $ksLogger;

    protected $orderRepository;

    /**
     * Data constructor.
     * @param Context $ksContext
     * @param StoreManagerInterface $ksStoreManager
     * @param Magento\Framework\App\Config\ScopeConfigInterface $ksScopeConfig
     * @param TransportBuilder $ksTransportBuilder
     * @param StateInterface $ksInlineTranslation
     * @param Magento\Customer\Model\SessionFactory $ksCustomerSession
     * @param \Softnoesis\CancelOrder\Model\OrderCancelFactory $OrderCancelFactory
     * @param LoggerInterface $ksLogger
     */
    public function __construct(
        Context $ksContext,
        StoreManagerInterface $ksStoreManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $ksScopeConfig,
        TransportBuilder $ksTransportBuilder,
        StateInterface $ksInlineTranslation,
        \Softnoesis\CancelOrder\Model\OrderCancelFactory $OrderCancelFactory,
        \Magento\Customer\Model\SessionFactory $ksCustomerSession,
        LoggerInterface $ksLogger,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
    ) {
        $this->ksStoreManager = $ksStoreManager;
        $this->ksScopeConfig = $ksScopeConfig;
        $this->ksTransportBuilder = $ksTransportBuilder;
        $this->ksInlineTranslation = $ksInlineTranslation;
        $this->OrderCancelFactory = $OrderCancelFactory;
        $this->ksLogger = $ksLogger;
        $this->ksCustomerSession = $ksCustomerSession->create();
        $this->orderRepository = $orderRepository;
        parent::__construct($ksContext);
    }

    /**
     * Send Mail
     * @param $data
     * @param $type
     *
     * @throws LocalizedException
     * @throws MailException
     */
    public function sendMail($ksData, $ksType)
    {
        if ($this->getEmailConfig('cancel_notification')) {
            try {
                $ksStoreScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                if ($ksType =='request') {
                    $ksTemplate = 'cancelorder_email_settings_email_template';
                    $ksEmail = $this->getAdminEmail($this->getEmailConfig('email_sender')); //set receiver mail
                    $order = $this->orderRepository->get($ksData['entityId']);
                    $orderIncrementId = $order->getIncrementId();
                    $ksVars = [
                            'orderid' => $orderIncrementId,
                            'reason' => $ksData['reason'],
                            'customer-name' => $this->getCustomerData()->getName(),
                            'email' => $this->getCustomerData()->getEmail(),
                            'store' => $this->getStore()
                        ];
                    // set from email
                    $ksFrom = ['email' => $this->getCustomerData()->getEmail(), 'name' =>$this->getCustomerData()->getName()];
                } else {
                    $ksTemplate = 'cancelorder_email_cancel_email_template';
                    $ksFrom = ['email' => $this->getAdminEmail($this->getEmailConfig('email_sender')), 'name' => $this->getAdminName($this->getEmailConfig('email_sender')) ];
                    $ksEmail = $ksData['customer_email'];
                    $order = $this->orderRepository->get($ksData['entityId']);
                    $orderIncrementId = $order->getIncrementId();
                    $ksVars = [
                        'orderid' => $orderIncrementId,
                        'reason' => $ksData['reason'],
                        'customer-name' => $ksData['customer_name'],
                        'email' => $ksData['customer_email'],
                        'store' => $this->getStore()
                    ];
                }

                $this->ksInlineTranslation->suspend();
                $ksTransport = $this->ksTransportBuilder->setTemplateIdentifier(
                    $ksTemplate
                )->setTemplateOptions(
                    [
                        'area' => Area::AREA_FRONTEND,
                        'store' => $this->getStoreId()
                    ]
                )->setTemplateVars(
                    $ksVars
                )->setFromByScope(
                    $ksFrom
                )->addTo(
                    $ksEmail
                )->getTransport();
                $ksTransport->sendMessage();
            } catch (\Exception $exception) {
                $this->ksLogger->critical($exception->getMessage());
            }
            $this->ksInlineTranslation->resume();
        }
    }

    /*
    * get Current store id
    */
    public function getStoreId()
    {
        return $this->ksStoreManager->getStore()->getId();
    }

    /*
     * get Current store Info
     */
    public function getStore()
    {
        return $this->ksStoreManager->getStore();
    }

    /**
     * @param $field
     */
    public function getConfigValue($ksField, $ksStoreId = null)
    {
        return $this->ksScopeConfig->getValue(
            $ksField,
            ScopeInterface::SCOPE_STORE,
            $ksStoreId
        );
    }

    /**
     * @param $ksCode
     */
    public function getGeneralConfig($ksCode, $ksStoreId = null)
    {
        return $this->getConfigValue(self::XML_PATH_CANCELORDER .'general/'. $ksCode, $ksStoreId);
    }

    /**
     * @param $ksCode
     */
    public function getEmailConfig($ksCode, $storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_CANCELORDER .'email_settings/'. $ksCode, $storeId);
    }

    /**
     * @param $ksCode
     */
    public function getAdminEmail($ksCode)
    {
        $ksStoreScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $ksEmail = $this->scopeConfig->getValue(self::XML_PATH_EMAIL_RECIPIENT  . $ksCode . '/email', $ksStoreScope);
        return $ksEmail;
    }

    /**
     * @param $ksCode
     * @return adminname
     */
    public function getAdminname($ksCode)
    {
        $ksStoreScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $ksName = $this->scopeConfig->getValue(self::XML_PATH_EMAIL_RECIPIENT  . $ksCode . '/name', $ksStoreScope);
        return $ksName;
    }

    /**
     * @return customer
     */
    public function getCustomerData()
    {
        if ($this->ksCustomerSession->isLoggedIn()) {
            return $this->ksCustomerSession->getCustomer();
        }
    }

    /**
     * @var $orderid
     * @return string
     */
    public function getOrderRequest($ksOrderid)
    {
        $ksOrdercancel = $this->OrderCancelFactory->create()->load($ksOrderid, 'request_id');
        if ($ksOrdercancel->getData('Id')) {
            return 'true';
        } else {
            return 'false';
        }
    }
}
