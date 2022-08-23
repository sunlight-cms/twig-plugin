<?php

use SunlightExtend\Twig\TwigBridge;

return function ($template, ...$args) {
    return TwigBridge::render((string) $template, ['args' => $args]);
};
