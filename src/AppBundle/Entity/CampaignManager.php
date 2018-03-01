<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CampaignManager
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\CampaignManagerRepository")
 */
class CampaignManager
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
     * @var Campaign
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Campaign", inversedBy="campaignManagers", cascade={"persist"})
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    private $campaign;

    /**
     * @var UserCompany
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\UserCompany", inversedBy="campaignManagers")
     */
    private $userCompany;

    /**
     * @var boolean
     *
     * @ORM\Column(name="owner", type="boolean")
     */
    private $owner = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="jobManager", type="boolean")
     */
    private $jobManager = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="jobConsultant", type="boolean")
     */
    private $jobConsultant = true;


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
     * Set owner
     *
     * @param boolean $owner
     * @return CampaignManager
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return boolean 
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set jobManager
     *
     * @param boolean $jobManager
     * @return CampaignManager
     */
    public function setJobManager($jobManager)
    {
        $this->jobManager = $jobManager;

        return $this;
    }

    /**
     * Get jobManager
     *
     * @return boolean 
     */
    public function getJobManager()
    {
        return $this->jobManager;
    }

    /**
     * Set jobConsultant
     *
     * @param boolean $jobConsultant
     * @return CampaignManager
     */
    public function setJobConsultant($jobConsultant)
    {
        $this->jobConsultant = $jobConsultant;

        return $this;
    }

    /**
     * Get jobConsultant
     *
     * @return boolean 
     */
    public function getJobConsultant()
    {
        return $this->jobConsultant;
    }

    /**
     * Set campaign
     *
     * @param \AppBundle\Entity\Campaign $campaign
     * @return CampaignManager
     */
    public function setCampaign(\AppBundle\Entity\Campaign $campaign = null)
    {
        $this->campaign = $campaign;

        return $this;
    }

    /**
     * Get campaign
     *
     * @return \AppBundle\Entity\Campaign 
     */
    public function getCampaign()
    {
        return $this->campaign;
    }

    /**
     * Set userCompany
     *
     * @param \AppBundle\Entity\UserCompany $userCompany
     * @return CampaignManager
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
}
