Twig plugin
###########

Twig integration plugin.

.. contents::


Requirements
************

- PHP 7.2.5+
- SunLight CMS 8


Usage
*****

See `Twig documentation <https://twig.symfony.com/doc/3.x/>`_.

.. code:: php

   <?php

   use SunlightExtend\Twig\TwigBridge;

   $output = TwigBridge::render('@extend/my-plugin/example.html.twig', [
       'foo' => 'bar',
   ]);

HCM module
==========

Any extra arguments after template name are available in the ``args`` variable.

::

  [hcm]twig,upload/my_template.html.twig,foo,bar[/hcm]


Namespaces
==========

- default: project root
- ``@extend``: plugins/extend
- ``@templates``: plugin/templates


Globals
=======

The ``sl`` global variable is available to all templates.

Proxies allow calling any static method on the target class.

==================== =========================================
Variable             Description
==================== =========================================
``sl.debug``         debug mode (``true`` / ``false``)
``sl.root``          path to project root directory
``sl.url``           current URL object
``sl.baseUrl``       base URL object
``sl.urlHelper``     ``Sunlight\Util\UrlHelper`` proxy
``sl.router``        ``Sunlight\Router`` proxy
``sl.hcm``           ``Sunlight\Hcm`` proxy
``sl.extend``        ``Sunlight\Extend`` proxy
``sl.xsrf``          ``Sunlight\Xsrf`` proxy
``sl.user``          ``Sunlight\User`` proxy
``sl.form``          ``Sunlight\Util\Form`` proxy
``sl.post``          ``Sunlight\Post\PostService`` proxy
``sl.generic``       ``Sunlight\GenericTemplates`` proxy
``sl.request``       ``Sunlight\Util\Request`` proxy
==================== =========================================


Functions
=========

- ``lang()``: alias for ``_lang()``
- ``call()``: alias for ``call_user_func()``
- ``dump([value], [maxLevel], [maxStringLen])``: alias for ``Kuria\Debug\Dumper::dump()``


Extend events
=============

``twig.init``
-------------

Called when Twig is being initialized. Can be used by other plugins to register
their custom twig functionality.

Arguments:

- ``env`` - instance of ``Twig\Environment``
- ``loader`` - instance of ``SunlightExtend\Twig\TemplateLoader``


Overriding templates
====================

Templates may be overriden by calling ``$loader->override($name, $newName)``
during the `twig.init`_ event.

To bypass template overrides (e.g. when extending overriden templates),
prepend ``!`` to the template name. Example: ``!@extend/my-plugin/example.html.twig``.
