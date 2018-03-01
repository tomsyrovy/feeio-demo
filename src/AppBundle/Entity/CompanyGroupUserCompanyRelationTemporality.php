<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CompanyGroupUserCompanyRelationTemporality
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\CompanyGroupUserCompanyRelationTemporalityRepository")
 */
class CompanyGroupUserCompanyRelationTemporality
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
     * @var CompanyGroupUserCompanyRelation
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CompanyGroupUserCompanyRelation", inversedBy="temporalities")
     */
    private $companyGroupUserCompanyRelation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateFrom", type="datetime")
     */
    private $dateFrom;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateUntil", type="datetime", nullable=true)
     */
    private $dateUntil;

    /**
     * @var CompanyGroupUserCompanyRelationTemporalityType
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CompanyGroupUserCompanyRelationTemporalityType", inversedBy="temporalities")
     */
    private $companyGroupUserCompanyRelationTemporalityType;



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
     * Set dateFrom
     *
     * @param \DateTime $dateFrom
     * @return CompanyGroupUserCompanyRelationTemporality
     */
    public function setDateFrom($dateFrom)
    {
        $this->dateFrom = $dateFrom;

        return $this;
    }

    /**
     * Get dateFrom
     *
     * @return \DateTime 
     */
    public function getDateFrom()
    {
        return $this->dateFrom;
    }

    /**
     * Set dateUntil
     *
     * @param \DateTime $dateUntil
     * @return CompanyGroupUserCompanyRelationTemporality
     */
    public function setDateUntil($dateUntil)
    {
        $this->dateUntil = $dateUntil;

        return $this;
    }

    /**
     * Get dateUntil
     *
     * @return \DateTime 
     */
    public function getDateUntil()
    {
        return $this->dateUntil;
    }

    /**
     * Set companyGroupUserCompanyRelation
     *
     * @param \AppBundle\Entity\CompanyGroupUserCompanyRelation $companyGroupUserCompanyRelation
     * @return CompanyGroupUserCompanyRelationTemporality
     */
    public function setCompanyGroupUserCompanyRelation(\AppBundle\Entity\CompanyGroupUserCompanyRelation $companyGroupUserCompanyRelation = null)
    {
        $this->companyGroupUserCompanyRelation = $companyGroupUserCompanyRelation;

        return $this;
    }

    /**
     * Get companyGroupUserCompanyRelation
     *
     * @return \AppBundle\Entity\CompanyGroupUserCompanyRelation 
     */
    public function getCompanyGroupUserCompanyRelation()
    {
        return $this->companyGroupUserCompanyRelation;
    }

    /**
     * Set companyGroupUserCompanyRelationTemporalityType
     *
     * @param \AppBundle\Entity\CompanyGroupUserCompanyRelationTemporalityType $companyGroupUserCompanyRelationTemporalityType
     * @return CompanyGroupUserCompanyRelationTemporality
     */
    public function setCompanyGroupUserCompanyRelationTemporalityType(\AppBundle\Entity\CompanyGroupUserCompanyRelationTemporalityType $companyGroupUserCompanyRelationTemporalityType = null)
    {
        $this->companyGroupUserCompanyRelationTemporalityType = $companyGroupUserCompanyRelationTemporalityType;

        return $this;
    }

    /**
     * Get companyGroupUserCompanyRelationTemporalityType
     *
     * @return \AppBundle\Entity\CompanyGroupUserCompanyRelationTemporalityType 
     */
    public function getCompanyGroupUserCompanyRelationTemporalityType()
    {
        return $this->companyGroupUserCompanyRelationTemporalityType;
    }
}
