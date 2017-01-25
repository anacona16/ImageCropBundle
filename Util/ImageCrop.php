<?php

namespace Anacona16\Bundle\ImageCropBundle\Util;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Liip\ImagineBundle\Imagine\Data\DataManager;
use Liip\ImagineBundle\Imagine\Filter\FilterConfiguration;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Vich\UploaderBundle\Storage\StorageInterface;

class ImageCrop
{
    private $allowedExtensions = array('image/jpeg', 'image/gif', 'image/png', 'image/pjpeg');
    private $inCroppingMode = false;
    private $skipPreview = false;
    private $extraControls = false;

    private $file;
    private $imageStyle;
    private $propertyName = 'none';
    private $styleDestination;
    private $imageWidth;
    private $imageHeight;
    private $originalImageWidth;
    private $originalImageHeight;

    private $isResizable = false;
    private $downscalingAllowed = false;
    private $resizeAspectRatio = false;
    private $width = 0;
    private $startWidth = 0;
    private $height = 0;
    private $startHeight = 0;
    private $xoffset = 0;
    private $yoffset = 0;
    private $scale = 'original';
    private $disableIfNoData = false;
    private $hasSettings = false;

    /**
     * @var object
     */
    private $entity;

    /**
     * @var array
     */
    private $imageCropSettings;

    /**
     * @var StorageInterface
     */
    private $vichStorage;

    /**
     * @var DataManager
     */
    private $imagineDataManager;

    /**
     * @var FilterConfiguration
     */
    private $imagineFilterConfiguration;

    /**
     * @var FilterManager
     */
    private $imagineFilterManager;

    /**
     * @var CacheManager
     */
    private $imagineCacheManager;

    /**
     * Construct ImageCrop.
     *
     * @param array $imageCropSettings
     * @param StorageInterface $storage
     * @param DataManager $dataManager
     * @param FilterConfiguration $filterConfiguration
     * @param FilterManager $filterManager
     * @param CacheManager $cacheManager
     */
    public function __construct(
        array $imageCropSettings,
        StorageInterface $storage,
        DataManager $dataManager,
        FilterConfiguration $filterConfiguration,
        FilterManager $filterManager,
        CacheManager $cacheManager
    )
    {
        $this->skipPreview = true;
        $this->extraControls = false;

        $this->imageCropSettings = $imageCropSettings;
        $this->vichStorage = $storage;
        $this->imagineDataManager = $dataManager;
        $this->imagineFilterConfiguration = $filterConfiguration;
        $this->imagineFilterManager = $filterManager;
        $this->imagineCacheManager = $cacheManager;
    }

    /**
     * Load the imagecrop settings for the given fid or filesource.
     *
     * @param $object
     * @param $propertyName
     *
     * @throws \Exception
     */
    public function loadFile($object, $propertyName)
    {
        $filePath = $this->vichStorage->resolvePath($object, $propertyName);
        $fileUri = $this->vichStorage->resolveUri($object, $propertyName);

        $this->file = new \stdClass();
        $this->file->uri = $fileUri;
        $this->file->filename = basename($filePath);
        $this->file->filemime = mime_content_type($filePath);
        $this->file->filesize = filesize($filePath);
        $this->file->filepath = $filePath;

        if (!$this->file) {
            throw new \Exception('The image to crop was not found.');
        }

        if (!in_array($this->file->filemime, $this->allowedExtensions)) {
            throw new \Exception('The file to crop was not an image.');
        }
    }

    /**
     * Get the current file.
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set the current file
     *
     * @param $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * @return array
     */
    public function getImageCropSettings()
    {
        return $this->imageCropSettings;
    }

    /**
     * @param array $imageCropSettings
     */
    public function setImageCropSettings($imageCropSettings)
    {
        $this->imageCropSettings = $imageCropSettings;
    }

    /**
     * @return object
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param object $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    /**
     * Set the field name from the current imagecrop.
     *
     * @param $propertyName
     */
    public function setPropertyName($propertyName)
    {
        $this->propertyName = $propertyName;
    }

    /**
     * Get the field name from the current imagecrop.
     */
    public function getPropertyName()
    {
        return $this->propertyName;
    }

    /**
     * Is the crop resizable or not.
     */
    public function isResizable()
    {
        return $this->isResizable;
    }

    /**
     * Get the X offset from the current imagecrop object.
     */
    public function getXOffset()
    {
        return $this->xoffset;
    }

