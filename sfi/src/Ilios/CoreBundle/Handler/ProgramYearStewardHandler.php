<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\ORM\EntityManager;

use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\ProgramYearStewardType;
use Ilios\CoreBundle\Entity\Manager\ProgramYearStewardManager;
use Ilios\CoreBundle\Entity\ProgramYearStewardInterface;

class ProgramYearStewardHandler extends ProgramYearStewardManager
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
     * @return ProgramYearStewardInterface
     */
    public function post(array $parameters)
    {
        $programYearSteward = $this->createProgramYearSteward();

        return $this->processForm($programYearSteward, $parameters, 'POST');
    }

    /**
     * @param ProgramYearStewardInterface $programYearSteward
     * @param array $parameters
     *
     * @return ProgramYearStewardInterface
     */
    public function put(
        ProgramYearStewardInterface $programYearSteward,
        array $parameters
    ) {
        return $this->processForm(
            $programYearSteward,
            $parameters,
            'PUT'
        );
    }
    /**
     * @param ProgramYearStewardInterface $programYearSteward
     * @param array $parameters
     *
     * @return ProgramYearStewardInterface
     */
    public function patch(
        ProgramYearStewardInterface $programYearSteward,
        array $parameters
    ) {
        return $this->processForm(
            $programYearSteward,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param ProgramYearStewardInterface $programYearSteward
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return ProgramYearStewardInterface
     */
    protected function processForm(
        ProgramYearStewardInterface $programYearSteward,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            new ProgramYearStewardType(),
            $programYearSteward,
            array('method' => $method)
        );
        $form->submit($parameters, 'PATCH' !== $method);

        if ($form->isValid()) {
            $programYearSteward = $form->getData();
            $this->updateProgramYearSteward($programYearSteward, true);

            return $programYearSteward;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }
}
