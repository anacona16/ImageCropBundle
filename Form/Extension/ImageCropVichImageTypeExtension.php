<?php

namespace Anacona16\Bundle\ImageCropBundle\Form\Extension;

class ImageCropVichImageTypeExtension extends ImageCropTypeExtension
{
    /**
     * Returns the name of the type being extended.
     *
     * @return string The name of the type being extended
     */
    public function getExtendedType()
    {
        return 'vich_image';
    }
}
