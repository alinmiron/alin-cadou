<?php

namespace Alin\Cadou\Model;

class Mailer
{
    /**
     * Recipient email config path
     */
    const XML_PATH_EMAIL_RECIPIENT = 'contact/email/recipient_email';
    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var \Magento\Framework\Escaper
     */
    protected $_escaper;
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Escaper $escaper
    ) {
        $this->_transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->_escaper = $escaper;
        $this->messageManager = $context->getMessageManager();
    }

    public function sendNotification($data){

        if (!$data)
        {
            return false;
        }

        $this->inlineTranslation->suspend();
        try
        {
            $postObject = new \Magento\Framework\DataObject();
            $postObject->setData($data);

            $error = false;

            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

           /* $from = [
                'name' => '',
                'email' => ''
            ];*/

            $email_template = $this->scopeConfig->getValue('cadou/email/template');
            if (empty($email_template))
            {
                $email_template = (string)'cadou_email_template'; // this code we have mentioned in the email_templates.xml
            }

            $transport = $this->_transportBuilder
                ->setTemplateIdentifier($email_template)
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE, // this is using the area to get the template file
                        'store' => $this->storeManager->getDefaultStoreView()->getId() //\Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars(['data' => $postObject,'subject'=>$data['productname']])
                //->setFrom($from)
                ->setFrom($this->scopeConfig->getValue('contact/email/sender_email_identity', $storeScope))
                ->addTo($data['email'], isset($data['fullname'])?$data['fullname']:$data['name']) //email' => $this->_escaper->escapeHtml($this->getSender()->getEmail())
                ->getTransport();

            $transport->sendMessage(); ;
            $this->inlineTranslation->resume();
            /*$this->messageManager->addSuccess(
                __('Thanks for contacting us with your comments and questions. We\'ll respond to you very soon.')
            );*/
            return TRUE;
        }
        catch (\Exception $e)
        {
            $this->inlineTranslation->resume();
            $this->messageManager->addError(
                __('We can\'t process your request right now. Sorry, that\'s all we know.'.$e->getMessage())
            );
            return FALSE;
        }
    }
}