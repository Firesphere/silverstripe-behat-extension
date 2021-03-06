<?php

namespace SilverStripe\BehatExtension\Compiler;

use SilverStripe\Core\Manifest\ModuleLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Loads SilverStripe core. Required to initialize autoloading.
 */
class CoreInitializationPass implements CompilerPassInterface
{
    /**
     * Loads kernel file.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        // Connect to database and build manifest
        $_GET['flush'] = 1;
        if (!getenv('SS_ENVIRONMENT_TYPE')) {
            putenv('SS_ENVIRONMENT_TYPE=dev');
        }
        require_once('Core/Core.php');

        // Include bootstrap file
        $bootstrapFile = $container->getParameter('silverstripe_extension.bootstrap_file');
        if ($bootstrapFile) {
            require_once $bootstrapFile;
        }

        // Register all paths
        foreach (ModuleLoader::inst()->getManifest()->getModules() as $module) {
            $container->setParameter('paths.modules.'.$module->getShortName(), $module->getPath());
            $composerName = $module->getComposerName();
            if ($composerName) {
                list($vendor,$name) = explode('/', $composerName);
                $container->setParameter('paths.modules.'.$vendor.'.'.$name, $module->getPath());
            }
        }

        unset($_GET['flush']);

        // Remove the error handler so that PHPUnit can add its own
        restore_error_handler();
    }
}
