<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Amasty\Base\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

class Uninstall implements UninstallInterface
{
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $this
            ->uninstallColumns($setup)
            ->uninstallConfigData($setup);
    }

    private function uninstallColumns(SchemaSetupInterface $setup): self
    {
        $connection = $setup->getConnection();
        $connection->dropColumn(
            $setup->getTable('adminnotification_inbox'),
            'is_amasty'
        );
        $connection->dropColumn(
            $setup->getTable('adminnotification_inbox'),
            'expiration_date'
        );
        $connection->dropColumn(
            $setup->getTable('adminnotification_inbox'),
            'image_url'
        );

        return $this;
    }

    private function uninstallConfigData(SchemaSetupInterface $setup): self
    {
        $configTable = $setup->getTable('core_config_data');
        $setup->getConnection()->delete($configTable, "`path` LIKE 'amasty_base%'");

        return $this;
    }
}
