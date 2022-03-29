<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Amasty\Base\Debug;

/**
 * For Remote Debug
 * Same as VarDump class but with 'exit' after execution
 * @codeCoverageIgnore
 * @codingStandardsIgnoreFile
 */
class VarDie
{
    public static function execute()
    {
        if (VarDump::isAllowed()) {
            foreach (func_get_args() as $var) {
                System\Beautifier::getInstance()->beautify(VarDump::dump($var));
            }
            VarDump::amastyExit();
        }
    }

    public static function backtrace()
    {
        if (VarDump::isAllowed()) {
            $backtrace = debug_backtrace();
            array_shift($backtrace);
            foreach ($backtrace as $route) {
                System\Beautifier::getInstance()->beautify(
                    VarDump::dump(
                        [
                            'action' => $route['class'] . $route['type'] . $route['function'] . '()',
                            'object' => $route['object'],
                            'args' => $route['args'],
                            'file' => $route['file'] . ':' . $route['line']
                        ]
                    )
                );
            }
            VarDump::amastyExit();
        }
    }
}
