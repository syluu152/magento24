<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Amasty\Base\Block\Adminhtml;

use Magento\Backend\Block\Template;

class Import extends Template
{
    /**
     * @var string
     */
    private $importEntityTypeCode;

    public function __construct(
        Template\Context $context,
        array $data = []
    ) {
        if (empty($data['entityTypeCode'])) {
            throw new \Amasty\Base\Exceptions\EntityTypeCodeNotSet();
        }
        $this->importEntityTypeCode = $data['entityTypeCode'];
        parent::__construct($context, $data);
    }

    public function getImportEntity()
    {
        return $this->importEntityTypeCode;
    }
}
