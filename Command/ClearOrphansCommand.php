<?php

namespace Anacona16\Bundle\ImageCropBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class ClearOrphansCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('imagecrop:clear-orphans')
            ->setDescription('Clear orphaned crops according to the orphan_maxage you defined in your configuration.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $this->getContainer()->getParameter('image_crop');
        $kernelRootDir = $this->getContainer()->getParameter('kernel.root_dir');

        $webRootDir = "$kernelRootDir/../web/";
        $imagineCacheDir = $webRootDir . $config['imagine_cache_dir'];
        $filterTempDir = "$imagineCacheDir/_imagecrop_temp";

        $system = new Filesystem();
        $finder = new Finder();

        try {
            $finder->in($filterTempDir)->date('<=' . -1 * (int) $config['orphan_maxage'] . 'seconds')->files();
        } catch (\InvalidArgumentException $e) {
            // the finder will throw an exception of type InvalidArgumentException
            // if the directory he should search in does not exist
            // in that case we don't have anything to clean
            return;
        }

        foreach ($finder as $file) {
            $system->remove($file);

            $output->writeln(sprintf('File %s removed', $file));
        }
    }

}
