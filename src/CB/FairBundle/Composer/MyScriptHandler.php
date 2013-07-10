<?php
namespace CB\FairBundle\Composer;

use Sensio\Bundle\DistributionBundle\Composer\ScriptHandler;
use Composer\Script\CommandEvent;

class MyScriptHandler extends ScriptHandler
{
    /**
     * Clears the Symfony cache.
     *
     * @param $event CommandEvent A instance
     */
    public static function clearCache(CommandEvent $event)
    {
        $options = self::getOptions($event);
        $appDir = $options['symfony-app-dir'];

        if (!is_dir($appDir)) {
            echo 'The symfony-app-dir ('.$appDir.') specified in composer.json was not found in '.getcwd().', can not clear the cache.'.PHP_EOL;

            return;
        }

        foreach ($options['cache-env'] as $env) {
            static::executeCommand($event, $appDir, 'cache:clear --no-warmup --env='.$env, $options['process-timeout']);
        }

    }

}