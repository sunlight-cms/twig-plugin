<?php declare(strict_types=1);

namespace SunlightExtend\Twig;

use Sunlight\Plugin\ExtendPlugin;

class TwigPlugin extends ExtendPlugin
{
    function onHcmRender(array $module): void
    {
        $module['output'] = TwigBridge::render($module['args'][0], ['args' => array_slice($module['args'], 1)]);
    }
}
