<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\BlogManager\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

/**
 *
 */
class Comment extends AbstractModel implements IdentityInterface
{
    /**
     *
     */
    const NOROUTE_ENTITY_ID = 'no-route';
    /**
     *
     */
    const ENTITY_ID = 'entity_id';
    /**
     *
     */
    const CACHE_TAG = 'tigren_blogmanager_comment';
    /**
     * @var string
     */
    protected $_cacheTag = 'tigren_blogmanager_comment';
    /**
     * @var string
     */
    protected $_eventPrefix = 'tigren_blogmanager_comment';

    /**
     * @return void
     */
    public function _construct()
    {
        $this->_init(ResourceModel\Comment::class);
    }

    /**
     * @param $id
     * @param $field
     * @return mixed|Comment
     */
    public function load($id, $field = null)
    {
        if ($id === null) {
            return $this->noRoute();
        }
        return parent::load($id, $field);
    }

    /**
     * @return mixed|Comment
     */
    public function noRoute()
    {
        return $this->load(self::NOROUTE_ENTITY_ID, $this->getIdFieldName());
    }

    /**
     * @return string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @return array|mixed|null
     */
    public function getId()
    {
        return parent::getData(self::ENTITY_ID);
    }

    /**
     * @param $id
     * @return Comment
     */
    public function setId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }
}
