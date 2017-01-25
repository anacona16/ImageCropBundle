<?php

namespace Anacona16\Bundle\ImageCropBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ImageCropVichImageTypeExtension extends AbstractTypeExtension
{
    /**
     * Add the crop_property option.
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefined(array('crop'));
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
        if (array_key_exists('crop', $options) && true === $options['crop']) {
            $parentData = $form->getParent()->getData();

            $entity = get_class($parentData);

            $view->vars['entity_fqcn'] = urlencode($entity);
            $view->vars['entity_id'] = $parentData->getId();
        }
    }

    /**
     * Returns the name of the type being extended.
     *
     * @return string The name of the type being extended
     */
    public function getExtendedType()
    {
        return VichImageType::class;
    }
}
