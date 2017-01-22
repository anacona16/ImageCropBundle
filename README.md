ImageCrop
=========

<img src="https://raw.githubusercontent.com/anacona16/ImageCropBundle/master/Resources/doc/images/image_crop.png" width="50%" alt="ImageCrop" title="ImageCrop" align="right" />

ImageCrop lets you crop images in Symfony applications, this bundle add a new
form file field extension.

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/ac8e3237-f910-46b5-9fcc-3afe329496eb/mini.png)](https://insight.sensiolabs.com/projects/ac8e3237-f910-46b5-9fcc-3afe329496eb)

**This bundle don't have an upload utility.**

**Features**

  * Easy use, just add a new parameter to your form class.
  * Integration with standard form file type.
  * Integration with well known uploader bundles.

**Requirements**

  * Symfony 2.3+ applications (Silex not supported).
  * LiipImagineBundle.
  
Please install each bundle following their instructions.

Installation
------------

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
imagecrop_bundle:
    resource: @ImageCropBundle/Resources/config/routing.yml
```

### Step 4: Prepare the Web Assets of the Bundle

This bundle includes the JCrop JavaScript library. Execute the following
command to make those assets available in your Symfony application:

```cli
php app/console assets:install --symlink
```

That's it! Now everything is ready to create your first image crop mapping.

Enable ImageCropBundle
----------------------

After you configure the LiipImagineBundle correctly you must add this lines
to your main application configuration file (usually `app/config/config.yml`):

```yaml
# app/config/config.yml
image_crop:
    popup: popup
    mappings:
        post_image:
            uri_prefix: /uploads/images/post # Relative to web directory
            liip_imagine_filter: post_image
```

If you want to use a mapping with more than one **uri_prefix**, you can pass an array, like this:

```yaml
# app/config/config.yml
image_crop:
    popup: popup
    mappings:
        post_image:
            uri_prefix:
                Post: /uploads/images/post # Relative to web directory
                User: /uploads/images/user # Relative to web directory
            liip_imagine_filter: post_image
```

Where **Post** and **User** correspond to the Entity name.

This bundle require that you have a liip imagine filter, this filter must have
a thumbnail filter with width and height attributes:

```yaml
# app/config/config.yml
liip_imagine:
    filter_sets:
        post_image:
            filters:
                thumbnail: { size: [128, 128], mode: outbound }
```

This configuration is necessary for set the min/max crop size.

After this, you must add this lines to your `app/config/config.yml`

```yaml
# app/config/config.yml
twig:
    form_themes:
        - "ImageCropBundle:Form:fields.html.twig"
```

### Using standard file type

In your form type class you must add a new field:

```php
$builder
    ->add('title')
    ->add('imageFile', 'file', array(
        'crop_property' => 'image',
    ))
;
```
### Using VichUploaderBundle

```php
$builder
    ->add('title')
    ->add('imageFile', 'vich_image', array(
        'crop_property' => 'image',
    ))
;
```

That's all, now you can see a new button for crop the image.

-----

License
-------

This bundle is published under the [MIT License](LICENSE)
