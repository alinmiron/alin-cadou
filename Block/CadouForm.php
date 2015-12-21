<?php
namespace Alin\Cadou\Block;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\Template;


class CadouForm extends Template implements IdentityInterface
{
    protected $__cadouCollectionFactory;

    public function __construct(Template\Context $context, \Alin\Cadou\Model\Resource\Cadou\CollectionFactory $cadouCollectionFactory, array $data = [])
    {
        parent::__construct($context,$data);
        $this->_cadouCollectionFactory = $cadouCollectionFactory;
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getIdentities()
    {
        return [\Alin\Cadou\Model\Cadou::CACHE_TAG . '_' . 'form'];
    }
}