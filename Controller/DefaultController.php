<?php

namespace Anacona16\Bundle\ImageCropBundle\Controller;

use Anacona16\Bundle\ImageCropBundle\Form\Type\CropSettingFormType;
use Anacona16\Bundle\ImageCropBundle\Form\Type\ScalingSettingFormType;
use Anacona16\Bundle\ImageCropBundle\Form\Type\StyleSelectionFormType;
use Anacona16\Bundle\ImageCropBundle\Util\ClassUtil;
use Anacona16\Bundle\ImageCropBundle\Util\ImageCrop;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGenerator;

class DefaultController extends Controller
{
    /**
     * This action show the button crop.
     *
     * @param Request $request
     * @param $id
     * @param $fqcn
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buttonCropAction(Request $request, $id, $fqcn)
    {
        $entityFQCN = urldecode($fqcn);
        
        $classUtil = $this->get('anacona16_image_crop.util.class_util');
        $styles = $classUtil->getStyles($entityFQCN);

        $imageCropConfig = $this->getParameter('image_crop');

        $imageCropWindow = $imageCropConfig['window'];
        $imageCropWindowWidth = $imageCropConfig['window_width'];
        $imageCropWindowHeight = $imageCropConfig['window_height'];

        return $this->render('ImageCropBundle:Default:button.html.twig', [
            'image_crop_window' => $imageCropWindow,
            'image_crop_window_width' => $imageCropWindowWidth,
            'image_crop_window_height' => $imageCropWindowHeight,
            'style_name' => current($styles),
            'entity_id' => $id,
            'entity_fqcn' => $fqcn,
        ]);
    }

    /**
     * @param Request $request
     * @param $style
     * @param $id
     * @param $fqcn
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function overviewAction(Request $request, $style, $id, $fqcn)
    {
        $entityFQCN = urldecode($fqcn);
        $object = $this->getDoctrine()->getManager()->find($entityFQCN, $id);

        $mappingFactory = $this->get('vich_uploader.property_mapping_factory');
        $mapping = $mappingFactory->fromObject($object, $entityFQCN);

        $classUtil = $this->get('anacona16_image_crop.util.class_util');
        $styles = $classUtil->getStyles($entityFQCN);

        list($urlCrop, $urlAction) = $this->getUrlsAction($style, $id, $fqcn);

        $formStyleSelection = $this->get('form.factory')->create(StyleSelectionFormType::class, array(), array(
            'defaultStyle' => $style,
            'styles' => $styles,
            'imageCropUrl' => $urlAction,
            'action' => 'overview',
        ));

        return $this->render('ImageCropBundle:Default:overview.html.twig', array(
            'form_style_selection' => $formStyleSelection->createView(),
            'object' => $object,
            'file_property_name' => $mapping[0]->getFilePropertyName(),
            'style_name' => $style,
            'url_crop' => $urlCrop,
        ));
    }

    /**
     * @param Request $request
     * @param $style
     * @param $id
     * @param $fqcn
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function cropAction(Request $request, $style, $id, $fqcn)
    {
        $entityFQCN = urldecode($fqcn);
        $object = $this->getDoctrine()->getManager()->find($entityFQCN, $id);

        $mappingFactory = $this->get('vich_uploader.property_mapping_factory');
        $mapping = $mappingFactory->fromObject($object, $entityFQCN);

        $classUtil = $this->get('anacona16_image_crop.util.class_util');
        $styles = $classUtil->getStyles($entityFQCN);

        $imageCrop = $this->get('anacona16_image_crop.util.imagecrop');

        $imageCrop->setEntity($object);

        $imageCrop->loadFile($object, $mapping[0]->getFilePropertyName());
        $imageCrop->setImageStyle($style);
        $imageCrop->setPropertyName($mapping[0]->getFilePropertyName());
        $imageCrop->setInCroppingMode(true);
        $imageCrop->setCropDestinations();

        $imageCrop->loadCropSettings();
        $imageCrop->writeCropreadyImage();
        $settings = $imageCrop->addImagecropUi(true);

        $settings += array(
            'manipulationUrl' => $this->get('router')->generate('imagecrop_generate_image'),
            'cropped' => $request->get('cropping', false),
            'resizable' => $imageCrop->isResizable(),
        );

        list($urlAction, $urlOverview) = $this->getUrlsAction($style, $id, $fqcn);
        list($formStyleSelection, $formCropSetting, $formScalingSetting) = $this->getForms($imageCrop, $classUtil, $style, $styles, $urlAction);

        $formCropSetting->handleRequest($request);

        if (true === $formCropSetting->isSubmitted()) {
            return $this->generateFinalImage($request, $imageCrop, $formCropSetting);
        }

        return $this->render('ImageCropBundle:Default:crop.html.twig', array(
            'form_style_selection' => $formStyleSelection->createView(),
            'form_crop_setting' => $formCropSetting->createView(),
            'form_scaling_setting' => $formScalingSetting->createView(),
            'imageCrop' => $imageCrop,
            'settings' => $settings,
            'url_overview' => $urlOverview,
        ));
    }

    /**
     * Generate a new scaled version from the image to crop.
     *
     * @param Request $request
     * @return JsonResponse
     *
     * @throws \Exception
     */
    public function generateTempImageAction(Request $request)
    {
        if (false !== $request->isXmlHttpRequest()) {
            $result = new \stdClass();
            $result->success = false;

            $entityID = $request->request->getInt('entityID');
            $entityFQCN = $request->request->get('entityFQCN', false);
            $styleName = $request->request->get('style', false);
            $scale = $request->request->get('scale', false);

            try {
                if (0 === $entityID || false === $entityFQCN || false === $styleName || false === $scale) {
                    throw new \Exception('Required fields are empty');
                }

                $object = $this->getDoctrine()->getManager()->find($entityFQCN, $entityID);

                $mappingFactory = $this->get('vich_uploader.property_mapping_factory');
                $mapping = $mappingFactory->fromObject($object, $entityFQCN);

                $imageCrop = $this->get('anacona16_image_crop.util.imagecrop');

                $imageCrop->setEntity($object);

                $imageCrop->loadFile($object, $mapping[0]->getFilePropertyName());
                $imageCrop->setImageStyle($styleName);
                $imageCrop->setCropDestinations();

                $imageCrop->setScale($scale);
                $imageCrop->writeCropreadyImage();

                $result->success = true;

                return new JsonResponse($result);
            }
            catch (\Exception $e) {
                $result->message = $e->getMessage();

                return new JsonResponse($result);
            }
        }

        $this->createNotFoundException();
    }

