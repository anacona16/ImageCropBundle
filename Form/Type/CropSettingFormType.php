<?php

namespace Anacona16\Bundle\ImageCropBundle\Form\Type;

use Anacona16\Bundle\ImageCropBundle\Util\ImageCrop;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CropSettingFormType extends AbstractType
{
    /**
     * Add the image_path option
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefined(array('imageCrop'));
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /**
         * @var $imageCrop ImageCrop
         */
        $imageCrop = $options['imageCrop'];

        $builder
            ->add('image-crop-x', HiddenType::class, array(
                'data' => $imageCrop->getXOffset(),
            ))
            ->add('image-crop-y', HiddenType::class, array(
                'data' => $imageCrop->getYOffset(),
            ))
            ->add('image-crop-width', HiddenType::class, array(
                'data' => $imageCrop->getWidth(),
            ))
            ->add('image-crop-height', HiddenType::class, array(
                'data' => $imageCrop->getHeight(),
            ))
            ->add('image-crop-scale', HiddenType::class, array(
                'data' => $imageCrop->getScale(),
            ))
            ->add('entity-id', HiddenType::class, array(
                'data' => $imageCrop->getEntity()->getId(),
            ))
            ->add('entity-fqcn', HiddenType::class, array(
                'data' => get_class($imageCrop->getEntity()),
            ))
            ->add('style', HiddenType::class, array(
                'data' => $imageCrop->getImageStyle()['name'],
            ))
            ->add('style-destination', HiddenType::class, array(
                'data' => $imageCrop->getFile()->uri,
            ))
            ->add('temp-style-destination', HiddenType::class, array(
                'data' => $imageCrop->getStyleDestination(),
            ))
            ->add('submit', SubmitType::class, array(
                'label' => 'form.label.save',
                'translation_domain' => 'ImageCropBundle',
            ))
        ;
    }
}
