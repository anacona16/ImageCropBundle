Installation
============

**Requirements**

  * LiipImagineBundle.
  * VichUploaderBundle.

Please install each bundle following their instructions.

Installing ImagecropBundle
--------------------------

Installing ImageCrop requires you to edit two files and execute two console
commands:

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```bash
$ composer require anacona16/imagecrop-bundle
```

This command requires you to have Composer installed globally, as explained
in the [Composer documentation](https://getcomposer.org/doc/00-intro.md).

### Step 2: Enable the Bundle

Then, enable the bundle by adding the following line in the `app/AppKernel.php`
file of your Symfony application:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Anacona16\Bundle\ImageCropBundle\ImageCropBundle(),
        );
    }

    // ...
}
```

### Step 3: Load the Routes of the Bundle

Open your main routing configuration file (usually `app/config/routing.yml`)
and copy the following four lines at the very beginning of it:

```yaml
# app/config/routing.yml
_imagecrop_bundle:
    resource: @ImageCropBundle/Resources/config/routing.yml
```

### Step 4: Prepare the Web Assets of the Bundle

This bundle includes some javascript files. Execute the following
command to make those assets available in your Symfony application:

```cli
php app/console assets:install --symlink
```

That's it! Now everything is ready to create your first image crop mapping.

---
Next [usage](usage.md)
