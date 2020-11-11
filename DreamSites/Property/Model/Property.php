<?php

namespace DreamSites\Property\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class Property
 * @package DreamSites\Property\Model
 * @author Kashif <kash@dreamsites.co.uk>
 * @created 11/11/2020
 */
class Property extends AbstractModel
{
    /**
     * Initialize resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init('DreamSites\Property\Model\ResourceModel\Property');
    }
}
