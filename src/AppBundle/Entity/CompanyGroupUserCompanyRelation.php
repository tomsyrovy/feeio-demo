<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * CompanyGroupUserCompanyRelation
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\CompanyGroupUserCompanyRelationRepository")
 */
class CompanyGroupUserCompanyRelation
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
     * @var UserCompany
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\UserCompany", inversedBy="companyGroupUserCompanyRelations")
     */
    private $userCompany;

    /**
     * @var CompanyGroup
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CompanyGroup", inversedBy="companyGroupUserCompanyRelations")
     */
    private $companyGroup;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\CompanyGroupUserCompanyRelationTemporality", mappedBy="companyGroupUserCompanyRelation")
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private $temporalities;

    /**
     * Get last temporality data
     *
     * @return CompanyGroupUserCompanyRelationTemporality
     */
    public function getData(){

        return $this->getTemporalities()->first();

    }

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
     * Set userCompany
     *
     * @param \AppBundle\Entity\UserCompany $userCompany
     * @return CompanyGroupUserCompanyRelation
     */
    public function setUserCompany(\AppBundle\Entity\UserCompany $userCompany = null)
    {
        $this->userCompany = $userCompany;

        return $this;
    }

    /**
     * Get userCompany
     *
     * @return \AppBundle\Entity\UserCompany 
     */
    public function getUserCompany()
    {
        return $this->userCompany;
    }

    /**
     * Set companyGroup
     *
     * @param \AppBundle\Entity\CompanyGroup $companyGroup
     * @return CompanyGroupUserCompanyRelation
     */
    public function setCompanyGroup(\AppBundle\Entity\CompanyGroup $companyGroup = null)
    {
        $this->companyGroup = $companyGroup;

        return $this;
    }

    /**
     * Get companyGroup
     *
     * @return \AppBundle\Entity\CompanyGroup 
     */
    public function getCompanyGroup()
    {
        return $this->companyGroup;
    }

    /**
     * Add temporalities
     *
     * @param \AppBundle\Entity\CompanyGroupUserCompanyRelationTemporality $temporalities
     * @return CompanyGroupUserCompanyRelation
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
