<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Widget
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\WidgetRepository")
 */
class Widget
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="unit", type="string", length=255, nullable=true)
     */
    private $unit;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity = "UserWidget", mappedBy="widget")
     */
    private $userWidgets;

    /**
     * @var string
     *
     * @ORM\Column(name="dql", type="text")
     */
    private $DQL;

    /**
     * @var string
     *
     * @ORM\Column(name="dql2", type="text", nullable=true)
     */
    private $DQL2;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->userWidgets = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Set name
     *
     * @param string $name
     * @return Widget
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return Widget
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
     * Add userWidgets
     *
     * @param \AppBundle\Entity\UserWidget $userWidgets
     * @return Widget
     */
    public function addUserWidget(\AppBundle\Entity\UserWidget $userWidgets)
    {
        $this->userWidgets[] = $userWidgets;

        return $this;
    }

    /**
     * Remove userWidgets
     *
     * @param \AppBundle\Entity\UserWidget $userWidgets
     */
    public function removeUserWidget(\AppBundle\Entity\UserWidget $userWidgets)
    {
        $this->userWidgets->removeElement($userWidgets);
    }

    /**
     * Get userWidgets
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserWidgets()
    {
        return $this->userWidgets;
    }

    /**
     * Set DQL
     *
     * @param string $dQL
     * @return Widget
     */
    public function setDQL($dQL)
    {
        $this->DQL = $dQL;

        return $this;
    }

    /**
     * Get DQL
     *
     * @return string 
     */
    public function getDQL()
    {
        return $this->DQL;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Widget
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set unit
     *
     * @param string $unit
     * @return Widget
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;

        return $this;
    }

    /**
     * Get unit
     *
     * @return string 
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * Set DQL2
     *
     * @param string $dQL2
     * @return Widget
     */
    public function setDQL2($dQL2)
    {
        $this->DQL2 = $dQL2;

        return $this;
    }

    /**
     * Get DQL2
     *
     * @return string 
     */
    public function getDQL2()
    {
        return $this->DQL2;
    }
}
