<?php
/**
 * @package    Sds
 * @license    MIT
 */
namespace Sds\AuthModule\Controller;

use Sds\AuthModule\AuthService;
use Sds\AuthModule\Events;
use Sds\AuthModule\Exception;
use Sds\Common\User\ActiveUserAwareInterface;
use Sds\Common\User\ActiveUserAwareTrait;
use Sds\JsonRpc\Controller\AbstractJsonRpcController;

/**
 * Controller to handle login and logout actions via json rpc
 *
 * @since   1.0
 * @version $Revision$
 * @author  Tim Roediger <superdweebie@gmail.com>
 */
class AuthController extends AbstractJsonRpcController implements ActiveUserAwareInterface
{

    use ActiveUserAwareTrait;

    /**
     *
     * @var \SdsAuthModule\AuthService
     */
    protected $authService;

    /**
     *
     * @var string
     */
    protected $serializerCallable;

    /**
     *
     * @return \SdsAuthModule\AuthService
     */
    public function getAuthService() {
        return $this->authService;
    }

    /**
     *
     * @param \SdsAuthModule\AuthService $authService
     */
    public function setAuthService(AuthService $authService) {
        $this->authService = $authService;
    }

    /**
     *
     * @return callable
     */
    public function getSerializerCallable() {
        return $this->serializerCallable;
    }

    /**
     *
     * @param callable $serializeCallable
     */
    public function setSerializerCallable($serializerCallable) {

        if (!is_callable($serializerCallable)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '"%s" is not a callable',
                is_string($serializerCallable) ? $serializerCallable : gettype($serializerCallable)
            ));
        }

        $this->serializerCallable = $serializerCallable;
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function registerRpcMethods(){
        return array(
            'login',
            'logout',
            'recoverPassword',
            'register'
        );
    }

    /**
     * Checks the provided username and password against the authService and
     * returns the active user
     *
     * @param string $username
     * @param string $password
     * @return object
     * @throws Exception\AlreadyLoggedInException
     * @throws Exception\LoginFailedException
     */
    public function login($username, $password)
    {
        if($this->activeUser != $this->authService->getDefaultUser()){
            $this->getResponse()->setStatusCode(500);
            throw new Exception\AlreadyLoggedInException('You are aready logged in');
        }
        $result = $this->authService->login($username, $password);
        if (!$result->isValid()){
            $this->getResponse()->setStatusCode(500);
            throw new Exception\LoginFailedException(implode('. ', $result->getMessages()));
        }

        $activeUser = $result->getIdentity();

        $this->events->addIdentifiers(array(Events::identifier));
        $collection = $this->events->trigger(Events::login, $activeUser);

        $data = array();
        foreach($collection as $response){
            $data = array_merge($data, $response);
        }

        if (isset($this->serializerCallable)) {
            $activeUser = call_user_func($this->serializerCallable, $activeUser);
        }

        return array(
            'user' => $activeUser,
            'data' => $data
        );
    }

    /**
     * Clears the active user
     *
     * @return object
     */
    public function logout()
    {
        $this->authService->logout();

        $this->events->addIdentifiers(array(Events::identifier));
        $this->events->trigger(Events::logout);

        return array(
            'user' => null,
            'url' => null,
        );
    }

    /**
     *
     * @param string $username
     * @param string $email
     * @return object
     */
    public function recoverPassword($username = null, $email = null){

        $this->events->addIdentifiers(array(Events::identifier));
        $collection = $this->events->trigger(Events::recoverPassword, array(
            'username' => $username,
            'email' => $email
        ));


    }

    /**
     *
     * @param string $username
     * @param array $details
     * @return object
     */
    public function register($username, array $details){

        $this->events->addIdentifiers(array(Events::identifier));
        $collection = $this->events->trigger(Events::register, array(
            'username' => $username,
            'details' => $details
        ));

    }
}
