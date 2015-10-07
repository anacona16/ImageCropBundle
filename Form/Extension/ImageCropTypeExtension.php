<?php

namespace Anacona16\Bundle\ImageCropBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;

class ImageCropTypeExtension extends AbstractTypeExtension
{
    /**
     * Add the crop_property option.
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefined(array('crop_property'));
    }

    /**
     * Pass the image name to the view.
     *
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (array_key_exists('crop_property', $options)) {
            $parentData = $form->getParent()->getData();

            if (null !== $parentData) {
                $accessor = PropertyAccess::createPropertyAccessor();
                $imageName = $accessor->getValue($parentData, $options['crop_property']);
            } else {
                $imageName = null;
            }

            // set an "image_url" variable that will be available when rendering this field
            $view->vars['image_crop_name'] = $imageName;
        }
    }

    /**
     * Returns the name of the type being extended.
     *
     * @return string The name of the type being extended
     */
    public function getExtendedType()
    {
        return 'file';
    }
}
