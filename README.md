Dependency Injection
====================

The missing dependency injection for OXID eShop

  * [Blog (DE)](https://oxidforge.org/de/dependency-injection-fuer-oxid-eshop.html)
  * [Issues](https://github.com/OXIDprojects/dependency-injection/issues)
  
## Install

    composer require "oxidprojects/dependency-injection"
    

## How to use:

1. Create `services.yaml` in a module: (eg. `source/modules/tm/Sunshine/services.yaml`)

    ```yaml
    services:
      tm\ModuleOutput:
        class: 'tm\ModuleOutput'
    ```

2. `project_container()` is a global function.

    ```php
    <?php
        class Controller extends FrontendController
        {
            public function render()
            {
                $output = project_container()->get(ModuleOutput::class);
        
                $this->addTplParam('title', $output->html('Hello dependency injection'));
        
                return 'template';
             }
        }
    ```

## Weblinks

  * You have all the Power of [Symfony Service Container](https://symfony.com/doc/3.1/service_container.html).
  * See which other methods are available to [inject classes](https://symfony.com/doc/3.1/service_container/injection_types.html). You must not use every time the `__construct()` method.
