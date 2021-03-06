<?php declare(strict_types=1);

namespace SunlightExtend\Twig;

use Kuria\Debug\Dumper;
use Sunlight\Comment\Comment;
use Sunlight\Core;
use Sunlight\Extend;
use Sunlight\Hcm;
use Sunlight\Router;
use Sunlight\User;
use Sunlight\Util\Form;
use Sunlight\Util\Request;
use Sunlight\Util\Url;
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

        $loader = new TemplateLoader([], _root);

        $loader->setPaths(['']);
        $loader->setPaths(['plugins/extend'], 'extend');
        $loader->setPaths(['plugins/templates'], 'templates');

        $env = new Environment(
            $loader,
            [
                'debug' => _debug,
                'strict_variables' => _debug,
                'cache' => _root . 'system/cache/twig',
            ]
        );

        self::addGlobals($env);
        self::addFunctions($env);

        Extend::call('twig.init', ['env' => $env, 'loader' => $loader]);

        return $env;
    }

    private static function addGlobals(Environment $env)
    {
        $env->addGlobal('sl', [
            'debug' => _debug,
            'root' => _root,
            'loggedIn' => _logged_in,
            'userData' => Core::$userData,
            'userGroupData' => Core::$groupData,
            'router' => new StaticCallProxy(Router::class, [
                'user' => true,
            ]),
            'url' => new StaticCallProxy(Url::class),
            'urlHelper' => new StaticCallProxy(UrlHelper::class),
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
            'comment' => new StaticCallProxy(Comment::class, [
                'render' => true,
            ]),
            'request' => new StaticCallProxy(Request::class),
        ]);
    }

    private static function addFunctions(Environment $env)
    {
        $env->addFunction(new TwigFunction('lang', '_lang'));
        $env->addFunction(new TwigFunction('call', 'call_user_func_array', ['is_variadic' => true]));
        $env->addFunction(new TwigFunction('dump', [static::class, 'dump'], ['needs_context' => true]));
    }

    /**
     * @internal
     */
    static function call($callable, array $args = [])
    {
        return call_user_func_array($callable, $args);
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
}