<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use UserBundle\Entity\User;

/**
 * UserCompany
 *
 * @ORM\Table(name="UserCompany")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\UserCompanyRepository")
 */
class UserCompany
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
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="userCompanyRelations")
     */
    private $user;

    /**
     * @var Company
     *
     * @ORM\ManyToOne(targetEntity="Company", inversedBy="userCompanyRelations")
     */
    private $company;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated", type="datetime", nullable=true)
     */
    private $updated;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="UserCompanyTemporality", mappedBy="userCompany")
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private $temporalities;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\CommissionUserCompanyRelation", mappedBy="userCompany")
     */
    private $commissionUserCompanyRelations;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\SubcommissionTeamUserCompany", mappedBy="userCompany")
     */
    private $subcommissionTeamUserCompanyRelations;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Budget", mappedBy="author")
     */
    private $budgets;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\CompanyGroupUserCompanyRelation", mappedBy="userCompany")
     */
    private $companyGroupUserCompanyRelations;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\CampaignManager", mappedBy="userCompany")
     */
    private $campaignManagers;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\AllocationUnit", mappedBy="userCompany")
     */
    private $allocationUnits;

    /**
     * Get last temporality data
     *
     * @return UserCompanyTemporality
     */
    public function getData(){

        return $this->getTemporalities()->first();

    }

    /**
     * Vrátí celé jméno účastníka tohoto vztahu
     *
     * @return string
     */
    public function getFullname(){

        return $this->getUser()->getFullName();

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
     * Set created
     *
     * @param \DateTime $created
     * @return UserCompany
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return UserCompany
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set user
     *
     * @param \UserBundle\Entity\User $user
     * @return UserCompany
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
     * Set company
     *
     * @param \AppBundle\Entity\Company $company
     * @return UserCompany
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
     * Add temporalities
     *
     * @param \AppBundle\Entity\UserCompanyTemporality $temporalities
     * @return UserCompany
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
     * Add commissionUserCompanyRelations
     *
     * @param \AppBundle\Entity\CommissionUserCompanyRelation $commissionUserCompanyRelations
     * @return UserCompany
     */
    public function addCommissionUserCompanyRelation(\AppBundle\Entity\CommissionUserCompanyRelation $commissionUserCompanyRelations)
    {
        $this->commissionUserCompanyRelations[] = $commissionUserCompanyRelations;

        return $this;
    }

    /**
     * Remove commissionUserCompanyRelations
     *
     * @param \AppBundle\Entity\CommissionUserCompanyRelation $commissionUserCompanyRelations
     */
    public function removeCommissionUserCompanyRelation(\AppBundle\Entity\CommissionUserCompanyRelation $commissionUserCompanyRelations)
    {
        $this->commissionUserCompanyRelations->removeElement($commissionUserCompanyRelations);
    }

    /**
     * Get commissionUserCompanyRelations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCommissionUserCompanyRelations()
    {
        return $this->commissionUserCompanyRelations;
    }

    /**
     * Add subcommissionTeamUserCompanyRelations
     *
     * @param \AppBundle\Entity\SubcommissionTeamUserCompany $subcommissionTeamUserCompanyRelations
     * @return UserCompany
     */
    public function addSubcommissionTeamUserCompanyRelation(\AppBundle\Entity\SubcommissionTeamUserCompany $subcommissionTeamUserCompanyRelations)
    {
        $this->subcommissionTeamUserCompanyRelations[] = $subcommissionTeamUserCompanyRelations;

        return $this;
    }

    /**
     * Remove subcommissionTeamUserCompanyRelations
     *
     * @param \AppBundle\Entity\SubcommissionTeamUserCompany $subcommissionTeamUserCompanyRelations
     */
    public function removeSubcommissionTeamUserCompanyRelation(\AppBundle\Entity\SubcommissionTeamUserCompany $subcommissionTeamUserCompanyRelations)
    {
        $this->subcommissionTeamUserCompanyRelations->removeElement($subcommissionTeamUserCompanyRelations);
    }

    /**
     * Get subcommissionTeamUserCompanyRelations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSubcommissionTeamUserCompanyRelations()
    {
        return $this->subcommissionTeamUserCompanyRelations;
    }

    /**
     * Add budgets
     *
     * @param \AppBundle\Entity\Budget $budgets
     * @return UserCompany
     */
    public function addBudget(\AppBundle\Entity\Budget $budgets)
    {
        $this->budgets[] = $budgets;

        return $this;
    }

    /**
     * Remove budgets
     *
     * @param \AppBundle\Entity\Budget $budgets
     */
    public function removeBudget(\AppBundle\Entity\Budget $budgets)
    {
        $this->budgets->removeElement($budgets);
    }

    /**
     * Get budgets
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBudgets()
    {
        return $this->budgets;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->temporalities = new \Doctrine\Common\Collections\ArrayCollection();
        $this->commissionUserCompanyRelations = new \Doctrine\Common\Collections\ArrayCollection();
        $this->subcommissionTeamUserCompanyRelations = new \Doctrine\Common\Collections\ArrayCollection();
        $this->budgets = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     * Add companyGroupUserCompanyRelations
     *
     * @param \AppBundle\Entity\CompanyGroupUserCompanyRelation $companyGroupUserCompanyRelations
     * @return UserCompany
     */
    public function addCompanyGroupUserCompanyRelation(\AppBundle\Entity\CompanyGroupUserCompanyRelation $companyGroupUserCompanyRelations)
    {
        $this->companyGroupUserCompanyRelations[] = $companyGroupUserCompanyRelations;

        return $this;
    }

    /**
     * Remove companyGroupUserCompanyRelations
     *
     * @param \AppBundle\Entity\CompanyGroupUserCompanyRelation $companyGroupUserCompanyRelations
     */
    public function removeCompanyGroupUserCompanyRelation(\AppBundle\Entity\CompanyGroupUserCompanyRelation $companyGroupUserCompanyRelations)
    {
        $this->companyGroupUserCompanyRelations->removeElement($companyGroupUserCompanyRelations);
    }

    /**
     * Get companyGroupUserCompanyRelations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCompanyGroupUserCompanyRelations()
    {
        return $this->companyGroupUserCompanyRelations;
    }

    /**
     * Add campaignManagers
     *
     * @param \AppBundle\Entity\CampaignManager $campaignManagers
     * @return UserCompany
     */
    public function addCampaignManager(\AppBundle\Entity\CampaignManager $campaignManagers)
    {
        $this->campaignManagers[] = $campaignManagers;

        return $this;
    }

    /**
     * Remove campaignManagers
     *
     * @param \AppBundle\Entity\CampaignManager $campaignManagers
     */
    public function removeCampaignManager(\AppBundle\Entity\CampaignManager $campaignManagers)
    {
        $this->campaignManagers->removeElement($campaignManagers);
    }

    /**
     * Get campaignManagers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCampaignManagers()
    {
        return $this->campaignManagers;
    }

    /**
     * Add allocationUnits
     *
     * @param \AppBundle\Entity\AllocationUnit $allocationUnits
     * @return UserCompany
     */
    public function addAllocationUnit(\AppBundle\Entity\AllocationUnit $allocationUnits)
    {
        $this->allocationUnits[] = $allocationUnits;

        return $this;
    }

    /**
     * Remove allocationUnits
     *
     * @param \AppBundle\Entity\AllocationUnit $allocationUnits
     */
    public function removeAllocationUnit(\AppBundle\Entity\AllocationUnit $allocationUnits)
    {
        $this->allocationUnits->removeElement($allocationUnits);
    }

    /**
     * Get allocationUnits
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAllocationUnits()
    {
        return $this->allocationUnits;
    }
}
