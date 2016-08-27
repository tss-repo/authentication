<?php
/**
 * @link      http://github.com/zetta-repo/tss-authentication for the canonical source repository
 * @copyright Copyright (c) 2016 Zetta Code
 */

namespace TSS\Authentication\Entity;

/**
 * Interface UserInterface
 * @package TSS\Authentication\Entity
 */
interface UserInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     * @return void
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getUsername();

    /**
     * @param string $username
     * @return void
     */
    public function setUsername($username);

    /**
     * @return string
     */
    public function getEmail();

    /**
     * @param string $email
     * @return void
     */
    public function setEmail($email);

    /**
     * @return string
     */
    public function getAvatar();

    /**
     * @param string $avatar
     * @return void
     */
    public function setAvatar($avatar);

    /**
     * @return string
     */
    public function getToken();

    /**
     * @param string $token
     * @return void
     */
    public function setToken($token);

    /**
     * @return boolean
     */
    public function isConfirmedEmail();

    /**
     * @param boolean $confirmedEmail
     * @return void
     */
    public function setConfirmedEmail($confirmedEmail);

    /**
     * @return boolean
     */
    public function isSignAllowed();

    /**
     * @param boolean $signAllowed
     * @return void
     */
    public function setSignAllowed($signAllowed);

    /**
     * @return RoleInterface
     */
    public function getRole();

    /**
     * @param RoleInterface $role
     */
    public function setRole($role);

    /**
     * @return string
     */
    public function getRoleName();
}
