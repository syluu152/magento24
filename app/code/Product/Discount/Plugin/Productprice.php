<?php

namespace Product\Discount\Plugin;


class Productprice

{
    public function afterGetName(\Magento\Catalog\Model\Product $subject, $result)
    {
        $result = "HelloWorld";
        return $result;
    }

}
