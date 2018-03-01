<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\VarDumper\VarDumper;
use UserBundle\Entity\User;

/**
 * CompanyGroup
 *
 * @ORM\Table(name="CompanyGroup")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\CompanyGroupRepository")
 */
class CompanyGroup
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Company", inversedBy="companyGroups")
     */
    private $company;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="boolean")
     */
    private $enabled = true;

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
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="ownedCompanyGroups")
     */
    private $owner;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Campaign", mappedBy="companyGroup")
     */
    private $campaigns;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\CompanyGroupUserCompanyRelation", mappedBy="companyGroup")
     */
    private $companyGroupUserCompanyRelations;

    /**
     * Get CompanyGroupUserCompanyRelations of TemporalityStatus
     *
     * @param string $statusCode
     *
     * @return array
     */
    public function getCompanyGroupUserCompanyRelationsOfTemporalityStatus($statusCode){

        $r = array();

        foreach($this->companyGroupUserCompanyRelations as $cgucr){

            if($cgucr->getData()->getCompanyGroupUserCompanyRelationTemporalityType()->getCode() == $statusCode and !($cgucr->getData()->getDateUntil())){

                $r[] = $cgucr;

            }

        }

        return $r;

    }

    /**
     * Get CompanyGroupUserCompanyRelations of enabled temporalities
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getCompanyGroupUserCompanyRelationsOfEnabledTemporality(){

        $r = array();

        foreach($this->companyGroupUserCompanyRelations as $cgucr){

            if(!($cgucr->getData()->getDateUntil())){

                $r[] = $cgucr;

            }

        }

        return $r;

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
     * @return CompanyGroup
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
     * Set enabled
     *
     * @param boolean $enabled
     * @return CompanyGroup
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
     * Set company
     *
     * @param \AppBundle\Entity\Company $company
     * @return CompanyGroup
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
     * Set created
     *
     * @param \DateTime $created
     * @return CompanyGroup
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
     * @return CompanyGroup
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
     * Set owner
     *
     * @param \UserBundle\Entity\User $owner
     * @return CompanyGroup
     */
    public function setOwner(\UserBundle\Entity\User $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \UserBundle\Entity\User 
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->campaigns = new ArrayCollection();
        $this->companyGroupUserCompanyRelations = new ArrayCollection();
    }

    /**
     * Add companyGroupUserCompanyRelations
     *
     * @param \AppBundle\Entity\CompanyGroupUserCompanyRelation $companyGroupUserCompanyRelations
     * @return CompanyGroup
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
     * Add campaigns
     *
     * @param \AppBundle\Entity\Campaign $campaigns
     * @return CompanyGroup
     */
    public function addCampaign(\AppBundle\Entity\Campaign $campaigns)
    {
        $this->campaigns[] = $campaigns;

        return $this;
    }

    /**
     * Remove campaigns
     *
     * @param \AppBundle\Entity\Campaign $campaigns
     */
    public function removeCampaign(\AppBundle\Entity\Campaign $campaigns)
    {
        $this->campaigns->removeElement($campaigns);
    }

    /**
     * Get campaigns
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCampaigns()
    {
        return $this->campaigns;
    }
}
