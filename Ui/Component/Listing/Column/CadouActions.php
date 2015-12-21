<?php
namespace Alin\Cadou\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class CadouActions extends Column
{
    /** Url path */
    const CADOU_URL_PATH_EDIT = 'cadou/cadou/edit';
    const CADOU_URL_PATH_DELETE = 'cadou/cadou/delete';
    const CADOU_URL_PATH_NOTIFY = 'cadou/cadou/notify';

    /** @var UrlInterface */
    protected $urlBuilder;

    /**
     * @var string
     */
    private $editUrl;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     * @param string $editUrl
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = [],
        $editUrl = self::CADOU_URL_PATH_EDIT
    )
    {
        $this->urlBuilder = $urlBuilder;
        $this->editUrl = $editUrl;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items']))
        {
            foreach ($dataSource['data']['items'] as & $item)
            {
                $name = $this->getData('name');
                if (isset($item['cadou_id']))
                {
                    $item[$name]['edit'] = [
                        'href' => $this->urlBuilder->getUrl($this->editUrl, ['cadou_id' => $item['cadou_id']]),
                        'label' => __('Edit')
                    ];
                    if ($item['notified']==false)
                    {
                        $item[$name]['notify'] = [
                            'href' => $this->urlBuilder->getUrl(self::CADOU_URL_PATH_NOTIFY, ['cadou_id' => $item['cadou_id']]),
                            'label' => __('Notify')
                        ];
                    }
                    else
                    {
                        $item[$name]['notify'] = [
                            'href' => '',
                            'label' => __('Already Notified')
                        ];
                    }

                    $item[$name]['delete'] = [
                        'href' => $this->urlBuilder->getUrl(self::CADOU_URL_PATH_DELETE, ['cadou_id' => $item['cadou_id']]),
                        'label' => __('Delete'),
                        'confirm' => [
                            'title' => __('Delete "${ $.$data.cadou_id }"'),
                            'message' => __('Are you sure you wan\'t to delete a "${ $.$data.cadou_id }" record?')
                        ]
                    ];
                }
            }
        }
        return $dataSource;
    }
}