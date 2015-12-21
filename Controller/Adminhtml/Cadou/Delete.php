<?php
namespace Alin\Cadou\Controller\Adminhtml\Cadou;

use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;

class Delete extends \Magento\Backend\App\Action
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
        return $this->_authorization->isAllowed('Alin_Cadou::delete');
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('cadou_id');
        $model = $this->_objectManager->create('Alin\Cadou\Model\Cadou');

        if ($id) {
            $model->load($id);
            if (!$model->getId())
            {
                $this->messageManager->addError(__('This cadou no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
            else
            {
                $model->delete();
                $this->_coreRegistry->unregister('cadou', $model);

                $this->messageManager->addSuccess(__('The selected record has been deleted.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
    }
}