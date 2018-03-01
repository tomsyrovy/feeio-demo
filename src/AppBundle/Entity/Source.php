<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Source
 *
 * @ORM\Table(name="Source")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\SourceRepository")
 */
class Source
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
     * @var SourceList
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\SourceList", inversedBy="sources", cascade={"persist"}, fetch="EAGER")
     */
    private $sourceList;

    /**
     * @var UserCompany
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\UserCompany")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private $userCompany;

    /**
     * @var JobPosition
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\JobPosition")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private $jobPosition;

    /**
     * @var integer
     *
     * @ORM\Column(name="rateExternal", type="integer", nullable=true)
     */
    private $rateExternal;



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
     * Set rateExternal
     *
     * @param integer $rateExternal
     * @return Source
     */
    public function setRateExternal($rateExternal)
    {
        $this->rateExternal = $rateExternal;

        return $this;
    }

    /**
     * Get rateExternal
     *
     * @return integer
     */
    public function getRateExternal()
    {
        return $this->rateExternal;
    }

    /**
     * Set sourceList
     *
     * @param \AppBundle\Entity\SourceList $sourceList
     * @return Source
     */
    public function setSourceList(\AppBundle\Entity\SourceList $sourceList = null)
    {
        $this->sourceList = $sourceList;

        return $this;
    }

    /**
     * Get sourceList
     *
     * @return \AppBundle\Entity\SourceList 
     */
    public function getSourceList()
    {
        return $this->sourceList;
    }

    /**
     * Set userCompany
     *
     * @param \AppBundle\Entity\UserCompany $userCompany
     * @return Source
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
     * Set jobPosition
     *
     * @param \AppBundle\Entity\JobPosition $jobPosition
     * @return Source
     */
    public function setJobPosition(\AppBundle\Entity\JobPosition $jobPosition = null)
    {
        $this->jobPosition = $jobPosition;

        return $this;
    }

    /**
     * Get jobPosition
     *
     * @return \AppBundle\Entity\JobPosition 
     */
    public function getJobPosition()
    {
        return $this->jobPosition;
    }
}
