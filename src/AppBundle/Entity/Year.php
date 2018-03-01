<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Year
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\YearRepository")
 */
class Year
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
     * @ORM\Column(name="year", type="string", length=255)
     */
    private $year;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Campaign", mappedBy="year")
     */
    private $campaigns;


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
     * Set year
     *
     * @param string $year
     * @return Year
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return string 
     */
    public function getYear()
    {
        return $this->year;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->campaigns = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add campaigns
     *
     * @param \AppBundle\Entity\Campaign $campaigns
     * @return Year
     */
    public function addCampaign(\AppBundle\Entity\Campaign $campaigns)
    {
        $this->campaigns[] = $campaigns;

        return $this;
    }

    /**
     * Remove campaigns
     *
     * @param \AppBundle\Entity\Campaign $campaigns
     */
    public function removeCampaign(\AppBundle\Entity\Campaign $campaigns)
    {
        $this->campaigns->removeElement($campaigns);
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
}
