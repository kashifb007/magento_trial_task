<?php

namespace DreamSites\Property\Model\Config;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class TypeOptions
 * @package DreamSites\Property\Model\Config
 * @author Kashif <kash@dreamsites.co.uk>
 * @created 11/11/2020
 */
class TypeOptions implements ArrayInterface
{

    const RENT = 'rent';
    const SALE = 'sale';

    public function toOptionArray()
    {
        return [
            self::RENT => __('Rent'),
            self::SALE => __('Sale'),
        ];
    }
}
