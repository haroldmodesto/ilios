<?php

namespace App\Command;

use App\Service\Archive;
use App\Service\Config;
use App\Service\Fetch;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

use App\Service\Filesystem;

/**
 * Pull down asset archive from AWS and extract it so
 * assets can be served from the API host.
 *
 * Class UpdateFrontendCommand
 * @package App\Command
 */
class UpdateFrontendCommand extends Command implements CacheWarmerInterface
{
    /**
     * @var string
     */
    const FRONTEND_DIRECTORY = '/ilios/frontend/';
    const ARCHIVE_FILE_NAME = 'frontend.tar.gz';
    const UNPACKED_DIRECTORY = '/deploy-dist/';

    const STAGING_CDN_ASSET_DOMAIN = 'https://frontend-archive-staging.iliosproject.org/';
    const PRODUCTION_CDN_ASSET_DOMAIN = 'https://frontend-archive-production.iliosproject.org/';

    const STAGING = 'stage';
    const PRODUCTION = 'prod';

    /**
     * @var Fetch
     */
    protected $fetch;

    /**
     * @var Filesystem
     */
    protected $fs;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var string
     */
    protected $cacheDir;

    /**
     * @var Archive
     */
    private $archive;

    /**
     * @var string
     */
    protected $productionTemporaryFileStore;

    /**
     * @var string
     */
    protected $stagingTemporaryFileStore;

    /**
     * @var string
     */
    protected $apiVersion;

    /**
     * @var string
     */
    protected $environment;

    public function __construct(
        Fetch $fetch,
        Filesystem $fs,
        Config $config,
        Archive $archive,
        $kernelCacheDir,
        $kernelProjectDir,
        $apiVersion,
        $environment
    ) {
        $this->fetch = $fetch;
        $this->fs = $fs;
        $this->config = $config;
        $this->archive = $archive;
        $this->cacheDir = $kernelCacheDir;
        $this->apiVersion = $apiVersion;
        $this->environment = $environment;

        $temporaryFileStorePath = $kernelProjectDir . '/var/tmp/frontend-update-files';
        $this->fs->mkdir($temporaryFileStorePath);
        $this->productionTemporaryFileStore = $temporaryFileStorePath . '/prod';
        $this->fs->mkdir($this->productionTemporaryFileStore);
        $this->stagingTemporaryFileStore = $temporaryFileStorePath . '/stage';
        $this->fs->mkdir($this->stagingTemporaryFileStore);

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ilios:update-frontend')
            ->setAliases(['ilios:maintenance:update-frontend'])
            ->setDescription('Updates the frontend to the latest version.')
            ->addOption(
                'staging-build',
                null,
                InputOption::VALUE_NONE,
                'Pull a staging build of the frontend'
            )
            ->addOption(
                'at-version',
                null,
                InputOption::VALUE_REQUIRED,
                'Request a specific version of the frontend (instead of the default active one)'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $stagingBuild = $input->getOption('staging-build');
        $versionOverride = $input->getOption('at-version');
        $environment = $stagingBuild?self::STAGING:self::PRODUCTION;

        $this->downloadAndExtractArchive($environment, $versionOverride);

        $message = 'Frontend updated successfully';
        if ($stagingBuild) {
            $message .= ' from staging build';
        }
        if ($versionOverride) {
            $message .= ' to version ' . $versionOverride;
        }
        $output->writeln("<info>{$message}!</info>");
    }

    /**
     * {@inheritdoc}
     */
    public function warmUp($cacheDir)
    {
        try {
            $version = false;
            $releaseVersion = $this->config->get('frontend_release_version');
            $keepFrontendUpdated = $this->config->get('keep_frontend_updated');
            if (!$keepFrontendUpdated) {
                $version = $releaseVersion;
            }
            $this->downloadAndExtractArchive(self::PRODUCTION, $version);
        } catch (\Exception $e) {
            print "\n\n**Warning: Unable to load frontend. Please run ilios:maintenance:update-frontend again.**\n\n\n";
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isOptional()
    {
        return true;
    }

    /**
     * @param string $environment
     * @param string|bool $versionOverride
     *
     * @throws \Exception
     */
    protected function downloadAndExtractArchive($environment = 'prod', $versionOverride = false)
    {
        $fileName = $this->apiVersion . '/' . self::ARCHIVE_FILE_NAME;
        if ($versionOverride) {
            $fileName .= ':' . $versionOverride;
        }
        $url = self::PRODUCTION_CDN_ASSET_DOMAIN;
        if ($environment === self::STAGING) {
            $url = self::STAGING_CDN_ASSET_DOMAIN;
        }
        $archiveDir = $environment === 'prod'? $this->productionTemporaryFileStore : $this->stagingTemporaryFileStore;
        $versionPath = $versionOverride ? $versionOverride : 'active';
        $parts = [
            $archiveDir,
            $this->apiVersion,
            $versionPath,
            self::ARCHIVE_FILE_NAME
        ];
        $archivePath = join(DIRECTORY_SEPARATOR, $parts);

        $file = is_readable($archivePath) ? new \SplFileObject($archivePath, "r"): null;
        $string = $this->fetch->get($url . $fileName, $file);

        $this->fs->dumpFile($archivePath, $string);

        $this->archive::extract($archivePath, $archiveDir);
        $frontendPath = $this->cacheDir . self::FRONTEND_DIRECTORY;
        $this->fs->remove($frontendPath);
        $this->fs->rename($archiveDir . self::UNPACKED_DIRECTORY, $frontendPath);
    }
}
