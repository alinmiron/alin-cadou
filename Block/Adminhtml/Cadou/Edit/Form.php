<?php
namespace Alin\Cadou\Block\Adminhtml\Cadou\Edit;
use Magento\Config\Model\Config\Backend\Locale;
use Zend\Form\Element\Date;

/**
 * Adminhtml blog post edit form
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;
    protected $_productSelect;
    protected $_storeSelect;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Alin\Cadou\Model\Cadou\Source\ProductSelect $productSelect,
        \Magento\Store\Model\ResourceModel\Store\Collection $storeSelect,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_productSelect = $productSelect;
        $this->_storeSelect = $storeSelect;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Init form
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('cadou_form');
        $this->setTitle(__('Cadou Information'));
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Alin\Cadou\Model\Cadou $model */
        $model = $this->_coreRegistry->registry('cadou');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );

        $form->setHtmlIdPrefix('cadou_');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('General Information'), 'class' => 'fieldset-wide']
        );

        if ($model->getId()) {
            $fieldset->addField('cadou_id', 'hidden', ['name' => 'cadou_id']);
        }

        $fieldset->addField(
            'product_id',
            'select',
            [
                'label' => __('Product'),
                'title' => __('Product'),
                'name' => 'product_id',
                'required' => true,
                'readonly'=> true,
                'disabled'=> true,
                'options' =>$this->_productSelect->toOptionHash()
            ]
        );

        $fieldset->addField(
            'store_id',
            'select',
            [
                'label' => __('Store'),
                'title' => __('Store'),
                'name' => 'store_id',
                'required' => true,
                'readonly'=> true,
                'disabled'=> true,
                'options'=>$this->_storeSelect->toOptionHash()
            ]
        );

        $fieldset->addField(
            'fullname',
            'text',
            ['name' => 'fullname', 'label' => __('Full Name'), 'title' => __('Full Name'), 'required' => true]
        );

        $fieldset->addField(
            'email',
            'text',
            [
                'name' => 'email',
                'label' => __('Email'),
                'title' => __('Email'),
                'required' => true,
                'class' => ''
            ]
        );

        $fieldset->addField(
            'birthdate',
            //'text',
            'date',
            [
                'name' => 'birthdate',
                'label' => __('Birthdate'),
                'title' => __('Birthdate'),
                'required' => true,
                'class' => '',
                'singleClick'=> true,
                'format'=>'dd-MM-yyyy',
                'time'=>false
               //'format' =>$this->_localeDate->getDateFormat(\IntlDateFormatter::LONG)
            ]
        );

        $fieldset->addField(
            'address',
            'editor',
            [
                'name' => 'address',
                'label' => __('Address'),
                'title' => __('Address'),
                'style' => '',
                'required' => true
            ]
        );

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}