<?php

namespace Alin\Cadou\Model\Cadou\Source;

use Magento\Framework\Data\OptionSourceInterface;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Eav\Model\Entity\Attribute\OptionManagement as EavAttributeOptionManagement;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

class ProductSelect  implements OptionSourceInterface
{
    protected $_logger;
    protected $_products;
    protected $_product;
    protected $_configurable;
    protected $_eav_attribute;
    protected $_eav_attribute_option;

    public function __construct(\Psr\Log\LoggerInterface $logger,
                                Collection $products,
                                Product $product,
                                Attribute $attribute,
                                EavAttributeOptionManagement $attribute_option,
                                Configurable $configurable
                                )
    {
        $this->_logger =  $logger;
        $this->_products = $products;
        $this->_product = $product;
        $this->_configurable = $configurable;
        $this->_eav_attribute = $attribute;
        $this->_eav_attribute_option = $attribute_option;
    }

    private function toRawArray()
    {
        $out = [];

        foreach ($this->_products as $item)
        {
            $name ='';
            $product_attributes_values = [];
            $theproduct = $this->_product->load($item['entity_id']);
            $product_attributes = $theproduct->getExtensionAttributes()->getConfigurableProductOptions(); // Attribute objects

            $attribute_values = [];
            foreach ($product_attributes as $attribute)
            {
                $the_eav_attribute = $this->_eav_attribute->load(str_replace(' ','_',$attribute->getLabel()),'attribute_code');
                //  $this->_logger->debug('attribute',$the_eav_attribute->toArray());
                $the_eav_attribute_options = $this->_eav_attribute_option->getItems($the_eav_attribute['entity_type_id'], $the_eav_attribute['attribute_id']);
                foreach ($the_eav_attribute_options as $the_eav_attribute_option)
                {
                    $option_value = $the_eav_attribute_option->toArray();
                    if ($option_value['label'] !== '')
                    {
                        $attribute_values[str_replace(' ','_',$attribute->getLabel())][] = $option_value;
                    }
                }
            }
            $theproduct_array = $theproduct->toArray();
            foreach ($theproduct_array as $k => $v)
            { //marime=>4
                if(in_array($k,array_keys($attribute_values)))
                {
                    foreach ($attribute_values[$k] as $avalue) //
                    {
                        if ($avalue['value'] == $v)
                        {
                            $product_attributes_values[]=$k .': '.$avalue['label'];
                            //$this->_logger->debug($k,$avalue);
                        }
                    }
                }
            }

            $the_parent = $this->_configurable->getParentIdsByChild($item['entity_id']);

            if (!empty($the_parent))
            {
                $name = $this->_product->load($the_parent)->getName();
            }
            $out[] = [$item['entity_id']=>(empty($name)?$theproduct['name']:$name).(empty($product_attributes_values)?'': ' ['.implode(', ', $product_attributes_values).']')];
        }
        return $out;
    }

    public function toOptionArray()
    {
        $out = [];
        $data = $this->toRawArray();

        foreach ($data as $opt)
        {
            foreach ($opt as $v=>$k)
            {
                $out[] = ['value'=>$v, 'label'=>$k];
            }
        }
        return $out;
    }

    public function toOptionHash()
    {
        $out = [];
        $data = $this->toRawArray();
            foreach ($data as $opt)
            {
                foreach ($opt as $v=>$k)
                {
                    $out[$v] = $k;
                }
            }
            return $out;
    }
}