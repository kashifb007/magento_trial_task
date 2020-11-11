<?php
/**
 * New Property Item
 */
namespace DreamSites\Property\Controller\Adminhtml\Propertylist;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Backend\Model\View\Result\Page;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class NewAction
 * @package DreamSites\Property\Controller\Adminhtml\Propertylist
 * @author Kashif <kash@dreamsites.co.uk>
 * @created 11/11/2020
 */
class NewAction extends Action
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Initialize Group Controller
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param ForwardFactory $resultForwardFactory
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        ForwardFactory $resultForwardFactory,
        PageFactory $resultPageFactory
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('DreamSites_Property::propertylist');
    }

    /**
     * Edit PropertyList item. Forward to new action.
     *
     * @return Page|Redirect
     */
    public function execute()
    {
        $propertyId = $this->getRequest()->getParam('image_id');
        $this->_coreRegistry->register('current_image_id', $propertyId);

        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        if ($propertyId === null) {
            $resultPage->addBreadcrumb(__('New PropertyList'), __('New PropertyList'));
            $resultPage->getConfig()->getTitle()->prepend(__('New PropertyList'));
        } else {
            $resultPage->addBreadcrumb(__('Edit PropertyList'), __('Edit PropertyList'));
            $resultPage->getConfig()->getTitle()->prepend(__('Edit PropertyList'));
        }

        $resultPage->getLayout()
            ->addBlock('DreamSites\Property\Block\Adminhtml\Property\Edit', 'propertylist', 'content')
            ->setEditMode((bool)$propertyId);

        return $resultPage;
    }
}
