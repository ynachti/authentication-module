<?php
/**
 * @package    Sds
 * @license    MIT
 */
namespace Sds\AuthModule\Authentication\Adapter;

use Zend\Authentication\Adapter\AdapterInterface as ZendAdapterInterface;

/**
 *
 * @since   1.0
 * @version $Revision$
 * @author  Tim Roediger <superdweebie@gmail.com>
 */
interface AdapterInterface extends ZendAdapterInterface
{
    public function setIdentityValue($identityValue);

    public function setCredentialValue($credentialValue);
}

