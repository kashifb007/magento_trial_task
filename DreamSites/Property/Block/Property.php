<?php
namespace DreamSites\Property\Block;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\SalesRule\Model\Rule;

/**
 * Class Property
 * @package DreamSites\Property\Block
 * @author Kashif <kash@dreamsites.co.uk>
 * @created 11/11/2020
 */
class Property extends \Magento\Framework\View\Element\Template
{
    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \DreamSites\Property\Model\ResourceModel\Property\CollectionFactory
     */
    protected $_propertyColFactory;

    /**
     * @var
     */
    protected $_propertyCollection;

    /**
     * Property constructor.
     * @param ScopeConfigInterface $_scopeConfig
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \DreamSites\Property\Model\ResourceModel\Property\CollectionFactory $collectionFactory
     */
    public function __construct(
        ScopeConfigInterface $_scopeConfig,
        \Magento\Framework\View\Element\Template\Context $context,
        \DreamSites\Property\Model\ResourceModel\Property\CollectionFactory $collectionFactory
    ) {
        $this->_propertyColFactory = $collectionFactory;
        $this->_scopeConfig = $_scopeConfig;
        parent::__construct($context, []);
    }

    /**
     * @return string
     */
    public function getFormActionUrl()
    {
    	//change this to secure when launching live
        return $this->getUrl('property/index/property', array('_secure' => false));
    }

    /**
     * @return mixed
     */
    public function getStoreName()
	{
	    return $this->_scopeConfig->getValue(
	        'general/store_information/name',
	        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
	    );
	}

}
