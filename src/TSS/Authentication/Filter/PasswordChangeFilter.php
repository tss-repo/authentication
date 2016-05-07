<?php
/**
 * Created by PhpStorm.
 * User: Thiago
 * Date: 07/05/2016
 * Time: 09:18
 */

namespace TSS\Authentication\Filter;


use Zend\InputFilter\InputFilter;

class PasswordChangeFilter extends InputFilter
{

    /**
     * PasswordChangeFilter constructor.
     * @param array $options
     */
    public function __construct($options = [])
    {
        $this->add(array(
            'name' => 'password-old',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
        ));

        $this->add(array(
            'name' => 'password-new',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min' => 5,
                        'max' => 16,
                    ),
                ),
            ),
        ));

        $this->add(array(
            'name' => 'password-new-confirm',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'Identical',
                    'options' => array(
                        'token' => 'password-new',
                    ),
                ),
            ),
        ));
    }
}