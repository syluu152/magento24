<?php

namespace Product\Discount\Plugin;


class Productprice

{

    //    public function afterGetPrice(\Magento\Catalog\Model\Product $subject, $result)
    //    {
    //
    //        $result += 150; //add your product price logic
    //
    //        return $result;
    //
    //    }
    public function afterGetName(\Magento\Catalog\Model\Product $subject, $result)
    {
        $result = "HelloWorld";

        //
        //        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/custom.log');
        //        $logger = new \Zend_Log();
        //        $logger->addWriter($writer);
        //        $logger->info(print_r($result, true));
        return $result;
    }

}
