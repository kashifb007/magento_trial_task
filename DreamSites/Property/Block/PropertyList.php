<?php
namespace DreamSites\Property\Block;

use DreamSites\Property\Model\ResourceModel\Property\CollectionFactory;
use Magento\Framework\View\Element\Template;
use DreamSites\Property\Model\ResourceModel\Property\Collection as PropertyCollection;
use Magento\Store\Model\ScopeInterface;

/**
 * Class PropertyList
 * @package DreamSites\Property\Block
 * @author Kashif <kash@dreamsites.co.uk>
 * @created 11/11/2020
 */
class PropertyList extends Template
{

    /**
     * @var CollectionFactory
     */
    protected $_propertyColFactory;

    /**
     * PropertyList constructor.
     * @param Template\Context $context
     * @param CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->_propertyColFactory = $collectionFactory;
        $this->removeButton('add');
        parent::__construct(
            $context,
            $data
        );
    }

}
