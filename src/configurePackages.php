<?php
/**
 * Created by PhpStorm.
 * User: mrb
 * Date: 06/04/15
 * Time: 05:38 PM
 */
function configurePackage()
{
    echo "\n Do not start this process without the requisites (commands should be run on symfony root dir) ";
    echo "\n # Facebook App Client ID and Client Secret - find them in developers.facebook.com \n";
    echo "\n        [if you don't need facebook/google support, well, I am not prepared to handle that quite yet hehe)\n";
    echo "\n # You need Openssl certificates - (run this commands if you don't have them \n";
    echo "\n        mkdir -p app/var/jwt/";
    echo "\n        openssl genrsa -out app/var/jwt/private.pem -aes256 4096 ";
    echo "\n        openssl rsa -pubout -in app/var/jwt/private.pem -out app/var/jwt/public.pem \n\n";

    $continue = promptUser('Continue? (do this process only once per installation)', 'yes');

    if ($continue == 'yes') {
        $vendor = ucwords(strtolower(promptUser('Vendor name', 'Acme')));
        $bundle = ucwords(strtolower(promptUser('Bundle Name without the Bundle suffix', 'Checkout')));
        $facebookClientId = promptUser('Facebook App ID', '');
        $facebookClientSecret = promptUser('Facebook App Secret', '');
        $sslSecret = promptUser('SSL Secret', '');
        echo $vendor . $bundle . "Bundle";
        $vendorBundlename = $vendor . $bundle;
        $vendor_Bundle = strtolower($vendor) . "_" . strtolower($bundle);
        $fullBundleName = $vendor . $bundle . "Bundle";
        $namespacePrefix = $vendor . "\\" . $bundle . "Bundle";
//    echo __DIR__;
        // Dirs to create
//    echo "../../../src/".$vendor."/".$bundle."Bundle";
        $symfonyRootDir = __DIR__ . "/../../../../";
        $symfonyRootResourcesDir = $symfonyRootDir . "/app/Resources/";
        $fosSerializerDir = $symfonyRootResourcesDir . "/FOSUserBundle/serializer";
        $vendorSerializerDir = $symfonyRootResourcesDir . $fullBundleName . "/serializer";
        $baseBundleDir = __DIR__ . "/../../../../src/" . $vendor . "/" . $bundle . "Bundle";
        $fosUserBundleDir = $baseBundleDir . "/Security/Core/User/";
        $entityDir = $baseBundleDir . "/Entity/";
        $configDir = $baseBundleDir . "/Resources/config";
        $configDoctrineDir = $baseBundleDir . "/Resources/config/doctrine";
        $configSerializerDir = $baseBundleDir . "/Resources/config/serializer";
        $controllerRestDir = $baseBundleDir . "/Controller/Rest/v1";
        $controllerDir = $baseBundleDir . "/Controller/";
        $dependencyDir = $baseBundleDir . "/DependencyInjection/";

//    echo "********* mkdir " . __DIR__.$fosUserBundleDir;
        //Directory structure Creation
        mkdir($configSerializerDir, 0777, true);
        mkdir($fosSerializerDir, 0777, true);
        mkdir($vendorSerializerDir, 0777, true);
        mkdir($fosUserBundleDir, 0777, true);
        mkdir($entityDir, 0777, true);
        mkdir($configDir, 0777, true);
        mkdir($configDoctrineDir, 0777, true);
        mkdir($controllerRestDir, 0777, true);
        mkdir($controllerDir, 0777, true);
        mkdir($dependencyDir, 0777, true);

        //Copy standard files
        copy(__DIR__ . "/Model.User.yml", $fosSerializerDir . "/Model.User.yml");

        //Variable dependent file get parsed and then copied
        replaceInFile('<<SSLPASS>>', $sslSecret, __DIR__ . "/jwt-authentication.yml", $symfonyRootDir . "/app/config/jwt-authentication.yml");
        replaceInFile('<<CLIENTID>>', $facebookClientId, __DIR__ . "/hwi-oauth.yml", $symfonyRootDir . "/app/config/hwi-oauth.yml");
        replaceInFile('<<CLIENTSECRET>>', $facebookClientSecret, $symfonyRootDir . "/app/config/hwi-oauth.yml", $symfonyRootDir . "/app/config/hwi-oauth.yml");
        replaceInFile('<<FULLBUNDLENAME>>', $fullBundleName, __DIR__ . "/serializer.yml", $symfonyRootDir . "/app/config/serializer.yml");
        replaceInFile('<<NAMESPACEPREFIX>>', $namespacePrefix, $symfonyRootDir . "/app/config/serializer.yml", $symfonyRootDir . "/app/config/serializer.yml");
        replaceInFile('<<NAMESPACEPREFIX>>', $namespacePrefix, __DIR__ . "/fos-bundles.yml", $symfonyRootDir . "/app/config/fos-bundles.yml");
        replaceInFile('<<NAMESPACEPREFIX>>', $namespacePrefix, __DIR__ . "/Entity.User.yml", $configSerializerDir . "/Entity.User.yml");
        replaceInFile('<<NAMESPACEPREFIX>>', $namespacePrefix, __DIR__ . "/services.yml", $configDir . "/services.yml");
        replaceInFile('<<NAMESPACEPREFIX>>', $namespacePrefix, __DIR__ . "/User.orm.yml", $configDoctrineDir . "/User.orm.yml");
        replaceInFile('<<NAMESPACEPREFIX>>', $namespacePrefix, __DIR__ . "/User.php", $entityDir . "/User.php");
        replaceInFile('<<NAMESPACEPREFIX>>', $namespacePrefix, __DIR__ . "/FOSUBUserProvider.php", $fosUserBundleDir . "/FOSUBUserProvider.php");
        replaceInFile('<<NAMESPACEPREFIX>>', $namespacePrefix, __DIR__ . "/DefaultController.php", $controllerDir . "/DefaultController.php");
        replaceInFile('<<NAMESPACEPREFIX>>', $namespacePrefix, __DIR__ . "/UserController.php", $controllerRestDir . "/UserController.php");
        replaceInFile('<<VENDORBUNDLE>>', $vendorBundlename, __DIR__ . "/NAMESPACEPREFIXExtension.php", $dependencyDir . $vendorBundlename . "Extension.php");
        replaceInFile('<<NAMESPACEPREFIX>>', $namespacePrefix, $dependencyDir . $vendorBundlename . "Extension.php", $dependencyDir . $vendorBundlename . "Extension.php");
        replaceInFile('<<VENDOR_BUNDLE>>', $vendor_Bundle, __DIR__ . "/Configuration.php", $dependencyDir . "/Configuration.php");
        replaceInFile('<<NAMESPACEPREFIX>>', $namespacePrefix, $dependencyDir . "/Configuration.php", $dependencyDir . "/Configuration.php");
        replaceInFile('<<NAMESPACEPREFIX>>', $namespacePrefix, __DIR__ . "/api.v1.routing.yml", $configDir . "/api.v1.routing.yml");
        replaceInFile('<<FULLBUNDLENAME>>', $fullBundleName, $configDir . "/api.v1.routing.yml", $configDir . "/api.v1.routing.yml");
        replaceInFile('<<FULLBUNDLENAME>>', $fullBundleName, $controllerRestDir . "/UserController.php", $controllerRestDir . "/UserController.php");
        replaceInFile('<<NAMESPACEPREFIX>>', $namespacePrefix, __DIR__ . "/NAMESPACEPREFIXBundle.php", $baseBundleDir . "/" . $fullBundleName . ".php");
        replaceInFile('<<FULLBUNDLENAME>>', $fullBundleName, $baseBundleDir . "/" . $fullBundleName . ".php", $baseBundleDir . "/" . $fullBundleName . ".php");


        echo "\n\n MANUAL STUFF - there is not an App for that (yet) \n ";
        echo "\n\n  Add this lines to your: app/config/config.yml \n ";
        $configlines = <<<CONFIG

- { resource: fos-bundles.yml }
- { resource: serializer.yml }
- { resource: jwt-authentication.yml }
- { resource: hwi-oauth.yml }

CONFIG;
        echo $configlines;
        echo "\n\n  Add this lines to your app/AppKernel.php \n ";
        $kernellines = <<<KERNEL

    new HWI\\Bundle\\OAuthBundle\\HWIOAuthBundle(),
    new Gfreeau\\Bundle\\GetJWTBundle\\GfreeauGetJWTBundle(),
    new FOS\\UserBundle\\FOSUserBundle(),
    new FOS\\RestBundle\\FOSRestBundle(),
    new Lexik\\Bundle\\JWTAuthenticationBundle\\LexikJWTAuthenticationBundle(),
    new JMS\\SerializerBundle\\JMSSerializerBundle(),

KERNEL;
        $kernellines .= "    new " . $namespacePrefix . "\\" . $fullBundleName . "(),";
        echo $kernellines;
        echo "\n\n  Add this lines to your EXISTING app/config/routing.yml \n ";
        $routinglines = <<<ROUTING

api_v1:
    type: rest

ROUTING;
        $routinglines .= "    resource: @" . $fullBundleName . "/Resources/config/api.v1.routing.yml";
        $routinglines .= <<<ROUTING

    prefix:   /api/v1
fos_user:
    resource: "@FOSUserBundle/Resources/config/routing/all.xml"
hwi_oauth_redirect:
    resource: "@HWIOAuthBundle/Resources/config/routing/redirect.xml"
    prefix:   /connect
hwi_oauth_login:
    resource: "@HWIOAuthBundle/Resources/config/routing/login.xml"
    prefix:   /login
facebook_login:
    pattern: /login/check-facebook
google_login:
    pattern: /login/check-google
ROUTING;
        echo $routinglines;


        echo "\n\n  Add this lines to your EXISTING app/config/security.yml \n ";
        $securitylines = <<<SECURITY

gettoken:
     pattern:  ^/api/v1/getToken$
     stateless: true
     provider:                 fos_userbundle
     gfreeau_get_jwt:
         # this is the default config
         username_parameter: username
         password_parameter: password
         post_only: true
         success_handler: lexik_jwt_authentication.handler.authentication_success
         failure_handler: lexik_jwt_authentication.handler.authentication_failure
api:
     pattern:   ^/api
     methods: [POST, PUT, DELETE, GET]
     stateless: true
     lexik_jwt:
          authorization_header:
               enabled: true
               prefix:  Bearer
          query_parameter:
               enabled: true
               name:    bearer
          throw_exceptions: false
          create_entry_point: true

main:
    pattern: ^/
    form_login:
        provider: fos_userbundle
        csrf_provider: form.csrf_provider
    logout:       true
    anonymous:    true
    oauth:
        resource_owners:
            facebook:           "/login/check-facebook"
        login_path:        /login
        failure_path:      /loginFailure
        default_target_path: /
#                success_handler:    loginredirect_security_handler
#        failure_handler:    loginredirect_security_handler
        oauth_user_provider:
            #this is my custom user provider, created from FOSUBUserProvider - will manage the
            #automatic user registration on your site, with data from the provider (facebook. google, etc.)
            service: custom_user_provider
#                    service: hwi_oauth.user.provider.entity



#########################################################################
########################## DONT COPY PASTE ##############################
########################## THIS PART, READ ##############################
####################### AND ADD WHATS MISSING  ##########################
#######################  IN THE ORIGINAL FILE  ##########################
    encoders:
        FOS\UserBundle\Model\UserInterface: sha512
security:
    providers:
        fos_userbundle:
            id: fos_user.user_provider.username

SECURITY;

        echo $securitylines;
        echo "\n Don't add this lines without reading original files, add the lines that are missing ";
        echo "\n e.g. security providers is already on security.yml so just add the lines that are missing ";
        echo "\n + bear in mind that line order IS VERY IMPORTANT put them first if you don't know the correct order";

        //TODO

        $miscconfiguration = <<<MORECONFIG
# Now edit this file and put your database params - User/Pass and DB name

mysql -u<USER> -p<PASSWORD> -e 'create database <DATABASENAME>'

# Now edit this file and put your database params - User/Pass and DB name

app/config/parameters.yml
php app/console doctrine:schema:update

app_dev.php/api/v1/getToken

MORECONFIG;
        echo "\n\n";
    }
    echo "\n We are done ";

}

configurePackage();
function replaceInFile($find, $replace, $fileSource, $fileDestination)
{
    $oldMessage = "";
    $deletedFormat = "";
//read the entire string
    $str = file_get_contents($fileSource);
//replace something in the file string - this is a VERY simple example
    $str = str_replace("$find", "$replace", $str);
//write the entire string
    file_put_contents($fileDestination, $str);
}

function promptUser($promptStr, $defaultVal = false)
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