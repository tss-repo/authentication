<?php
/**
 * Created by PhpStorm.
 * User: Thiago
 * Date: 26/08/2015
 * Time: 14:35
 */

namespace TSS\Authentication\Permissions\Acl;


use Zend\Permissions\Acl\Acl as ZendAcl;
use Zend\Permissions\Acl\Resource\GenericResource;
use Zend\Permissions\Acl\Role\GenericRole;

class Acl extends ZendAcl
{
    /**
     *
     * @var string
     */
    protected $defaultRole = 'Guest';

    /**
     * Constructor
     *
     * @param array $config
     * @throws \Exception
     * @return Acl
     */
    public function __construct($config)
    {
        if (!isset($config['roles']) || !isset($config['resources'])) {
            throw new \Exception('Invalid ACL Config found');
        }

        $roles = $config['roles'];
        $resources = $config['resources'];
        if(isset($config['default_role'])) {
            $this->defaultRole = $config['default_role'];
        }


        if (!isset($roles[$this->defaultRole])) {
            $roles[$this->defaultRole] = '';
        }

        $this->addRoles($roles);
        $this->addResources($resources);
    }

    /**
     * @return string
     */
    public function getDefaultRole()
    {
        return $this->defaultRole;
    }

    /**
     * @param mixed $defaultRole
     */
    public function setDefaultRole($defaultRole)
    {
        $this->defaultRole = $defaultRole;
    }

    /**
     * Adds Roles to ACL
     *
     * @param array $roles
     * @return Acl
     */
    protected function addRoles($roles)
    {
        foreach ($roles as $name => $parent) {
            if (!$this->hasRole($name)) {
                if (empty($parent) && !is_array($parent)) {
                    $parent = array();
                }
                $this->addRole(new GenericRole($name), $parent);
            }
        }

        return $this;
    }

    /**
     * Adds Resources to ACL
     *
     * @param $resources
     * @return Acl
     * @throws \Exception
     */
    protected function addResources($resources)
    {
        foreach ($resources as $permission => $controllers) {
            foreach ($controllers as $controller => $actions) {
                if ($controller == '') {
                    $controller = null;
                } else {
                    if (!$this->hasResource($controller)) {
                        $this->addResource(new GenericResource($controller));
                    }
                }

                foreach ($actions as $action => $roles) {
                    if ($action == '') {
                        $action = null;
                    }

                    if ($permission == 'allow') {
                        foreach ($roles as $role) {
                            $this->allow($role, $controller, $action);
                        }
                    } elseif ($permission == 'deny') {
                        foreach ($roles as $role) {
                            $this->deny($role, $controller, $action);
                        }
                    } else {
                        throw new \Exception('No valid permission defined: ' . $permission);
                    }
                }

            }
        }

        return $this;
    }
}