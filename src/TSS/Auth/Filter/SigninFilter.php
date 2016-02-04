<?php
/**
 * Created by PhpStorm.
 * User: Thiago
 * Date: 17/10/2015
 * Time: 14:50
 */

namespace TSS\Auth\Filter;


use Zend\InputFilter\InputFilter;
 
class SigninFilter extends InputFilter
{
    public function __construct()
    {
        $this->add(array(
            'name'     => 'username',
            'required' => true,
            'filters'  => array(
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min'      => 5,
                        'max'      => 255,
                    ),
                ),
            ),
        ));
        
        $this->add(array(
            'name' => 'password', 
            'required' => true, 
            'filters' => array(
                array('name' => 'StringTrim')
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                    ),
                ),
            ),
        ));
    }
}
