<?php

namespace TableBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * TableDefaultColumn
 *
 * @ORM\Table(name="TableDefaultColumn")
 * @ORM\Entity(repositoryClass="TableBundle\Entity\TableDefaultColumnRepository")
 */
class TableDefaultColumn
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
     * @var TableEntity
     *
     * @ORM\ManyToOne(targetEntity="TableBundle\Entity\TableEntity", inversedBy="tableDefaultColumns")
     */
    private $tableEntity;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=2)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $property;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="TableBundle\Entity\UserDefaultColumn", mappedBy="tableDefaultColumn")
     */
    private $userDefaultColumns;


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
     * @return TableDefaultColumn
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
     * Set tableEntity
     *
     * @param \TableBundle\Entity\TableEntity $tableEntity
     * @return TableDefaultColumn
     */
    public function setTableEntity(\TableBundle\Entity\TableEntity $tableEntity = null)
    {
        $this->tableEntity = $tableEntity;

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

    /**
     * Set title
     *
     * @param string $title
     * @return TableDefaultColumn
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
     * Constructor
     */
    public function __construct()
    {
        $this->userDefaultColumns = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add userDefaultColumns
     *
     * @param \TableBundle\Entity\UserDefaultColumn $userDefaultColumns
     * @return TableDefaultColumn
     */
    public function addUserDefaultColumn(\TableBundle\Entity\UserDefaultColumn $userDefaultColumns)
    {
        $this->userDefaultColumns[] = $userDefaultColumns;

        return $this;
    }

    /**
     * Remove userDefaultColumns
     *
     * @param \TableBundle\Entity\UserDefaultColumn $userDefaultColumns
     */
    public function removeUserDefaultColumn(\TableBundle\Entity\UserDefaultColumn $userDefaultColumns)
    {
        $this->userDefaultColumns->removeElement($userDefaultColumns);
    }

    /**
     * Get userDefaultColumns
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserDefaultColumns()
    {
        return $this->userDefaultColumns;
    }




    /**
     * Set property
     *
     * @param string $property
     * @return TableDefaultColumn
     */
    public function setProperty($property)
    {
        $this->property = $property;

        return $this;
    }

    /**
     * Get property
     *
     * @return string 
     */
    public function getProperty()
    {
        return $this->property;
    }
}
