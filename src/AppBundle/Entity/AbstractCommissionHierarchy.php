<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AbstractCommissionHierarchy
 *
 * @ORM\MappedSuperclass()
 */
class AbstractCommissionHierarchy
{
    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    protected $enabled = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="closed", type="boolean")
     */
    protected $closed = false;

    /**
     * @var ContactPersonList
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ContactPersonList", cascade={"persist"})
     */
    protected $contactPersonList;

    /**
     * @var SourceList
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\SourceList", cascade={"persist"})
     */
    protected $sourceList;

}
