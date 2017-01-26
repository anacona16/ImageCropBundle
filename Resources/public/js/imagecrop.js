var ImageCrop = ImageCrop || {};

ImageCrop.hasUnsavedChanges = false;
ImageCrop.settings = ImageCrop.settings || {};

(function ($) {
    $(function () {
        $("#imagecrop-style-selection-form #edit-styles").change(function () {
            ImageCrop.changeViewedImage($(this).val());
        });

        if (ImageCrop.settings.cropped) {
            ImageCrop.forceUpdate();
            $('#cancel-crop').html(Drupal.t('Done cropping'));
        }
    });

    /**
     * Event listener, go to the view url when user selected a style.
     */
    ImageCrop.changeViewedImage = function (style_name) {
        document.location = $("#imagecrop-url").val().replace('/style_name/', '/' + style_name + '/');
    };

    /**
     * Refresh the given image
     */
    ImageCrop.refreshImage = function () {
        var source = $(this).attr('src');
        $(this).attr('src', (source + '?time=' + new Date().getTime()));
    }
})(jQuery); 
