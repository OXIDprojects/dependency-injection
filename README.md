Dependency Injection
====================

The missing dependency injection for OXID eShop

## Install

    composer require "oxidprojects/dependency-injection"

## How to use:

1. Create `service.yml` in a module: (eg. `source/modules/tm/Sunshine/service.yml`)

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