<?php

namespace AppBundle\Entity\Manager;

use AppBundle\Entity\Repository\ProgramYearRepository;

/**
 * Class ProgramYearManager
 * @package AppBundle\Entity\Manager
 */
class ProgramYearManager extends BaseManager
{
    /**
     * @param int $programYearId
     * @return array
     * @throws \Exception
     */
    public function getProgramYearObjectiveToCourseObjectivesMapping($programYearId): array
    {
        /** @var ProgramYearRepository $repo */
        $repo = $this->getRepository();
        return $repo->getProgramYearObjectiveToCourseObjectivesMapping($programYearId);
    }
}