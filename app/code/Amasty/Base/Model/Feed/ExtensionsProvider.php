<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Amasty\Base\Model\Feed;

class ExtensionsProvider
{
    /**
     * @var array|null
     */
    protected $modulesData = null;

    /**
     * @var FeedTypes\Extensions
     */
    private $extensionsFeed;

    public function __construct(
        FeedTypes\Extensions $extensionsFeed
    ) {
        $this->extensionsFeed = $extensionsFeed;
    }

    /**
     * @return array
     */
    public function getAllFeedExtensions()
    {
        if ($this->modulesData === null) {
            $this->modulesData = $this->extensionsFeed->execute();
        }

        return $this->modulesData;
    }

    /**
     * @param string $moduleCode
     *
     * @return array
     */
    public function getFeedModuleData($moduleCode)
    {
        $allModules = $this->getAllFeedExtensions();
        $moduleData = [];

        if ($allModules && isset($allModules[$moduleCode])) {
            $module = $allModules[$moduleCode];
            if ($module && is_array($module)) {
                $module = array_shift($module);
            }
            $moduleData = $module;
        }

        return $moduleData;
    }
}
