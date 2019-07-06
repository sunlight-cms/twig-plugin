<?php declare(strict_types=1);

namespace SunlightExtend\Twig;

use Twig\Loader\FilesystemLoader;

class TemplateLoader extends FilesystemLoader
{
    /** @var array */
    private $overrides = [];

    function getSourceContext($name)
    {
        return parent::getSourceContext($this->resolveName($name));
    }

    function getCacheKey($name)
    {
        return parent::getCacheKey($this->resolveName($name));
    }

    function isFresh($name, $time)
    {
        return parent::isFresh($this->resolveName($name), $time);
    }

    function exists($name)
    {
        return parent::exists($this->resolveName($name));
    }

    function override($name, $newName)
    {
        $this->overrides[$name] = $newName;
    }

    private function resolveName($name)
    {
        if (($name[0] ?? '') === '!') {
            // ignore overrides
            return substr($name, 1);
        }

        return $this->overrides[$name] ?? $name;
    }
}
