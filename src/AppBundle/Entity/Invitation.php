<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use UserBundle\Entity\User;

/**
 * Invitation
 *
 * @ORM\Table(name="Invitation")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\InvitationRepository")
 */
class Invitation
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
     * @var Company
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Company", inversedBy="invitations")
     */
    private $company;

    /**
     * @var Role
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Role", inversedBy="invitations")
     */
    private $role;

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
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="invitations")
     */
    private $author;

    /**
     * @var string
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var boolean
     *
     * @ORM\Column(name="accepted", type="boolean")
     */
    private $accepted = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled = true;


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
     * Set company
     *
     * @param string $company
     * @return Invitation
     */
    public function setCompany($company)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return string 
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set author
     *
     * @param string $author
     * @return Invitation
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return string 
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set created
     *
     * @param string $created
     * @return Invitation
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return string 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Invitation
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set accepted
     *
     * @param boolean $accepted
     * @return Invitation
     */
    public function setAccepted($accepted)
    {
        $this->accepted = $accepted;

        return $this;
    }

    /**
     * Get accepted
     *
     * @return boolean 
     */
    public function getAccepted()
    {
        return $this->accepted;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Invitation
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
     * Set role
     *
     * @param \AppBundle\Entity\Role $role
     * @return Invitation
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
     * Set rateInternal
     *
     * @param integer $rateInternal
     * @return Invitation
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
     * @return Invitation
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
}
