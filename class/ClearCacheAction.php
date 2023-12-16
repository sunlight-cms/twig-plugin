<?php declare(strict_types=1);

namespace SunlightExtend\Twig;

use Sunlight\Action\ActionResult;
use Sunlight\Plugin\Action\PluginAction;
use Sunlight\Util\Filesystem;

class ClearCacheAction extends PluginAction
{
    function getTitle(): string
    {
        return _lang('twig.clear-cache.title');
    }

    function isAllowed(): bool
    {
        return true;
    }

    function execute(): ActionResult
    {
        $cacheDir = TwigBridge::getEnvironment()->getCache();

        return !is_dir($cacheDir) || Filesystem::emptyDirectory($cacheDir)
            ? ActionResult::success()
            : ActionResult::failure();
    }
}
