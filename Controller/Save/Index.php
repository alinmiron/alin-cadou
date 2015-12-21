<?php
namespace Alin\Cadou\Controller\Save;

use Magento\Framework\App\Action\Action;
use Magento\Catalog\Api\ProductRepositoryInterface;


class Index extends Action
{

    protected $_logger;
    protected $_datecls;
    protected $_jsonFactory;
    protected $_cadouFactory;

    protected $_checkoutSession;
    protected $_cart;



    public function __construct(\Magento\Framework\App\Action\Context $context,
                                \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
                                \Alin\Cadou\Model\Resource\Cadou\CollectionFactory $cadouFactory,
                                \Magento\Checkout\Model\Session $checkoutSession,
                                \Magento\Checkout\Model\Cart $cart,
                                \Magento\Store\Model\StoreManagerInterface $storeInterface,
                                \Magento\Framework\Stdlib\DateTime\DateTime $dateClass,
                                \Psr\Log\LoggerInterface $logger)
    {
        $this->_logger =  $logger;
        $this->_jsonFactory = $jsonFactory;
        $this->_cadouFactory = $cadouFactory;
        $this->_checkoutSession = $checkoutSession;
        $this->_cart = $cart;
        $this->_datecls = $dateClass;
        $this->_store = $storeInterface;

        parent::__construct($context);
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {

       //$cart = $this->getRequest()->getParam('cart');
       $save_data = array();
       parse_str($this->getRequest()->getParam('form'),$form);
       parse_str($this->getRequest()->getParam('product'),$product);
       $session = $this->_checkoutSession->getData(); //contains {"last_added_product_id":"128","quote_id_1":"24","cart_was_updated":true,"is_exception":false}; quote_id_1 = quote_id.'website_id'
       $cart = $this->_cart->getItems()->toArray();

        $added_to_website = $this->_store->getWebsite()->getId();
        $added_to_store = $this->_store->getStore()->getId();
        $added_to_quote=$session['quote_id_'.$added_to_website];

       //$this->_logger->debug('form', $form);
       //$this->_logger->debug('product', $product);
       //$this->_logger->debug('session', $this->_checkoutSession->getData()); //bun session data - last added product
       //$this->_logger->debug('cart', $this->_cart->getItems()->toArray());

       $save_group = array();

       $save_data['fullname'] = $form['name'];
       $save_data['email'] = $form['email'];
       $save_data['address'] =  $form['address'];
       $save_data['birthdate'] =  $this->_datecls->date('Y-M-D H:i:s', $form['birthdate']);
       $save_data['birthdate'] =  $form['birthdate'];

        if ($product['product'] == $session["last_added_product_id"])
        {
            $save_data['quote_id'] = $added_to_quote;
            $save_data['store_id'] = $added_to_store;
            $save_data['product_id'] = $product['product'];


            //cycle through the cart data to see what's been added since some assholes used a crap definition for bundle/configurable products
            $real_item = 0;
            foreach($cart['items'] as $item)
            {
                if ($real_item != 0)
                {
                    if($item['item_id'] == $real_item)
                    {
                        $save_data['product_id'] = $item['product_id'];
                    }
                    $real_item = 0;
                }

                if ($item['product_id'] == $product['product'])
                {
                    $save_data['product_type'] = $item['product_type'];

                    if ($item['product_type'] == 'configurable' || $item['product_type'] == 'bundle')
                    {
                        $save_data['child_cart_item_id'] = $item['item_id']+1;
                        $save_data['cart_item_id'] = $item['item_id'];
                        $real_item = $item['item_id']+1;
                    }
                    else
                    {
                        $save_data['cart_item_id'] = $item['item_id'];
                    }
                }
                else
                {
                    //products group are a really shitty implementation... the AJAX sends ONE product id... which is grinded internally by the Monster which spits totally N different items
                    if (isset($product['super_group']))
                    {
                        foreach($product['super_group'] as $g_product => $qty) //"super_group":{"95":"1","96":"1"}
                        {
                            if ($item['product_id'] == $g_product)
                            {
                            $tmp = $save_data;
                            $tmp['product_id'] = $g_product;

                                $tmp['cart_item_id'] = $item['item_id'];
                                $tmp['product_type'] = $item['product_type'];
                                array_push($save_group,$tmp);
                            }
                        }
                    }
                }
            }
        }

        $cadou = $this->_cadouFactory->create();

        try{

        if (!empty($save_group))
        {
            foreach($save_group as $new_cadou)
            {
                $cadou->addItem($cadou->getNewEmptyItem()->addData($new_cadou));
                $cadou->save();
            }
        }
        else
        {
            $cadou->addItem($cadou->getNewEmptyItem()->addData($save_data));
            $cadou->save();
        }
        }
        catch(\Exception $ex)
        {
            return  $this->_jsonFactory->create()->setData(['result'=>0, 'error' => $ex->getMessage()]);
        }

        return  $this->_jsonFactory->create()->setData(['result'=>1]);
        //$response = $this->_jsonFactory->create();
        //$this->_logger->debug('something: ',$this->getRequest()->getParams());
    }
}