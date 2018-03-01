<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * SourceList
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\SourceListRepository")
 */
class SourceList
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
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Source", mappedBy="sourceList", cascade={"persist"}, fetch="EAGER")
     */
    private $sources;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->sources = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add sources
     *
     * @param \AppBundle\Entity\Source $sources
     * @return SourceList
     */
    public function addSource(\AppBundle\Entity\Source $sources)
    {
        $sources->setSourceList($this);
        $this->sources[] = $sources;

        return $this;
    }

    /**
     * Remove sources
     *
     * @param \AppBundle\Entity\Source $sources
     */
    public function removeSource(\AppBundle\Entity\Source $sources)
    {
        $this->sources->removeElement($sources);
    }

    /**
     * Get sources
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSources()
    {
        return $this->sources;
    }
}
