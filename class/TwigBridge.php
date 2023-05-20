<?php declare(strict_types=1);

namespace SunlightExtend\Twig;

use Kuria\Debug\Dumper;
use Sunlight\Core;
use Sunlight\Extend;
use Sunlight\GenericTemplates;
use Sunlight\Hcm;
use Sunlight\Post\PostService;
use Sunlight\Router;
use Sunlight\User;
use Sunlight\Util\Form;
use Sunlight\Util\Request;
use Sunlight\Util\UrlHelper;
use Sunlight\Xsrf;
use Twig\Environment;
use Twig\TwigFunction;

abstract class TwigBridge
{
    /** @var Environment|null */
    private static $env;

    /**
     * Render a Twig template
     */
    static function render(string $template, array $parameters = []): string
    {
        return self::getEnvironment()->load($template)->render($parameters);
    }

    static function getEnvironment(): Environment
    {
        return self::$env ?? (self::$env = self::createEnvironment());
    }

    private static function createEnvironment(): Environment
    {
        if (!Core::isReady()) {
            throw new \LogicException('Cannot use Twig bridge before full system initialization');
        }

        $loader = new TemplateLoader([], SL_ROOT);

        $loader->setPaths(['']);
        $loader->setPaths(['plugins/extend'], 'extend');
        $loader->setPaths(['plugins/templates'], 'templates');

        $env = new Environment(
            $loader,
            [
                'debug' => Core::$debug,
                'strict_variables' => Core::$debug,
                'cache' => SL_ROOT . 'system/cache/twig',
            ]
        );

        self::addGlobals($env);
        self::addFunctions($env);

        Extend::call('twig.init', ['env' => $env, 'loader' => $loader]);

        return $env;
    }

    private static function addGlobals(Environment $env): void
    {
        $env->addGlobal('sl', [
            'debug' => Core::$debug,
            'root' => SL_ROOT,
            'url' => Core::getCurrentUrl(),
            'baseUrl' => Core::getBaseUrl(),
            'urlHelper' => new StaticCallProxy(UrlHelper::class),
            'router' => new StaticCallProxy(Router::class, [
                'user' => true,
            ]),
            'hcm' => new StaticCallProxy(Hcm::class, [
                'parse' => true,
                'run' => true,
                'filter' => true,
                'remove' => true,
            ]),
            'extend' => new StaticCallProxy(Extend::class),
            'xsrf' => new StaticCallProxy(Xsrf::class, [
                'getinput' => true,
            ]),
            'user' => new StaticCallProxy(User::class, [
                'renderloginform' => true,
                'renderavatar' => true,
                'renderavatarfromquery' => true,
                'renderpostrepeatform' => true,
            ]),
            'form' => new StaticCallProxy(Form::class, [
                'activatecheckbox' => true,
                'disableinputunless' => true,
                'restorechecked' => true,
                'restorecheckedandname' => true,
                'restorepostvalue' => true,
                'restorepostvalueandname' => true,
                'restoregetvalue' => true,
                'restoregetvalueandname' => true,
                'restorevalue' => true,
                'renderhiddenpostinputs' => true,
                'renderhiddeninputs' => true,
                'renderhiddeninput' => true,
                'edittime' => true,
                'render' => true,
            ]),
            'post' => new StaticCallProxy(PostService::class, [
                'renderlist' => true,
                'renderform' => true,
                'renderpost' => true,
            ]),
            'generic' => new StaticCallProxy(GenericTemplates::class, [
                'renderhead' => true,
                'renderheadassets' => true,
                'renderinfos' => true,
                'rendermessagelist' => true,
                'jslimitlength' => true,
            ]),
            'request' => new StaticCallProxy(Request::class),
        ]);
    }

    private static function addFunctions(Environment $env): void
    {
        $env->addFunction(new TwigFunction('lang', '_lang'));
        $env->addFunction(new TwigFunction('call', [__CLASS__, 'call'], ['is_variadic' => true]));
        $env->addFunction(new TwigFunction('dump', [__CLASS__, 'dump'], ['needs_context' => true]));
    }

    /**
     * @internal
     */
    static function dump($context): string
    {
        if (func_num_args() > 1) {
            return call_user_func_array(
                ['Kuria\\Debug\\Dumper', 'dump'],
                array_slice(func_get_args(), 1)
            );
        }

        return Dumper::dump($context);
    }

    /**
     * @internal
     */
    static function call($callback, ...$args)
    {
        return $callback(...$args);
    }
}
