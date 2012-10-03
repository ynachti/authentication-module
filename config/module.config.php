<?php
return array(
    'sds' => array(
        'auth' => array(

            //Authentication service to use
            'authenticationService' => 'sds.auth.authenticationservice',

            'authenticationAdapter' => 'doctrine.authenticationadapter.odm_default',

            'authenticationStorage' => 'doctrine.authenticationstorage.odm_default',

            //Used to serialize objects
            'serializer' => 'sds.doctrineExtensions.serializer',

            'enableAccessControl' => false,
        ),

        //The path to place smd generated by the generate-json-rpc-smd command line tool
        'jsonRpcSmdGenerator' => array(
            'sds.auth' => array(
                'path' => 'vendor/dojo/Sds/AuthModule/Smd.js',
                'format' => 'dojo', // dojo | json
                'target' => 'http://localhost/ZendSkeletonApplication/auth', //Override this target in your own config
            ),
        ),

        //Only used if sds accessControl is in use
        'accessControl' => array(
            'controllers' => array(
                'auth' => array(
                    'jsonRpc' => true,
                    'methods' => array(
                        'serviceMap' => array(
                            'roles' => array(
                                \Sds\Common\AccessControl\Constant\Role::guest
                            ),
                        ),
                        'login' => array(
                            'roles' => array(
                                \Sds\Common\AccessControl\Constant\Role::guest
                            ),
                        ),
                        'logout' => array(
                            'roles' => array(
                                \Sds\Common\AccessControl\Constant\Role::user
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),

    'doctrine' => array(
        'authentication' => array(
            'odm_default' => array(
                'identityClass' => 'Sds\UserModule\Model\User',
                'credentialCallable' => 'Sds\Common\Crypt\Hash::hashPassword'
            )
        ),
    ),

    'router' => array(
        'routes' => array(
            'sds.auth' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/auth',
                    'defaults' => array(
                        'controller' => 'sds.auth',
                    ),
                ),
            ),
        ),
    ),

    'controllers' => array(
        'factories' => array(
            'sds.auth' => 'Sds\AuthModule\Service\AuthControllerFactory'
        ),
    ),

    'view_manager' => array(
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),

    'controller_plugins' => array(
        'factories' => array(
            'identity' => 'Sds\AuthModule\Service\ControllerPluginFactory'
        ),
    ),

    'view_helpers' => array(
        'factories' => array(
            'identity' => 'Sds\AuthModule\Service\ViewHelperFactory'
        ),
    ),

    'service_manager' => array(
        'factories' => array(
            'sds.auth.authenticationservice' => 'Sds\AuthModule\Service\AuthenticationServiceFactory'
        )
    )
);
