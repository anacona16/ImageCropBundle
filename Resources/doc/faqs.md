FAQs
====

This bundle have an AJAX upload utitliy?
----------------------------------------

Not, don't have, this bundle don't upload files itself, so it does not provide an AJAX upload utility.

If you are looking for an AJAX uploader bundle, I recommend this bundle: 
[JbFileUploaderBundle](https://github.com/jbouzekri/FileUploaderBundle)

Why use this bundle and not other like JbFileUploaderBundle?
------------------------------------------------------------

There are a few reasons:

  * This bundle allows to crop already uploaded images.
  * This bundle allows to scale the original image
  * This bundle allows to delete orphaned files.

Why this bundle use VichUploaderBundle?
---------------------------------------

ImageCropBundle use VichUploaderBundle to get information about uploaded files on entity.

If you want to use this bundle without VichUploaderBundle use the first version of the ImageCropBundle, it does not 
require VichUploaderBundle

This bundle is finished?
------------------------

I think it's not, there are a few things to do, but it's working at the moment.

----
[Back to index](index.md)
