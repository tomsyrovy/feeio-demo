<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * JobPosition
 *
 * @ORM\Table(name="JobPosition")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\JobPositionRepository")
 */
class JobPosition
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
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    protected $enabled = true;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="name_external", type="string", length=255)
     */
    private $nameExternal;

    /**
     * @var Company
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Company", inversedBy="jobPositions")
     */
    private $company;

    /**
     * @var integer
     *
     * @ORM\Column(name="externalRate", type="integer", nullable=true)
     */
    private $externalRate;

    /**
     * @var integer
     *
     * @ORM\Column(name="internalRate", type="integer", nullable=true)
     */
    private $internalRate;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="UserCompanyTemporality", mappedBy="jobposition", cascade={"persist"})
     */
    private $temporalities;

    /**
     * @return bool
     */
    public function canBeRemoved(){
        $ts = $this->getTemporalities()->toArray();
        /** @var UserCompanyTemporality $t */
        foreach($ts as $t){
            if($t->getUntil() == null){
                return false;
            }
        }
        return true;
    }

    /**
     * @return bool
     */
    public function getCanBeRemoved(){
        return $this->canBeRemoved();
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
     * @return JobPosition
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
     * Set externalRate
     *
     * @param integer $externalRate
     * @return JobPosition
     */
    public function setExternalRate($externalRate)
    {
        $this->externalRate = $externalRate;

        return $this;
    }

    /**
     * Get externalRate
     *
     * @return integer 
     */
    public function getExternalRate()
    {
        return $this->externalRate;
    }

    /**
     * Set internalRate
     *
     * @param integer $internalRate
     * @return JobPosition
     */
    public function setInternalRate($internalRate)
    {
        $this->internalRate = $internalRate;

        return $this;
    }

    /**
     * Get internalRate
     *
     * @return integer 
     */
    public function getInternalRate()
    {
        return $this->internalRate;
    }

    /**
     * Set company
     *
     * @param \AppBundle\Entity\Company $company
     * @return JobPosition
     */
    public function setCompany(\AppBundle\Entity\Company $company = null)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return \AppBundle\Entity\Company 
     */
    public function getCompany()
    {
        return $this->company;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->temporalities = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add temporalities
     *
     * @param \AppBundle\Entity\UserCompanyTemporality $temporalities
     * @return JobPosition
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
     * Set enabled
     *
     * @param boolean $enabled
     * @return JobPosition
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean 
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set nameExternal
     *
     * @param string $nameExternal
     * @return JobPosition
     */
    public function setNameExternal($nameExternal)
    {
        $this->nameExternal = $nameExternal;

        return $this;
    }

    /**
     * Get nameExternal
     *
     * @return string 
     */
    public function getNameExternal()
    {
        return $this->nameExternal;
    }
}
