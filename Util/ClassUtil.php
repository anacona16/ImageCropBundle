<?php

namespace Anacona16\Bundle\ImageCropBundle\Util;

class ClassUtil
{
    public function __construct(private array $imageCropSettings)
    {
    }

    /**
     * Return list of styles for use on form.
     */
    public function getStyles($entity): array
    {
        $styles = array();

        foreach ($this->imageCropSettings['mappings'][$entity]['filters'] as $key => $filter) {
            $styles[$filter] = $filter;
        }

        return $styles;
    }

    /**
     * Return the scale options.
     */
    public function getScaling(int $originalImageWidth, int $originalImageHeight, int $cropWidth, int $cropHeight): array
    {
        $step = $this->imageCropSettings['scale_step'];
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

        return $options;
    }
}
