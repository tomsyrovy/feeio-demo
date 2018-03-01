<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use UserBundle\Entity\User;

/**
 * Company
 *
 * @ORM\Table(name="Company")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\CompanyRepository")
 */
class Company
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
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled = true;

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
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="ownedCompanies")
     */
    private $owner;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\UserCompany", mappedBy="company")
     */
    private $userCompanyRelations;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Invitation", mappedBy="company")
     */
    private $invitations;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Role", mappedBy="company")
     */
    private $roles;

	/**
	 * @var ArrayCollection
	 *
	 * @ORM\OneToMany(targetEntity="AppBundle\Entity\Contact", mappedBy="company")
	 */
	private $contacts;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\CurrentSettings", mappedBy="company")
     */
    private $currentSettings;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\CompanyGroup", mappedBy="company")
     */
    private $companyGroups;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Activity", mappedBy="company")
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private $activities;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\JobPosition", mappedBy="company")
     * @ORM\OrderBy({"id" = "ASC"})
     */
    private $jobPositions;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Client", mappedBy="company")
     * @ORM\OrderBy({"enabled" = "DESC"})
     */
    private $clients;

    public function getSums(){

        $array = [];

        /** @var Client $item */
        foreach($this->getClients() as $item){
            if($item->getEnabled()){
                $array[] = $item->getSums();
            }
        }

        $sums = new AllocationsContainerSums();
        $sums->sumarize($array);

        return $sums;

    }

    /**
     * Get jobPositions enabled
     *
     * @return array
     */
    public function getJobPositionsEnabled()
    {
        return array_filter($this->getJobPositions()->toArray(), function($entry){
            return $entry->getEnabled();
        });
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
     * @return Company
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
     * @return Company
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
     * Set owner
     *
     * @param \UserBundle\Entity\User $owner
     * @return Company
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
     * Set enabled
     *
     * @param boolean $enabled
     * @return Company
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
     * Set updated
     *
     * @param \DateTime $updated
     * @return Company
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
     * Constructor
     */
    public function __construct()
    {
        $this->userCompanyRelations = new \Doctrine\Common\Collections\ArrayCollection();
        $this->activities = new ArrayCollection();
    }

    /**
     * Add userCompanyRelations
     *
     * @param \AppBundle\Entity\UserCompany $userCompanyRelations
     * @return Company
     */
    public function addUserCompanyRelation(\AppBundle\Entity\UserCompany $userCompanyRelations)
    {
        $this->userCompanyRelations[] = $userCompanyRelations;

        return $this;
    }

    /**
     * Remove userCompanyRelations
     *
     * @param \AppBundle\Entity\UserCompany $userCompanyRelations
     */
    public function removeUserCompanyRelation(\AppBundle\Entity\UserCompany $userCompanyRelations)
    {
        $this->userCompanyRelations->removeElement($userCompanyRelations);
    }

    /**
     * Get userCompanyRelations
     *
     * @return ArrayCollection
     */
    public function getUserCompanyRelations()
    {
        return $this->userCompanyRelations;
    }

    /**
     * Get userCompanyRelations of TemporalityStatus
     *
     * @param string $statusCode
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getUserCompanyRelationsOfTemporalityStatus($statusCode){

        $ucrots = array();

        foreach($this->userCompanyRelations as $ucr){

            if($ucr->getData()->getStatus()->getCode() == $statusCode){

                $ucrots[] = $ucr;

            }

        }

        uasort($ucrots, function($a, $b){
            return $a->getUser()->getLastname() > $b->getUser()->getLastname();
        });

        return $ucrots;

    }

    /**
     * Add roles
     *
     * @param \AppBundle\Entity\Role $roles
     * @return Company
     */
    public function addRole(\AppBundle\Entity\Role $roles)
    {
        $this->roles[] = $roles;

        return $this;
    }

    /**
     * Remove roles
     *
     * @param \AppBundle\Entity\Role $roles
     */
    public function removeRole(\AppBundle\Entity\Role $roles)
    {
        $this->roles->removeElement($roles);
    }

    /**
     * Get roles
     *
     * @return ArrayCollection
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Return enabled roles of company
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRolesEnabled(){

        $criteria = Criteria::create();
        $criteria->where(Criteria::expr()->eq("enabled", true));

        return $this->getRoles()->matching($criteria);
    }

    /**
     * Add invitations
     *
     * @param \AppBundle\Entity\Invitation $invitations
     * @return Company
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

    /**
     * Add currentSettings
     *
     * @param \AppBundle\Entity\CurrentSettings $currentSettings
     * @return Company
     */
    public function addCurrentSetting(\AppBundle\Entity\CurrentSettings $currentSettings)
    {
        $this->currentSettings[] = $currentSettings;

        return $this;
    }

    /**
     * Remove currentSettings
     *
     * @param \AppBundle\Entity\CurrentSettings $currentSettings
     */
    public function removeCurrentSetting(\AppBundle\Entity\CurrentSettings $currentSettings)
    {
        $this->currentSettings->removeElement($currentSettings);
    }

    /**
     * Get currentSettings
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCurrentSettings()
    {
        return $this->currentSettings;
    }

    /**
     * Add contacts
     *
     * @param \AppBundle\Entity\Contact $contacts
     * @return Company
     */
    public function addContact(\AppBundle\Entity\Contact $contacts)
    {
        $this->contacts[] = $contacts;

        return $this;
    }

    /**
     * Remove contacts
     *
     * @param \AppBundle\Entity\Contact $contacts
     */
    public function removeContact(\AppBundle\Entity\Contact $contacts)
    {
        $this->contacts->removeElement($contacts);
    }

    /**
     * Get contacts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getContacts()
    {
        return $this->contacts;
    }

    /**
     * Return contacts of defined type code
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getContactsOfTypeCode($code){

        $contacts = [];

        foreach($this->getContacts() as $contact){

            if($contact->getType()->getCode() === $code){
                $contacts[] = $contact;
            }

        }

        uasort($contacts, function($a, $b){
            return $a->getTitle() > $b->getTitle();
        });


       return new ArrayCollection($contacts);
    }

    /**
     * Add companyGroups
     *
     * @param \AppBundle\Entity\CompanyGroup $companyGroups
     * @return Company
     */
    public function addCompanyGroup(\AppBundle\Entity\CompanyGroup $companyGroups)
    {
        $this->companyGroups[] = $companyGroups;

        return $this;
    }

    /**
     * Remove companyGroups
     *
     * @param \AppBundle\Entity\CompanyGroup $companyGroups
     */
    public function removeCompanyGroup(\AppBundle\Entity\CompanyGroup $companyGroups)
    {
        $this->companyGroups->removeElement($companyGroups);
    }

    /**
     * Get companyGroups
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCompanyGroups()
    {
        return $this->companyGroups;
    }

    /**
     * Return enabled companyGroups of company
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCompanyGroupsEnabled(){

        $orderings = [
            'name' => 'ASC'
        ];

        $criteria = Criteria::create();
        $criteria->where(Criteria::expr()->eq("enabled", true))->orderBy($orderings);

        return $this->getCompanyGroups()->matching($criteria);
    }

    /**
     * Add activities
     *
     * @param \AppBundle\Entity\Activity $activities
     * @return Company
     */
    public function addActivity(\AppBundle\Entity\Activity $activities)
    {
        $this->activities[] = $activities;

        return $this;
    }

    /**
     * Remove activities
     *
     * @param \AppBundle\Entity\Activity $activities
     */
    public function removeActivity(\AppBundle\Entity\Activity $activities)
    {
        $this->activities->removeElement($activities);
    }

    /**
     * Get activities
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getActivities()
    {
        return $this->activities;
    }

    /**
     * Add jobPositions
     *
     * @param \AppBundle\Entity\JobPosition $jobPositions
     * @return Company
     */
    public function addJobPosition(\AppBundle\Entity\JobPosition $jobPositions)
    {
        $this->jobPositions[] = $jobPositions;

        return $this;
    }

    /**
     * Remove jobPositions
     *
     * @param \AppBundle\Entity\JobPosition $jobPositions
     */
    public function removeJobPosition(\AppBundle\Entity\JobPosition $jobPositions)
    {
        $this->jobPositions->removeElement($jobPositions);
    }

    /**
     * Get jobPositions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getJobPositions()
    {
        return $this->jobPositions;
    }

    /**
     * Add clients
     *
     * @param \AppBundle\Entity\Client $clients
     * @return Company
     */
    public function addClient(\AppBundle\Entity\Client $clients)
    {
        $this->clients[] = $clients;

        return $this;
    }

    /**
     * Remove clients
     *
     * @param \AppBundle\Entity\Client $clients
     */
    public function removeClient(\AppBundle\Entity\Client $clients)
    {
        $this->clients->removeElement($clients);
    }

    /**
     * Get clients
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getClients()
    {
        return $this->clients;
    }
}
