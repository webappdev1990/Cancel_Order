<?php
/**
 * Softnoesis
 * Copyright(C) 04/2023 Softnoesis <ideveloper1990@gmail.com>
 * @package Softnoesis_CancelOrder
 * @copyright Copyright(C) 2015 Softnoesis (ideveloper1990@gmail.com)
 * @author Softnoesis <ideveloper1990@gmail.com>
 */

namespace Softnoesis\CancelOrder\Ui\Component\Listing\OrderCancel\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

/**
 * class Action
 */
class KsAction extends Column
{
    /** Url path */
    public const ROW_EDIT_URL = 'ordercancel/order/cancel';

    public const ROW_VIEW_URL = 'sales/order/view/';

    /**
     * @var UrlInterface
     */
    protected $ksUrlBuilder;

    /**
     * @var string
     */
    private $ksEditUrl;
    private $ksViewUrl;

    /**
     * @param ContextInterface $ksContext
     * @param UiComponentFactory $ksUiComponentFactory
     * @param UrlInterface $ksUrlBuilder
     * @param array $ksComponents
     * @param array $ksData
     * @param string $ksEditUrl
     * @param string $ksViewUrl
     */
    public function __construct(
        ContextInterface $ksContext,
        UiComponentFactory $ksUiComponentFactory,
        UrlInterface $ksUrlBuilder,
        array $components = [],
        array $data = [],
        $ksEditUrl = self::ROW_EDIT_URL,
        $ksViewUrl = self::ROW_VIEW_URL
    ) {
        $this->ksUrlBuilder = $ksUrlBuilder;
        $this->ksEditUrl = $ksEditUrl;
        $this->ksViewUrl = $ksViewUrl;
        parent::__construct($ksContext, $ksUiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source.
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $ksDataSource)
    {
        if (isset($ksDataSource['data']['items'])) {
            foreach ($ksDataSource['data']['items'] as &$ksItem) {
                $ksName = $this->getData('name');
                if (isset($ksItem['Id'])) {
                    if ($ksItem['status'] != 'cancelled') {
                        $ksItem[$ksName]['edit'] = [
                        'href' => $this->ksUrlBuilder->getUrl(
                            $this->ksEditUrl,
                            ['id' => $ksItem['request_id']]
                        ),
                        'label' => __('Cancel Order'),
                         ];
                    }
                    $ksItem[$ksName]['view'] = [
                        'href' => $this->ksUrlBuilder->getUrl(
                            $this->ksViewUrl,
                            ['order_id' => $ksItem['request_id']]
                        ),
                        'label' => __('View Order'),
                    ];
                }
            }
        }
        return $ksDataSource;
    }
}
