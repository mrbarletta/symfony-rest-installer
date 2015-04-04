<?php
/**
 * Created by PhpStorm.
 * User: mrb@lionix.com
 * Date: 01/04/15
 * Time: 02:03 PM
 */

namespace LioniX;

use Composer\Script\Event;
use Composer\Installer\PackageEvent;

class symfonyRestInstaller
{

    public static function configureRequiredPackages(Event $event)
    {

        //TODO Create configuration profiles
    }

    public static function postPackageInstall(PackageEvent $event)
    {
        $installedPackage = $event->getOperation()->getPackage();
        // do stuff
    }

    public static function warmCache(Event $event)
    {
        // make cache toasty
    }
} 