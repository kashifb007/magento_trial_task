<?php
/**
 * Delete controller
 */
namespace DreamSites\Property\Controller\Adminhtml\Propertylist;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Redirect;

/**
 * Class Delete
 * @package DreamSites\Property\Controller\Adminhtml\Propertylist
 * @author Kashif <kash@dreamsites.co.uk>
 * @created 11/11/2020
 */
class Delete extends Action
{
    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('DreamSites::propertylist');
    }

    /**
     * Delete action
     *
     * @return Redirect
     */
    public function execute()
    {
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('image_id');
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            $title = "";
            try {
                // init model and delete
                $model = $this->_objectManager->create('DreamSites\Property\Model\Property');
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccess(__('The item has been deleted.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['image_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addError(__('We can\'t find a listing to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
