<?php
return array(
    'sds' => array(
        'auth' => array(

            //Authentication service to use
            'authService' => 'zend.authentication.authenticationService',

            //The user class. Defaults to the user class shipped with superdweeibe/userModule
            'userClass' => 'Sds\UserModule\Model\User',

            //Name that can be used by the serviceManager to retrieve an object that will be returned when there is no user logged in.
            'defaultUser' => array(
                'username' => 'anonymous',
                'roles' => array(
                    \Sds\Common\AccessControl\Constant\Role::guest,
                )
            ),

            //The auth adapter to use. Defaults to the adapter supplied with the Doctrine integration modules
            'adapter' => 'sds.auth.doctrineAuthenticationAdapter',

            //Used to serialize objects
            'serializer' => 'sds.doctrineExtensions.serializer',

            'enableAccessControl' => false,
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
        'authenticationadapter' => array(
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

    'service_manager' => array(
        'invokables' => array(
            'zend.authentication.authenticationService' => 'Zend\Authentication\AuthenticationService',
        ),
        'factories' => array(
            'sds.auth.activeUser'       => 'Sds\AuthModule\Service\ActiveUserFactory',
            'sds.auth.defaultUser'      => 'Sds\AuthModule\Service\DefaultUserFactory',
            'sds.auth.authServiceBase'  => 'Sds\AuthModule\Service\AuthServiceBaseFactory',
            'sds.auth.authService'      => 'Sds\AuthModule\Service\AuthServiceFactory',
            'sds.auth.doctrineAuthenticationAdapter' => 'Sds\AuthModule\Service\DoctrineAuthenticationAdapterFactory'
        )
    )
);
