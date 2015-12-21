<?php
namespace Alin\Cadou\Controller\Adminhtml\Cadou;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Alin_Cadou::manage_cadouri';

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Alin_Cadou::manage_cadouri');
        $resultPage->addBreadcrumb(__('Cadouri'), __('Cadouri'));
        $resultPage->addBreadcrumb(__('Manage'), __('Manage'));
        $resultPage->getConfig()->getTitle()->prepend(__('Cadouri'));
        return $resultPage;
    }
}

