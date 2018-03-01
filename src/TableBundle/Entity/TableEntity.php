<?php

namespace TableBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * TableEntity
 *
 * @ORM\Table(name="TableEntity")
 * @ORM\Entity(repositoryClass="TableBundle\Entity\TableEntityRepository")
 */
class TableEntity
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
     * @ORM\Column(name="code", type="string", length=255)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="TableBundle\Entity\TableDefaultColumn", mappedBy="tableEntity")
     */
    private $tableDefaultColumns;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="TableBundle\Entity\UserColumn", mappedBy="tableEntity")
     */
    private $userColumns;



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
     * Set code
     *
     * @param string $code
     * @return TableEntity
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return TableEntity
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
     * Set tableDefaultColumns
     *
     * @param \TableBundle\Entity\TableDefaultColumn $tableDefaultColumns
     * @return TableEntity
     */
    public function setTableDefaultColumns(\TableBundle\Entity\TableDefaultColumn $tableDefaultColumns = null)
    {
        $this->tableDefaultColumns = $tableDefaultColumns;

        return $this;
    }

    /**
     * Get tableDefaultColumns
     *
     * @return \TableBundle\Entity\TableDefaultColumn 
     */
    public function getTableDefaultColumns()
    {
        return $this->tableDefaultColumns;
    }

    /**
     * Set userColumns
     *
     * @param \TableBundle\Entity\UserColumn $userColumns
     * @return TableEntity
     */
    public function setUserColumns(\TableBundle\Entity\UserColumn $userColumns = null)
    {
        $this->userColumns = $userColumns;

        return $this;
    }

    /**
     * Get userColumns
     *
     * @return \TableBundle\Entity\UserColumn 
     */
    public function getUserColumns()
    {
        return $this->userColumns;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tableDefaultColumns = new \Doctrine\Common\Collections\ArrayCollection();
        $this->userColumns = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add tableDefaultColumns
     *
     * @param \TableBundle\Entity\TableDefaultColumn $tableDefaultColumns
     * @return TableEntity
     */
    public function addTableDefaultColumn(\TableBundle\Entity\TableDefaultColumn $tableDefaultColumns)
    {
        $this->tableDefaultColumns[] = $tableDefaultColumns;

        return $this;
    }

    /**
     * Remove tableDefaultColumns
     *
     * @param \TableBundle\Entity\TableDefaultColumn $tableDefaultColumns
     */
    public function removeTableDefaultColumn(\TableBundle\Entity\TableDefaultColumn $tableDefaultColumns)
    {
        $this->tableDefaultColumns->removeElement($tableDefaultColumns);
    }

    /**
     * Add userColumns
     *
     * @param \TableBundle\Entity\UserColumn $userColumns
     * @return TableEntity
     */
    public function addUserColumn(\TableBundle\Entity\UserColumn $userColumns)
    {
        $this->userColumns[] = $userColumns;

        return $this;
    }

    /**
     * Remove userColumns
     *
     * @param \TableBundle\Entity\UserColumn $userColumns
     */
    public function removeUserColumn(\TableBundle\Entity\UserColumn $userColumns)
    {
        $this->userColumns->removeElement($userColumns);
    }
}
