<?php
/**
 * Form container for property edit/new
 */
namespace DreamSites\Property\Block\Adminhtml\Property;

use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Registry;

/**
 * Class Edit
 * @package DreamSites\Property\Block\Adminhtml\Property
 * @author Kashif <kash@dreamsites.co.uk>
 * @created 11/11/2020
 */
class Edit extends Container
{
    /**
     * Core registry
     *
     * @var Registry
     */
     protected $coreRegistry;

     /**
      * Current property record id
      *
      * @var bool|int
      */
     protected $propertyId = false;

     /**
      * Constructor
      *
      * @param \Magento\Backend\Block\Widget\Context $context
      * @param \Magento\Framework\Registry $registry
      * @param array $data
      */
     public function __construct(
         \Magento\Backend\Block\Widget\Context $context,
         \Magento\Framework\Registry $registry,
         array $data = []
     ) {
         $this->coreRegistry = $registry;
         parent::__construct($context, $data);
     }

     /**
      * Remove Delete button if record can't be deleted.
      *
      * @return void
      */
     protected function _construct()
     {
         $this->_objectId = 'property_id';
         $this->_controller = 'adminhtml_property';
         $this->_blockGroup = 'DreamSites_Property';

         parent::_construct();

         $propertyId = $this->getpropertyId();
         if (!$propertyId) {
             $this->buttonList->remove('delete');
         }
     }

     /**
      * Retrieve the header text, either editing an existing record or creating a new one.
      *
      * @return \Magento\Framework\Phrase
      */
     public function getHeaderText()
     {
         $propertyId = $this->getpropertyId();
         if (!$propertyId) {
             return __('New Property Item');
         } else {
             return __('Edit Property Item');
         }
     }

    /**
     * @return bool|int|mixed|null
     */
     public function getpropertyId()
     {
         if (!$this->propertyId) {
             $this->propertyId=$this->coreRegistry->registry('current_image_id');
         }
         return $this->propertyId;
     }

}
