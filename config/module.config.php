<?php
return array(
    'sdsAuthConfig' => array(
        
        //Authentication service to use
        'authService' => 'Zend\Authentication\AuthenticationService',
        
        //Name that can be used by the serviceManager to retrieve an object that will be returned when there is no user logged in.
        'defaultUser' => 'SdsUserModule\DefaultUser',
        
        //The auth adapter to use. Defaults to the adapter supplied with the Doctrine integration modules
        'adapter' => 'doctrine_auth_adapter',  
        
        //The method on the adapter to inject the identity/username value
        'adapterUsernameMethod' => 'setIdentityValue',
        
        //The method of the adapter to inject the credential/password value
        'adapterPasswordMethod' => 'setCredentialValue',        
    ),
     
    'controller' => array(
        'factories' => array(
            'auth' => 'SdsAuthModule\Service\AuthControllerFactory'
        ),              
    ),  
    
    //Used by SdsAccessControlModule. If you are not using SdsAccessControlModule,
    //this part of the config is ignored.
    'sdsAccessControl' => array(
        'controllers' => array(
            'auth' => array(                
                'actions' => array(
                    'serviceMap' => array(
                        'roles' => array(
                            array(
                                'name' => 'default'
                            ),
                        ),                         
                    ),
                    'login' => array(
                        'roles' => array(
                            array(
                                'name' => 'default'
                            ),
                        ), 
                     ),
                    'logout' => array(
                        'roles' => array(
                            array(
                                'name' => 'authenticated'
                            ),
                        ), 
                     ),                    
                ),
            ),
        ),
    ),    
);
