<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserCompanyTemporality
 *
 * @ORM\Table(name="UserCompanyTemporality")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\UserCompanyTemporalityRepository")
 */
class UserCompanyTemporality
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
     * @var \DateTime
     *
     * @ORM\Column(name="date_from", type="datetime")
     */
    private $from;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_until", type="datetime", nullable=true)
     */
    private $until;

    /**
     * @var Role
     *
     * @ORM\ManyToOne(targetEntity="Role", inversedBy="temporalities")
     */
    private $role;

    /**
     * @var JobPosition
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\JobPosition", inversedBy="temporalities", cascade={"persist"})
     */
    private $jobposition;

    /**
     * @var UserCompanyTemporalityStatus
     *
     * @ORM\ManyToOne(targetEntity="UserCompanyTemporalityStatus", inversedBy="temporalities")
     */
    private $status;

    /**
     * @var UserCompany
     *
     * @ORM\ManyToOne(targetEntity="UserCompany", inversedBy="temporalities")
     */
    private $userCompany;

    /**
     * @var integer
     *
     * @ORM\Column(name="rateInternal", type="integer")
     */
    private $rateInternal;

    /**
     * @var integer
     *
     * @ORM\Column(name="hours", type="integer")
     */
    private $hours;


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
     * Set from
     *
     * @param \DateTime $from
     * @return UserCompanyTemporality
     */
    public function setFrom($from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * Get from
     *
     * @return \DateTime 
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Set until
     *
     * @param \DateTime $until
     * @return UserCompanyTemporality
     */
    public function setUntil($until)
    {
        $this->until = $until;

        return $this;
    }

    /**
     * Get until
     *
     * @return \DateTime 
     */
    public function getUntil()
    {
        return $this->until;
    }

    /**
     * Set rateInternal
     *
     * @param integer $rateInternal
     * @return UserCompanyTemporality
     */
    public function setRateInternal($rateInternal)
    {
        $this->rateInternal = $rateInternal;

        return $this;
    }

    /**
     * Get rateInternal
     *
     * @return integer 
     */
    public function getRateInternal()
    {
        return $this->rateInternal;
    }

    /**
     * Set hours
     *
     * @param integer $hours
     * @return UserCompanyTemporality
     */
    public function setHours($hours)
    {
        $this->hours = $hours;

        return $this;
    }

    /**
     * Get hours
     *
     * @return integer 
     */
    public function getHours()
    {
        return $this->hours;
    }

    /**
     * Set role
     *
     * @param \AppBundle\Entity\Role $role
     * @return UserCompanyTemporality
     */
    public function setRole(\AppBundle\Entity\Role $role = null)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return \AppBundle\Entity\Role 
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set userCompany
     *
     * @param \AppBundle\Entity\UserCompany $userCompany
     * @return UserCompanyTemporality
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
     * Set status
     *
     * @param \AppBundle\Entity\UserCompanyTemporalityStatus $status
     * @return UserCompanyTemporality
     */
    public function setStatus(\AppBundle\Entity\UserCompanyTemporalityStatus $status = null)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return \AppBundle\Entity\UserCompanyTemporalityStatus 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set jobposition
     *
     * @param \AppBundle\Entity\JobPosition $jobposition
     * @return UserCompanyTemporality
     */
    public function setJobposition(\AppBundle\Entity\JobPosition $jobposition = null)
    {
        $jobposition->addTemporality($this);
        $this->jobposition = $jobposition;

        return $this;
    }

    /**
     * Get jobposition
     *
     * @return \AppBundle\Entity\JobPosition 
     */
    public function getJobposition()
    {
        return $this->jobposition;
    }
}
