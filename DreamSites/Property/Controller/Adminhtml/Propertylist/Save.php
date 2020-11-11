<?php
/**
 * Save property form data
 */
namespace DreamSites\Property\Controller\Adminhtml\Propertylist;

use Magento\Framework\Filesystem;

use Magento\MediaStorage\Model\File\UploaderFactory;

use Magento\Backend\App\Action;

/**
 * Class Save
 * @package DreamSites\Property\Controller\Adminhtml\Propertylist
 * @author Kashif <kash@dreamsites.co.uk>
 * @created 11/11/2020
 */
class Save extends Action
{

    /**
     * Property factory
     *
     * @var \DreamSites\Property\Model\PropertyFactory
     */
    private $propertyFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    protected $fileUploaderFactory;

    protected $storeManager;

    protected $dir;

    /**
     * Initialize Group Controller
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \DreamSites\Property\Model\PropertyFactory $propertyFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \DreamSites\Property\Model\PropertyFactory $propertyFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem\DirectoryList $dir
    ) {
        $this->context = $context;
        $this->_coreRegistry = $coreRegistry;
        $this->propertyFactory = $propertyFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_storeManager = $storeManager;
        $this->_dir = $dir;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('DreamSites_Property::propertylist');
    }

    /**
     * Save PropertyList item.
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('image_id');
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $data = $this->getRequest()->getParams();

            if ($id !== null) {
                //update
                $propertyData = $this->propertyFactory->create()->load((int)$id);
                $data['update_time'] = date('Y/m/d H:i:s');
                $data['store_id'] = implode(",", $this->getRequest()->getPostValue('store_id'));
                $data['filename'] = str_replace('/home/', '', $this->getRequest()->getPostValue('filename')['value']);
            } else {
                $data['creation_time'] = date('Y/m/d H:i:s');
                $data['update_time'] = date('Y/m/d H:i:s');
                $propertyData = $this->propertyFactory->create();
            }

            //////////////////////
                /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
                if(($_FILES['filename']['name'] !== '') && isset($_FILES['filename']['name']))
                {
                        try{
                            $directory = $this->_dir->getPath('media').'/home/';
                            $uploader = $this->_fileUploaderFactory->create(['fileId' => 'filename']);
                            /** Allowed extension types */
                            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                            /** rename file name if already exists */
                            $uploader->setAllowRenameFiles(false);
                            $uploader->setFilesDispersion(false);
                            $uploader->setAllowCreateFolders(false);
                            /** upload file in folder "mycustomfolder" */
                            $result = $uploader->save($directory);
                            if ($result['file']) {
                                $this->messageManager->addSuccess(__('File has been successfully uploaded'));
                            }
                        } catch (\Exception $e) {
                            $this->messageManager->addError($e->getMessage());
                        }
                //end block upload image
            //////////////////////


            if ($result['file']) {
                $data['filename'] = str_replace(' ', '_',trim(addslashes($_FILES['filename']['name'])));
            }

            }

            $propertyData->setData($data)->save();

            $this->messageManager->addSuccess(__('Saved Home Image item.'));
            $resultRedirect->setPath('propertyral/propertylist');
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_getSession()->setPropertyData($data);

            $resultRedirect->setPath('propertyral/propertylist/edit', ['image_id' => $id]);
        }
        return $resultRedirect;
    }

}
