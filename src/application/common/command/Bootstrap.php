<?php

namespace app\common\command;


use think\console\Command;
use think\console\Input;
use think\console\Output;

/**
 * Class Bootstrap
 * @package app\common\command
 */
class Bootstrap extends Command
{
    protected function configure()
    {
        $this->setName('bootstrap')
            ->setDescription('fix bootstrap 4 to support "-webkit-" style');
    }

    protected function execute(Input $input, Output $output)
    {
        $bootstrap = app()->getAppPath() . '/../public/static/bootstrap/css/';
        if (is_dir($bootstrap)) {
            $files = scandir($bootstrap);
            if (!$files) {
                $output->writeln("The bootstrap folder error.");
                return;
            }
            foreach ($files as $filename) {
                if ($filename == '.' || $filename == '..') continue;
                if (strrpos($filename, '.css') == strlen($filename) - 4) {
                    $content = file_get_contents($bootstrap . $filename);
                    if (strpos($content, '-webkit-flex') === false) {
                        $content = preg_replace(
                            [
                                '/display\\:(\\s)?-ms-flexbox(\\s!important)?;([\r\n\s]+)?/',
                                '/display\\:(\\s)?-ms-inline-flexbox(\\s!important)?;([\r\n\s]+)?/',
                                '/-ms-flex-preferred-size\\:([^;]+);([\r\n\s]+)?/',
                                '/-ms-flex-positive\\:([^;]+);([\r\n\s]+)?/',
                                '/-ms-flex([\w\-\d]+)?\\:([^;]+);([\r\n\s]+)?/'
                            ],
                            [
                                'display:${1}-ms-flexbox${2};${3}display:${1}-webkit-flex${2};${3}',
                                'display:${1}-ms-inline-flexbox${2};${3}display:${1}-webkit-inline-flex${2};${3}',
                                '-ms-flex-preferred-size:${1};${2}-webkit-flex-basis:${1};${2}',
                                '-ms-flex-positive:${1};${2}-webkit-flex-grow:${1};${2}',
                                '-ms-flex${1}:${2};${3}-webkit-flex${1}:${2};${3}'
                            ],
                            $content
                        );
                        file_put_contents($bootstrap . $filename, $content);
                    }
                }
            }
            $output->writeln("fix ok.");
            return;
        }
    }
}
