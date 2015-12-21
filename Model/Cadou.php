<?php

namespace Alin\Cadou\Model;


use Alin\Cadou\Api\Data\CadouInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class Cadou extends AbstractModel implements CadouInterface, IdentityInterface
{
    const CACHE_TAG = 'cadou';

    protected $_cacheTag = 'cadou';

    protected $_eventPrefix = 'cadou';

    protected function _construct()
    {
     $this->_init('Alin\Cadou\Model\Resource\Cadou');
    }

    public function getId(){
        return $this->getData(SELF::CADOU_ID);
    }

    public function getProductId()
    {
        return $this->getData(SELF::PRODUCT_ID);
    }

    public function getQuoteId()
    {
        return $this->getData(SELF::QUOTE_ID);
    }

    public function getStoreId()
    {
        return $this->getData(SELF::STORE_ID);
    }

    public function getFullname()
    {
        return $this->getData(SELF::FULLNAME);
    }

    public function getBirthdate()
    {
        return $this->getData(SELF::BIRTHDATE);
    }

    public function getAddress()
    {
        return $this->getData(SELF::ADDRESS);
    }

    public function getNotified()
    {
        return $this->getData(SELF::NOTIFIED);
    }

    public function setId($id)
    {
        return $this->setData(SELF::CADOU_ID, $id);
    }

    public function setProductId($productId)
    {
        return $this->setData(SELF::PRODUCT_ID, $productId);
    }

    public function setQuoteId($quoteId)
    {
        return $this->setData(SELF::QUOTE_ID, $quoteId);
    }

    public function setStoreId($storeId)
    {
        return $this->setData(SELF::STORE_ID, $storeId);
    }

    public function setFullname($fullname)
    {
        return $this->setData(SELF::FULLNAME, $fullname);
    }

    public function setBirthdate($birthdate)
    {
        return $this->setData(SELF::BIRTHDATE, $birthdate);
    }

    public function setAddress($address)
    {
        return $this->setData(SELF::ADDRESS, $address);
    }

    public function setNotified($notified)
    {
        return $this->setData(SELF::NOTIFIED, $notified);
    }

    public function getNotifiedOptions()
    {
        return ['1' => __('Yes'), '0' => __('No')];
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

}