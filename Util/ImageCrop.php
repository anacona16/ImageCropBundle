<?php

namespace Anacona16\Bundle\ImageCropBundle\Util;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Liip\ImagineBundle\Imagine\Data\DataManager;
use Liip\ImagineBundle\Imagine\Filter\FilterConfiguration;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Vich\UploaderBundle\Storage\StorageInterface;

class ImageCrop
{
    private array $allowedExtensions = array('image/jpeg', 'image/gif', 'image/png', 'image/pjpeg');
    private bool $inCroppingMode = false;
    private bool $skipPreview = true;
    private bool $extraControls = false;

    private $file;
    private $imageStyle;
    private string $propertyName = 'none';
    private $styleDestination;
    private int $imageWidth;
    private int $imageHeight;
    private int $originalImageWidth;
    private int $originalImageHeight;

    private bool $isResizable = false;
    private bool $downscalingAllowed = false;
    private bool $resizeAspectRatio = false;
    private int $width = 0;
    private int $startWidth = 0;
    private int $height = 0;
    private int $startHeight = 0;
    private int $xoffset = 0;
    private int $yoffset = 0;
    private string $scale = 'original';
    private bool $disableIfNoData = false;
    private bool $hasSettings = false;

    /**
     * @var object
     */
    private $entity;

    public function __construct(
        private array $imageCropSettings,
        private StorageInterface $storage,
        private DataManager $dataManager,
        private FilterConfiguration $filterConfiguration,
        private FilterManager $filterManager,
        private CacheManager $cacheManager
    )
    {
    }

    /**
     * Load the imagecrop settings for the given fid or filesource.
     *
     * @throws \Exception
     */
    public function loadFile($object, string $propertyName)
    {
        $filePath = $this->storage->resolvePath($object, $propertyName);
        $fileUri = $this->storage->resolveUri($object, $propertyName);

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

    public function getFile()
    {
        return $this->file;
    }

    public function setFile($file)
    {
        $this->file = $file;
    }

    public function getImageCropSettings(): array
    {
        return $this->imageCropSettings;
    }

    public function setImageCropSettings(array $imageCropSettings)
    {
        $this->imageCropSettings = $imageCropSettings;
    }

    public function getEntity(): object
    {
        return $this->entity;
    }

    public function setEntity(object $entity)
    {
        $this->entity = $entity;
    }

    /**
     * Set the field name from the current imagecrop.
     */
    public function setPropertyName(string $propertyName)
    {
        $this->propertyName = $propertyName;
    }

    public function getPropertyName(): string
    {
        return $this->propertyName;
    }

    public function isResizable(): bool
    {
        return $this->isResizable;
    }

    public function getXOffset(): int
    {
        return $this->xoffset;
    }

    public function getYOffset(): int
    {
        return $this->yoffset;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getImageWidth(): int
    {
        return $this->imageWidth;
    }

    public function getOriginalImageWidth(): int
    {
        return $this->originalImageWidth;
    }

    public function setScale(string $scale)
    {
        $this->scale = $scale;
    }

    public function getScale(): string
    {
        return $this->scale;
    }

    public function getImageHeight(): int
    {
        return $this->imageHeight;
    }

    public function getOriginalImageHeight(): int
    {
        return $this->originalImageHeight;
    }

    /**
     * Set the status of cropping mode (true = busy cropping).
     */
    public function setInCroppingMode(bool $inCroppingMode)
    {
        $this->inCroppingMode = $inCroppingMode;
    }

    public function getInCroppingMode(): bool
    {
        return $this->inCroppingMode;
    }

    /**
     * Set the current cropped image style.
     *
     * @throws \Exception
     */
    public function setImageStyle(string $styleName)
    {
        $this->imageStyle = $this->filterConfiguration->get($styleName);
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

    public function getImageStyle()
    {
        return $this->imageStyle;
    }

    public function setCropDestinations()
    {
        $image = $this->dataManager->find('_imagecrop_temp', $this->file->uri);
        $image = $this->filterManager->applyFilter($image, '_imagecrop_temp');
        $this->cacheManager->store($image, $this->file->uri, '_imagecrop_temp');

        $this->styleDestination = $this->cacheManager->getBrowserPath($this->file->uri, '_imagecrop_temp');
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
            $this->xoffset = match ($this->xoffset) {
                'right' => $this->imageWidth - $this->width,
                'center' => round(($this->imageWidth / 2) - ($this->width / 2)),
                default => 0,
            };
        }

        if (!is_numeric($this->yoffset)) {
            $this->yoffset = match ($this->yoffset) {
                'bottom' => $this->imageHeight - $this->height,
                'center' => round(($this->imageHeight / 2) - ($this->height / 2)),
                default => 0,
            };
        }
    }

    /**
     * Write the file to crop, and apply all effects, until the imagecrop effects cropping can be done.
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
            $style['filters']['scale'] = [
                'dim' => [$this->scale],
            ];
        }

        $image = $this->dataManager->find('_imagecrop_temp', $this->file->uri);
        $image = $this->filterManager->apply($image, $style);
        $this->cacheManager->store($image, $this->file->uri, '_imagecrop_temp');
    }

    /**
     * Write the file to crop, and apply all effects, until the imagecrop effects cropping can be done.
     */
    public function writeCropFinalImage(int $imageCropX, int $imageCropY, $imageCropScale)
    {
        $style = $this->imageStyle;

        $cropFilter = $style['filters']['crop'];
        unset($style['filters']['crop']);

        $style['filters']['scale'] = [
            'dim' => [$imageCropScale],
        ];

        $cropFilter['start'][0] = $imageCropX;
        $cropFilter['start'][1] = $imageCropY;

        $style['filters']['crop'] = $cropFilter;

        $image = $this->dataManager->find($style['name'], $this->file->uri);
        $image = $this->filterManager->apply($image, $style);
        $this->cacheManager->store($image, $this->file->uri, $style['name']);

        $this->cacheManager->remove($this->file->uri, '_imagecrop_temp');
    }

    /**
     * Add all the files for the cropping UI.
     */
    public function addImagecropUi(bool $inCroppingMode): array
    {
        $settings = [];

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
