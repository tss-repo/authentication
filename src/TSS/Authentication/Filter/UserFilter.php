<?php
/**
 * Created by PhpStorm.
 * User: Thiago
 * Date: 02/12/2015
 * Time: 14:01
 */

namespace TSS\Authentication\Filter;


use Doctrine\ORM\EntityManagerInterface;
use TSS\Authentication\Filter\Fieldset\UserFiledsetFilter;
use Zend\InputFilter\InputFilter;

class UserFilter extends InputFilter
{
    /**
     * UserFilter constructor.
     * @param EntityManagerInterface $em
     * @param null $config
     */
    public function __construct(EntityManagerInterface $em, $config = null)
    {
        $this->add(new UserFiledsetFilter($em, $config), 'user');
    }
}