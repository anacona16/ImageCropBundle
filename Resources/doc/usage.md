Usage
=====

Enable ImageCropBundle
----------------------

After you configure the LiipImagineBundle correctly you must add this lines
to your main application configuration file (usually `config/packages/image_crop.yml`):

```yaml
# config/packages/image_crop.yml
image_crop:
    window: popup
    
    mappings:
        App\Entity\Post:
            filters:
                - post_image
```

This bundle requires that you have a **liip imagine filter**, this filter must have
a thumbnail filter with width and height attributes:

```yaml
# config/packages/liip_imagine.yaml
liip_imagine:
    filter_sets:
        _imagecrop_temp: ~
        post_image:
            filters:
                crop: { size: [128, 128] }
```

This configuration is necessary for set the min/max crop size.
See [Filters at LiipImagineBundle](https://symfony.com/bundles/LiipImagineBundle/current/filters/sizing.html#cropping-images)

**Please be sure that exists a filter called *_imagecrop_temp: ~* this is mandatory**

After this, you must add this lines to your `config/packages/twig.yml`

```yaml
# packages/twig.yml
twig:
    form_themes:
        - '@ImageCrop/Form/fields.html.twig'
```

### Using VichUploaderBundle

```php
$builder
    ->add('title')
    ->add('imageFile', 'Vich\UploaderBundle\Form\Type\VichImageType', [
        'crop' => true,
    ])
;
```

Follow the instructions of [usage of VichUploaderBundle](https://github.com/dustin10/VichUploaderBundle/blob/master/docs/usage.md)

That's all, now you can see a new button for crop the image.

---
Next [Removing orphaned files](orphaned.md)
