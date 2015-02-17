<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\ORM\EntityManager;

use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\SchoolType;
use Ilios\CoreBundle\Entity\Manager\SchoolManager;
use Ilios\CoreBundle\Entity\SchoolInterface;

class SchoolHandler extends SchoolManager
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
     * @return SchoolInterface
     */
    public function post(array $parameters)
    {
        $school = $this->createSchool();

        return $this->processForm($school, $parameters, 'POST');
    }

    /**
     * @param SchoolInterface $school
     * @param array $parameters
     *
     * @return SchoolInterface
     */
    public function put(
        SchoolInterface $school,
        array $parameters
    ) {
        return $this->processForm(
            $school,
            $parameters,
            'PUT'
        );
    }
    /**
     * @param SchoolInterface $school
     * @param array $parameters
     *
     * @return SchoolInterface
     */
    public function patch(
        SchoolInterface $school,
        array $parameters
    ) {
        return $this->processForm(
            $school,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param SchoolInterface $school
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return SchoolInterface
     */
    protected function processForm(
        SchoolInterface $school,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            new SchoolType(),
            $school,
            array('method' => $method)
        );
        $form->submit($parameters, 'PATCH' !== $method);

        if ($form->isValid()) {
            $school = $form->getData();
            $this->updateSchool($school, true);

            return $school;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }
}
