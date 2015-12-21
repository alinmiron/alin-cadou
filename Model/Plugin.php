<?php

namespace Alin\Cadou\Model;


use Magento\Framework\App\Action\Forward;
use Magento\Framework\Filter\DataObject;


class Plugin
{

    protected $logger;

    protected $existingProducts;

    protected  $_cadouFactory;

    protected $_cadouCollectionFactory;

    public function __construct(\Psr\Log\LoggerInterface $logger,
                                \Alin\Cadou\Model\Resource\Cadou\CollectionFactory $cadouCollectionFactory,
                                \Alin\Cadou\Model\CadouFactory $cadouFactory
                                ){
        $this->logger = $logger;
        $this->_cadouFactory = $cadouFactory;
        $this->_cadouCollectionFactory = $cadouCollectionFactory;

    }

    public function beforeRemoveItem(\Magento\Checkout\Model\Cart\Interceptor $interceptor,  $item)
    {
        $records = $this->_cadouCollectionFactory->create();
        $records->addFilter('cart_item_id',$item);
        $records->loadData();
        $records=$records->toArray();
        foreach($records['items'] as $record)
        {
            $cadou = $this->_cadouFactory->create();
            $cadou->load($record['cart_item_id'],'cart_item_id');
            $cadou->delete();
        }
    }

    public function beforeUpdateItem(\Magento\Checkout\Model\Cart\Interceptor $interceptor,  $item, $quantity)
    {
        $this->logger->debug('beforeUpdateQuoteItem'.$item.' qty '.$quantity );
        //TODO: handle the quantity update. if needed!!!
    }

}
