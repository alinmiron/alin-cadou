<?php
namespace Alin\Cadou\Model\Resource\Cadou;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Alin\Cadou\Model\Cadou', 'Alin\Cadou\Model\Resource\Cadou');
    }
}