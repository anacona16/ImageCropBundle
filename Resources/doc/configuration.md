Configuration
=============

All configurations must be donde under **image_crop** key

**window**

Which type of window you want to use, there are three options:

  * popup (default)
  * bootstrap (you must include boostrap framework on your layout)
  * iframe

**window_width**

Width in pixels of the window choiced, default: 700

**window_height**

Height in pixels of the window choiced, default: 500

**scale_step**

Step size for scale dropdown, default: 50
 
**scale_default**

Autoscale the initial crop to fit the popup, default: false. *Not implemented yed* 

**mappings**

The list mapping, this is an important configuration.

Set the FQCN for each entity where you want to use the ImageCropBundle
   
**filters**

*Under mappings key*. For each entity you can set an array of filters.
    
**orphan_maxage**

Life time of orphaned files (in seconds).

**imagine_cache_dir**

The same value as *cache_dir* config on LiipImagineBundle

----
Next [FAQs](faqs.md)
