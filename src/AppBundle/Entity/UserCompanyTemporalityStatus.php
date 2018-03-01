<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * UserCompanyTemporalityStatus
 *
 * @ORM\Table(name="UserCompanyTemporalityStatus")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\UserCompanyTemporalityStatusRepository")
 */
class UserCompanyTemporalityStatus
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
     * @var boolean
     *
     * @ORM\Column(name="automation", type="boolean")
     */
    private $automation;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="UserCompanyTemporality", mappedBy="status")
     */
    private $temporalities;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->temporalities = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return UserCompanyTemporalityStatus
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
     * @return UserCompanyTemporalityStatus
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
     * Add temporalities
     *
     * @param \AppBundle\Entity\UserCompanyTemporality $temporalities
     * @return UserCompanyTemporalityStatus
     */
    public function addTemporality(\AppBundle\Entity\UserCompanyTemporality $temporalities)
    {
        $this->temporalities[] = $temporalities;

        return $this;
    }

    /**
     * Remove temporalities
     *
     * @param \AppBundle\Entity\UserCompanyTemporality $temporalities
     */
    public function removeTemporality(\AppBundle\Entity\UserCompanyTemporality $temporalities)
    {
        $this->temporalities->removeElement($temporalities);
    }

    /**
     * Get temporalities
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTemporalities()
    {
        return $this->temporalities;
    }

    /**
     * Set automation
     *
     * @param boolean $automation
     * @return UserCompanyTemporalityStatus
     */
    public function setAutomation($automation)
    {
        $this->automation = $automation;

        return $this;
    }

    /**
     * Get automation
     *
     * @return boolean 
     */
    public function getAutomation()
    {
        return $this->automation;
    }
}
