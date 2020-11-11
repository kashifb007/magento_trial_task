<?php

namespace DreamSites\Property\Model\ResourceModel\Property;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package DreamSites\Property\Model\ResourceModel\Property
 * @author Kashif <kash@dreamsites.co.uk>
 * @created 11/11/2020
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'property_id';

    /**
     * Define resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init('DreamSites\Property\Model\Property', 'DreamSites\Property\Model\ResourceModel\Property');
    }
}
