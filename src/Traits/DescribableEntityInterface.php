<?php

namespace App\Traits;

/**
 * Interface DescribableEntityInterface
 */
interface DescribableEntityInterface
{
    /**
     * @param string $description
     */
    public function setDescription($description);

    /**
     * @return string
     */
    public function getDescription();
}
