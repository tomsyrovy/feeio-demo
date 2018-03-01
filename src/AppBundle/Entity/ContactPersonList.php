<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * ContactPersonList
 *
 * @ORM\Table(name="ContactPersonList")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\ContactPersonListRepository")
 */
class ContactPersonList
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
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ContactPerson", mappedBy="contactPersonList", cascade={"persist"}, fetch="EAGER")
     */
    private $contactPersons;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->contactPersons = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add contactPersons
     *
     * @param \AppBundle\Entity\ContactPerson $contactPersons
     * @return ContactPersonList
     */
    public function addContactPerson(\AppBundle\Entity\ContactPerson $contactPersons)
    {
        $contactPersons->setContactPersonList($this);
        $this->contactPersons[] = $contactPersons;

        return $this;
    }

    /**
     * Remove contactPersons
     *
     * @param \AppBundle\Entity\ContactPerson $contactPersons
     */
    public function removeContactPerson(\AppBundle\Entity\ContactPerson $contactPersons)
    {
        $this->contactPersons->removeElement($contactPersons);
    }

    /**
     * Get contactPersons
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getContactPersons()
    {
        return $this->contactPersons;
    }
}
