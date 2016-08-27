<?php
/**
 * @link      http://github.com/zetta-repo/tss-authentication for the canonical source repository
 * @copyright Copyright (c) 2016 Zetta Code
 */

namespace TSS\Authentication\Form\Fieldset;

use Doctrine\ORM\EntityManagerInterface;
use TSS\Authentication\Entity\AbstractUser;
use TSS\Bootstrap\Hydrator\Strategy\DateStrategy;
use TSS\DoctrineUtil\Hydrator\DoctrineObject;
use Zend\Form\Fieldset;

class UserFieldset extends Fieldset
{
    /**
     * UserFieldset constructor.
     * @param EntityManagerInterface $em
     * @param string $name
     * @param array $options
     */
    public function __construct(EntityManagerInterface $em, $name = 'user', $options = [])
    {
        parent::__construct($name, $options);

        $hidrator = new DoctrineObject($em);
        $hidrator->addStrategy('birthday', new DateStrategy());
        $this->setHydrator($hidrator);

        $this->add([
            'name' => 'id',
            'type' => 'hidden',
        ]);

        $this->add([
            'name' => 'firstName',
            'type' => 'text',
            'attributes' => [
                'class' => 'form-control',
                'placeholder' => _('First Name'),
            ],
            'options' => [
                'label' => _('First Name'),
                'label_attributes' => ['class' => 'control-label'],
                'div' => ['class' => 'form-group', 'class_error' => 'has-error'],
            ],
        ]);

        $this->add([
            'name' => 'lastName',
            'type' => 'text',
            'attributes' => [
                'class' => 'form-control',
                'placeholder' => _('Last Name'),
            ],
            'options' => [
                'label' => _('Last Name'),
                'label_attributes' => ['class' => 'control-label'],
                'div' => ['class' => 'form-group', 'class_error' => 'has-error'],
            ],
        ]);

        $this->add([
            'name' => 'username',
            'type' => 'text',
            'attributes' => [
                'class' => 'form-control',
                'placeholder' => _('Username'),
            ],
            'options' => [
                'label' => _('Username'),
                'label_attributes' => ['class' => 'control-label'],
                'div' => ['class' => 'form-group', 'class_error' => 'has-error'],
            ],
        ]);

        $this->add([
            'name' => 'email',
            'type' => 'text',
            'attributes' => [
                'class' => 'form-control',
                'placeholder' => _('Email'),
            ],
            'options' => [
                'label' => _('Email'),
                'label_attributes' => ['class' => 'control-label'],
                'div' => ['class' => 'form-group', 'class_error' => 'has-error'],
            ],
        ]);

        $this->add([
            'name' => 'password',
            'type' => 'password',
            'attributes' => [
                'class' => 'form-control',
                'placeholder' => _('Password'),
            ],
            'options' => [
                'label' => 'Password',
                'label_attributes' => ['class' => 'control-label'],
                'div' => ['class' => 'form-group', 'class_error' => 'has-error'],
            ],
        ]);

        $this->add([
            'name' => 'avatar',
            'type' => 'file',
            'attributes' => [
                'class' => 'form-control',
                'placeholder' => _('Avatar'),
            ],
            'options' => [
                'label' => _('Avatar'),
                'label_attributes' => ['class' => 'control-label'],
                'div' => ['class' => 'form-group', 'class_error' => 'has-error'],
            ],
        ]);

        $this->add([
            'name' => 'gender',
            'type' => 'select',
            'attributes' => [
                'class' => 'form-control',
                'value' => AbstractUser::GENDER_MALE
            ],
            'options' => [
                'label' => _('Gender'),
                'label_attributes' => ['class' => 'control-label'],
                'div' => ['class' => 'form-group', 'class_error' => 'has-error'],
                'value_options' => [
                    AbstractUser::GENDER_FEMALE => _('Female'),
                    AbstractUser::GENDER_MALE => _('Male')
                ]
            ],
        ]);

        $this->add([
            'name' => 'birthday',
            'type' => 'text',
            'attributes' => [
                'class' => 'form-control input-date',
                'placeholder' => _('Birthday'),
            ],
            'options' => [
                'label' => _('Birthday'),
                'label_attributes' => ['class' => 'control-label'],
                'div' => ['class' => 'form-group', 'class_error' => 'has-error'],
            ],
        ]);

        $this->add([
            'name' => 'bio',
            'type' => 'textarea',
            'attributes' => [
                'class' => 'form-control',
                'placeholder' => _('Bio'),
            ],
            'options' => [
                'label' => _('Bio'),
                'label_attributes' => ['class' => 'control-label'],
                'div' => ['class' => 'form-group', 'class_error' => 'has-error'],
            ],
        ]);
    }
}
