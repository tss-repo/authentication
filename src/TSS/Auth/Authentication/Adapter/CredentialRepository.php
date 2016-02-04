<?php
/**
 * Created by PhpStorm.
 * User: Thiago S. Santos
 * Date: 01/09/2015
 * Time: 08:37 AM
 */

namespace TSS\Auth\Authentication\Adapter;


use TSS\Auth\Options\CredentialOptions;
use Zend\Authentication\Adapter\AbstractAdapter;
use Zend\Authentication\Adapter\Exception;
use Zend\Authentication\Result as AuthenticationResult;

/**
 * Authentication adapter that uses a Doctrine object for verification.
 *
 * @author  Thiago S. Santos <thiagos.xsantos@gmail.com>
 */
class CredentialRepository extends AbstractAdapter
{
    /**
     * @var CredentialOptions
     */
    protected $options;

    /**
     * Contains the authentication results.
     *
     * @var array
     */
    protected $authenticationResultInfo = null;

    /**
     * Constructor
     *
     * @param array|CredentialOptions $options
     */
    public function __construct($options = array())
    {
        $this->setOptions($options);
    }

    /**
     * @param  array|CredentialOptions $options
     * @return CredentialRepository
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
     * @return CredentialOptions
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set the value to be used as the identity
     *
     * @param  mixed $identityValue
     * @return CredentialRepository
     * @deprecated use setIdentity instead
     */
    public function setIdentityValue($identityValue)
    {
        $this->identity = $identityValue;
        return $this;
    }

    /**
     * @return string
     * @deprecated use getIdentity instead
     */
    public function getIdentityValue()
    {
        return $this->identity;
    }

    /**
     * Set the credential value to be used.
     *
     * @param  mixed $credentialValue
     * @return CredentialRepository
     * @deprecated use setCredential instead
     */
    public function setCredentialValue($credentialValue)
    {
        $this->credential = $credentialValue;
        return $this;
    }

    /**
     * @return string
     * @deprecated use getCredential instead
     */
    public function getCredentialValue()
    {
        return $this->credential;
    }

    /**
     * {@inheritDoc}
     */
    public function authenticate()
    {
        $this->setup();
        $options = $this->options;
        if($options->getIdentityClass() != null) {
            $identity = $options
                ->getIdentityRepository()
                ->findOneBy(array($options->getIdentityProperty() => $this->identity));

            if (!$identity) {
                $this->authenticationResultInfo['code'] = AuthenticationResult::FAILURE_IDENTITY_NOT_FOUND;
                $this->authenticationResultInfo['messages'][] = 'A record with the supplied identity could not be found.';

                return $this->createAuthenticationResult();
            }

            $authResult = $this->validateIdentity($identity);
        } else {
            $authResult = $this->validateCredential();
        }

        return $authResult;
    }

    /**
     * This method attempts to validate that the record in the resultset is indeed a
     * record that matched the identity provided to this adapter.
     *
     * @param  object $identity
     * @throws Exception\UnexpectedValueException
     * @return AuthenticationResult
     */
    protected function validateIdentity($identity)
    {
        $options = $this->options;
        $credential = $options
            ->getCredentialRepository()
            ->findOneBy(array($options->getCredentialIdentityProperty() => $identity, $options->getCredentialProperty() => $this->credential));

        if (!$credential) {
            $this->authenticationResultInfo['code'] = AuthenticationResult::FAILURE_IDENTITY_NOT_FOUND;
            $this->authenticationResultInfo['messages'][] = 'A record with the supplied credential could not be found.';

            return $this->createAuthenticationResult();
        }

        $credentialIdentityProperty = $this->options->getCredentialIdentityProperty();
        $getter = 'get' . ucfirst($credentialIdentityProperty);
        $credentialIdentity = null;

        if (method_exists($credential, $getter)) {
            $credentialIdentity = $credential->$getter();
        } elseif (property_exists($credential, $credentialIdentityProperty)) {
            $credentialIdentity = $credential->{$credentialIdentityProperty};
        } else {
            throw new Exception\UnexpectedValueException(
                sprintf(
                    'Property (%s) in (%s) is not accessible. You should implement %s::%s()',
                    $credentialIdentityProperty,
                    get_class($credential),
                    get_class($credential),
                    $getter
                )
            );
        }

        $callable = $this->options->getCredentialCallable();

        if ($callable) {
            $credentialValue = call_user_func($callable, $identity, $credential);
        } else {
            $credentialValue = $identity;
        }

        if ($credentialValue !== true && $credentialValue !== $credentialIdentity) {
            $this->authenticationResultInfo['code'] = AuthenticationResult::FAILURE_CREDENTIAL_INVALID;
            $this->authenticationResultInfo['messages'][] = 'Supplied credential is invalid.';

            return $this->createAuthenticationResult();
        }

        $this->authenticationResultInfo['code'] = AuthenticationResult::SUCCESS;
        $this->authenticationResultInfo['identity'] = $identity;
        $this->authenticationResultInfo['messages'][] = 'Authentication successful.';

        return $this->createAuthenticationResult();
    }

