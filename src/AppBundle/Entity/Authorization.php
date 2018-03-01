<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Authorization
 *
 * @ORM\Table(name="Authorization")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\AuthorizationRepository")
 */
class Authorization
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
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255)
     */
    private $code;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\RoleAuthorization", mappedBy="authorization")
     */
    private $roleAuthorizationRelations;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->roleAuthorizationRelations = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Authorization
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
     * Set code
     *
     * @param string $code
     * @return Authorization
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Add roleAuthorizationRelations
     *
     * @param \AppBundle\Entity\RoleAuthorization $roleAuthorizationRelations
     * @return Authorization
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
}
