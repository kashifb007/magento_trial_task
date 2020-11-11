<?php

namespace DreamSites\Property\Controller\Adminhtml\Propertylist;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action as BackendAction;

/**
 * Class Index
 * @package DreamSites\Property\Controller\Adminhtml\Propertylist
 * @author Kashif <kash@dreamsites.co.uk>
 * @created 11/11/2020
 */
class Index extends BackendAction
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Check the permission to run it
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('DreamSites_Property::propertylist');
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('DreamSites_Property::propertylist');
        $resultPage->addBreadcrumb(__('CMS'), __('CMS'));
        $resultPage->addBreadcrumb(__('Propertyrals History'), __('Propertyrals History'));
        $resultPage->getConfig()->getTitle()->prepend(__('Propertyrals History'));

        return $resultPage;
    }
}
