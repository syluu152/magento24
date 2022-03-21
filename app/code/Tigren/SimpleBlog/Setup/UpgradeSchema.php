<?php
/*
 *  @author  Tigren Solutions <info@tigren.com>
 *   @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 *  @license  Open Software License (“OSL”) v. 3.0
 */

namespace Tigren\SimpleBlog\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 *
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        //Add new fields to the created table
        if (version_compare($context->getVersion(), '0.0.1') < 0) {
            $table = $setup->getTable('tigren_blog');
            //Check for the existence of the table
            if ($setup->getConnection()->isTableExists($table) == true) {
                // Declare data
                $columns = [
                    'image' => [
                        'type' => Table::TYPE_TEXT,
                        ['nullable' => true],
                        'comment' => 'Image',
                    ],
                    'status' => [
                        'type' => Table::TYPE_SMALLINT,
                        ['nullable' => false, 'default' => 0],
                        'comment' => 'Status',
                    ],
                ];
                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($table, $name, $definition);
                }
            }
        }
        $setup->endSetup();
    }
}
