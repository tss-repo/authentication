<?php
/**
 * @link      http://github.com/zetta-repo/tss-authentication for the canonical source repository
 * @copyright Copyright (c) 2016 Zetta Code
 */

namespace TSS\Authentication\Form;

use TSS\Authentication\Filter\SigninFilter;
use Zend\Form\Form;

class SigninForm extends Form
{

    public function __construct(EntityManagerInterface $em, $name = 'signin', $options = null)
    {
        parent::__construct($name, $options);
        $this->setAttribute('method', 'post');
        $this->setAttribute('role', 'form');
        $this->setInputFilter(new SigninFilter());

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
                'label' => _('Password'),
                'label_attributes' => ['class' => 'control-label'],
            ],
        ]);

        $this->add([
            'name' => 'remember-me',
            'type' => 'checkbox',
            'options' => [
                'label' => _('Remember-me'),
                'label_attributes' => ['class' => 'checkbox-inline'],
            ],
        ]);

        $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'class' => 'btn btn-lg btn-block btn-primary',
                'value' => _('Sign me in'),
                'id' => 'submit',
            ],
        ]);
    }
}
