<?php

namespace Anacona16\Bundle\ImageCropBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ScalingSettingFormType extends AbstractType
{
    /**
     * Add the image_path option
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefined(array('scaling'));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $optionsScaling = $options['scaling'];

        $builder
            ->add('scaling', ChoiceType::class, array(
                'label' => 'form.label.scaling',
                'choices' => $optionsScaling,
                'translation_domain' => 'ImageCropBundle',
            ))
        ;
    }
}
