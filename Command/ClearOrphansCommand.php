<?php

namespace Anacona16\Bundle\ImageCropBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

# For use after Support of Symfony <5 is dropped
##[AsCommand(name: 'imagecrop:clear-orphans', description: 'Clear orphaned crops according to the orphan_maxage you defined in your configuration.')]
class ClearOrphansCommand extends Command
{
    public function __construct(protected ParameterBag $parameterBag)
    {
        parent::__construct();
    }

    # For lazy loading (remove after Support of Symfony <5 is dropped)
    protected static $defaultName = 'imagecrop:clear-orphans';
    protected static $defaultDescription = 'Clear orphaned crops according to the orphan_maxage you defined in your configuration.';

    protected function configure(): void
    {
        # remove after Support of Symfony <5 is dropped
        $this
            ->setName('imagecrop:clear-orphans')
            ->setDescription('Clear orphaned crops according to the orphan_maxage you defined in your configuration.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $config = $this->parameterBag->get('image_crop');

        $imagineCacheDir = $this->parameterBag->get('kernel.project_dir')."/public/".$config['imagine_cache_dir'];
        $filterTempDir = $imagineCacheDir."/_imagecrop_temp";

        $system = new Filesystem();
        $finder = new Finder();

        try {
            # filter files by date
            $finder->in($filterTempDir)->date('<='.-1 * (int)$config['orphan_maxage'].'seconds')->files();

            foreach ($finder as $file) {
                $system->remove($file);
                $output->writeln(sprintf('File %s removed', $file));
            }

            # For use after Support of Symfony <5 is dropped
            #return Command::SUCCESS;
            return 0;
        } catch (\InvalidArgumentException $e) {
            // the finder will throw an InvalidArgumentException if the directory he should search in does not exist
            // in that case we don't have anything to clean
            return 0;
        } catch (IOException $e) {
            $output->writeln('Removing of orphaned files failed');
            return 1;
        }
    }
}
