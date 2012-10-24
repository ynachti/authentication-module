<?php

namespace Sds\AuthenticationModule\Test\Controller;

use Sds\Common\Crypt\Hash;
use Sds\ModuleUnitTester\AbstractControllerTest;
use Sds\AuthenticationModule\Test\TestAsset\Identity;
use Zend\Http\Request;

class ControllerTest extends AbstractControllerTest{

    protected $serviceMapArray;

    public function setUp(){

        $this->controllerName = 'Sds\AuthenticationModule\Controller\AuthenticationController';

        parent::setUp();

        $identity = new Identity;
        $identity->setIdentityName('toby');
        $identity->setCredential(Hash::hashAndPrependSalt(Hash::getSalt(), 'password'));

        $this->documentManager = $this->serviceManager->get('doctrine.documentmanager.odm_default');

        $this->documentManager->persist($identity);
        $this->documentManager->flush();
    }

    public function testLogout(){
        $this->logout();
    }

    public function testLoginFail(){
        $this->request->setMethod(Request::METHOD_POST);
        $this->request->setContent('{"method": "login", "params": ["toby", "wrong password"], "id": 1}');
        $result = $this->controller->dispatch($this->request, $this->response);
        $returnArray = $result->getVariables();

        $this->assertEquals(1, $returnArray['id']);
        $this->assertEquals('Sds\AuthenticationModule\Exception\LoginFailedException', $returnArray['error']['type']);
    }

    public function testLoginSuccess(){
        $this->request->setMethod(Request::METHOD_POST);
        $this->request->setContent('{"method": "login", "params": ["toby", "password"], "id": 1}');
        $result = $this->controller->dispatch($this->request, $this->response);
        $returnArray = $result->getVariables();

        $this->assertEquals(1, $returnArray['id']);
        $this->assertEquals('toby', $returnArray['result']['identity']['name']);

        $this->request->setMethod(Request::METHOD_POST);
        $this->request->setContent('{"method": "login", "params": ["toby", "password"], "id": 1}');
        $result = $this->controller->dispatch($this->request, $this->response);
        $returnArray = $result->getVariables();

        $this->assertEquals(1, $returnArray['id']);
    }

    public function testSecondLogout(){
        $this->logout();
    }

    protected function logout(){
        $this->request->setMethod(Request::METHOD_POST);
        $this->request->setContent('{"method": "logout", "id": 1}');
        $result = $this->controller->dispatch($this->request, $this->response);
        $returnArray = $result->getVariables();
        $this->assertEquals(
            array(
                'id' => 1,
                'result' => array(
                    'identity' => null
                ),
                'error' => null
           ),
           $returnArray
        );
    }
}

