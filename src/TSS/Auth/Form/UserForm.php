<?php
/**
 * Created by PhpStorm.
 * User: Thiago
 * Date: 02/12/2015
 * Time: 13:36
 */

namespace TSS\Auth\Form;


use Doctrine\ORM\EntityManagerInterface;
use TSS\Auth\Filter\UserFilter;
use TSS\Auth\Form\Fieldset\UserFieldset;
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

        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'class' => 'btn btn-primary',
                'value' => _('Submit'),
                'id' => 'submit',
            ),
        ));
    }
}