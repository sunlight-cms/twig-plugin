<?php declare(strict_types=1);

namespace SunlightExtend\Twig;

use Twig\Loader\FilesystemLoader;
use Twig\Source;

class TemplateLoader extends FilesystemLoader
{
    /** @var array */
    private $overrides = [];

    /** @var array */
    private $originals = [];

    function getSourceContext(string $name): Source
    {
        $path = $this->findTemplate($this->resolveOverride($name));

        return new Source(file_get_contents($path), $name, $path);
    }

    public function getCacheKey(string $name): string
    {
        if (isset($this->originals[$name])) {
            return 'original::' . parent::getCacheKey($this->originals[$name]);
        }

        return parent::getCacheKey($name);
    }

    function isFresh(string $name, int $time): bool
    {
        return parent::isFresh($this->resolveOverride($name), $time);
    }

    function exists(string $name): bool
    {
        return parent::exists($this->resolveOverride($name));
    }

    function override($name, $newName)
    {
        $this->overrides[$name] = $newName;
        $this->originals["!{$name}"] = $name;
    }

    private function resolveOverride($name): string
    {
        return $this->originals[$name] ?? $this->overrides[$name] ?? $name;
    }
}
