<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Amasty\Prot\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var Operation\UpgradeDependencyField
     */
    private $upgradeDependencyField;

    /**
     * @var Operation\AddOptionValueTables
     */
    private $addOptionValueTables;

    /**
     * @var Operation\UpgradeStyleFields
     */
    private $upgradeStyleFields;

    public function __construct(
        Operation\AddOptionValueTables $addOptionValueTables,
        Operation\UpgradeDependencyField $upgradeDependencyField,
        Operation\UpgradeStyleFields $upgradeStyleFields
    ) {
        $this->upgradeDependencyField = $upgradeDependencyField;
        $this->addOptionValueTables = $addOptionValueTables;
        $this->upgradeStyleFields = $upgradeStyleFields;
    }

    /**
     * @inheritDoc
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $this->addOptionValueTables->execute($setup);
            $this->upgradeDependencyField->execute($setup);
        }

        if (version_compare($context->getVersion(), '1.2.0', '<')) {
            $this->upgradeStyleFields->execute($setup);
        }
    }
}
