<?php

namespace App\Entity\DTO;

use App\Annotation as IS;

/**
 * Class SessionDTO
 * Data transfer object for a session.
 *
 * @IS\DTO
 */
class SessionDTO
{
    /**
     * @var int
     * @IS\Expose
     * @IS\Type("integer")
     */
    public $id;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     */
    public $title;

    /**
     * @var boolean
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public $attireRequired;

    /**
     * @var boolean
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public $equipmentRequired;

    /**
     * @var boolean
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public $supplemental;

    /**
     * @var boolean
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public $attendanceRequired;

    /**
     * @var boolean
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public $publishedAsTbd;

    /**
     * @var boolean
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public $published;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     */
    public $instructionalNotes;

    /**
     * @var \DateTime
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    public $updatedAt;

    /**
     * @var int
     * @IS\Expose
     * @IS\Type("string")
     */
    public $sessionType;

    /**
     * @var int
     * @IS\Expose
     * @IS\Type("string")
     */
    public $course;

    /**
     * @var int
     * @IS\Expose
     * @IS\Type("string")
     */
    public $ilmSession;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $terms;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $objectives;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $meshDescriptors;

    /**
     * @var int
     * @IS\Expose
     * @IS\Type("string")
     */
    public $sessionDescription;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $learningMaterials;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $administrators;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $offerings;

    /**
     * @var int
     * @IS\Expose
     * @IS\Type("string")
     */
    public $postrequisite;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $prerequisites;

    /**
     * For Voter use, not public
     * @var int
     */
    public $school;

    public function __construct(
        $id,
        $title,
        $attireRequired,
        $equipmentRequired,
        $supplemental,
        $attendanceRequired,
        $publishedAsTbd,
        $published,
        $instructionalNotes,
        $updatedAt
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->attireRequired = $attireRequired;
        $this->equipmentRequired = $equipmentRequired;
        $this->supplemental = $supplemental;
        $this->attendanceRequired = $attendanceRequired;
        $this->publishedAsTbd = $publishedAsTbd;
        $this->published = $published;
        $this->instructionalNotes = $instructionalNotes;
        $this->updatedAt = $updatedAt;

        $this->terms = [];
        $this->objectives = [];
        $this->meshDescriptors = [];
        $this->learningMaterials = [];
        $this->offerings = [];
        $this->administrators = [];
        $this->prerequisites = [];
    }
}
