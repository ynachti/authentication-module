<?php

namespace Sds\AuthModule\Test\TestAsset;

use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;

class AuthenticationAdapter implements AdapterInterface
{

    protected $identityValue;

    protected $credentialValue;

    protected $identity;

    public function setIdentityValue($identityValue) {
        $this->identityValue = $identityValue;
    }

    public function setCredentialValue($credentialValue) {
        $this->credentialValue = $credentialValue;
    }

    public function setIdentity($identity)
    {
        $this->identity = $identity;
    }

    public function authenticate()
    {
        if ($this->identity->getUsername() == $this->identityValue &&
            $this->identity->getPassword() == $this->credentialValue
        ) {
            return new Result(Result::SUCCESS, $this->identity);
        } else {
            return new Result(Result::FAILURE, $this->identity);
        }
    }
}
