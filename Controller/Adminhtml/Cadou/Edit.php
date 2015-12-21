<?php
namespace Alin\Cadou\Controller\Adminhtml\Cadou;

use Magento\Backend\App\Action;

class Edit extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry

    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Alin_Cadou::save');
    }

    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Alin_Cadou::cadou')
            ->addBreadcrumb(__('Cadou'), __('Cadou'))
            ->addBreadcrumb(__('Manage'), __('Manage'));
        return $resultPage;
    }

    /**
     * Edit Blog post
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('cadou_id');
        $model = $this->_objectManager->create('Alin\Cadou\Model\Cadou');

        if ($id)
        {
            $model->load($id);
            if (!$model->getId())
            {
                $this->messageManager->addError(__('This cadou no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);

        if (!empty($data))
        {
            $model->setData($data);
        }

        $this->_coreRegistry->register('cadou', $model);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Cadou') : __('New Cadou'),
            $id ? __('Edit Cadou') : __('New Cadou')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Cadouri'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? $model->getTitle() : __('New Cadou'));

        return $resultPage;
    }
}