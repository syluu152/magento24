<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Model\ResourceModel\Template\Grid;

use Amasty\Prot\Api\Data\TemplateOptionInterface;

class ModalCollection extends \Amasty\Prot\Model\ResourceModel\Template\Collection
{
    /**
     * @param int $currentProductId
     *
     * @return $this
     */
    public function addNonProductFilter(int $currentProductId)
    {
        $this->getSelect()->join(
            ['am_options' => $this->getTable(TemplateOptionInterface::MAIN_TABLE)],
            'am_options.template_id = main_table.template_id',
            null
        )->join(
            ['prot_relation' => $this->getTable(TemplateOptionInterface::RELATION_TABLE)],
            'prot_relation.parent_option_id = am_options.option_id',
            null
        )->join(
            ['po' => $this->getTable('catalog_product_option')],
            'prot_relation.option_id = po.option_id AND po.product_id !=' . (int)$currentProductId,
            null
        );
        $this->getSelect()->group('main_table.template_id');

        return $this;
    }
}
