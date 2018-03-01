<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Role
 *
 * @ORM\Table(name="Role")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\RoleRepository")
 */
class Role
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

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
     * @var boolean
     *
     * @ORM\Column(name="noneditable", type="boolean")
     */
    private $noneditable = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled = true;

    /**
     * @var Company
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Company", inversedBy="roles")
     */
    private $company;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\RoleAuthorization", mappedBy="role", cascade={"persist", "remove"})
     */
    private $roleAuthorizationRelations;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="UserCompanyTemporality", mappedBy="role")
     */
    private $temporalities;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Invitation", mappedBy="role", cascade={"persist", "remove"})
     */
    private $invitations;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->roleAuthorizationRelations = new \Doctrine\Common\Collections\ArrayCollection();
        $this->temporalities = new \Doctrine\Common\Collections\ArrayCollection();
        $this->invitations = new ArrayCollection();
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
     * @return Role
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
     * Set created
     *
     * @param \DateTime $created
     * @return Role
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
     * @return Role
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
     * Set noneditable
     *
     * @param boolean $noneditable
     * @return Role
     */
    public function setNoneditable($noneditable)
    {
        $this->noneditable = $noneditable;

        return $this;
    }

    /**
     * Get noneditable
     *
     * @return boolean 
     */
    public function getNoneditable()
    {
        return $this->noneditable;
    }

    /**
     * Set company
     *
     * @param \AppBundle\Entity\Company $company
     * @return Role
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
     * Add roleAuthorizationRelations
     *
     * @param \AppBundle\Entity\RoleAuthorization $roleAuthorizationRelations
     * @return Role
     */
    public function addRoleAuthorizationRelation(\AppBundle\Entity\RoleAuthorization $roleAuthorizationRelations)
    {
        $this->roleAuthorizationRelations[] = $roleAuthorizationRelations;

        return $this;
    }

    /**
     * Remove roleAuthorizationRelations
     *
     * @param \AppBundle\Entity\RoleAuthorization $roleAuthorizationRelations
     */
    public function removeRoleAuthorizationRelation(\AppBundle\Entity\RoleAuthorization $roleAuthorizationRelations)
    {
        $this->roleAuthorizationRelations->removeElement($roleAuthorizationRelations);
    }

    /**
     * Get roleAuthorizationRelations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRoleAuthorizationRelations()
    {
        return $this->roleAuthorizationRelations;
    }

    /**
     * Add temporalities
     *
     * @param \AppBundle\Entity\UserCompanyTemporality $temporalities
     * @return Role
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
     * Get actual temporalities (userCompanyRelations)
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getActualTemporalities()
    {

        $temporalities = new ArrayCollection();

        foreach($this->getTemporalities() as $temporality){
            $until = $temporality->getUntil();
            if($until == null OR empty($until)){
                $temporalities->add($temporality);
            }
        }

        return $temporalities;

    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Role
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
     * Add invitations
     *
     * @param \AppBundle\Entity\Invitation $invitations
     * @return Role
     */
    public function addInvitation(\AppBundle\Entity\Invitation $invitations)
    {
        $this->invitations[] = $invitations;

        return $this;
    }

    /**
     * Remove invitations
     *
     * @param \AppBundle\Entity\Invitation $invitations
     */
    public function removeInvitation(\AppBundle\Entity\Invitation $invitations)
    {
        $this->invitations->removeElement($invitations);
    }

    /**
     * Get invitations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInvitations()
    {
        return $this->invitations;
    }
}
