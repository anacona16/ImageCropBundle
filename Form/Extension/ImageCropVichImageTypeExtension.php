<?php

namespace Anacona16\Bundle\ImageCropBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ImageCropVichImageTypeExtension extends AbstractTypeExtension
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefined(array('crop'));
    }

    /**
     * Pass the image name to the view.
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (array_key_exists('crop', $options) && true === $options['crop']) {
            $parentData = $form->getParent()->getData();

            $entity = $parentData::class;
            #$meta = $em->getClassMetadata($entity);
            #$identifier = $meta->getSingleIdentifierFieldName();
            #$em->getFieldValue($entity, $field)
            $view->vars['entity_fqcn'] = urlencode($entity);
            # TODO UUID
            $view->vars['entity_id'] = $parentData->getId();
        }
    }

    public static function getExtendedTypes(): iterable
    {
        return [VichImageType::class];
    }
}
