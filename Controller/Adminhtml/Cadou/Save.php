<?php
namespace Alin\Cadou\Controller\Adminhtml\Cadou;

use Magento\Backend\App\Action;
use Magento\TestFramework\ErrorLog\Logger;

class Save extends \Magento\Backend\App\Action
{

    /**
     * @param Action\Context $context
     */
    public function __construct(Action\Context $context)
    {
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data)
        {
            /** @var \Alin\Cadou\Model\Post $model */
            $model = $this->_objectManager->create('Alin\Cadou\Model\Cadou');

            $id = $this->getRequest()->getParam('cadou_id');
            if ($id)
            {
                $model->load($id);
            }

            $model->setData($data);

            $this->_eventManager->dispatch(
                'cadou_prepare_save',
                ['cadou' => $model, 'request' => $this->getRequest()]
            );

            try
            {
                $model->save();
                $this->messageManager->addSuccess(__('You saved this Cadou.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back'))
                {
                    return $resultRedirect->setPath('*/*/edit', ['post_id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            }
            catch (\Magento\Framework\Exception\LocalizedException $e)
            {
                $this->messageManager->addError($e->getMessage());
            }
            catch (\RuntimeException $e)
            {
                $this->messageManager->addError($e->getMessage());
            }
            catch (\Exception $e)
            {
                $this->messageManager->addException($e, __('Something went wrong while saving the cadou.'));
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['cadou_id' => $this->getRequest()->getParam('cadou_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}