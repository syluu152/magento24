<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Plugin\Catalog\Block\Product\View;

use Amasty\Prot\Api\Data\TemplateOptionInterface;
use Magento\Catalog\Block\Product\View\Options;
use Amasty\Prot\Block\Catalog\Block\Product\View\Options as AmastyOptions;

class OptionsPlugin
{
    const INCREMENT = 100;

    /**
     * @var \Amasty\Prot\Api\TemplateOptionRepositoryInterface
     */
    private $templateOptionRepository;

    /**
     * @var \Amasty\Prot\Model\Repository\Template
     */
    private $templateRepository;

    public function __construct(
        \Amasty\Prot\Api\TemplateOptionRepositoryInterface $templateOptionRepository,
        \Amasty\Prot\Model\Repository\Template $templateRepository
    ) {
        $this->templateOptionRepository = $templateOptionRepository;
        $this->templateRepository = $templateRepository;
    }

    /**
     * @param Options $subject
     * @param $options
     * @return mixed
     */
    public function afterGetOptions(Options $subject, $options)
    {
        if ($options) {
            $this->prepareOptions($subject, $options);
        }

        return $options;
    }

    /**
     * @param Options $subject
     * @param $template
     *
     * @return array
     */
    public function afterGetOptionTemplates(Options $subject, $template)
    {
        return $this->templateRepository->getAppliedTemplateByProduct($subject->getProduct()->getId());
    }

    /**
     * @param Options $subject
     * @param $options
     */
    private function prepareOptions(Options $subject, &$options)
    {
        $isAmastyBlock = $subject instanceof AmastyOptions;
        $optionIds = array_map([$this, "getOptionId"], $options);
        $templateOptions = [];
        foreach ($this->templateOptionRepository->getOptionsByOriginalOptionIds($optionIds) as $option) {
            $data = $option->getData();
            unset($data[TemplateOptionInterface::OPTION_ID]);
            $templateOptions[$option->getData('originalOptionId')] = $data;
        }

        foreach ($options as $key => $option) {
            if (array_key_exists($option->getId(), $templateOptions)) {
                foreach ($templateOptions[$option->getId()] as $index => $value) {
                    $option->setData($index, $value);
                }

                $option->setSortOrder(
                    $option->getSortOrder() + self::INCREMENT * $option->getData(TemplateOptionInterface::TEMPLATE_ID)
                );
            }
            //remove template option for magento block and remove magento options for amasty block
            if ((!$isAmastyBlock && array_key_exists($option->getId(), $templateOptions))
                || ($isAmastyBlock && !array_key_exists($option->getId(), $templateOptions))
            ) {
                unset($options[$key]);
            }
        }

        if ($isAmastyBlock) {
            $options = $this->sortOptions($options);
        }
    }

    /**
     * @param $option
     * @return int
     */
    public function getOptionId($option)
    {
        return $option->getId();
    }

    /**
     * @param array $options
     *
     * @return array
     */
    protected function sortOptions(array $options)
    {
        $sorted = [];
        foreach ($options as $option) {
            $sorted[$option->getSortOrder()] = $option;
        }

        ksort($sorted);
        return $sorted;
    }
}
