PHP: **7.2.5+, 8.0+**, current release: **1.0.29** build 2022-04-29

## Supported Symfony versions: **4.4**, **5.x**, **6.x**

# eXtended Dump Bundle

**eXtended Dump Bundle is an extension to the Symfony framework. It extends the excellent framework's `dump` function with new features. It attaches a new dockable window to the application with the debug collected by all dumps made by the new way. In one place you can get quick access to dumped variables and information related to the system. Bundle provides a new global function `xdump` so that you can use eXtended Dump anywhere in your application code.**

## How to install:
```
composer require szczyglis/extended-dump-bundle
```
## Features:

- a new, configurable window containing grouped debugged variables
- new global function named `xdump`, for quick debugging
- all debugged variables in one place
- displaying many useful information, such as debug of the current user, the RequestStack object, POST array, cookies and server variables
- the ability to expand with symfony events system.


## How it works:

**Appearance after installation:**

After installing, a small icon will appear in the lower right corner of the page, pressing it will open the debugger window:

![trigger _cornerpng](https://user-images.githubusercontent.com/61396542/165001953-7c7a33d1-e2a7-4b24-b5d1-f52389e03e97.png)

The debug window is divided into 3 sections:

- **app** - the section contains all variables collected with the use of the `xdump` function,
- **event** - the event section displays the debug added using your own Event Subscriber,
- **system** - displays handy, most useful system information.

![debug](https://user-images.githubusercontent.com/61396542/165871308-8b86285f-0294-4c5a-9eb9-11fab12fa384.png)

## The new `xdump` global function

The extension adds a new global function to the framework's: `xdump`. Thanks to it, you can use **eXtended Dump** from anywhere in the code. It works similar to the standard `dump` function, except that debugged objects fly collectively to the **eXtended Dump** window. Example of use:

```php
$foo1 = 'bar1';
$foo2 = 'bar2';
$foo3 = 'bar3';

xdump($foo1, $foo2, $foo3);
```

The above code added anywhere in the application will add the "app" section to the debugger and display the dumped object (or many objects) there. Example:

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

![result](https://user-images.githubusercontent.com/61396542/165867858-ef8a021a-f2dc-4c77-9695-64458e826fb3.png)

## Extension for Twig

You can use **eXtended Dump** in Twig templates - twig extension is included in the package. 
To use in a template, just use the `xdump` function inside twig template:

```twig
# templates/template.html.twig

{% set foo1 = "bar1" %}
{% set foo2 = "bar2" %}
{% set foo3 = "bar3" %}

{{ xdump(foo1, foo2, foo3) }} # you can dump multiple objects at once also in Twig
```

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
        $var = 'some variable that will be dumped every time';

        $event->add($var, 'My event variable');
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

![event](https://user-images.githubusercontent.com/61396542/165001856-e240743d-c432-4e2f-932e-1d663c4d15a7.png)

You can add multiple items:

```php
$foo1 = "bar1";
$foo2 = "bar2";

$event->add($foo1, "optional label");
$event->add($foo2);
```

## Configuration

In `config/packages/extended_dump.yaml`, you can create a configuration and change the way sections are displayed. The default appearance of `extended_dump.yaml`:

```yaml
# config/packages/extended_dump.yaml
extended_dump:
  env: [dev] # an array with the names of the environments in which the add-on will be active, if not given - only the DEV environment will use the add-on

  display:
    enabled: true # enables/disables debugger window
    sections:
      app: 
        enabled: true # enables/disables "app" section
        collapsed: false # collapses "app" section at start
      event: 
        enabled: true # enables/disables "event" section
        collapsed: false # collapses "event" section at start
      system: 
        enabled: true # enables/disables "system" section
        collapsed: false # collapses "system" section at start
```
___

## Bundle works with followed Symfony versions:


**Symfony 4.4:**

![symfony4](https://user-images.githubusercontent.com/61396542/165001864-88122f53-8364-4820-9cd2-a237a072d5bd.png)

**Symfony 5.x:**

![symfony5](https://user-images.githubusercontent.com/61396542/165001872-3f9f3822-7997-411b-b294-6e3d1016d112.png)

**Symfony 6.x:**

![symfony6](https://user-images.githubusercontent.com/61396542/165001873-71412895-4f28-4ec3-b410-44358fef9fbb.png)
___

# Changelog
**- 1.0.13** - Published first release. (2022-04-25)

**- 1.0.29** - Added support for multiple arguments in xdump(), user debug moved to bottom of the debugger window, added version info, added dumped items counters and some more features (2022-04-29)

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



