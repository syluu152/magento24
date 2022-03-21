<?php
/*
 *  @author  Tigren Solutions <info@tigren.com>
 *   @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 *  @license  Open Software License (“OSL”) v. 3.0
 */

namespace Tigren\SimpleBlog\Block\Adminhtml\Renderer;

use Magento\Backend\Block\Context;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;


/**
 *
 */
class Image extends AbstractRenderer
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        Context               $context,
        StoreManagerInterface $storeManager,
        array                 $data = [])
    {
        parent::__construct($context, $data);
        $this->storeManager = $storeManager;
    }

    /**
     * @param DataObject $row
     * @return string
     * @throws NoSuchEntityException
     */
    public function render(DataObject $row)
    {
        $image = $this->_getValue($row);
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        return '<img width="100" height="100" src="' . $mediaUrl . 'tigren/simpleblog/' . $image . '" />';
    }
}