    /**
     * This method attempts to validate that the record in the resultset is indeed a
     * record that matched the identity provided to this adapter.
     *
     * @throws Exception\UnexpectedValueException
     * @return AuthenticationResult
     */
    protected function validateCredential()
    {
        $options = $this->options;
        $credential = $options
            ->getCredentialRepository()
            ->findOneBy(array($options->getIdentityProperty() => $this->identity, $options->getCredentialProperty() => $this->credential));

        if (!$credential) {
            $this->authenticationResultInfo['code'] = AuthenticationResult::FAILURE_IDENTITY_NOT_FOUND;
            $this->authenticationResultInfo['messages'][] = 'A record with the supplied credential could not be found.';

            return $this->createAuthenticationResult();
        }

        $credentialIdentityProperty = $this->options->getCredentialIdentityProperty();
        $getter = 'get' . ucfirst($credentialIdentityProperty);
        $identity = null;

        if (method_exists($credential, $getter)) {
            $identity = $credential->$getter();
        } elseif (property_exists($credential, $credentialIdentityProperty)) {
            $identity = $credential->{$credentialIdentityProperty};
        } else {
            throw new Exception\UnexpectedValueException(
                sprintf(
                    'Property (%s) in (%s) is not accessible. You should implement %s::%s()',
                    $credentialIdentityProperty,
                    get_class($credential),
                    get_class($credential),
                    $getter
                )
            );
        }

        $callable = $this->options->getCredentialCallable();

        if ($callable) {
            $credentialValue = call_user_func($callable, $identity, $credential);
        } else {
            $credentialValue = true;
        }

        if ($credentialValue !== true) {
            $this->authenticationResultInfo['code'] = AuthenticationResult::FAILURE_CREDENTIAL_INVALID;
            $this->authenticationResultInfo['messages'][] = 'Supplied credential is invalid.';

            return $this->createAuthenticationResult();
        }

        $this->authenticationResultInfo['code'] = AuthenticationResult::SUCCESS;
        $this->authenticationResultInfo['identity'] = $identity;
        $this->authenticationResultInfo['messages'][] = 'Authentication successful.';

        return $this->createAuthenticationResult();
    }

    /**
     * This method abstracts the steps involved with making sure that this adapter was
     * indeed setup properly with all required pieces of information.
     *
     * @throws Exception\RuntimeException - in the event that setup was not done properly
     */
    protected function setup()
    {
        if (null === $this->credential) {
            throw new Exception\RuntimeException(
                'A credential value was not provided prior to authentication with CredentialRepository'
                . ' authentication adapter'
            );
        }

        $this->authenticationResultInfo = array(
            'code' => AuthenticationResult::FAILURE,
            'identity' => $this->identity,
            'messages' => array()
        );
    }

    /**
     * Creates a Zend\Authentication\Result object from the information that has been collected
     * during the authenticate() attempt.
     *
     * @return \Zend\Authentication\Result
     */
    protected function createAuthenticationResult()
    {
        return new AuthenticationResult(
            $this->authenticationResultInfo['code'],
            $this->authenticationResultInfo['identity'],
            $this->authenticationResultInfo['messages']
        );
    }
}