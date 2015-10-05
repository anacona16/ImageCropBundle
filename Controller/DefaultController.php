<?php

namespace Anacona16\Bundle\ImageCropBundle\Controller;

use Anacona16\Bundle\ImageCropBundle\Form\ImageCropType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        $imageCropMapping   = $request->query->get('image_crop_mapping', null);
        $imageName          = $request->query->get('image_name', null);

        if (null === $imageCropMapping || null === $imageName) {
            throw new \InvalidArgumentException('Some required arguments are missing.');
        }

        $imageCropConfig = $this->container->getParameter('image_crop');

        $imageCropMappings = $imageCropConfig['mappings'];
        $imageCropMapping = $imageCropMappings[$imageCropMapping];
        $imageCropLiipImagineFilter = $imageCropMapping['liip_imagine_filter'];

        $downloadUri = $imageCropMapping['uri_prefix'] . '/' . $imageName;

        $lippImagineFilterMananger = $this->container->get('liip_imagine.filter.manager');
        $liipImagineFilter = $lippImagineFilterMananger->getFilterConfiguration()->get($imageCropLiipImagineFilter);

        list($cropWidth, $cropHeight) = $liipImagineFilter['filters']['thumbnail']['size'];

        // Get the original image data
        $binary = $this->container->get('liip_imagine.data.manager')->find($imageCropLiipImagineFilter, $downloadUri);
        $originalImage = $this->get('liip_imagine')->load($binary->getContent());

        $originalWidth = $originalImage->getSize()->getWidth();
        $originalHeight = $originalImage->getSize()->getHeight();

        // Get scaling options
        $scaling = $this->container->get('anacona16_image_crop.util.class_util')->getScaling(50, $originalWidth, $originalHeight, $cropWidth, $cropHeight);

        $form = $this->createForm(new ImageCropType($scaling, $downloadUri, $originalWidth, $originalHeight));

        $form->handleRequest($request);

        if ($form->isValid()) {
            list($scalingWidth, $scalingHeight) = explode('x', $form->get('scaling')->getData());

            $filteredBinary = $lippImagineFilterMananger->applyFilter($binary, $imageCropLiipImagineFilter, [
                'filters' => [
                    'thumbnail' => [
                        'size' => [$scalingWidth, $scalingHeight],
                    ],
                    'crop' => [
                        'start' => [$form->get('cropx')->getData(), $form->get('cropy')->getData()],
                        'size' => [$form->get('cropw')->getData(), $form->get('croph')->getData()]
                    ],
                ],
            ]);

            $this->container->get('liip_imagine.cache.manager')->store($filteredBinary, $downloadUri, $imageCropLiipImagineFilter);

            $this->container->get('session')->getFlashBag()->add('success', 'form.submit.message');
        }

        return $this->render('ImageCropBundle:Default:index.html.twig', [
            'form' => $form->createView(),
            'image' => $downloadUri,
            'height' => $cropHeight,
            'width' => $cropWidth,
        ]);
    }
}
