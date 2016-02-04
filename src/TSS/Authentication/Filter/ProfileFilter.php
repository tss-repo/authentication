<?php
/**
 * Created by PhpStorm.
 * User: Thiago
 * Date: 02/12/2015
 * Time: 14:40
 */

namespace TSS\Authentication\Filter;


use Doctrine\ORM\EntityManagerInterface;
use TSS\Authentication\Filter\Fieldset\UserFiledsetFilter;
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