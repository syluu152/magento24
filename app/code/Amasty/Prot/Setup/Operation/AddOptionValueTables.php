<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Amasty\Prot\Setup\Operation;

use Amasty\Prot\Api\Data\TemplateOptionInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;
use Psr\Log\LoggerInterface;

class AddOptionValueTables
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $setup->startSetup();

        try {
            $this->addOptionValueTable($setup);
            $this->createRelationTable($setup);
        } catch (\Zend_Db_Exception $e) {
            $this->logger->error($e->getMessage());
        }

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     * @throws \Zend_Db_Exception
     */
    private function addOptionValueTable(SchemaSetupInterface $setup)
    {

        $table = $setup->getConnection()->newTable(
            $setup->getTable('amasty_prot_template_option_value')
        )->addColumn(
            'id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Option Value Id'
        )->addColumn(
            'template_option_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => false, 'nullable' => false, 'primary' => false],
            'Template Option Id'
        )->addIndex(
            $setup->getIdxName('amasty_prot_template_option_value', ['template_option_id']),
            ['template_option_id']
        )->addForeignKey(
            $setup->getFkName(
                'amasty_prot_template_option_value',
                'template_option_id',
                $setup->getTable(TemplateOptionInterface::MAIN_TABLE),
                TemplateOptionInterface::OPTION_ID
            ),
            'template_option_id',
            $setup->getTable(TemplateOptionInterface::MAIN_TABLE),
            TemplateOptionInterface::OPTION_ID,
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Template Option Value Table'
        );

        $setup->getConnection()->createTable($table);
    }

    /**
     * @param SchemaSetupInterface $setup
     * @throws \Zend_Db_Exception
     */
    protected function createRelationTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()->newTable(
            $setup->getTable('amasty_prot_option_value_relation')
        )->addColumn(
            'value_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => false, 'unsigned' => true, 'nullable' => false, 'primary' => false],
            'Value Id'
        )->addColumn(
            'parent_value_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => false, 'nullable' => false, 'primary' => false],
            'Parent Value Id'
        )->addIndex(
            $setup->getIdxName(
                'amasty_prot_option_value_relation',
                ['value_id', 'parent_value_id']
            ),
            ['value_id', 'parent_value_id'],
            ['type' => 'unique']
        )->addForeignKey(
            $setup->getFkName(
                'amasty_prot_option_value_relation',
                'parent_value_id',
                $setup->getTable('amasty_prot_template_option_value'),
                'id'
            ),
            'parent_value_id',
            $setup->getTable('amasty_prot_template_option_value'),
            'id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $setup->getFkName(
                'amasty_prot_option_value_relation',
                'value_id',
                $setup->getTable('catalog_product_option_type_value'),
                'option_type_id'
            ),
            'value_id',
            $setup->getTable('catalog_product_option_type_value'),
            'option_type_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Relation Option Value Table'
        );

        $setup->getConnection()->createTable($table);
    }
}
