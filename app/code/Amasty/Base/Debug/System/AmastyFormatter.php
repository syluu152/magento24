<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Amasty\Base\Debug\System;

class AmastyFormatter extends \Monolog\Formatter\LineFormatter
{
    /**
     * @param array $record
     *
     * @return string
     */
    public function format(array $record): string
    {
        $output = $this->format;
        $output = str_replace('%datetime%', date('H:i d/m/Y'), $output);
        $output = str_replace('%message%', $record['message'], $output);
        return $output;
    }
}
