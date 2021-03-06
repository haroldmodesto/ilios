<?php
namespace App\Tests\Entity;

use App\Entity\MeshQualifier;
use Mockery as m;

/**
 * Tests for Entity MeshQualifier
 */
class MeshQualifierTest extends EntityBase
{
    /**
     * @var MeshQualifier
     */
    protected $object;

    /**
     * Instantiate a MeshQualifier object
     */
    protected function setUp()
    {
        $this->object = new MeshQualifier;
    }

    public function testNotBlankValidation()
    {
        $notBlank = array(
            'name'
        );
        $this->validateNotBlanks($notBlank);

        $this->object->setName('test_name');
        $this->validate(0);
    }
    /**
     * @covers \App\Entity\MeshQualifier::__construct
     */
    public function testConstructor()
    {
        $now = \DateTime::createFromFormat('U', time());
        $createdAt = $this->object->getCreatedAt();
        $this->assertTrue($createdAt instanceof \DateTime);
        $diff = $now->diff($createdAt);
        $this->assertTrue($diff->s < 2);
    }

    /**
     * @covers \App\Entity\MeshQualifier::setName
     * @covers \App\Entity\MeshQualifier::getName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers \App\Entity\MeshQualifier::stampUpdate
     */
    public function testStampUpdate()
    {
        $now = new \DateTime();
        $this->object->stampUpdate();
        $updatedAt = $this->object->getUpdatedAt();
        $this->assertTrue($updatedAt instanceof \DateTime);
        $diff = $now->diff($updatedAt);
        $this->assertTrue($diff->s < 2);
    }

    /**
     * @covers \App\Entity\MeshQualifier::addDescriptor
     */
    public function testAddDescriptor()
    {
        $this->entityCollectionAddTest('descriptor', 'MeshDescriptor');
    }

    /**
     * @covers \App\Entity\MeshQualifier::removeDescriptor
     */
    public function testRemoveDescriptor()
    {
        $this->entityCollectionRemoveTest('descriptor', 'MeshDescriptor');
    }

    /**
     * @covers \App\Entity\MeshQualifier::getDescriptors
     * @covers \App\Entity\MeshQualifier::setDescriptors
     */
    public function getGetDescriptors()
    {
        $this->entityCollectionSetTest('descriptor', 'MeshDescriptor');
    }
}
