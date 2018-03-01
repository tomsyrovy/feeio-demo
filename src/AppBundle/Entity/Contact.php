<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Contact
 *
 * @ORM\Table(name="Contact")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\ContactRepository")
 */
class Contact extends AbstractCommissionHierarchy
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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="street", type="string", length=255, nullable=true)
     */
    private $street;

    /**
     * @var string
     *
     * @ORM\Column(name="number", type="string", length=255, nullable=true)
     */
    private $number;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255, nullable=true)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="zipcode", type="string", length=255, nullable=true)
     */
    private $zipcode;

    /**
     * @var ContactCountry
     *
     * @ORM\ManyToOne(targetEntity = "ContactCountry", inversedBy="contacts")
     */
    private $country;

    /**
     * @var integer
     *
     * @ORM\Column(name="vatnumber", type="string", nullable=true)
     */
    private $vatnumber;

    /**
     * @var integer
     *
     * @ORM\Column(name="taxnumber", type="string", nullable=true)
     */
    private $taxnumber;

    /**
     * @var string
     *
     * @ORM\Column(name="website", type="string", length=255, nullable=true)
     */
    private $website;

    /**
     * @var string
     *
     * @ORM\Column(name="bankaccountnumber", type="string", length=255, nullable=true)
     */
    private $bankaccountnumber;

    /**
     * @var string
     *
     * @ORM\Column(name="iban", type="string", length=255, nullable=true)
     */
    private $iban;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", length=255, nullable=true)
     */
    private $telephone;

    /**
     * @var ContactType
     * @ORM\ManyToOne(targetEntity = "ContactType", inversedBy="contacts")
     */
    private $type;

    /**
     * @var Company
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Company", inversedBy="contacts")
     */
    private $company;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Client", mappedBy="contact")
     */
    private $clients;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Cost", mappedBy="supplier")
     */
    private $costs;

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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Contact
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set street
     *
     * @param string $street
     * @return Contact
     */
    public function setStreet($street)
    {
        $this->street = $street;

        return $this;
    }

    /**
     * Get street
     *
     * @return string 
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Set number
     *
     * @param string $number
     * @return Contact
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return string 
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return Contact
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set zipcode
     *
     * @param string $zipcode
     * @return Contact
     */
    public function setZipcode($zipcode)
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    /**
     * Get zipcode
     *
     * @return string 
     */
    public function getZipcode()
    {
        return $this->zipcode;
    }

    /**
     * Set Contact Country
     *
     * @param \AppBundle\Entity\ContactCountry $country
     *
     * @return $this
     */
    public function setCountry(ContactCountry $country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get Contact Country
     *
     * @return \AppBundle\Entity\ContactCountry
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set vatnumber
     *
     * @param integer $vatnumber
     * @return Contact
     */
    public function setVatnumber($vatnumber)
    {
        $this->vatnumber = $vatnumber;

        return $this;
    }

    /**
     * Get vatnumber
     *
     * @return integer 
     */
    public function getVatnumber()
    {
        return $this->vatnumber;
    }

    /**
     * Set taxnumber
     *
     * @param integer $taxnumber
     * @return Contact
     */
    public function setTaxnumber($taxnumber)
    {
        $this->taxnumber = $taxnumber;

        return $this;
    }

    /**
     * Get taxnumber
     *
     * @return integer 
     */
    public function getTaxnumber()
    {
        return $this->taxnumber;
    }

    /**
     * Set website
     *
     * @param string $website
     * @return Contact
     */
    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * Get website
     *
     * @return string 
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set bankaccountnumber
     *
     * @param string $bankaccountnumber
     * @return Contact
     */
    public function setBankaccountnumber($bankaccountnumber)
    {
        $this->bankaccountnumber = $bankaccountnumber;

        return $this;
    }

    /**
     * Get bankaccountnumber
     *
     * @return string 
     */
    public function getBankaccountnumber()
    {
        return $this->bankaccountnumber;
    }

    /**
     * Set iban
     *
     * @param string $iban
     * @return Contact
     */
    public function setIban($iban)
    {
        $this->iban = $iban;

        return $this;
    }

    /**
     * Get iban
     *
     * @return string 
     */
    public function getIban()
    {
        return $this->iban;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Contact
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
     * Set telephone
     *
     * @param string $telephone
     * @return Contact
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get telephone
     *
     * @return string 
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Set type
     *
     * @param \AppBundle\Entity\ContactType $type
     * @return Contact
     */
    public function setType(\AppBundle\Entity\ContactType $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return \AppBundle\Entity\ContactType 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set company
     *
     * @param \AppBundle\Entity\Company $company
     * @return Contact
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
        $this->commissions = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add costs
     *
     * @param \AppBundle\Entity\Cost $costs
     * @return Contact
     */
    public function addCost(\AppBundle\Entity\Cost $costs)
    {
        $this->costs[] = $costs;

        return $this;
    }

    /**
     * Remove costs
     *
     * @param \AppBundle\Entity\Cost $costs
     */
    public function removeCost(\AppBundle\Entity\Cost $costs)
    {
        $this->costs->removeElement($costs);
    }

    /**
     * Get costs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCosts()
    {
        return $this->costs;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Contact
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
     * @return Contact
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
     * Set contactPersonList
     *
     * @param string $contactPersonList
     * @return Contact
     */
    public function setContactPersonList($contactPersonList)
    {
        $this->contactPersonList = $contactPersonList;

        return $this;
    }

    /**
     * Get contactPersonList
     *
     * @return string 
     */
    public function getContactPersonList()
    {
        return $this->contactPersonList;
    }

    /**
     * Set contractData
     *
     * @param string $contractData
     * @return Contact
     */
    public function setContractData($contractData)
    {
        $this->contractData = $contractData;

        return $this;
    }

    /**
     * Get contractData
     *
     * @return string 
     */
    public function getContractData()
    {
        return $this->contractData;
    }

    /**
     * Add clients
     *
     * @param \AppBundle\Entity\Client $clients
     * @return Contact
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

    /**
     * Set sourceList
     *
     * @param \AppBundle\Entity\SourceList $sourceList
     * @return Contact
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
}
