<?php
/**
 * Created by PhpStorm.
 * User: Thiago
 * Date: 02/12/2015
 * Time: 13:46
 */

namespace TSS\Authentication\Form\Fieldset;


use Doctrine\ORM\EntityManagerInterface;
use TSS\Authentication\Entity\AbstractUser;
use TSS\Bootstrap\Hydrator\MyDateDoctrineObject;
use TSS\Bootstrap\Hydrator\Strategy\DateStrategy;
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

        $hidrator = new MyDateDoctrineObject($em);
        $hidrator->addStrategy('birthday', new DateStrategy());
        $this->setHydrator($hidrator);

        $this->add(array(
            'name' => 'id',
            'type' => 'hidden',
        ));

        $this->add(array(
            'name' => 'firstName',
            'type' => 'text',
            'attributes' => array(
                'class' => 'form-control',
                'placeholder' => _('First Name'),
            ),
            'options' => array(
                'label' => _('First Name'),
                'label_attributes' => array('class' => 'control-label'),
            ),
        ));

        $this->add(array(
            'name' => 'lastName',
            'type' => 'text',
            'attributes' => array(
                'class' => 'form-control',
                'placeholder' => _('Last Name'),
            ),
            'options' => array(
                'label' => _('Last Name'),
                'label_attributes' => array('class' => 'control-label'),
            ),
        ));

        $this->add(array(
            'name' => 'username',
            'type' => 'text',
            'attributes' => array(
                'class' => 'form-control',
                'placeholder' => _('Username'),
            ),
            'options' => array(
                'label' => _('Username'),
                'label_attributes' => array('class' => 'control-label'),
            ),
        ));

        $this->add(array(
            'name' => 'email',
            'type' => 'text',
            'attributes' => array(
                'class' => 'form-control',
                'placeholder' => _('Email'),
            ),
            'options' => array(
                'label' => _('Email'),
                'label_attributes' => array('class' => 'control-label'),
            ),
        ));

        $this->add(array(
            'name' => 'password',
            'type' => 'password',
            'attributes' => array(
                'class' => 'form-control',
                'placeholder' => _('Password'),
            ),
            'options' => array(
                'label' => 'Password',
                'label_attributes' => array('class' => 'control-label'),
            ),
        ));

        $this->add(array(
            'name' => 'avatar',
            'type' => 'file',
            'attributes' => array(
                'class' => 'form-control',
                'placeholder' => _('Avatar'),
            ),
            'options' => array(
                'label' => _('Avatar'),
                'label_attributes' => array('class' => 'control-label'),
            ),
        ));

        $this->add(array(
            'name' => 'gender',
            'type' => 'select',
            'attributes' => array(
                'class' => 'form-control',
                'value' => AbstractUser::GENDER_MALE
            ),
            'options' => array(
                'label' => _('Gender'),
                'label_attributes' => array('class' => 'control-label'),
                'value_options' => array(
                    AbstractUser::GENDER_FEMALE => _('Female'),
                    AbstractUser::GENDER_MALE => _('Male'),
                )

            ),
        ));

        $this->add(array(
            'name' => 'birthday',
            'type' => 'text',
            'attributes' => array(
                'class' => 'form-control input-date',
                'placeholder' => _('Birthday'),
            ),
            'options' => array(
                'label' => _('Birthday'),
                'label_attributes' => array('class' => 'control-label'),
            ),
        ));

        $this->add(array(
            'name' => 'bio',
            'type' => 'textarea',
            'attributes' => array(
                'class' => 'form-control',
                'placeholder' => _('bio'),
            ),
            'options' => array(
                'label' => _('Birthday'),
                'label_attributes' => array('class' => 'control-label'),
            ),
        ));
    }
}