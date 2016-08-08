<?php
/**
 * @link      http://github.com/zetta-repo/tss-authentication for the canonical source repository
 * @copyright Copyright (c) 2016 Zetta Code
 */

namespace TSS\Authentication\Form;

use Doctrine\ORM\EntityManagerInterface;
use TSS\Authentication\Filter\UserFilter;
use TSS\Authentication\Form\Fieldset\UserFieldset;
use Zend\Form\Form;

class UserForm extends Form
{

    /**
     * UserForm constructor.
     * @param EntityManagerInterface $em
     * @param string $name
     * @param array $options
     */
    public function __construct(EntityManagerInterface $em, $name = 'user', $options = [])
    {
        parent::__construct($name, $options);
        $this->setAttribute('method', 'post');
        $this->setAttribute('role', 'form');
        $this->setInputFilter(new UserFilter($em, $options));

        $userFieldser = new UserFieldset($em);
        $userFieldser->setUseAsBaseFieldset(true);

        $this->add($userFieldser);

        $this->add([
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => [
                'class' => 'btn btn-primary',
                'value' => _('Submit'),
                'id' => 'submit',
            ],
        ]);
    }
}
