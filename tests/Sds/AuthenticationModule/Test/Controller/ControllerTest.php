<?php

namespace Sds\AuthenticationModule\Test\Controller;

use Sds\AuthenticationModule\Test\TestAsset\Identity;
use Sds\Common\Crypt\Hash;
use Sds\Common\Crypt\Salt;
use Sds\ModuleUnitTester\AbstractControllerTest;
use Zend\Http\Header\GenericHeader;
use Zend\Http\Request;


class ControllerTest extends AbstractControllerTest{

    public function setUp(){

        $this->controllerName = 'Sds\AuthenticationModule\Controller\AuthenticatedIdentityController';

        parent::setUp();

        $identity = new Identity;
        $identity->setIdentityName('toby');
        $identity->setCredential(Hash::hashAndPrependSalt(Salt::getSalt(), 'password'));

        $this->documentManager = $this->serviceManager->get('doctrine.documentmanager.odm_default');

        $this->documentManager->persist($identity);
        $this->documentManager->flush();

        $serviceManager = $this->serviceManager;
        $config = $this->serviceManager->get('config');
        $config['sds']['authentication']['authenticationServiceOptions']['enablePerSession'] = true;

        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Config', $config);
        $serviceManager->setAllowOverride(false);
    }

    public function testLogoutWithNoAuthenticatedIdentity(){
        $this->routeMatch->setParam('id', -1);
        $this->request->setMethod(Request::METHOD_DELETE);
        $result = $this->getController()->dispatch($this->request, $this->response);
        $returnArray = $result->getVariables();
        $this->assertEquals(0, count($returnArray));
    }

    public function testLogoutWithAuthenticatedIdentity(){

        $this->getController()->getOptions()->getAuthenticationService()->login('toby', 'password');

        $this->routeMatch->setParam('id', -1);
        $this->request->setMethod(Request::METHOD_DELETE);
        $result = $this->getController()->dispatch($this->request, $this->response);
        $returnArray = $result->getVariables();
        $this->assertEquals(0, count($returnArray));
    }

    public function testLoginFail(){
        $this->setExpectedException('Sds\AuthenticationModule\Exception\LoginFailedException');

        $this->request->setMethod(Request::METHOD_POST);
        $this->request->getHeaders()->addHeader(GenericHeader::fromString('Content-type: application/json'));
        $this->request->setContent('{"identityName": "toby", "credential": "wrong password"}');
        $this->getController()->dispatch($this->request, $this->response);
    }

    public function testLoginSuccess(){

        $this->request->setMethod(Request::METHOD_POST);
        $this->request->getHeaders()->addHeader(GenericHeader::fromString('Content-type: application/json'));
        $this->request->setContent('{"identityName": "toby", "credential": "password"}');
        $result = $this->getController()->dispatch($this->request, $this->response);
        $returnArray = $result->getVariables();

        $this->assertEquals('toby', $returnArray['name']);
    }

    public function testLoginSuccessWithAuthenticatedIdentity(){

        $this->getController()->getOptions()->getAuthenticationService()->login('toby', 'password');

        $this->request->setMethod(Request::METHOD_POST);
        $this->request->getHeaders()->addHeader(GenericHeader::fromString('Content-type: application/json'));
        $this->request->setContent('{"identityName": "toby", "credential": "password"}');
        $result = $this->getController()->dispatch($this->request, $this->response);
        $returnArray = $result->getVariables();

        $this->assertEquals('toby', $returnArray['name']);
    }

    public function testLoginFailWithAuthenticatedIdentity(){
        $this->setExpectedException('Sds\AuthenticationModule\Exception\LoginFailedException');

        $this->getController()->getOptions()->getAuthenticationService()->login('toby', 'password');

        $this->request->setMethod(Request::METHOD_POST);
        $this->request->getHeaders()->addHeader(GenericHeader::fromString('Content-type: application/json'));
        $this->request->setContent('{"identityName": "toby", "credential": "wrong password"}');
        $this->getController()->dispatch($this->request, $this->response);
    }

    public function testGetWithAuthenticatedIdentity(){

        $this->getController()->getOptions()->getAuthenticationService()->login('toby', 'password');

        $this->request->setMethod(Request::METHOD_GET);
        $result = $this->getController()->dispatch($this->request, $this->response);
        $returnArray = $result->getVariables();

        $this->assertEquals('toby', $returnArray[0]['name']);
    }

    public function testGetWithoutAuthenticatedIdentity(){

        $this->request->setMethod(Request::METHOD_GET);
        $result = $this->getController()->dispatch($this->request, $this->response);
        $returnArray = $result->getVariables();

        $this->assertCount(0, $returnArray);
    }
}

