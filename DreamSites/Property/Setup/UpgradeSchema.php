<?php

namespace DreamSites\Property\Setup;

use Magento\Catalog\Model\Indexer\Product\Price\Plugin\TableResolver;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class UpgradeSchema
 * @package DreamSites\Property\Setup
 * @author Kashif <kash@dreamsites.co.uk>
 * @created 10/11/2020
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.0.1', '<'))
        {
            $installer = $setup;
            $installer->startSetup();
            /**
             * Create table 'dreamsites_property'
             */
            $table = $installer->getConnection()->newTable(
                $installer->getTable('dreamsites_property')
            )->addColumn(
                'ID',
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary'	=> true
                ],
                'Primary ID'
            )->addColumn(
                'county',
                Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'County'
            )->addColumn(
                'country',
                Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'Country'
            )->addColumn(
                'town',
                Table::TYPE_TEXT,
                100,
                ['nullable' => false],
                'Town'
            )->addColumn(
                'descriptions',
                Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Descriptions'
            )->addColumn(
                'displayable_address',
                Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Displayable Address'
            )->addColumn(
                'image_url',
                Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Image URL'
            )->addColumn(
                'thumbnail_url',
                Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Thumbnail URL'
            )->addColumn(
                'latitude',
                Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Latitude'
            )->addColumn(
                'number_of_bedroom',
                Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Number of Bedrooms'
            )->addColumn(
                'price',
                Table::TYPE_DECIMAL,
                null,
                ['nullable' => true],
                'Price'
            )->addColumn(
                'property_type',
                Table::TYPE_TEXT,
                50,
                ['nullable' => true],
                'Property Type'
            )->addColumn(
                'for_sale_rent',
                Table::TYPE_TEXT,
                10,
                ['nullable' => false],
                'For Sale / Rent'
            )->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false],
                'Creation Time'
            )->addColumn(
                'updated_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => true],
                'Modification Time'
            )->setComment(
                'Dream Sites Property System'
            );
            $installer->getConnection()->createTable($table);
            $installer->endSetup();
        }
    }

}
