<?php
/**
 * @link      http://github.com/zetta-repo/tss-authentication for the canonical source repository
 * @copyright Copyright (c) 2016 Zetta Code
 */

namespace TSS\Authentication\Authentication\Storage;

use TSS\Authentication\Options\Authentication as AuthenticationOptions;
use Zend\Session\ManagerInterface as SessionManager;

/**
 * Authentication storage that uses a Doctrine object for verification.
 */
class Session extends \Zend\Authentication\Storage\Session
{
    /**
     *
     * @var AuthenticationOptions
     */
    protected $options;

    /**
     * Sets session storage options and initializes session namespace object
     *
     * @param  mixed $namespace
     * @param  mixed $member
     * @param  SessionManager $manager
     * @param array | \DoctrineModule\Options\Authentication $options
     */
    public function __construct($namespace = null, $member = null, SessionManager $manager = null, $options = [])
    {
        parent::__construct($namespace, $member, $manager);
        $this->setOptions($options);
    }

    /**
     * @param  array | AuthenticationOptions $options
     * @return Session
     */
    public function setOptions($options)
    {
        if (!$options instanceof AuthenticationOptions) {
            $options = new AuthenticationOptions($options);
        }

        $this->options = $options;
        return $this;
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

            return $this->options->getObjectRepository()->find($identity);
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
     * @param bool $rememberMe
     * @param int $time
     * @return void
     */
    public function setRememberMe($rememberMe = false, $time = 1209600)
    {
        if ($rememberMe) {
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
