<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use UserBundle\Entity\User;

/**
 * CurrentSettings
 *
 * @ORM\Table(name="CurrentSettings")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\CurrentSettingsRepository")
 */
class CurrentSettings
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
     * @ORM\OneToOne(targetEntity="UserBundle\Entity\User", inversedBy="currentSettings")
     */
    private $user;

    /**
     * @var Company
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Company", inversedBy="currentSettings")
     */
    private $company;


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
     * Set user
     *
     * @param \UserBundle\Entity\User $user
     * @return CurrentSettings
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
     * @return CurrentSettings
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
}