    /**
     * Get the X offset from the current imagecrop object.
     */
    public function getYOffset()
    {
        return $this->yoffset;
    }

    /**
     * Get the width from the current crop area.
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Get the height from the current crop area.
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Get the width from the image to crop.
     */
    public function getImageWidth()
    {
        return $this->imageWidth;
    }

    /**
     * Get the original width from the image to crop.
     */
    public function getOriginalImageWidth()
    {
        return $this->originalImageWidth;
    }

    /**
     * Set the scaling width from the image to crop.
     *
     * @param $scale
     */
    public function setScale($scale)
    {
        $this->scale = $scale;
    }

    /**
     * Get the scaling from the image to crop.
     */
    public function getScale()
    {
        return $this->scale;
    }

    /**
     * Get the height from the image to crop.
     */
    public function getImageHeight()
    {
        return $this->imageHeight;
    }

    /**
     * Get the original height from the image to crop.
     */
    public function getOriginalImageHeight()
    {
        return $this->originalImageHeight;
    }

    /**
     * Set the status of cropping mode (true = busy cropping).
     *
     * @param $inCroppingMode
     */
    public function setInCroppingMode($inCroppingMode)
    {
        $this->inCroppingMode = $inCroppingMode;
    }

    /**
     * Get the current value for cropping mode.
     */
    public function getInCroppingMode()
    {
        return $this->inCroppingMode;
    }

    /**
     * Set the current cropped image style.
     *
     * @param $styleName
     *
     * @throws \Exception
     */
    public function setImageStyle($styleName)
    {
        $this->imageStyle = $this->imagineFilterConfiguration->get($styleName);
        $this->imageStyle['name'] = $styleName;

        // add default settings
        foreach ($this->imageStyle['filters'] as $key => $effect) {
            if ($key === 'crop') {
                $this->width = $effect['size'][0];
                $this->startWidth = 0;
                $this->height = $effect['size'][1];
                $this->startHeight = 0;

                $this->xoffset = 0;
                $this->yoffset = 0;
                $this->isResizable = false;
                $this->disableIfNoData = true;
                $this->resizeAspectRatio = 'CROP';
                $this->downscalingAllowed = false;

                /*if ($effect['data']['xoffset']) {
                    $this->xoffset = $effect['data']['xoffset'];
                }

                if ($effect['data']['yoffset']) {
                    $this->yoffset = $effect['data']['yoffset'];
                }

                $this->isResizable = $effect['data']['resizable'];
                $this->disableIfNoData = $effect['data']['disable_if_no_data'];
                $this->resizeAspectRatio = !empty($effect['data']['aspect_ratio']) ? $effect['data']['aspect_ratio'] : FALSE;
                $this->downscalingAllowed = !$effect['data']['downscaling'];*/
                break;
            }
        }
    }

    /**
     * Get the current cropped image style.
     */
    public function getImageStyle()
    {
        return $this->imageStyle;
    }

    /**
     * Set the crop destinations.
     */
    public function setCropDestinations()
    {
        $image = $this->imagineDataManager->find('_imagecrop_temp', $this->file->uri);
        $image = $this->imagineFilterManager->applyFilter($image, '_imagecrop_temp');
        $this->imagineCacheManager->store($image, $this->file->uri, '_imagecrop_temp');

        $this->styleDestination = $this->imagineCacheManager->getBrowserPath($this->file->uri, '_imagecrop_temp');
    }

    /**
     * Get the destination from the image for current style.
     */
    public function getStyleDestination()
    {
        return $this->styleDestination;
    }

