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

use Symfony\Component\HttpKernel\KernelInterface;
use Sensio\Bundle\GeneratorBundle\Manipulator\KernelManipulator;
use RuntimeException;


class symfonyRestInstaller
{


    public static function configureRequiredPackages(Event $event)
    {

        //Automatic Registration is a more complex issue,
        error_log("configureRequiredPackages");
        echo "## Installation of required packages is finished\n";
        echo "##You have to manually change the app/AppKernel.php";
        echo "##You have to manually change the app/AppKernel.php";
        echo "Add this lines (check you didn't add them before) \n\n";
        echo "new FOS\UserBundle\FOSUserBundle(),\n";
        echo "new FOS\RestBundle\FOSRestBundle(),\n";
        echo "new HWI\Bundle\OAuthBundle\HWIOAuthBundle(),\n";
        echo "new JMS\SerializerBundle\JMSSerializerBundle(),\n";
        echo "new Gfreeau\Bundle\GetJWTBundle\GfreeauGetJWTBundle(),\n";
        echo "\n We will setup the bundle - If you already did this step before, type no \n ";
        $continue = symfonyRestInstaller::promptUser('Continue?', 'yes');
        if ($continue == 'yes') {
            $vendor = symfonyRestInstaller::promptUser('Vendor name', 'Acme');
            $bundle = symfonyRestInstaller::promptUser('Bundle Name', 'Checkout');
            echo $vendor . $bundle . "Bundle";
            echo __DIR__;
//            mkdir("../src/".$vendor."/".$bundle."Bundle",true);


            echo "\n\n";
        }
        echo "\n We are done ";



    }

    private static function promptUser($promptStr, $defaultVal = false)
    {

        if ($defaultVal) {                             // If a default set
            echo $promptStr . "[" . $defaultVal . "] : "; // print prompt and default
        } else {                                        // No default set
            echo $promptStr . ": ";                     // print prompt only
        }
        $name = chop(fgets(STDIN));                   // Read input. Remove CR
        if (empty($name)) {                            // No value. Enter was pressed
            return $defaultVal;                        // return default
        } else {                                        // Value entered
            return $name;                              // return value
        }
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
