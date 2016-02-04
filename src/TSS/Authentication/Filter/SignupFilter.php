<?php
/**
 * Created by PhpStorm.
 * User: Thiago
 * Date: 17/10/2015
 * Time: 14:50
 */

namespace TSS\Authentication\Filter;


use Doctrine\ORM\EntityManagerInterface;
use Zend\InputFilter\InputFilter;
use DoctrineModule\Validator\UniqueObject;
use Zend\Validator\Identical;

class SignupFilter extends InputFilter
{
    public function __construct(EntityManagerInterface $em, $config = null) {

        $this->add(array(
            'name'     => 'id',
            'required' => true,
            'filters'  => array(
                array('name' => 'Int'),
            ),
        ));

        $this->add(array(
            'name'     => 'username',
            'required' => true,
            'filters'  => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min'      => 5,
                        'max'      => 100,
                    ),
                ),
                array(
                    'name' => 'DoctrineModule\Validator\UniqueObject',
                    'options' => array(
                        'use_context' => true,
                        'object_manager' => $em,
                        'object_repository' => $em->getRepository($config['tss']['authentication']['config']['identityClass']),
                        'fields' => $config['tss']['authentication']['config']['identityProperty'],
                        'messages' => array(UniqueObject::ERROR_OBJECT_NOT_UNIQUE => sprintf(_('The username %s already exists'), '\'%value%\''))
                    ),
                ),
            ),
        ));

        $this->add(array(
            'name' => 'email',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),

            ),
            'validators' => array(
                array(
                    'name' => 'EmailAddress',
                    'options' => array(
                        'message'  => _('Invalid email address'),
                    ),
                ),
                array(
                    'name' => 'DoctrineModule\Validator\UniqueObject',
                    'options' => array(
                        'use_context' => true,
                        'object_manager' => $em,
                        'object_repository' => $em->getRepository($config['tss']['authentication']['config']['identityClass']),
                        'fields' => $config['tss']['authentication']['config']['identityEmail'],
                        'messages' => array(UniqueObject::ERROR_OBJECT_NOT_UNIQUE => sprintf(_('The email %s already exists'), '\'%value%\''))
                    ),
                ),
            ),
        ));
        
        $this->add(array(
            'name' => 'password', 
            'required' => true, 
            'filters' => array(
                array('name' => 'StringTrim'),
                array('name' => 'StringTrim')
            ),
            'validators' => array(
                array( 
                    'name'    => 'StringLength', 
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min'      => 6,
                        'max'      => 128,
                    ),
                ),
            ), 
        ));
        
        $this->add(array(
            'name' => 'password-confirm',
            'filters' => array(
                array('name' => 'StringTrim'),
                array('name' => 'StringTrim')
            ),
            'validators' => array( 
                array( 
                    'name' => 'identical', 
                    'options' => array('token' => 'password' ) 
                ), 
            ), 
        ));
        
        $this->add(array(
            'name' => 'accepted-terms',
            'validators' => array(
                array(
                    'name'    => 'Identical',
                    'options' => array(
                        'token' => '1',
                        'literal' => TRUE,
                        'messages' => array(Identical::NOT_SAME => _('You must agree to the terms of use.'))
                    ),
                ),
            ),
        ));
    }
}
