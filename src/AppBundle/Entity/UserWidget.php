<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\VarDumper\VarDumper;
use UserBundle\Entity\User;

/**
 * UserWidget
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\UserWidgetRepository")
 */
class UserWidget
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
     * @ORM\ManyToOne(targetEntity="\UserBundle\Entity\User", inversedBy="userWidgets")
     */
    private $user;

    /**
     * @var Widget
     *
     * @ORM\ManyToOne(targetEntity="Widget", inversedBy="userWidgets")
     */
    private $widget;


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
     * Set user
     *
     * @param \UserBundle\Entity\User $user
     * @return UserWidget
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
     * Set widget
     *
     * @param \AppBundle\Entity\Widget $widget
     * @return UserWidget
     */
    public function setWidget(\AppBundle\Entity\Widget $widget = null)
    {
        $this->widget = $widget;

        return $this;
    }

    /**
     * Get widget
     *
     * @return \AppBundle\Entity\Widget 
     */
    public function getWidget()
    {
        return $this->widget;
    }
}
