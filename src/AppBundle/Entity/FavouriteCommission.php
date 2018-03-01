<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use UserBundle\Entity\User;

/**
 * FavouriteCommission
 *
 * @ORM\Table(name="FavouriteCommission")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\FavouriteCommissionRepository")
 */
class FavouriteCommission
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
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="favouriteCommissions")
     */
    private $user;

    /**
     * @var Commission
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Commission", inversedBy="favouriteCommissions")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    private $commission;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    private $sort;




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
     * Set sort
     *
     * @param integer $sort
     * @return FavouriteCommission
     */
    public function setSort($sort)
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * Get sort
     *
     * @return integer 
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * Set user
     *
     * @param \UserBundle\Entity\User $user
     * @return FavouriteCommission
     */
    public function setUser(\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set commission
     *
     * @param \AppBundle\Entity\Commission $commission
     * @return FavouriteCommission
     */
    public function setCommission(\AppBundle\Entity\Commission $commission = null)
    {
        $this->commission = $commission;

        return $this;
    }

    /**
     * Get commission
     *
     * @return \AppBundle\Entity\Commission 
     */
    public function getCommission()
    {
        return $this->commission;
    }
}
