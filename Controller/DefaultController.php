<?php

namespace Anacona16\Bundle\ImageCropBundle\Controller;

use Anacona16\Bundle\ImageCropBundle\Form\Type\ImageCropType;
use Liip\ImagineBundle\Binary\BinaryInterface;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * This action show the button crop.
     *
     * @param Request $request
     * @param $imageName
     * @param $entityName
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buttonCropAction(Request $request, $imageName, $entityName)
    {
        $imageCropMappings = $this->getImageCropMappings();

        $imageCropConfig = $this->getImageCropConfig();
        $imageCropPopup = $imageCropConfig['popup'];
        $imageCropPopupWidth = $imageCropConfig['popup_width'];
        $imageCropPopupHeight = $imageCropConfig['popup_height'];

        return $this->render('ImageCropBundle:Default:button.html.twig', [
            'image_crop_mapping' => key($imageCropMappings),
            'image_crop_popup' => $imageCropPopup,
            'image_crop_popup_width' => $imageCropPopupWidth,
            'image_crop_popup_height' => $imageCropPopupHeight,
            'image_name' => $imageName,
            'entity_name' => $entityName,
        ]);
    }

    /**
     * Show the form for select a mapping.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function cropMappingSelectAction(Request $request)
    {
        $imageName = $request->query->get('image_name', null);
        $entityName = $request->query->get('entity_name', null);

        if (null === $imageName) {
            throw new \InvalidArgumentException('Some required arguments are missing.');
        }

        $imageCropMappings = $this->getImageCropMappingsAsArray();

        $form = $this->container->get('form.factory')->createBuilder('form')
            ->add('mapping', 'choice', array(
                'choices' => $imageCropMappings,
                'label' => 'form.label.mapping',
                'translation_domain' => 'ImageCropBundle',
            ))
            ->add('submit', 'submit', array(
                'label' => 'form.label.submit_mapping',
                'translation_domain' => 'ImageCropBundle',
            ))
            ->getForm();

        $form->handleRequest($request);

        $mapping = null;
        $renderCrop = false;

        if ($form->isValid()) {
            $mapping = $form->get('mapping')->getData();

            $renderCrop = true;
        }

        return $this->render('ImageCropBundle:Default:form_mapping.html.twig', [
            'form' => $form->createView(),
            'renderCrop' => $renderCrop,
            'mapping' => $mapping,
            'imageName' => $imageName,
            'entityName' => $entityName,
        ]);
    }

    /**
     * Render de form with crop enable.
     *
     * @param Request $request
     * @param $useImageCropMapping
     * @param $imageName
     * @param $entityName
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function cropAction(Request $request, $useImageCropMapping, $imageName, $entityName)
    {
        $imageCropMappings = $this->getImageCropMappings();
        $imageCropMapping = $imageCropMappings[$useImageCropMapping];
        $imageCropLiipImagineFilter = $imageCropMapping['liip_imagine_filter'];
        $mappingUriPrefix = $imageCropMapping['uri_prefix'];

        if (is_array($mappingUriPrefix)) {
            $downloadUri = $mappingUriPrefix[$entityName].'/'.$imageName;
        } else {
            $downloadUri = $mappingUriPrefix.'/'.$imageName;
        }

        $lippImagineFilterManager = $this->container->get('liip_imagine.filter.manager');
        $liipImagineFilter = $lippImagineFilterManager->getFilterConfiguration()->get($imageCropLiipImagineFilter);

        list($cropWidth, $cropHeight) = $liipImagineFilter['filters']['thumbnail']['size'];

        // Get the original image data
        $binary = $this->container->get('liip_imagine.data.manager')->find($imageCropLiipImagineFilter, $downloadUri);
        $originalImage = $this->get('liip_imagine')->load($binary->getContent());

        $originalWidth = $originalImage->getSize()->getWidth();
        $originalHeight = $originalImage->getSize()->getHeight();

        // Get scaling options
        $scaling = $this->container->get('anacona16_image_crop.util.class_util')->getScaling(50, $originalWidth, $originalHeight, $cropWidth, $cropHeight);

        $form = $this->createForm(new ImageCropType($scaling, $downloadUri, $originalWidth, $originalHeight), null, array(
            'action' => $this->generateUrl('image_crop_crop_image', array(
                'useImageCropMapping' => $useImageCropMapping,
                'imageName' => $imageName,
            )),
        ));

        $form->handleRequest($request);

        if ($form->isValid()) {
            return $this->processSubmittedForm($form, $lippImagineFilterManager, $binary, $imageCropLiipImagineFilter, $downloadUri);
        }

        return $this->render('ImageCropBundle:Default:index.html.twig', [
            'form' => $form->createView(),
            'image' => $downloadUri,
            'height' => $cropHeight,
            'width' => $cropWidth,
        ]);
    }

    /**
     * Process submitted form.
     *
     * @param Form $form
     * @param FilterManager $lippImagineFilterManager
     * @param BinaryInterface $binary
     * @param $imageCropLiipImagineFilter
     * @param $downloadUri
     * @return JsonResponse
     */
    private function processSubmittedForm(Form $form, FilterManager $lippImagineFilterManager, BinaryInterface $binary, $imageCropLiipImagineFilter, $downloadUri)
    {
        try {
            list($scalingWidth, $scalingHeight) = explode('x', $form->get('scaling')->getData());

            $filteredBinary = $lippImagineFilterManager->applyFilter($binary, $imageCropLiipImagineFilter, [
                'filters' => [
                    'thumbnail' => [
                        'size' => [$scalingWidth, $scalingHeight],
                    ],
                    'crop' => [
                        'start' => [$form->get('cropx')->getData(), $form->get('cropy')->getData()],
                        'size' => [$form->get('cropw')->getData(), $form->get('croph')->getData()],
                    ],
                ],
            ]);

            $this->container->get('liip_imagine.cache.manager')->store($filteredBinary, $downloadUri, $imageCropLiipImagineFilter);

            $message = 'form.submit.message';
        } catch (\Exception $e) {
            $message = 'form.submit.error';
        }

        return new JsonResponse(array('message' => $this->container->get('translator')->trans($message, array(), 'ImageCropBundle')));
    }

    /**
     * Return the bundle configuration.
     *
     * @return mixed
     */
    private function getImageCropConfig()
    {
        $imageCropConfig = $this->container->getParameter('image_crop');

        return $imageCropConfig;
    }

    /**
     * Return the configured mappings.
     *
     * @return mixed
     */
    private function getImageCropMappings()
    {
        $imageCropConfig = $this->getImageCropConfig();

        $imageCropMappings = $imageCropConfig['mappings'];

        return $imageCropMappings;
    }

    /**
     * Return a kery value array with mappings name.
     *
     * @return array
     */
    private function getImageCropMappingsAsArray()
    {
        $imageCropMappings = $this->getImageCropMappings();

        $mappings = array();

        foreach (array_keys($imageCropMappings) as $mapping) {
            $mappings[$mapping] = $mapping;
        }

        return $mappings;
    }
}
