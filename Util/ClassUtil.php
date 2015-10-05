<?php

namespace Anacona16\Bundle\ImageCropBundle\Util;

class ClassUtil
{
    /**
     * Return the scale options.
     *
     * @param $step
     * @param $originalImageWidth
     * @param $originalImageHeight
     * @param $cropWidth
     * @param $cropHeight
     * @return array
     */
    public function getScaling($step, $originalImageWidth, $originalImageHeight, $cropWidth, $cropHeight)
    {
        $image_width = $scale_width = $originalImageWidth;
        $image_height = $originalImageHeight;
        $aspect = $image_width / $image_height;
        $crop_width = $cropWidth;
        $crop_height = $cropHeight;

        $options = array();

        if ($step > 0) {
            $options[$image_width . 'x' . $image_height] = $image_width . ' x ' . $image_height . 'px (Original)';
            $scale_width -= $step;

            while ($scale_width > $crop_width && ($scale_width / $aspect) > $crop_height) {
                $scaled_height = intval($scale_width / $aspect);
                $options[$scale_width . 'x' . $scaled_height] = $scale_width . ' x ' . $scaled_height . 'px (' . round((($scale_width / $image_width) * 100), 2) . '%)';
                $scale_width -= $step;
            }
        }

        // Set the smallest scaling option to match the width of the crop (if it fits).
        $cropped_height = intval($crop_width / $aspect);
        $crop_width_with_border = $crop_width + 2;

        if ($crop_width != ($scale_width + $step) && $cropped_height >= $crop_height) {
            $options[$crop_width_with_border . 'x' . $cropped_height] = $crop_width_with_border . ' x ' . $cropped_height . 'px (' . round((($scale_width / $image_width) * 100), 2) . '%)';
        }

        return $options;
    }
}