<?php
/**
 * Created by PhpStorm.
 * User: Thiago
 * Date: 02/12/2015
 * Time: 14:40
 */

namespace TSS\Auth\Filter;


use Doctrine\ORM\EntityManagerInterface;
use TSS\Auth\Filter\Fieldset\UserFiledsetFilter;
use Zend\InputFilter\InputFilter;

class ProfileFilter extends InputFilter
{
    /**
     * ProfileFilter constructor.
     * @param EntityManagerInterface $em
     * @param null $config
     */
    public function __construct(EntityManagerInterface $em, $config = null)
    {
        $userFieldsetFilter = new UserFiledsetFilter($em, $config);
        $userFieldsetFilter->remove('password');
        $this->add($userFieldsetFilter, 'user');
    }
}