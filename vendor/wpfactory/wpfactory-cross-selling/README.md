# WPFactory Cross-Selling
A library designed for WPFactory plugins, aimed at cross-selling by offering WPFactory product recommendations

## Installation

Installation via Composer. Instructions to setup the `composer.json`.

1. Add these objects to the `repositories` array:

```json
"repositories": [    
    {
      "type": "vcs",
      "url": "https://github.com/wpcodefactory/wpfactory-cross-selling"
    },
    {
      "type": "vcs",
      "url": "https://github.com/wpcodefactory/wpfactory-admin-menu"
    }
]
```

2. Require the library and its dependencies:

```json
"require": {
  "wpfactory/wpfactory-cross-selling": "*", 
  "wpfactory/wpfactory-admin-menu": "*"
},
```

3. Use `preferred-install` parameter set as `dist` on `config`.

```json
"config": {
  "preferred-install": "dist"
}
```

**Full Example:**

```json
{
  "repositories": [    
    {
      "type": "vcs",
      "url": "https://github.com/wpcodefactory/wpfactory-cross-selling"
    },
    {
      "type": "vcs",
      "url": "https://github.com/wpcodefactory/wpfactory-admin-menu"
    }
  ],
  "require": {    
    "wpfactory/wpfactory-cross-selling": "*",
    "wpfactory/wpfactory-admin-menu": "*"
  },
  "config": {
    "preferred-install": "dist"
  }
}
```

## How to use it?
1. Create/Put the composer.json on the root folder.

2. Require the Composer `autoload.php` on main plugin file. Most of our plugins are already doing it. Example:
```php
require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
```

3. Then initialize the library with `new \WPFactory\WPFactory_Cross_Selling\WPFactory_Cross_Selling()` from within the main plugin class. Probably the best place is inside the hook `plugins_loaded`. If the main class is already being loaded with that hook, you can simply load the library in the class constructor.
> [!NOTE]  
> Try to remember to only run it inside a `is_admin()` check.

*Example:*

```php
add_action( 'plugins_loaded', function(){  
    $main_plugin_class = new Main_Plugin_Class();  
} );
```

```php
class Main_Plugin_Class(){

    function __construct() { 
        $this->init_cross_selling_library();
    }

    function init_cross_selling_library(){
        if ( ! is_admin() ) {
            return;
        }

        // Initializes WPFactory Key Manager library.
        $cross_selling = new \WPFactory\WPFactory_Cross_Selling\WPFactory_Cross_Selling();
        $cross_selling->setup( array( 'plugin_file_path'   => __FILE__ ) );
        $cross_selling->init()
    }

}
```

## Methods

### `setup( array $args = null )`

Setups the library. 

**Parameters:**

* **`plugin_file_path`** (string) - Plugin file path.
* **`plugin_action_link`** (array)
  * **`enabled`** (boolean) - Enables/Disabled the plugin action link. Default value: `true`.      
  * **`label`** (string) - Label for the plugin action link. Default value: `'Recommendations'`.

### `init()`

Initializes the library.

## Full example:

```php
$cross_selling = new \WPFactory\WPFactory_Cross_Selling\WPFactory_Cross_Selling();
$cross_selling->setup( array(
    'plugin_file_path'   => $this->get_filesystem_path(),
    'plugin_action_link' => array(
        'enabled' => true,
        'label'   => 'More plugins'
    ),
) );
$cross_selling->init();
```
