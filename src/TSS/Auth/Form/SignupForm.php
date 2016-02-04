<?php
/**
 * Created by PhpStorm.
 * User: Thiago
 * Date: 17/10/2015
 * Time: 15:00
 */

namespace TSS\Auth\Form;


use Doctrine\ORM\EntityManagerInterface;
use TSS\Auth\Filter\SignupFilter;
use Zend\Form\Form;
use Zend\Stdlib\Hydrator\ClassMethods;

class SignupForm extends Form{
    
    public function __construct(EntityManagerInterface $em, $config = null)
    {
        // we want to ignore the name passed
        parent::__construct('signup');
        $this->setAttribute('method', 'post');
        $this->setAttribute('role', 'form');
        $this->setHydrator(new ClassMethods(false));
        $this->setInputFilter(new SignupFilter($em, $config));

        $this->add(array(
            'name' => 'id',
            'type' => 'hidden',
        ));

        $this->add(array(
            'name' => 'username',
            'type' => 'text',
            'attributes' => array(
                'class' => 'form-control',
                'placeholder' => _('Username'),
            ),
            'options' => array(
                'label' => 'Username',
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
                'label' => 'Email',
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
            'name' => 'password-confirm',
            'type'  => 'password',
            'attributes' => array(
                'class' => 'form-control',
                'placeholder' => _('Confirm Password'),
            ),
            'options' => array(
                'label' => _('Confirm Password'),
                'label_attributes' => array('class' => 'control-label'),
            ),
        ));

        $this->add(array(
            'name' => 'accepted-terms',
            'type' => 'checkbox',
            'options' => array(
                'label' => _('I have read and accepted the terms of use.'),
                'label_attributes' => array('class'  => 'checkbox-inline'),
            ),
        ));

        $this->add(array(
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => array(
                'class' => 'btn btn-lg btn-block btn-primary',
                'value' => _('Sign me up'),
                'id' => 'submit',
            ),
        ));
    }
}
