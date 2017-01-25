ImageCrop.cropUi = ImageCrop.cropUi || {};

(function ($) {
    $(function () {
        ImageCrop.cropUi.initControls();
        ImageCrop.cropUi.initScaling();
    });

    ImageCrop.imageCropWidthField = null;
    ImageCrop.imageCropHeightField = null;
    ImageCrop.imageCropXField = null;
    ImageCrop.imageCropYField = null;
    ImageCrop.imageCropScaleField = null;
    ImageCrop.resizeMe = null;

    /**
     * Init the controls.
     */
    ImageCrop.cropUi.initControls = function () {
        // Store input fields
        var $imagecropform = $('#imagecrop-crop-settings-form');

        ImageCrop.imageCropWidthField = $('input[name="crop_setting_form[image-crop-width]"]', $imagecropform);
        ImageCrop.imageCropHeightField = $('input[name="crop_setting_form[image-crop-height]"]', $imagecropform);
        ImageCrop.imageCropXField = $('input[name="crop_setting_form[image-crop-x]"]', $imagecropform);
        ImageCrop.imageCropYField = $('input[name="crop_setting_form[image-crop-y]"]', $imagecropform);
        ImageCrop.imageCropScaleField = $('input[name="crop_setting_form[image-crop-scale]"]', $imagecropform);

        // Event listeners on input fields
        ImageCrop.imageCropWidthField.change(ImageCrop.cropUi.sizeListener);
        ImageCrop.imageCropHeightField.change(ImageCrop.cropUi.sizeListener);
        ImageCrop.imageCropXField.change(ImageCrop.cropUi.positionListener);
        ImageCrop.imageCropYField.change(ImageCrop.cropUi.positionListener);

        ImageCrop.resizeMe = $('#resizeMe');
        ImageCrop.cropUi.cropContainer = $('#image-crop-container');

        if (ImageCrop.resizeMe.resizable) {
            ImageCrop.resizeMe.resizable({
                containment: ImageCrop.cropUi.cropContainer,
                aspectRatio: ImageCrop.settings.resizeAspectRatio,
                autohide: true,
                handles: 'n, e, s, w, ne, se, sw, nw',
                resize: ImageCrop.cropUi.resizeListener
            });
        }

        ImageCrop.resizeMe.draggable({
            cursor: 'move',
            containment: ImageCrop.cropUi.cropContainer,
            drag: ImageCrop.cropUi.dragListener
        });

        ImageCrop.cropUi.cropContainer.css({opacity: 0.5});
        ImageCrop.resizeMe.css({position: 'absolute'});

        var leftpos = ImageCrop.imageCropXField.val();
        var toppos = ImageCrop.imageCropYField.val();

        ImageCrop.resizeMe.css({backgroundPosition: '-' + leftpos + 'px -' + toppos + 'px'});
        ImageCrop.resizeMe.width(ImageCrop.imageCropWidthField.val() + 'px');
        ImageCrop.resizeMe.height($('#edit-image-crop-height', '#imagecrop-crop-settings-form').val() + 'px');
        ImageCrop.resizeMe.css({top: toppos + 'px'});
        ImageCrop.resizeMe.css({left: leftpos + 'px'});
    };

    /**
     * Init the scaling dropdown.
     */
    ImageCrop.cropUi.initScaling = function () {
        var $imagecropform = $('#imagecrop-crop-settings-form');

        ImageCrop.entityID = $('input[name="crop_setting_form[entity-id]"]', $imagecropform).val();
        ImageCrop.entityFQCN = $('input[name="crop_setting_form[entity-fqcn]"]', $imagecropform).val();
        ImageCrop.style = $('input[name="crop_setting_form[style]"]', $imagecropform).val();
        ImageCrop.cropFile = $('input[name="crop_setting_form[temp-style-destination]"]', $imagecropform).val();

        $('#edit-scaling', '#imagecrop-scale-settings-form').bind('change', ImageCrop.cropUi.scaleImage);

        ImageCrop.cropUi.cropWrapper = $('#imagecrop-crop-wrapper');
    };

    /**
     * Listener on the jquery ui resize plugin.
     */
    ImageCrop.cropUi.resizeListener = function (e, ui) {
        var curr_width = parseInt(ImageCrop.resizeMe.width());
        var curr_height = parseInt(ImageCrop.resizeMe.height());
        ImageCrop.imageCropWidthField.val(curr_width);
        ImageCrop.imageCropHeightField.val(curr_height);

        ImageCrop.cropUi.validateSizeChanges(curr_width, curr_height, false);
    };

    /**
     * Listener on the jquery ui draggable plugin.
     */
    ImageCrop.cropUi.dragListener = function (e, ui) {
        ImageCrop.cropUi.setBackgroundPosition(ui.position.left, ui.position.top, true);
    };

    /**
     * Listener on the X and Y field.
     */
    ImageCrop.cropUi.positionListener = function () {
        var x = parseInt(ImageCrop.imageCropXField.val());
        var y = parseInt(ImageCrop.imageCropYField.val());
        var changeInput = false;

        // Left must be integer
        if (isNaN(x)) {
            var position = ImageCrop.resizeMe.position();
            ImageCrop.imageCropXField.val(position.left);
            return;
        }

        // Top must be integer
        if (isNaN(y)) {
            var position = ImageCrop.resizeMe.position();
            ImageCrop.imageCropYField.val(position.top);
            return;
        }

        // X position can not be higher then width from container - width from cropping.
        var max_x = ImageCrop.cropUi.cropWrapper.width() - ImageCrop.imageCropWidthField.val();
        if (x > max_x) {
            x = max_x;
            changeInput = true;
        }

        // Y position can not be higher then height from container - height from cropping.
        var max_y = ImageCrop.cropUi.cropWrapper.width() - ImageCrop.imageCropWidthField.val();
        if (y > max_x) {
            y = max_y;
            changeInput = true;
        }

        ImageCrop.resizeMe.css({'left': x, 'top': y});
        ImageCrop.cropUi.setBackgroundPosition(x, y, changeInput);
    };

    /**
     * Set the current background position from the cropping area.
     */
    ImageCrop.cropUi.setBackgroundPosition = function (x, y, changeInput) {
        ImageCrop.resizeMe.css({'background-position': '-' + x + 'px -' + y + 'px'});
        if (changeInput) {
            ImageCrop.imageCropXField.val(x);
            ImageCrop.imageCropYField.val(y);
        }
    };

    /**
     * Event listener on the width / height field.
     */
    ImageCrop.cropUi.sizeListener = function () {
        var curr_height = parseInt(ImageCrop.imageCropHeightField.val());
        var curr_width = parseInt(ImageCrop.imageCropWidthField.val());

        // Height must be integer
        if (isNaN(curr_height)) {
            ImageCrop.imageCropHeightField.val(ImageCrop.resizeMe.height());
            return;
        }

        // Width must be integer
        if (isNaN(curr_width)) {
            ImageCrop.imageCropWidthField.val(ImageCrop.resizeMe.width());
            return;
        }

        ImageCrop.resizeMe.height(curr_height);
        ImageCrop.resizeMe.width(curr_width);
        ImageCrop.cropUi.validateSizeChanges(parseInt(curr_width), parseInt(curr_height), true);
    };

    /**
     * Validate the new width / height and update if needed.
     */
    ImageCrop.cropUi.validateSizeChanges = function (curr_width, curr_height, event) {
        var width_changed = false;
        var height_changed = false;

        if (curr_width < parseInt(ImageCrop.settings.minWidth)) {
            width_changed = true;
            curr_width = ImageCrop.settings.minWidth;
            if (ImageCrop.settings.resizeAspectRatio !== false) {
                height_changed = true;
                curr_height = ImageCrop.settings.minWidth / ImageCrop.settings.resizeAspectRatio;
            }
        }

        if (curr_height < parseInt(ImageCrop.settings.minHeight)) {
            curr_height = ImageCrop.settings.minHeight;
            height_changed = true;
            if (ImageCrop.settings.resizeAspectRatio !== false) {
                width_changed = true;
                curr_width = ImageCrop.settings.minHeight * ImageCrop.settings.resizeAspectRatio;
            }
        }

        if (curr_height > ImageCrop.cropUi.cropContainer.height()) {
            height_changed = true;
            ImageCrop.resizeMe.css({top: '0'});
            if (ImageCrop.settings.resizeAspectRatio !== false) {
                width_changed = true;
                curr_width = ImageCrop.settings.minHeight * ImageCrop.settings.resizeAspectRatio;
            }
            curr_height = ImageCrop.cropUi.cropContainer.height();
        }

        if (curr_width > ImageCrop.cropUi.cropContainer.width()) {
            width_changed = true;
            curr_width = ImageCrop.cropUi.cropContainer.width();
            ImageCrop.resizeMe.css({left: '0'});
            if (ImageCrop.settings.resizeAspectRatio !== false) {
                height_changed = true;
                curr_height = ImageCrop.settings.minWidth / ImageCrop.settings.resizeAspectRatio;
            }
        }

        if (width_changed || event) {
            ImageCrop.imageCropWidthField.val(curr_width);
            ImageCrop.resizeMe.width(curr_width);
        }

        if (height_changed || event) {
            ImageCrop.imageCropHeightField.val(curr_height);
            ImageCrop.resizeMe.height(curr_height);
        }

        if (curr_width < ImageCrop.settings.startWidth || curr_height < ImageCrop.settings.startHeight) {
            ImageCrop.resizeMe.addClass('boxwarning');
        }
        else {
            ImageCrop.resizeMe.removeClass('boxwarning');
        }

        var pos = ImageCrop.resizeMe.position();
        var left = (pos.left > 0) ? pos.left : 0;
        var top = (pos.top > 0) ? pos.top : 0;
        ImageCrop.resizeMe.css({backgroundPosition: ('-' + left + 'px -' + top + 'px')});
        ImageCrop.imageCropXField.val(left);
        ImageCrop.imageCropYField.val(top);
    };

    /**
     * Scale the image to the selected width / height.
     */
    ImageCrop.cropUi.scaleImage = function () {
        var dimensions = $(this).val().split('x');

        if (dimensions.length != 2) {
            return false;
        }

        var imagecropData = {
            'entityID': ImageCrop.entityID,
            'entityFQCN': ImageCrop.entityFQCN,
            'style': ImageCrop.style,
            'scale': dimensions[0]
        };

        $.ajax({
            url: ImageCrop.settings.manipulationUrl,
            data: imagecropData,
            type: 'post',
            success: function () {
                ImageCrop.hasUnsavedChanges = true;

                // force new backgrounds and width / height
                var background = ImageCrop.cropFile + '?time=' + new Date().getTime();

                ImageCrop.cropUi.cropContainer.css({
                    'background-image': 'url(' + background + ')',
                    'width': dimensions[0],
                    'height': dimensions[1]
                });

                ImageCrop.cropUi.cropWrapper.css({
                    'width': dimensions[0],
                    'height': dimensions[1]
                });

                // force background-size on resizeMe's background image as well.
                ImageCrop.resizeMe.css({
                    'background-size': dimensions[0] + 'px ' + dimensions[1] + 'px ',
                    '-moz-background-size': dimensions[0] + 'px ' + dimensions[1] + 'px ',
                    '-o-background-size': dimensions[0] + 'px ' + dimensions[1] + 'px ',
                    '-webkit-background-size': dimensions[0] + 'px ' + dimensions[1] + 'px '
                });

                // make resize smaller when new image is smaller
                if (ImageCrop.resizeMe.height() > dimensions[1]) {
                    ImageCrop.resizeMe.height(dimensions[1]);
                }
                if (ImageCrop.resizeMe.width() > dimensions[0]) {
                    ImageCrop.resizeMe.width(dimensions[0]);
                }

                ImageCrop.resizeMe.css({'background-image': 'url(' + background + ')'});
                ImageCrop.imageCropScaleField.val(dimensions[0]);
            }
        })
    }
})(jQuery); 
