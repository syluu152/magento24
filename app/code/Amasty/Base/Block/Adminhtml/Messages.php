<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Amasty\Base\Block\Adminhtml;

class Messages extends \Magento\Backend\Block\Template
{
    public const AMASTY_BASE_SECTION_NAME = 'amasty_base';
    /**
     * @var \Amasty\Base\Model\AdminNotification\Messages
     */
    private $messageManager;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Amasty\Base\Model\AdminNotification\Messages $messageManager,
        \Magento\Framework\App\Request\Http $request,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->messageManager = $messageManager;
        $this->request = $request;
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->messageManager->getMessages();
    }

    /**
     * @return string
     */
    public function _toHtml()
    {
        $html  = '';
        if ($this->request->getParam('section') === self::AMASTY_BASE_SECTION_NAME) {
            $html = parent::_toHtml();
        }

        return $html;
    }
}
