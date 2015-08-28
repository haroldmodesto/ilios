<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Traits\NameableEntityInterface;
use Ilios\CoreBundle\Traits\TimestampableEntityInterface;
use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;

/**
 * Interface MeshTermInterface
 * @package Ilios\CoreBundle\Entity
 */
interface MeshTermInterface extends
    IdentifiableEntityInterface,
    NameableEntityInterface,
    TimestampableEntityInterface,
    LoggableEntityInterface
{

    /**
     * @param string $meshTermUid
     */
    public function setMeshTermUid($meshTermUid);

    /**
     * @return string
     */
    public function getMeshTermUid();

    /**
     * @param string $lexicalTag
     */
    public function setLexicalTag($lexicalTag);

    /**
     * @return string
     */
    public function getLexicalTag();

    /**
     * @param boolean $conceptPreferred
     */
    public function setConceptPreferred($conceptPreferred);

    /**
     * @return boolean
     */
    public function isConceptPreferred();

    /**
     * @param boolean $recordPreferred
     */
    public function setRecordPreferred($recordPreferred);

    /**
     * @return boolean
     */
    public function isRecordPreferred();

    /**
     * @param boolean $permuted
     */
    public function setPermuted($permuted);

    /**
     * @return boolean
     */
    public function isPermuted();

    /**
     * @param boolean $printable
     */
    public function setPrintable($printable);

    /**
     * @return boolean
     */
    public function isPrintable();

    /**
     * @param Collection $concepts
     */
    public function setConcepts(Collection $concepts);

    /**
     * @param MeshConceptInterface $concept
     */
    public function addConcept(MeshConceptInterface $concept);

    /**
     * @return ArrayCollection|MeshConceptInterface[]
     */
    public function getConcepts();
}
