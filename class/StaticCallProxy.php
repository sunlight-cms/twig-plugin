<?php declare(strict_types=1);

namespace SunlightExtend\Twig;

use Twig\Markup;

class StaticCallProxy
{
    /** @var string */
    private $class;

    /** @var array|null */
    private $rawMarkupMethodMap;

    /**
     * @param string $class target class name
     * @param array|null $rawMarkupMethodMap lowercased map of method names that return raw HTML markup
     */
    function __construct(string $class, ?array $rawMarkupMethodMap = null)
    {
        $this->class = $class;
        $this->rawMarkupMethodMap = $rawMarkupMethodMap;
    }

    function __call($name, $arguments)
    {
        $result = $this->class::$name(...$arguments);

        if (
            $this->rawMarkupMethodMap
            && isset($this->rawMarkupMethodMap[strtolower($name)])
            && is_string($result)
        ) {
            return new Markup($result, 'UTF-8');
        }

        return $result;
    }
}
