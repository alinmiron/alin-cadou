<?php
namespace Alin\Cadou\Block\Adminhtml\Cadou;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ){
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize  cadou edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'cadou_id';
        $this->_blockGroup = 'Alin_Cadou';
        $this->_controller = 'adminhtml_cadou';

        parent::_construct();

        if ($this->_isAllowedAction('Alin_Cadou::save'))
        {
            $this->buttonList->update('save', 'label', __('Save'));
            $this->buttonList->add(
                'saveandcontinue',
                [
                    'label' => __('Save and Continue Edit'),
                    'class' => 'save',
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                        ],
                    ]
                ],
                -100
            );
        }
        else
        {
            $this->buttonList->remove('save');
        }

        if ($this->_isAllowedAction('Alin_Cadou::delete'))
        {
            $this->buttonList->update('delete', 'label', __('Delete'));
        }
        else
        {
            $this->buttonList->remove('delete');
        }

        //TODO: see how to add /handle new event
        if ($this->_isAllowedAction('Alin_Cadou::notify'))
        {
            if ($this->_coreRegistry->registry('cadou')->getNotified() == false)
            {
                $this->buttonList->add(
                    'notify',
                    [
                        'label' => __('Send notification'),
                        'class' => 'notify',
                        /*'data_attribute' => [
                            'mage-init' => [
                                'button' => ['event' => 'sendNotification', 'target' => '#edit_form'],
                            ],
                        ],*/
                        //'onclick' => 'setLocation(\'' .$this->getUrl('*/notify/').$this->_coreRegistry->registry('cadou')->getId().'\')',
                    ],
                    -100
                );
            }
        }
        else
        {
            $this->buttonList->remove('notify');
        }
    }

    /**
     * Retrieve text for header element depending on loaded cadou
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('cadou')->getId())
        {
            return __("Edit Cadou '%1'", $this->escapeHtml($this->_coreRegistry->registry('cadou')->getTitle()));
        }
        else
        {
            return __('New');
        }
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    /**
     * Getter of url for "Save and Continue" button
     * tab_id will be replaced by desired by JS later
     *
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('cadou/*/save', ['_current' => true, 'back' => 'edit', 'active_tab' => '']);
    }
}