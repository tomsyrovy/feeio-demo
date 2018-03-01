<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Tests\Common\DataFixtures\TestEntity\User;

/**
 * Client
 *
 * @ORM\Table(name="Client")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\ClientRepository")
 */
class Client extends AbstractCommissionHierarchy
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
     * @var Contact
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Contact", inversedBy="clients")
     */
    private $contact;

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
     * @var Company
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Company", inversedBy="clients")
     *
     */
    private $company;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Campaign", mappedBy="client", cascade={"persist"})
     * @ORM\OrderBy({"enabled" = "DESC"})
     */
    private $campaigns;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private $author;

    public function getSums(){

        $array = [];

        /** @var Campaign $item */
        foreach($this->getCampaigns() as $item){
            if($item->getEnabled()){
                $array[] = $item->getSums();
            }
        }

        $sums = new AllocationsContainerSums();
        $sums->sumarize($array);

        return $sums;

    }

    public function getJobPositions(){
        $jobPositions = [];
        $sources = $this->getSourceList()->getSources();
        /** @var Source $source */
        foreach($sources as $source){
            $jobPositions[] = $source->getJobPosition();
        }
        return $jobPositions;
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
     * @return Client
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
     * @return Client
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
     * Set enabled
     *
     * @param boolean $enabled
     * @return Client
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
     * Set closed
     *
     * @param boolean $closed
     * @return Client
     */
    public function setClosed($closed)
    {
        $this->closed = $closed;

        return $this;
    }

    /**
     * Get closed
     *
     * @return boolean 
     */
    public function getClosed()
    {
        return $this->closed;
    }

    /**
     * Set contact
     *
     * @param \AppBundle\Entity\Contact $contact
     * @return Client
     */
    public function setContact(\AppBundle\Entity\Contact $contact = null)
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * Get contact
     *
     * @return \AppBundle\Entity\Contact 
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * Set contactPersonList
     *
     * @param \AppBundle\Entity\ContactPersonList $contactPersonList
     * @return Client
     */
    public function setContactPersonList(\AppBundle\Entity\ContactPersonList $contactPersonList = null)
    {
        $this->contactPersonList = $contactPersonList;

        return $this;
    }

    /**
     * Get contactPersonList
     *
     * @return \AppBundle\Entity\ContactPersonList 
     */
    public function getContactPersonList()
    {
        return $this->contactPersonList;
    }

    /**
     * Set sourceList
     *
     * @param \AppBundle\Entity\SourceList $sourceList
     * @return Client
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
     * Set company
     *
     * @param \AppBundle\Entity\Company $company
     * @return Client
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
     * Constructor
     */
    public function __construct()
    {
        $this->campaigns = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add campaign
     *
     * @param \AppBundle\Entity\Campaign $campaign
     * @return Client
     */
    public function addCampaign(\AppBundle\Entity\Campaign $campaign)
    {
        $this->campaigns[] = $campaign;

        return $this;
    }

    /**
     * Remove campaign
     *
     * @param \AppBundle\Entity\Campaign $campaign
     */
    public function removeCampaign(\AppBundle\Entity\Campaign $campaign)
    {
        $this->campaigns->removeElement($campaign);
    }

    /**
     * Get campaigns
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCampaigns()
    {
        return $this->campaigns;
    }

    /**
     * Set author
     *
     * @param \UserBundle\Entity\User $author
     * @return Client
     */
    public function setAuthor(\UserBundle\Entity\User $author = null)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return \UserBundle\Entity\User 
     */
    public function getAuthor()
    {
        return $this->author;
    }
}
