<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use UserBundle\Entity\User;

/**
 * FavouriteActivity
 *
 * @ORM\Table(name="FavouriteActivity")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\FavouriteActivityRepository")
 */
class FavouriteActivity
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
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="favouriteActivities")
     */
    private $user;

    /**
     * @var Activity
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Activity", inversedBy="favouriteActivities")
     */
    private $activity;

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
     * @return FavouriteActivity
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
     * @return FavouriteActivity
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
     * Set activity
     *
     * @param \AppBundle\Entity\Activity $activity
     * @return FavouriteActivity
     */
    public function setActivity(\AppBundle\Entity\Activity $activity = null)
    {
        $this->activity = $activity;

        return $this;
    }

    /**
     * Get activity
     *
     * @return \AppBundle\Entity\Activity
     */
    public function getActivity()
    {
        return $this->activity;
    }
}
