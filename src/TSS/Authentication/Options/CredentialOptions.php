<?php
/**
 * Created by PhpStorm.
 * User: Thiago S. Santos
 * Date: 01/08/2015
 * Time: 08:38 AM
 */

namespace TSS\Authentication\Options;


use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Zend\Authentication\Adapter\Exception;
use Zend\Stdlib\AbstractOptions;

/**
 * Authentication options that uses a Doctrine object for verification.
 *
 * @author  Thiago S. Santos <thiagos.xsantos@gmail.com>
 */
class CredentialOptions extends AbstractOptions
{
    /**
     * A valid object implementing ObjectManager interface
     *
     * @var string | ObjectManager
     */
    protected $entityManager;

    /**
     * A valid object implementing ObjectRepository interface (or ObjectManager/identityClass)
     *
     * @var ObjectRepository
     */
    protected $identityRepository;

    /**
     * A valid object implementing ObjectRepository interface (or ObjectManager/identityClass)
     *
     * @var ObjectRepository
     */
    protected $credentialRepository;

    /**
     * Identity's class name
     *
     * @var string
     */
    protected $identityClass;

    /**
     * Credential's class name
     *
     * @var string
     */
    protected $credentialClass;

    /**
     * Property to use for the identity
     *
     * @var string
     */
    protected $identityProperty;

    /**
     * Property to use for the credential
     *
     * @var string
     */
    protected $credentialProperty;

    /**
     * Property to use for the credential identity
     *
     * @var string
     */
    protected $credentialIdentityProperty;

    /**
     * Callable function to check if a credential is valid
     *
     * @var mixed
     */
    protected $credentialCallable;

    /**
     * If an objectManager is not supplied, this metadata will be used
     * by DoctrineModule/Authentication/Storage/ObjectRepository
     *
     * @var \Doctrine\Common\Persistence\Mapping\ClassMetadata
     */
    protected $classMetadata;

    /**
     * When using this options class to create a DoctrineModule/Authentication/Storage/ObjectRepository
     * this is the storage instance that the object key will be stored in.
     *
     * When using this options class to create an AuthenticationService with and
     * the option storeOnlyKeys == false, this is the storage instance that the whole
     * object will be stored in.
     *
     * @var \Zend\Authentication\Storage\StorageInterface|string;
     */
    protected $storage = 'DoctrineModule\Authentication\Storage\Session';

    /**
     * @param  string | ObjectManager $entityManager
     * @return CredentialOptions
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
        return $this;
    }

    /**
     * @return ObjectManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @param  ObjectRepository $identityRepository
     * @return CredentialOptions
     */
    public function setRepository(ObjectRepository $identityRepository)
    {
        $this->identityRepository = $identityRepository;
        return $this;
    }

    /**
     * @return ObjectRepository
     */
    public function getIdentityRepository()
    {
        if ($this->identityRepository) {
            return $this->identityRepository;
        }

        return $this->entityManager->getRepository($this->identityClass);
    }

    /**
     * @param  ObjectRepository $credentialRepository
     * @return CredentialOptions
     */
    public function setCredentialRepository(ObjectRepository $credentialRepository)
    {
        $this->credentialRepository = $credentialRepository;
        return $this;
    }

    /**
     * @return ObjectRepository
     */
    public function getCredentialRepository()
    {
        if ($this->credentialRepository) {
            return $this->credentialRepository;
        }

        return $this->entityManager->getRepository($this->credentialClass);
    }

    /**
     * @return string
     */
    public function getIdentityClass()
    {
        return $this->identityClass;
    }

    /**
     * @param string $identityClass
     * @return CredentialOptions
     */
    public function setIdentityClass($identityClass)
    {
        $this->identityClass = $identityClass;
        return $this;
    }

    /**
     * @param string $credentialClass
     * @return CredentialOptions
     */
    public function setCredentialClass($credentialClass)
    {
        $this->credentialClass = $credentialClass;
        return $this;
    }

    /**
     * @return string
     */
    public function getCredentialClass()
    {
        return $this->credentialClass;
    }

    /**
     * @param  string $identityProperty
     * @throws Exception\InvalidArgumentException
     * @return CredentialOptions
     */
    public function setIdentityProperty($identityProperty)
    {
        if (!is_string($identityProperty) || $identityProperty === '') {
            throw new Exception\InvalidArgumentException(
                sprintf('Provided $identityProperty is invalid, %s given', gettype($identityProperty))
            );
        }

        $this->identityProperty = $identityProperty;

        return $this;
    }

    /**
     * @return string
     */
    public function getIdentityProperty()
    {
        return $this->identityProperty;
    }

    /**
     * @param  string $credentialProperty
     * @throws Exception\InvalidArgumentException
     * @return CredentialOptions
     */
    public function setCredentialProperty($credentialProperty)
    {
        if (!is_string($credentialProperty) || $credentialProperty === '') {
            throw new Exception\InvalidArgumentException(
                sprintf('Provided $credentialProperty is invalid, %s given', gettype($credentialProperty))
            );
        }

        $this->credentialProperty = $credentialProperty;

        return $this;
    }

    /**
     * @return string
     */
    public function getCredentialProperty()
    {
        return $this->credentialProperty;
    }

    /**
     * @return string
     */
    public function getCredentialIdentityProperty()
    {
        return $this->credentialIdentityProperty;
    }

    /**
     * @param string $credentialIdentityProperty
     * @return CredentialOptions
     */
    public function setCredentialIdentityProperty($credentialIdentityProperty)
    {
        $this->credentialIdentityProperty = $credentialIdentityProperty;
        return $this;
    }

    /**
     * @param  mixed $credentialCallable
     * @throws Exception\InvalidArgumentException
     * @return CredentialOptions
     */
    public function setCredentialCallable($credentialCallable)
    {
        if (!is_callable($credentialCallable)) {
            throw new Exception\InvalidArgumentException(
                sprintf(
                    '"%s" is not a callable',
                    is_string($credentialCallable) ? $credentialCallable : gettype($credentialCallable)
                )
            );
        }

        $this->credentialCallable = $credentialCallable;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCredentialCallable()
    {
        return $this->credentialCallable;
    }

    /**
     * @return \Doctrine\Common\Persistence\Mapping\ClassMetadata
     */
    public function getClassMetadata()
    {
        if ($this->classMetadata) {
            return $this->classMetadata;
        }

        return $this->entityManager->getClassMetadata($this->identityClass);
    }

    /**
     *
     * @param \Doctrine\Common\Persistence\Mapping\ClassMetadata $classMetadata
     */
    public function setClassMetadata(ClassMetadata $classMetadata)
    {
        $this->classMetadata = $classMetadata;
    }

    /**
     * @return \Zend\Authentication\Storage\StorageInterface|string
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * @param \Zend\Authentication\Storage\StorageInterface|string $storage
     */
    public function setStorage($storage)
    {
        $this->storage = $storage;
    }
}