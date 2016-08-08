<?php
/**
 * @link      http://github.com/zetta-repo/tss-authentication for the canonical source repository
 * @copyright Copyright (c) 2016 Zetta Code
 */

namespace TSS\Authentication\Authentication\Storage;

use TSS\Authentication\Options\CredentialOptions;
use Zend\Authentication\Storage\Session;

/**
 * Authentication storage that uses a Doctrine object for verification.
 *
 * @author  Thiago S. Santos <thiagos.xsantos@gmail.com>
 */
class CredentialStorage extends Session
{
    /**
     *
     * @var CredentialOptions
     */
    protected $options;

    /**
     * @param  array | CredentialOptions $options
     * @return CredentialStorage
     */
    public function setOptions($options)
    {
        if (!$options instanceof CredentialOptions) {
            $options = new CredentialOptions($options);
        }

        $this->options = $options;
        return $this;
    }

    /**
     * Constructor
     *
     * @param array | \DoctrineModule\Options\Authentication $options
     */
    public function __construct($options = [])
    {
        parent::__construct();
        $this->setOptions($options);
    }

    /**
     * This function assumes that the storage only contains identifier values (which is the case if
     * the ObjectRepository authentication adapter is used).
     *
     * @return null|object
     */
    public function read()
    {
        if (($identity = parent::read())) {

            return $this->options->getIdentityRepository()->find($identity);
        }

        return null;
    }

    /**
     * Will return the key of the identity. If only the key is needed, this avoids an
     * unnecessary db call
     *
     * @return mixed
     */
    public function readKeyOnly()
    {
        return $identity = parent::read();
    }

    /**
     * @param  object $identity
     * @return void
     */
    public function write($identity)
    {
        $metadataInfo = $this->options->getClassMetadata();
        $identifierValues = $metadataInfo->getIdentifierValues($identity);

        parent::write($identifierValues);
    }

    /**
     * @param int $rememberMe
     * @param int $time
     * @return void
     */
    public function setRememberMe($rememberMe = 0, $time = 1209600)
    {
        if ($rememberMe == 1) {
            $this->session->getManager()->rememberMe($time);
        }
    }

    /**
     * @return void
     */
    public function forgetMe()
    {
        $this->session->getManager()->forgetMe();
    }
}
