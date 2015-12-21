<?php
namespace Alin\Cadou\Controller\Adminhtml\Cadou;

use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;

class Notify extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;


    protected $_mailer ;
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;


    protected $resultJsonFactory;
    /**
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Registry $registry,
        \Alin\Cadou\Model\Mailer $mailer,
        \Psr\Log\LoggerInterface  $logger
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory =  $resultJsonFactory;
        $this->_mailer = $mailer;
        $this->_coreRegistry = $registry;
        $this->_logger = $logger;

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
        $isAjax = $this->getRequest()->getParam('isAjax');

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
            else
            {
                $data = $model->getOrigData();
                $data['name'] = $data['fullname'];

                $mediaUrl = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')
                    ->getStore($data['store_id'])
                    ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'catalog/product';

                $productSelect = $this->_objectManager->create('Alin\Cadou\Model\Cadou\Source\ProductSelect');

                $product_names = $productSelect->toOptionHash();
                $data['productname'] = $product_names[$data['product_id']];

                $product = $this->_objectManager->create('Magento\Catalog\Model\Product');
                $product  = $product->setStoreId($data['store_id'])->load($data['product_id']);
                $data['producturl'] = $product->getUrlInStore();
                $data['productimage'] = $product->getImage();

                $customerData = $this->_objectManager->create('Magento\Quote\Model\Quote');
                $customerData->loadByIdWithoutStore($data['quote_id']);

                if (!empty($data['child_cart_item_id']))
                {
                    $cart_item = $customerData->getItemById($data['cart_item_id'])->toArray();
                    $product = $this->_objectManager->create('Magento\Catalog\Model\Product'); //recreate product object
                    $product  = $product->setStoreId($data['store_id'])->load($cart_item['product_id']);
                    $data['producturl'] = $product->getUrlInStore();
                }

                if($customerData->getCustomerIsGuest())
                {
                    $data['customer_name'] = 'Guest';
                    $data['customer_email'] = $customerData->getCustomerEmail();
                }
                else
                {
                    $data['customer_name'] = $customerData->getCustomerFirstname(). ' '. $customerData->getCustomerLastname();
                    $data['customer_email'] = $customerData->getCustomerEmail();
                }

                if (!empty($data['productimage'])) $data['productimage'] = $mediaUrl.$data['productimage'];

                $resp = $this->_mailer->sendNotification($data);
                if ($resp == true)
                {
                    $model->setNotified(true);
                    $model->save();
                    $this->messageManager->addSuccess(__('The selected record has been NOTIFIED.'));
                }

                if ($isAjax)
                {
                    return $this->resultJsonFactory->create()->setData(['result'=>$resp]);
                }
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

    }
}