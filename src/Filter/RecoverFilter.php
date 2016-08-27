<?php
/**
 * @link      http://github.com/zetta-repo/tss-authentication for the canonical source repository
 * @copyright Copyright (c) 2016 Zetta Code
 */

namespace TSS\Authentication\Filter;

use Zend\InputFilter\InputFilter;
use Zend\Validator\EmailAddress;

class RecoverFilter extends InputFilter
{
    /**
     * RecoverPasswordFilter constructor.
     * @param array $options
     */
    public function __construct($options = [])
    {
        $this->add([
            'name' => 'email',
            'required' => true,
            'filters' => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],

            ],
            'validators' => [
                [
                    'name' => EmailAddress::class,
                    'options' => [
                        'message' => _('Invalid email address')
                    ],
                ],
            ],
        ]);
    }
}
