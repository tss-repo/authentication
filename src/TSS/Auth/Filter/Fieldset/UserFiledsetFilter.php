<?php
/**
 * Created by PhpStorm.
 * User: Thiago
 * Date: 02/12/2015
 * Time: 14:03
 */

namespace TSS\Auth\Filter\Fieldset;


use Doctrine\ORM\EntityManagerInterface;
use DoctrineModule\Validator\UniqueObject;
use Zend\InputFilter\InputFilter;

class UserFiledsetFilter extends InputFilter
{

    /**
     * UserFiledsetFilter constructor.
     * @param EntityManagerInterface $em
     * @param null $config
     */
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
                        'object_repository' => $em->getRepository($config['tss']['auth']['config']['identityClass']),
                        'fields' => $config['tss']['auth']['config']['identityProperty'],
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
                        'object_repository' => $em->getRepository($config['tss']['auth']['config']['identityClass']),
                        'fields' => $config['tss']['auth']['config']['identityEmail'],
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
    }
}