<?php
namespace Alin\Cadou\Model\System\Config;

use Magento\Framework\Option\ArrayInterface;

class EmailTemplate extends \Magento\Framework\DataObject  implements ArrayInterface
{

    /**
     * Core store config
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;
    protected $_coreRegistry;
    protected $_templatesFactory;
    protected $_emailConfig;

    protected $_logger;


    /*
     * @var \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Email\Model\ResourceModel\Template\CollectionFactory $templatesFactory,
        \Magento\Email\Model\Template\Config $emailConfig,
        \Psr\Log\LoggerInterface $logger
    ){
        $this->_scopeConfig = $scopeConfig;

        $this->_coreRegistry = $coreRegistry;
        $this->_templatesFactory = $templatesFactory;
        $this->_emailConfig = $emailConfig;

        $this->_logger = $logger;
    }


    public function getEmailTemplate()
    {
        /** @var $collection \Magento\Email\Model\ResourceModel\Template\Collection */
        if (!($collection = $this->_coreRegistry->registry('config_system_email_template'))) {
            $collection = $this->_templatesFactory->create();
            $collection->load();
            $this->_coreRegistry->register('config_system_email_template', $collection);
        }
        $options = $collection->toOptionArray();

       $this->_logger->debug('OPTIONS ', $options);

        $templateId = str_replace('/', '_', $this->getPath());
        $templateLabel = $this->_emailConfig->getTemplateLabel($templateId);
        $templateLabel = __('%1 (Default)', $templateLabel);
        array_unshift($options, ['value' => $templateId, 'label' => $templateLabel]);
        return $options;
    }



    public function toOptionArray()
    {
        return
           $this->getEmailTemplate();
    }
}
