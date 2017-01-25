(function ($) {
    ImageCrop.openPopup = function(link, width, height) {
        var url = $(link).attr('href');
        window.open(url, 'imagecrop', 'menubar=0,scrollbars=1,resizable=1,width=' + width + ',height=' + height + "'");
    };
})(jQuery);
