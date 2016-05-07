<?php
/**
 * Created by PhpStorm.
 * User: Thiago
 * Date: 07/05/2016
 * Time: 09:17
 */

namespace TSS\Authentication\Form;


use TSS\Authentication\Filter\PasswordChangeFilter;
use Zend\Form\Form;

class PasswordChangeForm extends Form
{
    public function __construct($name = 'password-change', $options = [])
    {
        parent::__construct($name, $options);

        $this->setAttribute('method', 'post');
        $this->setAttribute('role', 'form');
        $this->setInputFilter(new PasswordChangeFilter($options));

        $this->add(array(
            'name' => 'password-old',
            'type' => 'password',
            'attributes' => array(
                'class' => 'form-control',
                'placeholder' => _('Current Password'),
            ),
            'options' => array(
                'label' => _('Current Password'),
                'label_attributes' => array('class' => 'control-label'),
            ),
        ));

        $this->add(array(
            'name' => 'password-new',
            'type' => 'password',
            'attributes' => array(
                'class' => 'form-control',
                'placeholder' => _('New Password'),
            ),
            'options' => array(
                'label' =>  _('New Password'),
                'label_attributes' => array('class' => 'control-label'),
            ),
        ));

        $this->add(array(
            'name' => 'password-new-confirm',
            'type' => 'password',
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
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'class' => 'btn btn-primary',
                'value' => _('Change Password'),
                'id' => $name . '-submit',
            ),
        ));
    }
}