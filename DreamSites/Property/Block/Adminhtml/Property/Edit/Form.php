<?php

namespace DreamSites\Property\Block\Adminhtml\Property\Edit;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Backend\Block\Widget\Form\Generic;

/**
 * Class Form
 * @package DreamSites\Property\Block\Adminhtml\Property\Edit
 * @author Kashif <kash@dreamsites.co.uk>
 * @created 11/11/2020
 */
class Form extends Generic
{

    /**
     * @var \DreamSites\Property\Model\PropertyFactory
     */
     protected $_propertyDataFactory;

    /**
     * @var \Magento\Store\Model\System\Store
     */
     protected $_systemStore;

    /**
     * Form constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \DreamSites\Property\Model\PropertyFactory $propertyDataFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
     public function __construct(
         \Magento\Backend\Block\Template\Context $context,
         \Magento\Framework\Registry $registry,
         \Magento\Framework\Data\FormFactory $formFactory,
         \DreamSites\Property\Model\PropertyFactory $propertyDataFactory,
         \Magento\Store\Model\System\Store $systemStore,
         array $data = []
     ) {
         $this->_propertyDataFactory = $propertyDataFactory;
         $this->_systemStore = $systemStore;
         parent::__construct($context, $registry, $formFactory, $data);
     }

     /**
      * Prepare form for render
      *
      * @return void
      */
     protected function _prepareLayout()
     {
         parent::_prepareLayout();

         $form = $this->_formFactory->create(
             ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post', 'enctype' => 'multipart/form-data']]
         );

         $propertyId = $this->_coreRegistry->registry('current_property_id');
         if ($propertyId === null) {
             $propertyData = $this->_propertyDataFactory->create();
         } else {
             $propertyData = $this->_propertyDataFactory->create()->load($propertyId);
         }

         $yesNo = [];
         $yesNo[0] = 'No';
         $yesNo[1] = 'Yes';

         $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Property Information')]);

         $fieldset->addField(
             'store_id',
             'multiselect',
             [
                 'name' => 'store_id[]',
                 'label' => __('Store View'),
                 'title' => __('Store View'),
                 'required' => true,
                 'values' => $this->_systemStore->getStoreValuesForForm(false, true),
             ]
         );

         $fieldset->addField(
             'county',
             'text',
             [
                 'name' => 'county',
                 'label' => __('County'),
                 'county' => __('County'),
                 'required' => true
             ]
         );

         $fieldset->addField(
             'country',
             'text',
             [
                 'name' => 'country',
                 'label' => __("Country"),
                 'country' => __("Country"),
                 'required' => true
             ]
         );

         $fieldset->addField(
             'town',
             'text',
             [
                 'name' => 'town',
                 'label' => __('Town'),
                 'town' => __("Town"),
                 'required' => true
             ]
         );

         $fieldset->addField(
             'descriptions',
             'text',
             [
                 'name' => 'descriptions',
                 'label' => __('Descriptions'),
                 'descriptions' => __("Descriptions"),
                 'required' => false
             ]
         );

         $fieldset->addField(
             'displayable_address',
             'text',
             [
                 'name' => 'displayable_address',
                 'label' => __('Displayable Address'),
                 'displayable_address' => __("Displayable Address"),
                 'required' => false
             ]
         );

         $fieldset->addField(
             'image_url',
             'text',
             [
                 'name' => 'image_url',
                 'label' => __('Image URL'),
                 'image_url' => __("Image URL"),
                 'required' => false
             ]
         );

         $fieldset->addField(
             'thumbnail_url',
             'text',
             [
                 'name' => 'thumbnail_url',
                 'label' => __('Thumbnail URL'),
                 'thumbnail_url' => __("Thumbnail URL"),
                 'required' => false
             ]
         );

         $fieldset->addField(
             'latitude',
             'text',
             [
                 'name' => 'latitude',
                 'label' => __('Latitude'),
                 'latitude' => __("Latitude"),
                 'required' => false
             ]
         );

         $fieldset->addField(
             'number_of_bedroom',
             'text',
             [
                 'name' => 'number_of_bedroom',
                 'label' => __('Number of Bedrooms'),
                 'number_of_bedroom' => __("Number of Bedrooms"),
                 'required' => false
             ]
         );

         $fieldset->addField(
             'price',
             'text',
             [
                 'name' => 'price',
                 'label' => __('Price'),
                 'price' => __("Price"),
                 'required' => false
             ]
         );

         $fieldset->addField(
             'property_type',
             'text',
             [
                 'name' => 'property_type',
                 'label' => __('Property Type'),
                 'property_type' => __("Property Type"),
                 'required' => false
             ]
         );

         $fieldset->addField(
             'for_sale_rent',
             'text',
             [
                 'name' => 'for_sale_rent',
                 'label' => __('For Sale / Rent'),
                 'for_sale_rent' => __("For Sale / Rent"),
                 'required' => false
             ]
         );

         if ($propertyData->getId() !== null) {
             // If edit add id
             $form->addField('property_id', 'hidden', ['name' => 'property_id', 'value' => $propertyData->getId()]);
         }

         if ($this->_backendSession->getPropertyData()) {
             $form->addValues($this->_backendSession->getPropertyData());
             $this->_backendSession->setPropertyData(null);
         } else {
             $form->addValues(
                 [
                     'id' => $propertyData->getId(),
                     'county' => $propertyData->getCounty(),
                     'country' => $propertyData->getCountry(),
                     'town' => $propertyData->getTown(),
                     'descriptions' => $propertyData->getDescriptions(),
                     'displayable_address' => $propertyData->getDisplayableAddress(),
                     'image_url' => $propertyData->getImageUrl(),
                     'thumbnail_url' => $propertyData->getThumbnailUrl(),
                     'latitude' => $propertyData->getLatitude(),
                     'number_of_bedroom' => $propertyData->getNumberOfBedroom(),
                     'price' => $propertyData->getPrice(),
                     'property_type' => $propertyData->getPropertyType(),
                     'for_sale_rent' => $propertyData->getForSaleRent(),
                     'created_at' => $propertyData->getCreatedAt(),
                 ]
             );
         }

         $form->setUseContainer(true);
         $form->setId('edit_form');
         $form->setAction($this->getUrl('*/*/save'));
         $form->setMethod('post');
         $this->setForm($form);
     }
}
