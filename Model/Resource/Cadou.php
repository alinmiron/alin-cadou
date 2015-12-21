<?php

namespace Alin\Cadou\Model\Resource;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

class Cadou extends AbstractDb
{
    public function __construct(Context $context, $connectionName = null)
    {
        parent::__construct($context, $connectionName);
    }

    public function load(\Magento\Framework\Model\AbstractModel $object, $value, $field = null)
    {
        return parent::load($object, $value, $field);
    }

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('alin_cadou', 'cadou_id');
    }

    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {

        if ($this->isEmpty($object, 'fullname')) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('The name cannot be empty')
            );
        }

        if (!$this->isEmail($object)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('The email address contains disallowed characters or is not well formed!')
            );
        }

        if ($this->isEmpty($object, 'birthdate')) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('The birthdate cannot be empty')
            );
        }

        if ($this->isEmpty($object, 'address')) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('The address cannot be empty')
            );
        }

        return parent::_beforeSave($object);
    }


    protected function isEmail(\Magento\Framework\Model\AbstractModel $object)
    {
        return filter_var($object->getData('email'),FILTER_VALIDATE_EMAIL);
    }

    protected function isEmpty(\Magento\Framework\Model\AbstractModel $object,  $field)
    {
        return (empty($object->getData($field)) || strlen(trim($object->getData($field))) == 0);
    }
//  here we'll add validation checks that can be applied in _beforeSave which should reside in this class also.

}