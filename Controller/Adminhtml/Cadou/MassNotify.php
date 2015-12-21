<?php
namespace Alin\Cadou\Controller\Adminhtml\Cadou;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class MassNotify
 */
class MassNotify extends \Magento\Backend\App\Action
{
    /**
     * Field id
     */
    const ID_FIELD = 'cadou_id';

    /**
     * Resource collection
     *
     * @var string
     */
    protected $collection = 'Alin\Cadou\Model\Resource\Cadou\Collection';

    /**
     * Page model
     *
     * @var string
     */
    protected $model = 'Alin\Cadou\Model\Cadou';
    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $selected = $this->getRequest()->getParam('selected');
        $excluded = $this->getRequest()->getParam('excluded');
        try
        {
            if (isset($excluded))
            {
                if (!empty($excluded))
                {
                    $this->excludedNotify($excluded);
                }
                else
                {
                    $this->notifyAll();
                }
            }
            elseif (!empty($selected))
            {
                $this->selectedNotify($selected);
            }
            else
            {
                $this->messageManager->addError(__('Please select item(s).'));
            }
        }
        catch (\Exception $e)
        {
            $this->messageManager->addError($e->getMessage());
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Notify all
     *
     * @return void
     * @throws \Exception
     */
    protected function notifyAll()
    {
        /** @var AbstractCollection $collection */
        $collection = $this->_objectManager->get($this->collection);
        $this->setSuccessMessage('ALL');
        $this->setSuccessMessage($this->notify($collection));
    }

    /**
     * Notify all but the not selected
     *
     * @param array $excluded
     * @return void
     * @throws \Exception
     */
    protected function excludedNotify(array $excluded)
    {
        /** @var AbstractCollection $collection */
        $collection = $this->_objectManager->get($this->collection);
        $collection->addFieldToFilter(static::ID_FIELD, ['nin' => $excluded]);
        //$this->setSuccessMessage('excluded');
        $this->setSuccessMessage($this->notify($collection));
    }

    /**
     * Notify selected items
     *
     * @param array $selected
     * @return void
     * @throws \Exception
     */
    protected function selectedNotify(array $selected)
    {
        /** @var AbstractCollection $collection */
        $collection = $this->_objectManager->get($this->collection);
        $collection->addFieldToFilter(static::ID_FIELD, ['in' => $selected]);
    //  $this->setSuccessMessage('selected');
        $this->setSuccessMessage($this->notify($collection));
    }

    /**
     * Notify collection items
     *
     * @param AbstractCollection $collection
     * @return int
     */
    protected function notify($collection)
    {
        $count = 0;
        $this->_mailer = $this->_objectManager->get('Alin\Cadou\Model\Mailer');
        foreach ($collection->getAllIds() as $id)
        {
            /** @var \Magento\Framework\Model\AbstractModel $model */
            $model = $this->_objectManager->get($this->model);
            $model->load($id);

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

            if (!empty($data['productimage'])) $data['productimage'] = $mediaUrl.$data['productimage'];

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

            $resp = $this->_mailer->sendNotification($data);
            if ($resp == true)
            {
                $model->setNotified(true);
                $model->save();
                ++$count;
            }
        }
        return $count;
    }

    /**
     * Set error messages
     *
     * @param int $count
     * @return void
     */
    protected function setSuccessMessage($count)
    {
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been notified.', $count));
    }
}