<?php
/**
 * Created by PhpStorm.
 * User: Thiago
 * Date: 26/08/2015
 * Time: 14:56
 */

namespace TSS\Authentication\Entity;


use Doctrine\ORM\Mapping as ORM;
use TSS\Bootstrap\Entity\AbstractEntity;

/**
 * AbstractCredential
 *
 * @ORM\MappedSuperclass
 */
class AbstractCredential extends AbstractEntity {
    const TYPE_PASSWORD = 1;
    const TYPE_FACEBOOK = 2;
    const TYPE_API_TOKEN = 3;

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var integer
     *
     * @ORM\Column(type="smallint")
     */
    protected $type;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $value;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    protected $active;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param boolean $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }
}