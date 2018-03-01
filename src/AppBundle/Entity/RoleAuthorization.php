<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RoleAuthorization
 *
 * @ORM\Table(name="RoleAuthorization")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\RoleAuthorizationRepository")
 */
class RoleAuthorization
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
     * @var Role
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Role", inversedBy="roleAuthorizationRelations")
     */
    private $role;

    /**
     * @var Authorization
     *
     * @ORM\ManyToOne(targetEntity="Authorization", inversedBy="roleAuthorizationRelations")
     */
    private $authorization;

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
     * Set role
     *
     * @param \AppBundle\Entity\Role $role
     * @return RoleAuthorization
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
     * Set authorization
     *
     * @param \AppBundle\Entity\Authorization $authorization
     * @return RoleAuthorization
     */
    public function setAuthorization(\AppBundle\Entity\Authorization $authorization = null)
    {
        $this->authorization = $authorization;

        return $this;
    }

    /**
     * Get authorization
     *
     * @return \AppBundle\Entity\Authorization 
     */
    public function getAuthorization()
    {
        return $this->authorization;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return RoleAuthorization
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
}
