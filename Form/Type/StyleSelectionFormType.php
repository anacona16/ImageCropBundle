<?php

namespace Anacona16\Bundle\ImageCropBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StyleSelectionFormType extends AbstractType
{
    /**
     * Add the image_path option
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefined(array(
            'defaultStyle',
            'styles',
            'imageCropUrl',
            'action'
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('styles', ChoiceType::class, array(
                'label' => 'crop' === $options['action'] ? 'form.label.style_crop_form' : 'form.label.style_overview_form',
                'choices' => $options['styles'],
                'data' => $options['defaultStyle'],
                'translation_domain' => 'ImageCropBundle',
            ))
            ->add('imageCropUrl', HiddenType::class, array(
                'data' => $options['imageCropUrl'],
            ))
        ;
    }
}
