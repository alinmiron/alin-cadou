<?php
namespace Alin\Cadou\Block;

use Alin\Cadou\Api\Data\CadouInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\Template;

/**
 * Class CadouList
 * @package Alin\Cadou\Block
 * @TODO : this is just a basic sketch and should be developed further more if needed to be shown in the frontend
 *
 */

class CadouList extends Template implements IdentityInterface{

    public function __construct(Template\Context $context, \Alin\Cadou\Model\Resource\Cadou\CollectionFactory $cadouCollectionFactory, array $data = [])
    {

    parent::__construct($context,$data);
        $this->_cadouCollectionFactory = $cadouCollectionFactory;
    }

    public function getCadouri()
    {
        if (!$this->hasData('cadouri'))
        {
            $cadouri = $this->_cadouCollectionFactory->create()->addOrder(
                CadouInterface::CADOU_ID,
                CadouInterface::SORT_ORDER_DESC);
            //)->join('quote_item','main_table.product_id = quote_item.product_id','*');

           // )->join('quote_item','main_table.product_id = quote_item.product_id',array('cadouid'=>'main_table.cadou_id', 'fullname'=>'main_table.fullname', 'birthdate'=>'main_table.birthdate', 'address'=>'main_table.address'));
            $this->setData('cadouri', $cadouri);
            //$this->setData('cadouri', $cadouri->getSelectSql(true));
        }
        return $this->getData('cadouri');
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getIdentities()
    {
        return [\Alin\Cadou\Model\Cadou::CACHE_TAG . '_' . 'list'];
    }
}
