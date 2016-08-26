<?php
/**
 * @link      http://github.com/zetta-repo/tss-authentication for the canonical source repository
 * @copyright Copyright (c) 2016 Zetta Code
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
