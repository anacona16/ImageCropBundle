Removing orphaned files
=======================

When the crop view is opened and the "Save selection" button is not clicked the changes will not be saved, and the temp 
file generated will be not deleted.

This bundle include a console command to delete the orphaned files.

To delete orphaned files use this command.

```php
php app/console imagecrop:clear-orphans
```

This command will clean all orphaned files older than the **orphan_maxage** value in your configuration.

The configuration assumes that LiipImagineBundle generated image on /web/media/cache dir, if your configuration is 
different, change ImageCropBundle configuration as well.

----
Next [Configuration](configuration.md)
