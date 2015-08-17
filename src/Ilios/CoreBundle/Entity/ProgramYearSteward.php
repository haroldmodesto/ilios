<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\SchoolEntity;

/**
 * Class ProgramYearSteward
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="program_year_steward",
 *   uniqueConstraints={
 *     @ORM\UniqueConstraint(name="program_year_id_school_id_department_id",
 *       columns={
 *         "program_year_Id",
 *         "school_Id",
 *         "department_Id"
 *       }
 *     )
 *   },
 *   indexes={@ORM\Index(name="IDX_program_year_school", columns={"program_year_Id", "school_Id"})})
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessType("public_method")
 */
class ProgramYearSteward implements ProgramYearStewardInterface
{
    use IdentifiableEntity;
    use SchoolEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="program_year_steward_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @Assert\Type(type="integer")
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     */
    protected $id;

    /**
     * @var DepartmentInterface
     *
     * @ORM\ManyToOne(targetEntity="Department")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="department_id", referencedColumnName="department_id", onDelete="CASCADE")
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $department;

    /**
     * @var ProgramYearInterface
     *
     * @ORM\ManyToOne(targetEntity="ProgramYear")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="program_year_id", referencedColumnName="program_year_id", onDelete="CASCADE")
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("programYear")
     */
    protected $programYear;

    /**
     * @var SchoolInterface
     *
     * @ORM\ManyToOne(targetEntity="School")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="school_id", referencedColumnName="school_id", onDelete="CASCADE")
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $school;

    /**
     * @param DepartmentInterface $department
     */
    public function setDepartment(DepartmentInterface $department)
    {
        $this->department = $department;
    }

    /**
     * @return DepartmentInterface
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * @param ProgramYearInterface $programYear
     */
    public function setProgramYear(ProgramYearInterface $programYear)
    {
        $this->programYear = $programYear;
    }

    /**
     * @return ProgramYearInterface
     */
    public function getProgramYear()
    {
        if ($this->programYear && !$this->programYear->isDeleted()) {
            return $this->programYear;
        }
        
        return null;
    }
}
