<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CommissionUserCompanyRelation
 *
 * @ORM\Table(name="CommissionUserCompanyRelation")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\CommissionUserCompanyRelationRepository")
 */
class CommissionUserCompanyRelation
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
     * @var UserCompany
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\UserCompany", inversedBy="commissionUserCompanyRelations")
     */
    private $userCompany;

    /**
     * @var Commission
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Commission", inversedBy="commissionUserCompanyRelations")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    private $commission;

    /**
     * @var CommissionUserCompanyRelationType
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CommissionUserCompanyRelationType")
     */
    private $commissionUserCompanyRelationType;


    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $editable = true;




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
     * Set editable
     *
     * @param boolean $editable
     * @return CommissionUserCompanyRelation
     */
    public function setEditable($editable)
    {
        $this->editable = $editable;

        return $this;
    }

    /**
     * Get editable
     *
     * @return boolean 
     */
    public function getEditable()
    {
        return $this->editable;
    }

    /**
     * Set userCompany
     *
     * @param \AppBundle\Entity\UserCompany $userCompany
     * @return CommissionUserCompanyRelation
     */
    public function setUserCompany(\AppBundle\Entity\UserCompany $userCompany = null)
    {
        $this->userCompany = $userCompany;

        return $this;
    }

    /**
     * Get userCompany
     *
     * @return \AppBundle\Entity\UserCompany 
     */
    public function getUserCompany()
    {
        return $this->userCompany;
    }

    /**
     * Set commission
     *
     * @param \AppBundle\Entity\Commission $commission
     * @return CommissionUserCompanyRelation
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

    /**
     * Set commissionUserCompanyRelationType
     *
     * @param \AppBundle\Entity\CommissionUserCompanyRelationType $commissionUserCompanyRelationType
     * @return CommissionUserCompanyRelation
     */
    public function setCommissionUserCompanyRelationType(\AppBundle\Entity\CommissionUserCompanyRelationType $commissionUserCompanyRelationType = null)
    {
        $this->commissionUserCompanyRelationType = $commissionUserCompanyRelationType;

        return $this;
    }

    /**
     * Get commissionUserCompanyRelationType
     *
     * @return \AppBundle\Entity\CommissionUserCompanyRelationType 
     */
    public function getCommissionUserCompanyRelationType()
    {
        return $this->commissionUserCompanyRelationType;
    }
}
