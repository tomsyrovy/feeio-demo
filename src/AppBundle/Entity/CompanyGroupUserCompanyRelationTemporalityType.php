<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * CompanyGroupUserCompanyRelationTemporalityType
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\CompanyGroupUserCompanyRelationTemporalityTypeRepository")
 */
class CompanyGroupUserCompanyRelationTemporalityType
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\CompanyGroupUserCompanyRelationTemporality", mappedBy="companyGroupUserCompanyRelationTemporalityType")
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
     * Set code
     *
     * @param string $code
     * @return CompanyGroupUserCompanyRelationTemporalityType
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
     * Set name
     *
     * @param string $name
     * @return CompanyGroupUserCompanyRelationTemporalityType
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
     * Add temporalities
     *
     * @param \AppBundle\Entity\CompanyGroupUserCompanyRelationTemporality $temporalities
     * @return CompanyGroupUserCompanyRelationTemporalityType
     */
    public function addTemporality(\AppBundle\Entity\CompanyGroupUserCompanyRelationTemporality $temporalities)
    {
        $this->temporalities[] = $temporalities;

        return $this;
    }

    /**
     * Remove temporalities
     *
     * @param \AppBundle\Entity\CompanyGroupUserCompanyRelationTemporality $temporalities
     */
    public function removeTemporality(\AppBundle\Entity\CompanyGroupUserCompanyRelationTemporality $temporalities)
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
}
