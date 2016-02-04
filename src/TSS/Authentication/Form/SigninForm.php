<?php
/**
 * Created by PhpStorm.
 * User: Thiago
 * Date: 01/09/2015
 * Time: 11:03
 */

namespace TSS\Authentication\Form;


use TSS\Authentication\Filter\SigninFilter;
use Zend\Form\Form;

class SigninForm extends Form{
    
    public function __construct()
    {
        // we want to ignore the name passed
        parent::__construct('signin');
        $this->setAttribute('method', 'post');
        $this->setAttribute('role', 'form');
        $this->setInputFilter(new SigninFilter());

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
            'name' => 'password',
            'type' => 'password',
            'attributes' => array(
                'class' => 'form-control',
                'placeholder' => _('Password'),
            ),
            'options' => array(
                'label' => _('Password'),
                'label_attributes' => array('class' => 'control-label'),
            ),
        ));

        $this->add(array(
            'name' => 'remember-me',
            'type' => 'checkbox',
            'options' => array(
                'label' => _('Remember-me'),
                'label_attributes' => array('class'  => 'checkbox-inline'),
            ),
        ));
        
        $this->add(array(
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => array(
                'class' => 'btn btn-lg btn-block btn-primary',
                'value' => _('Sign me in'),
                'id' => 'submit',
            ),
        ));
    }
}
