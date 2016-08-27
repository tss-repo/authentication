<?php
/**
 * @link      http://github.com/zetta-repo/tss-authentication for the canonical source repository
 * @copyright Copyright (c) 2016 Zetta Code
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

        $this->add([
            'name' => 'password-old',
            'type' => 'password',
            'attributes' => [
                'class' => 'form-control',
                'placeholder' => _('Current Password'),
            ],
            'options' => [
                'label' => _('Current Password'),
                'label_attributes' => ['class' => 'control-label'],
                'div' => ['class' => 'form-group', 'class_error' => 'has-error'],
            ],
        ]);

        $this->add([
            'name' => 'password-new',
            'type' => 'password',
            'attributes' => [
                'class' => 'form-control',
                'placeholder' => _('New Password'),
            ],
            'options' => [
                'label' => _('New Password'),
                'label_attributes' => ['class' => 'control-label'],
                'div' => ['class' => 'form-group', 'class_error' => 'has-error'],
            ],
        ]);

        $this->add([
            'name' => 'password-new-confirm',
            'type' => 'password',
            'attributes' => [
                'class' => 'form-control',
                'placeholder' => _('Confirm Password'),
            ],
            'options' => [
                'label' => _('Confirm Password'),
                'label_attributes' => ['class' => 'control-label'],
                'div' => ['class' => 'form-group', 'class_error' => 'has-error'],
            ],
        ]);

        $this->add([
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => [
                'class' => 'btn btn-primary',
                'value' => _('Change Password'),
                'id' => $name . '-submit',
            ],
        ]);
    }
}