    /**
     * @param Request $request
     * @param ImageCrop $imageCrop
     * @param Form $form
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    private function generateFinalImage(Request $request, ImageCrop $imageCrop, Form $form)
    {
        $data = $form->getData();

        $imageCropX = $data['image-crop-x'];
        $imageCropY = $data['image-crop-y'];
        $imageCropScale = $data['image-crop-scale'];

        if ('original' === $imageCropScale) {
            $imageCropScale = $imageCrop->getOriginalImageWidth();
        }

        $imageCrop->writeCropFinalImage($imageCropX, $imageCropY, $imageCropScale);

        return $this->redirectToRoute('imagecrop_overview', array(
            'style' => $data['style'],
            'id' => $data['entity-id'],
            'fqcn' => urlencode($data['entity-fqcn'])
        ));
    }

    /**
     * Usefull method to get forms
     *
     * @param ImageCrop $imageCrop
     * @param ClassUtil $classUtil
     * @param $style
     * @param $styles
     * @param $urlAction
     *
     * @return array
     */
    private function getForms(ImageCrop $imageCrop, ClassUtil $classUtil, $style, $styles, $urlAction)
    {
        $formStyleSelection = $this->get('form.factory')->create(StyleSelectionFormType::class, array(), array(
            'defaultStyle' => $style,
            'styles' => $styles,
            'imageCropUrl' => $urlAction,
            'action' => 'crop',
        ));

        $formCropSetting = $this->get('form.factory')->create(CropSettingFormType::class, array(), array(
            'action' => str_replace('style_name', $style, $urlAction),
            'imageCrop' => $imageCrop,
        ));

        $formScalingSetting = $this->get('form.factory')->create(ScalingSettingFormType::class, array(), array(
            'scaling' => $classUtil->getScaling($imageCrop->getOriginalImageWidth(), $imageCrop->getOriginalImageHeight(), $imageCrop->getWidth(), $imageCrop->getHeight()),
        ));

        return array($formStyleSelection, $formCropSetting, $formScalingSetting);
    }

    /**
     * Useful method to generate urls
     *
     * @param $styleName
     * @param $id
     * @param $fqcn
     *
     * @return array
     */
    private function getUrlsAction($styleName, $id, $fqcn)
    {
        $urlCropAction = $this->generateUrl('imagecrop_crop', array(
            'style' => $styleName,
            'id' => $id,
            'fqcn' => $fqcn
        ), UrlGenerator::ABSOLUTE_URL);

        $urlOverviewAction = $this->generateUrl('imagecrop_overview', array(
            'style' => $styleName,
            'id' => $id,
            'fqcn' => $fqcn
        ), UrlGenerator::ABSOLUTE_URL);

        return array($urlCropAction, $urlOverviewAction);
    }
}
