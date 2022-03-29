<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Amasty\Base\Plugin\Framework\View\TemplateEngine;

use Magento\Framework\View\Element\BlockInterface;

class Php
{
    /**
     * @var \Magento\Framework\Escaper
     */
    private $escaper;

    public function __construct(\Magento\Framework\Escaper $escaper)
    {
        $this->escaper = $escaper;
    }

    public function beforeRender(
        \Magento\Framework\View\TemplateEngine\Php $subject,
        BlockInterface $block,
        $fileName,
        array $dictionary = []
    ) {
        if (!isset($dictionary['escaper'])) {
            $dictionary['escaper'] = $this->escaper;
        }

        return [$block, $fileName, $dictionary];
    }
}
