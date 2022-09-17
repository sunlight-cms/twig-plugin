<?php

use SunlightExtend\Twig\TwigBridge;

return function ($template = null, ...$args) {
    if (empty($template)) {
        return '';
    }

    return TwigBridge::render((string) $template, ['args' => $args]);
};
