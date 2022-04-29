PHP: **7.2.5+, 8.0+**, current release: **1.1.7** build 2022-04-29

## Supported versions of Symfony: **4.4+**, **5.x**, **6.x**

# eXtended Dump Bundle

**eXtended Dump Bundle is an extension to the Symfony framework. It extends the excellent framework `dump` function with new features. It attaches a new dockable window to the application with the debug collected by all dumps made by the new way. In one place you can get quick access to dumped objects and variables and information related to the system. Bundle provides a new global function `xdump` so that you can use eXtended Dump anywhere in your application code.**

## How to install:
```
composer require szczyglis/extended-dump-bundle
```
## Features:

- new, configurable window containing grouped dumped objects
- new global function: `xdump`, for quick debugging
- all dumped objects grouped in one handy place
- displaying in one handy place many useful information, such as debug of the current user, the request and response objects, variables from $_GET, $_POST, $_SESSION, $_COOKIE, $_ENV and $_SERVER, information about PHP and modules, Doctrine entities and repositories with properties and methods list, parameters list and more
- ability to extend with Events
- customizable layout (Twig)
- fully configurable


## How it works:

**Appearance after installation:**

After installing, a small icon will appear in the lower right corner of the page, pressing it will open the debugger window:

![trigger _cornerpng](https://user-images.githubusercontent.com/61396542/165001953-7c7a33d1-e2a7-4b24-b5d1-f52389e03e97.png)

The debug window is divided into 3 sections:

- **app** - the section contains all variables collected with the use of the `xdump` function,
- **event** - the event section displays the debug added using your own Event Subscriber,
- **system** - displays handy, most useful system information.

![divide](https://user-images.githubusercontent.com/61396542/165999714-e3a2d4d5-c315-42c0-a1f7-2e4154c66b87.png)

## The new `xdump` global function

The extension adds to framework a new global function called `xdump`. Thanks to it, you can use **eXtended Dump** from anywhere in the code. It works similar to the standard `dump` function, except that debugged objects fly collectively to the **eXtended Dump** window. 

*Example of use:*

```php
    /**
     * @Route("/", name="index")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $foo1 = 'bar1';
        $foo2 = 'bar2';
        $foo3 = 'bar3';

        xdump($foo1, $foo2, $foo3);

        return $this->render('index.html.twig');
    }
```
The above code added anywhere in the application (both in controllers and in services) will add the "app" section to the debugger and display the dumped object (or objects) there. 

*Example of use:*

```php
<?php
// src/Controller/IndexController.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class IndexController
 * @package App\Controller
 */
class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $foo1 = 'bar1';
        $foo2 = 'bar2';

        xdump($foo1, $foo2); // you can put one or multiple arguments

        return $this->render('index.html.twig');
    }
}
```

Result of the action:

![vars](https://user-images.githubusercontent.com/61396542/165999781-8bb3c81e-95bb-4736-bbe8-82f74da1c1ea.png)

## Extension for Twig

You can use **eXtended Dump** in Twig templates - twig extension is included in the package. 
To use in a template, just use the `xdump` function inside twig template:

```twig
# templates/template.html.twig

{% set foo = "I'm in Twig!" %}
{% set bar = "I'm in Twig too!" %}

{{ xdump(foo, bar) }} # you can dump multiple objects at once also in Twig
```
Result of the action:

![twig](https://user-images.githubusercontent.com/61396542/165999368-7a1d6b07-36a3-474a-9a34-881de51b3941.png)

## Extending eXtended Dump with EventSubscriber or EventListener

You can extend the debugger window with your own elements that will be placed there permanently. Thanks to this, you can, for example, have a quick preview of the status of selected objects in the application. To do this, create a new **EventSubscriber** or **EventListener** and handle the `Szczyglis\ExtendedDumpBundle\Event\RenderEvent`:

*Example of use:*

```php
<?php
// src/EventSubscriber/CustomDumpSubscriber.php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Szczyglis\ExtendedDumpBundle\Event\ExtendedDumpEvents;
use Szczyglis\ExtendedDumpBundle\Event\RenderEvent;

class CustomDumpSubscriber implements EventSubscriberInterface
{
    /**
     * @param RenderEvent $event
     */
    public function onRender(RenderEvent $event)
    {  
        $foo = "I want to be dumped every time!";

        $event->add($foo, "I'm Event object");
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            ExtendedDumpEvents::RENDER => 'onRender',
        ];
    }
}
```

The above will add a new section to the debugger window with the following elements added:

![event](https://user-images.githubusercontent.com/61396542/165999873-10bf5c04-afaf-4e5b-913e-d95e6ff0d41e.png)

You can add multiple items:

```php
class CustomDumpSubscriber implements EventSubscriberInterface
{
    /**
     * @param RenderEvent $event
     */
    public function onRender(RenderEvent $event)
    {  
        $foo1 = "bar1";
        $foo2 = "bar2";

        $event->add($foo1, "Optional label to display");
        $event->add($foo2);
    }   

```

## Customizing eXtended Dump

You can fully customize window appearance, CSS and JS by overriding templates from `./src/Resources/views` in your own templates directory.

# Built-in system components

### Request / Response component
This section displays the current Request and Response objects:

![sys_request](https://user-images.githubusercontent.com/61396542/166000766-94cb1d4b-0f59-4d37-b495-198786cefc5d.png)

### Doctrine component
This section displays a list of all entities used in the application, along with the names and types of defined fields, methods from the class of the given Entity, as well as methods available in the repositories:

![sys_doctrine](https://user-images.githubusercontent.com/61396542/166000823-f7f7d8ca-0230-4127-8e45-cc280867d3fb.png)

### Request variables component
This section displays the contents of $_GET, $_POST, $_SESSION and $_COOKIE:

![sys_vars](https://user-images.githubusercontent.com/61396542/166000783-64b2fecf-10c9-4c52-8648-4eeb58714422.png)

### User component
This section displays the object with the currently logged in user:

![sys_user](https://user-images.githubusercontent.com/61396542/166001242-ada036df-2883-4220-86a1-1055f5bb0db8.png)

### Server component
Useful information about the server is displayed in this section, such as the PHP version, the versions of the loaded modules, and the contents of $_ENV and $_SERVER:

![sys_server](https://user-images.githubusercontent.com/61396542/166000868-720e2e12-78ce-41cd-865f-3de887f8e74c.png)

### Parameters component
This section displays the values of all parameters defined in the application:

![sys_params](https://user-images.githubusercontent.com/61396542/166000876-b0090b91-db6d-4fdd-99a7-19ce069445a0.png)

## Configuration

In `config/packages/extended_dump.yaml`, you can create a configuration and change the way sections are displayed. 

The default appearance of `extended_dump.yaml`:

```yaml
# config/packages/extended_dump.yaml
extended_dump:
  env: [dev] # Array with enabled environments, if empty then only DEV environment will be enabled

  display:
    enabled: true # Enable/disable Xdump dockable window
    dump:
        max_depth: 1 # Var Dumper max depth config value
        max_string_depth: 160 # Var Dumper max max string depth config value
        max_items: -1 # Var Cloner max items config value
    sections:
      app: 
        enabled: true # Enable/disable App section
        collapsed: false # Collapse App section at start
      event: 
        enabled: true # Enable/disable Event section
        collapsed: false # Collapse Event section at start
      system: 
        enabled: true # Enable/disable System section
        collapsed: false # Collapse System section at start
        items:
            request: true # Enable/disable Request dump
            response: true # Enable/disable Response dump
            session: true # Enable/disable Session dump
            get: true # Enable/disable $_GET dump
            post: true # Enable/disable $_POST dump
            cookies: true # Enable/disable Cookies dump
            user: true # Enable/disable User dump
            server: true # Enable/disable Server dump
            doctrine: true # Enable/disable Doctrine dump
            parameters: true # Enable/disable Parameters dump
```

Example config template is included in package: `./src/Resources/config/extended_dump.yaml`.
___

## Bundle works with followed versions of Symfony framework:


**Symfony 4.4+:**

![symfony4](https://user-images.githubusercontent.com/61396542/165999915-73a6a22c-d607-421e-975f-03286711a8fc.png)

**Symfony 5.x:**

![symfony5](https://user-images.githubusercontent.com/61396542/165999929-dff734a5-8a38-4fcf-a0d1-bb6b3e207f03.png)

**Symfony 6.x:**

![symfony6](https://user-images.githubusercontent.com/61396542/165999958-9361c6d9-fbe4-4460-981f-12c2ebb1f514.png)

___

# Changelog
**- 1.0.13** - Published first release. (2022-04-25)

**- 1.0.36** - Added support for multiple arguments in xdump(), user debug moved to bottom of the debugger window, added version info, added dumped items counters and some more features (2022-04-29)

**- 1.1.7** - Added doctrine entities and repositories debugger, added parameters dumper, increased configuration options and added some other small improvements (2022-04-29)

# Credits
 
### eXtended Dump is free to use but if you liked then you can donate project via BTC: 

**14X6zSCbkU5wojcXZMgT9a4EnJNcieTrcr**

or by PayPal:
 **[https://www.paypal.me/szczyglinski](https://www.paypal.me/szczyglinski)**


**Enjoy!**

MIT License | 2022 Marcin 'szczyglis' Szczygliński

https://github.com/szczyglis-dev/extended-dump-bundle

https://szczyglis.dev

Contact: szczyglis@protonmail.com
