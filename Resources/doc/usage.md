Usage
=====

Enable ImageCropBundle
----------------------

After you configure the LiipImagineBundle correctly you must add this lines
to your main application configuration file (usually `app/config/config.yml`):

```yaml
# app/config/config.yml
image_crop:
    window: popup
    
    mappings:
        AppBundle\Entity\Post
            filters:
                - imagine_filter_name
```

This bundle require that you have a liip imagine filter, this filter must have
a thumbnail filter with width and height attributes:

```yaml
# app/config/config.yml
liip_imagine:
    filter_sets:
        post_image:
            filters:
                crop: { size: [128, 128] }
```

This configuration is necessary for set the min/max crop size.

After this, you must add this lines to your `app/config/config.yml`

```yaml
# app/config/config.yml
twig:
    form_themes:
        - "ImageCropBundle:Form:fields.html.twig"
```

```
### Using VichUploaderBundle

```php
$builder
    ->add('title')
    ->add('imageFile', 'Vich\UploaderBundle\Form\Type\VichImageType', array(
        'crop' => true,
    ))
;
```

That's all, now you can see a new button for crop the image.

Next [Removing orphaned files](orphaned.md)
