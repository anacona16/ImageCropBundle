<?php

namespace Anacona16\Bundle\ImageCropBundle\Form\Type;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;

class ImageCropFieldType extends AbstractType
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * ImageCropTypeExtension constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'image_crop_mapping' => null,
        ));

        $resolver->setRequired('image_crop_mapping');
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (array_key_exists('image_crop_mapping', $options)) {
            $parentData = $form->getParent()->getData();

            if (null !== $parentData) {
                $imageCropMappingName = $options['image_crop_mapping'];
                $imageCropConfig = $this->container->getParameter('image_crop');

                $fieldImageCropConfig = $imageCropConfig['mappings'][$imageCropMappingName];

                $accessor = PropertyAccess::createPropertyAccessor();
                $imageName = $accessor->getValue($parentData, $fieldImageCropConfig['property']);

                $view->vars['image_crop_mapping'] = $imageCropMappingName;
                $view->vars['image_crop_popup'] = $imageCropConfig['popup'];
                $view->vars['image_crop_popup_width'] = $imageCropConfig['popup_width'];
                $view->vars['image_crop_popup_height'] = $imageCropConfig['popup_height'];
            } else {
                $imageName = null;
            }

            // set an "image_name" variable that will be available when rendering this field
            $view->vars['image_name'] = $imageName;
        }
    }

    public function getParent()
    {
        return 'form';
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'anacona16_image_crop';
    }
}
