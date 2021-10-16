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
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefined(array('imageCrop'));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var ImageCrop $imageCrop */
        $imageCrop = $options['imageCrop'];

        $builder
            ->add('image-crop-x', HiddenType::class, [
                'data' => $imageCrop->getXOffset(),
            ])
            ->add('image-crop-y', HiddenType::class, [
                'data' => $imageCrop->getYOffset(),
            ])
            ->add('image-crop-width', HiddenType::class, [
                'data' => $imageCrop->getWidth(),
            ])
            ->add('image-crop-height', HiddenType::class, [
                'data' => $imageCrop->getHeight(),
            ])
            ->add('image-crop-scale', HiddenType::class, [
                'data' => $imageCrop->getScale(),
            ])
            ->add('entity-id', HiddenType::class, [
                'data' => $imageCrop->getEntity()->getId(),
            ])
            ->add('entity-fqcn', HiddenType::class, [
                'data' => get_class($imageCrop->getEntity()),
            ])
            ->add('style', HiddenType::class, [
                'data' => $imageCrop->getImageStyle()['name'],
            ])
            ->add('style-destination', HiddenType::class, [
                'data' => $imageCrop->getFile()->uri,
            ])
            ->add('temp-style-destination', HiddenType::class, [
                'data' => $imageCrop->getStyleDestination(),
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'form.label.save',
                'translation_domain' => 'ImageCropBundle',
            ])
        ;
    }
}
