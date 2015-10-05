ImageCrop
=========

<img src="https://photos-3.dropbox.com/t/2/AACDHn2qvBfJed2s_tkSCSXe1lhxkG2i0zv1H1tCo1oYDQ/12/71119624/png/32x32/1/1443830400/0/2/Screenshot%202015-10-02%2017.34.43.png/CIjm9CEgASACIAMgBiAHKAEoBw/QDhoWgNmG-tjsZTa8nons1jB6fgJlpWj7k2aZhWq5hQ?size=1600x1200&size_mode=2" alt="ImageCrop" title="ImageCrop" align="right" />

ImageCrop lets you crop images in Symfony applications, this bundle add a new
form field type.

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
    popup: bootstrap
    mappings:
        post_image:
            property: image # The property entity that have the image name
            uri_prefix: /uploads/images/post # Relative to web directory
            liip_imagine_filter: post_image
```

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

In your form type class you must add a new field:

```php
$builder
    ->add('title', null, array('label' => 'label.title'))
    ->add('image_crop', 'anacona16_image_crop', array(
        'image_crop_mapping' => 'post_image',
        'required' => false,
        'mapped' => false,
    ))
;
```

That's all, now you can see a new button for crop the image.

-----

License
-------

This bundle is published under the [MIT License](LICENSE.md)
