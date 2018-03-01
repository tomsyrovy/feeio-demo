<?php

namespace TableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use UserBundle\Entity\User;

/**
 * UserDefaultColumn
 *
 * @ORM\Table(name="UserDefaultColumn")
 * @ORM\Entity(repositoryClass="TableBundle\Entity\UserDefaultColumnRepository")
 */
class UserDefaultColumn
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="userDefaultColumns")
     */
    private $user;

    /**
     * @var TableDefaultColumn
     *
     * @ORM\ManyToOne(targetEntity="TableBundle\Entity\TableDefaultColumn", inversedBy="userDefaultColumns")
     */
    private $tableDefaultColumn;

    /**
     * @var boolean
     *
     * @ORM\Column(name="hidden", type="boolean")
     */
    private $hidden = false;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set hidden
     *
     * @param boolean $hidden
     * @return UserDefaultColumn
     */
    public function setHidden($hidden)
    {
        $this->hidden = $hidden;

        return $this;
    }

    /**
     * Get hidden
     *
     * @return boolean 
     */
    public function getHidden()
    {
        return $this->hidden;
    }

    /**
     * Set user
     *
     * @param \UserBundle\Entity\User $user
     * @return UserDefaultColumn
     */
    public function setUser(\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set tableDefaultColumn
     *
     * @param \TableBundle\Entity\TableDefaultColumn $tableDefaultColumn
     * @return UserDefaultColumn
     */
    public function setTableDefaultColumn(\TableBundle\Entity\TableDefaultColumn $tableDefaultColumn = null)
    {
        $this->tableDefaultColumn = $tableDefaultColumn;

        return $this;
    }

    /**
     * Get tableDefaultColumn
     *
     * @return \TableBundle\Entity\TableDefaultColumn 
     */
    public function getTableDefaultColumn()
    {
        return $this->tableDefaultColumn;
    }
}
