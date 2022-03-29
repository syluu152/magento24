<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Setup\InstallSchema;

use Amasty\Prot\Api\Data\ScheduleInterface;
use Amasty\Prot\Api\Data\TemplateInterface;
use Amasty\Prot\Api\Data\TemplateOptionInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Setup\SchemaSetupInterface;

class AddModuleTables
{
    /**
     * @var MetadataPool
     */
    private $metadataPool;

    public function __construct(MetadataPool $metadataPool)
    {
        $this->metadataPool = $metadataPool;
    }

    /**
     * Create tables
     *
     * @param SchemaSetupInterface $installer
     * @throws \Zend_Db_Exception
     */
    public function execute(SchemaSetupInterface $installer)
    {
        $installer->startSetup();

        $this->createTemplateTable($installer);
        $this->createOptionTable($installer);
        $this->createRelationTable($installer);
        $this->createScheduleTable($installer);

        $installer->endSetup();
    }

    /**
     * @param SchemaSetupInterface $installer
     *
     * @throws \Zend_Db_Exception
     */
    protected function createTemplateTable(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()->newTable(
            $installer->getTable(TemplateInterface::MAIN_TABLE)
        )->addColumn(
            TemplateInterface::TEMPLATE_ID,
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Template ID'
        )->addColumn(
            TemplateInterface::NAME,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => ''],
            'Name'
        )->setComment(
            'Template Table'
        );
        $installer->getConnection()->createTable($table);
    }

    /**
     * @param SchemaSetupInterface $installer
     *
     * @throws \Zend_Db_Exception
     */
    protected function createRelationTable(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()->newTable(
            $installer->getTable(TemplateOptionInterface::RELATION_TABLE)
        )->addColumn(
            TemplateOptionInterface::RELATION_OPTION_ID,
            Table::TYPE_INTEGER,
            null,
            ['identity' => false, 'unsigned' => true, 'nullable' => false, 'primary' => false],
            'Option ID'
        )->addColumn(
            TemplateOptionInterface::PARENT_OPTION_ID,
            Table::TYPE_INTEGER,
            null,
            ['identity' => false, 'nullable' => false, 'primary' => false],
            'Parent Option ID'
        )->addIndex(
            $installer->getIdxName(
                TemplateOptionInterface::RELATION_TABLE,
                [TemplateOptionInterface::RELATION_OPTION_ID, TemplateOptionInterface::PARENT_OPTION_ID]
            ),
            [TemplateOptionInterface::RELATION_OPTION_ID, TemplateOptionInterface::PARENT_OPTION_ID],
            ['type' => 'unique']
        )->addForeignKey(
            $installer->getFkName(
                TemplateOptionInterface::RELATION_TABLE,
                TemplateOptionInterface::PARENT_OPTION_ID,
                $installer->getTable(TemplateOptionInterface::MAIN_TABLE),
                TemplateOptionInterface::OPTION_ID
            ),
            TemplateOptionInterface::PARENT_OPTION_ID,
            $installer->getTable(TemplateOptionInterface::MAIN_TABLE),
            TemplateOptionInterface::OPTION_ID,
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName(
                TemplateOptionInterface::RELATION_TABLE,
                TemplateOptionInterface::OPTION_ID,
                $installer->getTable('catalog_product_option'),
                'option_id'
            ),
            TemplateOptionInterface::RELATION_OPTION_ID,
            $installer->getTable('catalog_product_option'),
            'option_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Relation Table'
        );

        $installer->getConnection()->createTable($table);
    }

    /**
     * @param SchemaSetupInterface $installer
     *
     * @throws \Zend_Db_Exception
     */
    protected function createOptionTable(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()->newTable(
            $installer->getTable(TemplateOptionInterface::MAIN_TABLE)
        )->addColumn(
            TemplateOptionInterface::OPTION_ID,
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Option ID'
        )->addColumn(
            TemplateOptionInterface::TEMPLATE_ID,
            Table::TYPE_INTEGER,
            null,
            ['identity' => false, 'nullable' => false, 'primary' => false],
            'Template ID'
        )->addColumn(
            TemplateOptionInterface::DEPENDENCY,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => ''],
            'Dependency'
        )->addColumn(
            TemplateOptionInterface::FONT_COLOR,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => ''],
            'Font Color'
        )->addColumn(
            TemplateOptionInterface::FONT_SIZE,
            Table::TYPE_INTEGER,
            null,
            ['nullable' => true, 'default' => null],
            'Font Size'
        )->addIndex(
            $installer->getIdxName(TemplateOptionInterface::MAIN_TABLE, [TemplateOptionInterface::TEMPLATE_ID]),
            [TemplateOptionInterface::TEMPLATE_ID]
        )->addForeignKey(
            $installer->getFkName(
                TemplateOptionInterface::MAIN_TABLE,
                TemplateOptionInterface::TEMPLATE_ID,
                $installer->getTable(TemplateInterface::MAIN_TABLE),
                TemplateInterface::TEMPLATE_ID
            ),
            TemplateOptionInterface::TEMPLATE_ID,
            $installer->getTable(TemplateInterface::MAIN_TABLE),
            TemplateInterface::TEMPLATE_ID,
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Template Option Table'
        );
        $installer->getConnection()->createTable($table);
    }

    /**
     * @param SchemaSetupInterface $installer
     *
     * @throws \Zend_Db_Exception
     */
    protected function createScheduleTable(SchemaSetupInterface $installer)
    {
        $productMetadata = $this->metadataPool->getMetadata(ProductInterface::class);
        $table = $installer->getConnection()->newTable(
            $installer->getTable(ScheduleInterface::MAIN_TABLE)
        )->addColumn(
            ScheduleInterface::SCHEDULE_ID,
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Schedule ID'
        )->addColumn(
            ScheduleInterface::AMASTY_OPTION_ID,
            Table::TYPE_INTEGER,
            null,
            ['identity' => false, 'nullable' => false, 'primary' => false],
            'Amasty Option ID'
        )->addColumn(
            ScheduleInterface::ORIGINAL_PRODUCT_ID,
            Table::TYPE_INTEGER,
            null,
            ['identity' => false, 'nullable' => true, 'primary' => false],
            'Original Product ID'
        )->addColumn(
            ScheduleInterface::PRODUCT_ID,
            Table::TYPE_INTEGER,
            null,
            ['identity' => false, 'unsigned' => true, 'nullable' => false, 'primary' => false],
            'Product ID'
        )->addColumn(
            ScheduleInterface::CREATED_AT,
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Created At'
        )->addIndex(
            $installer->getIdxName(
                ScheduleInterface::MAIN_TABLE,
                [ScheduleInterface::AMASTY_OPTION_ID, ScheduleInterface::PRODUCT_ID]
            ),
            [ScheduleInterface::AMASTY_OPTION_ID, ScheduleInterface::PRODUCT_ID],
            ['type' => 'unique']
        )->addForeignKey(
            $installer->getFkName(
                ScheduleInterface::MAIN_TABLE,
                ScheduleInterface::AMASTY_OPTION_ID,
                $installer->getTable(TemplateOptionInterface::MAIN_TABLE),
                TemplateOptionInterface::OPTION_ID
            ),
            ScheduleInterface::AMASTY_OPTION_ID,
            $installer->getTable(TemplateOptionInterface::MAIN_TABLE),
            TemplateOptionInterface::OPTION_ID,
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName(
                ScheduleInterface::MAIN_TABLE,
                ScheduleInterface::PRODUCT_ID,
                $installer->getTable('catalog_product_entity'),
                TemplateOptionInterface::OPTION_ID
            ),
            ScheduleInterface::PRODUCT_ID,
            $installer->getTable('catalog_product_entity'),
            $productMetadata->getLinkField(),
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Schedule Table'
        );
        $installer->getConnection()->createTable($table);
    }
}