    /**
     * Load the crop settings that are available.
     */
    public function loadCropSettings()
    {
        $size = getimagesize($this->file->filepath);
        $this->imageWidth = $this->originalImageWidth = $size[0];
        $this->imageHeight = $this->originalImageHeight = $size[1];

        $settings = false;

        // Load settings
        if ($settings) {
            $this->xoffset = $settings->xoffset;
            $this->yoffset = $settings->yoffset;

            if (!$this->inCroppingMode || $this->isResizable) {
                $this->width = $settings->width;
                $this->height = $settings->height;
            }

            $this->scale = $settings->scale;
            $this->hasSettings = true;
        } else { // Check for default scale.
            if (true === $this->imageCropSettings['scale_default']) {
                $step = $this->imageCropSettings['scale_step'];
                $popup_width = $this->imageCropSettings['popup_width'];
                $popup_height = $this->imageCropSettings['popup_height'] - 50;

                if ($this->extraControls) {
                    $popup_width -= 215;
                }

                $scale_width = $this->originalImageWidth;
                $aspect = $this->originalImageWidth / $this->originalImageHeight;

                if ($step > 0) {
                    $scale_width -= $step;

                    while ($scale_width > $this->width && ($scale_width / $aspect) > $this->height) {
                        $scaled_height = intval($scale_width / $aspect);

                        if ($scaled_height < $popup_height && $scale_width < $popup_width) {
                            $this->scale = $scale_width;
                            break;
                        }

                        $scale_width -= $step;
                    }
                }
            }
        }

        if ($this->scale != 'original') {
            $aspect = $this->originalImageWidth / $this->originalImageHeight;
            $this->imageWidth = $this->scale;
            $this->imageHeight = intval($this->imageWidth / $aspect);
        }

        if ($this->resizeAspectRatio == 'KEEP') {
            $this->resizeAspectRatio = $this->imageWidth / $this->imageHeight;
        } elseif ($this->resizeAspectRatio == 'CROP') {
            $this->resizeAspectRatio = $this->width / $this->height;
        }

        if (!is_numeric($this->xoffset)) {
            switch ($this->xoffset) {
                case 'right':
                    $this->xoffset = $this->imageWidth - $this->width;
                    break;

                case 'center':
                    $this->xoffset = round(($this->imageWidth / 2) - ($this->width / 2));
                    break;

                case 'left':
                default:
                    $this->xoffset = 0;
                    break;
            }
        }

        if (!is_numeric($this->yoffset)) {
            switch ($this->yoffset) {
                case 'bottom':
                    $this->yoffset = $this->imageHeight - $this->height;
                    break;

                case 'center':
                    $this->yoffset = round(($this->imageHeight / 2) - ($this->height / 2));
                    break;

                case 'top':
                default:
                    $this->yoffset = 0;
                    break;
            }
        }
    }

    /**
     * Write the file to crop, and apply all effects, untill the imagecrop effects cropping can be done.
     */
    public function writeCropreadyImage()
    {
        $unset = FALSE;
        $style = $this->imageStyle;

        foreach ($this->imageStyle['filters'] as $key => $effect) {
            if ($key == 'crop') {
                $unset = true;
            }

            unset($style['filters'][$key]);
        }

        if ($this->scale !== 'original') {
            $style['filters']['scale'] = array(
                'dim' => array($this->scale),
            );
        }

        $image = $this->imagineDataManager->find('_imagecrop_temp', $this->file->uri);
        $image = $this->imagineFilterManager->apply($image, $style);
        $this->imagineCacheManager->store($image, $this->file->uri, '_imagecrop_temp');
    }

    /**
     * Write the file to crop, and apply all effects, untill the imagecrop effects cropping can be done.
     *
     * @param $imageCropX
     * @param $imageCropY
     * @param $imageCropScale
     */
    public function writeCropFinalImage($imageCropX, $imageCropY, $imageCropScale)
    {
        $style = $this->imageStyle;

        $cropFilter = $style['filters']['crop'];
        unset($style['filters']['crop']);

        $style['filters']['scale'] = array(
            'dim' => array($imageCropScale),
        );

        $cropFilter['start'][0] = $imageCropX;
        $cropFilter['start'][1] = $imageCropY;

        $style['filters']['crop'] = $cropFilter;

        $image = $this->imagineDataManager->find($style['name'], $this->file->uri);
        $image = $this->imagineFilterManager->apply($image, $style);
        $this->imagineCacheManager->store($image, $this->file->uri, $style['name']);

        $this->imagineCacheManager->remove($this->file->uri, '_imagecrop_temp');
    }

    /**
     * Add all the files for the cropping UI.
     *
     * @param $inCroppingMode
     *
     * @return array
     */
    public function addImagecropUi($inCroppingMode)
    {
        $settings = array();

        // Add crop ui if in cropping mode.
        if ($inCroppingMode) {
            if ($this->isResizable) {
                $settings['resizeAspectRatio'] = $this->resizeAspectRatio;
                $settings['minWidth'] = ($this->downscalingAllowed ? 0 : $this->startWidth);
                $settings['minHeight'] = ($this->downscalingAllowed ? 0 : $this->startHeight);
                $settings['startHeight'] = $this->startHeight;
                $settings['startWidth'] = $this->startWidth;
            }
        }

        return $settings;
    }
}
