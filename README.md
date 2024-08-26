Release: **1.2.2** | build: **2024.08.26** | PHP: **^7.2.5|^8.0**

### Supported Versions of Symfony:

**4.4+**, **5.x**, **6.x**

# Extended Dump Bundle

**The Extended Dump Bundle is an extension for the Symfony framework. It enhances the `dump` function of the framework with new features. It attaches a new dockable window to the application that displays debug information collected from all dumps made using the new method. This centralized view allows quick access to dumped objects, variables, and system-related information. The bundle introduces a new global function, `xdump`, enabling you to use Extended Dump anywhere in your application code.**

## How to install
```
composer require szczyglis/extended-dump-bundle
```
## Features:

- **New Configurable Window:** Groups dumped objects in an organized manner.
- **New Global Function (`xdump`):** Facilitates quick debugging.
- **Centralized Dumped Objects:** Consolidates all dumped objects in one easily accessible place.
- **Comprehensive Information Display:** Shows useful debug information such as the current user, request and response objects, variables from `$_GET`, `$_POST`, `$_SESSION`, `$_COOKIE`, `$_ENV`, and `$_SERVER`, as well as information about PHP and its modules. It also includes lists of Doctrine entities and repositories with their properties and methods, parameters, and more.
- **Event Extensibility:** Extend functionality using events.
- **Customizable Layout:** Modify the appearance using Twig.
- **Fully Configurable:** Adjust settings to fit your needs.


## How it works

**Appearance After Installation**

After installation, a small icon will appear in the lower right corner of the page. Clicking this icon will open the debugger window.

![trigger _cornerpng](https://user-images.githubusercontent.com/61396542/165001953-7c7a33d1-e2a7-4b24-b5d1-f52389e03e97.png)

The debug window is divided into three sections:

- **app:** This section contains all variables collected using the `xdump` function.
- **event:** Displays debug information added using your own `Event Subscriber`.
- **system:** Shows the most useful and handy system information.

![divide](https://user-images.githubusercontent.com/61396542/165999714-e3a2d4d5-c315-42c0-a1f7-2e4154c66b87.png)

## The New `xdump` Global Function

The extension adds a new global function to the framework called `xdump`. This function allows you to use **Extended Dump** from anywhere in your code. It works similarly to the standard `dump` function, but the debugged objects are collectively sent to the **Extended Dump** window.

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
Adding the above code anywhere in the application, whether in controllers or services, will include the `app` section in the debugger and display the dumped object (or objects) there.

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

You can use **Extended Dump** in Twig templates thanks to the included Twig extension. To utilize it in a template, simply use the `xdump` function within the Twig template:

```twig
# templates/template.html.twig

{% set foo = "I'm in Twig!" %}
{% set bar = "I'm in Twig too!" %}

{{ xdump(foo, bar) }} # you can dump multiple objects at once also in Twig
```
Result of the action:

![twig](https://user-images.githubusercontent.com/61396542/165999368-7a1d6b07-36a3-474a-9a34-881de51b3941.png)

## Extending Extended Dump with EventSubscriber or EventListener

You can enhance the debugger window with your own elements that will be permanently displayed. This allows you to, for example, quickly preview the status of selected objects in the application. To achieve this, create a new **EventSubscriber** or **EventListener** and handle the `Szczyglis\ExtendedDumpBundle\Event\RenderEvent`.

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

The above code will add a new section to the debugger window, displaying the following elements:

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

## Customizing Extended Dump

You can fully customize the window's appearance, CSS, and JS by overriding the templates located in `./src/Resources/views` within your own templates directory.

## Built-in system components

### Request / Response Component

This section displays the current `Request` and `Response` objects:

![sys_request](https://user-images.githubusercontent.com/61396542/166000766-94cb1d4b-0f59-4d37-b495-198786cefc5d.png)

### Doctrine Component

This section displays a list of all entities used in the application, including the names and types of defined fields, methods from the class of the given Entity, and methods available in the repositories:

![sys_doctrine](https://user-images.githubusercontent.com/61396542/166000823-f7f7d8ca-0230-4127-8e45-cc280867d3fb.png)

### Request Variables Component

This section displays the contents of `$_GET`, `$_POST`, `$_SESSION`, and `$_COOKIE`:

![sys_vars](https://user-images.githubusercontent.com/61396542/166000783-64b2fecf-10c9-4c52-8648-4eeb58714422.png)

### User Component

This section displays the object representing the currently logged-in user:

![sys_user](https://user-images.githubusercontent.com/61396542/166001242-ada036df-2883-4220-86a1-1055f5bb0db8.png)

### Server Component

This section displays useful information about the server, such as the PHP version, the versions of loaded modules, and the contents of `$_ENV` and `$_SERVER`:

![sys_server](https://user-images.githubusercontent.com/61396542/166000868-720e2e12-78ce-41cd-865f-3de887f8e74c.png)

### Parameters Component

This section displays the values of all parameters defined in the application:

![sys_params](https://user-images.githubusercontent.com/61396542/166000876-b0090b91-db6d-4fdd-99a7-19ce069445a0.png)

## Configuration

In `config/packages/extended_dump.yaml`, you can define configuration settings and modify how sections are displayed.

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

An example config template is included in the package: `./src/Resources/config/extended_dump.yaml`.
___

## Bundle Compatibility

This bundle works with the following versions of the Symfony framework:


**Symfony 4.4+:**

![symfony4](https://user-images.githubusercontent.com/61396542/165999915-73a6a22c-d607-421e-975f-03286711a8fc.png)

**Symfony 5.x:**

![symfony5](https://user-images.githubusercontent.com/61396542/165999929-dff734a5-8a38-4fcf-a0d1-bb6b3e207f03.png)

**Symfony 6.x:**

![symfony6](https://user-images.githubusercontent.com/61396542/165999958-9361c6d9-fbe4-4460-981f-12c2ebb1f514.png)

___

# Changelog

**1.0.13** - Published first release. (2022-04-25)

**1.0.36** - Added support for multiple arguments in `xdump()`, moved user debug to the bottom of the debugger window, added version info, added counters for dumped items, and some other features. (2022-04-29)

**1.1.8** - Added Doctrine entities and repositories debugger, added parameters dumper, increased configuration options, and made other small improvements. (2022-04-29)

**1.2.0** - Added session existence check, added Content Security Policy nonce append. (2023-11-05)

**1.2.1** - Added style nonce append. (2023-11-21)

**1.2.2** - Improved documentation. (2024-08-26)

--- 
**Extended Dump is free to use, but if you like it, you can support my work by buying me a coffee ;)**

https://www.buymeacoffee.com/szczyglis

**Enjoy!**

MIT License | 2022 Marcin 'szczyglis' Szczygliński

https://github.com/szczyglis-dev/extended-dump-bundle

https://szczyglis.dev

Contact: szczyglis@protonmail.com
