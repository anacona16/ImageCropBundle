<?php

namespace Anacona16\Bundle\ImageCropBundle\Tests\App;

use Anacona16\Bundle\ImageCropBundle\ImageCropBundle;
use Liip\ImagineBundle\LiipImagineBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Vich\UploaderBundle\VichUploaderBundle;

class TestKernel extends Kernel
{
    public function registerBundles(): array
    {
        return array(
            new FrameworkBundle(),
            new LiipImagineBundle(),
            new VichUploaderBundle(),
            new ImageCropBundle(),
        );
    }

    /*
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(function(ContainerBuilder $containerBuilder) {
            $containerBuilder->loadFromExtension('framework', array(
               'test' => true
               # 'secret' => 'MarkdownTesting'
            ));
        });
    }*/

    /**
     * @throws \Exception
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}