<?php
/**
 * @link      http://github.com/zetta-repo/tss-authentication for the canonical source repository
 * @copyright Copyright (c) 2016 Zetta Code
 */

namespace TSS\Authentication\Entity;

/**
 * Interface CredentialInterface
 * @package TSS\Authentication\Entity
 */
interface CredentialInterface
{
    const TYPE_PASSWORD = 1;
    const TYPE_FACEBOOK = 2;
    const TYPE_API_TOKEN = 3;

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     */
    public function setId($id);

    /**
     * @return int
     */
    public function getType();

    /**
     * @param int $type
     */
    public function setType($type);

    /**
     * @return string
     */
    public function getValue();

    /**
     * @param string $value
     */
    public function setValue($value);
}