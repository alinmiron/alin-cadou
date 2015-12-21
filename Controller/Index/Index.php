<?php
namespace Alin\Cadou\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{

    /** @var \Magento\Framework\View\Result\Page */
    protected $resultPageFactory;

    /**
     * Index constructor.
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(\Magento\Framework\App\Action\Context $context,
                                PageFactory $resultPageFactory)
    {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        return $this->resultPageFactory->create();
    }
}