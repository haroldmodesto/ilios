<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\ORM\EntityManager;

use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\CurriculumInventorySequenceType;
use Ilios\CoreBundle\Entity\Manager\CurriculumInventorySequenceManager;
use Ilios\CoreBundle\Entity\CurriculumInventorySequenceInterface;

class CurriculumInventorySequenceHandler extends CurriculumInventorySequenceManager
{
    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @param EntityManager $em
     * @param string $class
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(EntityManager $em, $class, FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
        parent::__construct($em, $class);
    }

    /**
     * @param array $parameters
     *
     * @return CurriculumInventorySequenceInterface
     */
    public function post(array $parameters)
    {
        $curriculumInventorySequence = $this->createCurriculumInventorySequence();

        return $this->processForm($curriculumInventorySequence, $parameters, 'POST');
    }

    /**
     * @param CurriculumInventorySequenceInterface $curriculumInventorySequence
     * @param array $parameters
     *
     * @return CurriculumInventorySequenceInterface
     */
    public function put(
        CurriculumInventorySequenceInterface $curriculumInventorySequence,
        array $parameters
    ) {
        return $this->processForm(
            $curriculumInventorySequence,
            $parameters,
            'PUT'
        );
    }
    /**
     * @param CurriculumInventorySequenceInterface $curriculumInventorySequence
     * @param array $parameters
     *
     * @return CurriculumInventorySequenceInterface
     */
    public function patch(
        CurriculumInventorySequenceInterface $curriculumInventorySequence,
        array $parameters
    ) {
        return $this->processForm(
            $curriculumInventorySequence,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param CurriculumInventorySequenceInterface $curriculumInventorySequence
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return CurriculumInventorySequenceInterface
     */
    protected function processForm(
        CurriculumInventorySequenceInterface $curriculumInventorySequence,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            new CurriculumInventorySequenceType(),
            $curriculumInventorySequence,
            array('method' => $method)
        );
        $form->submit($parameters, 'PATCH' !== $method);

        if ($form->isValid()) {
            $curriculumInventorySequence = $form->getData();
            $this->updateCurriculumInventorySequence($curriculumInventorySequence, true);

            return $curriculumInventorySequence;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }
}
