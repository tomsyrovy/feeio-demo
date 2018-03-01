<?php

namespace TableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use UserBundle\Entity\User;

/**
 * UserColumn
 *
 * @ORM\Table(name="UserColumn")
 * @ORM\Entity(repositoryClass="TableBundle\Entity\UserColumnRepository")
 */
class UserColumn
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
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="formula", type="string")
     */
    private $formula;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="userColumns")
     */
    private $user;

    /**
     * @var TableEntity
     *
     * @ORM\ManyToOne(targetEntity="TableBundle\Entity\TableEntity", inversedBy="userColumns")
     */
    private $tableEntity;



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
     * Set title
     *
     * @param string $title
     * @return UserColumn
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set formula
     *
     * @param string $formula
     * @return UserColumn
     */
    public function setFormula($formula)
    {
        $this->formula = $formula;

        return $this;
    }

    /**
     * Get formula
     *
     * @return string 
     */
    public function getFormula()
    {
        return $this->formula;
    }

    /**
     * Set user
     *
     * @param \UserBundle\Entity\User $user
     * @return UserColumn
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
     * Set tableEntity
     *
     * @param \TableBundle\Entity\TableEntity $tableEntity
     * @return UserColumn
     */
    public function setTableEntity(\TableBundle\Entity\TableEntity $tableEntity = null)
    {
        $this->tableEntity = $tableEntity;
        $tableEntity->addUserColumn($this);

        return $this;
    }

    /**
     * Get tableEntity
     *
     * @return \TableBundle\Entity\TableEntity 
     */
    public function getTableEntity()
    {
        return $this->tableEntity;
    }
}
