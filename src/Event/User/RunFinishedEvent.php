<?php

namespace App\Event\User;

use App\Entity\Run;

class RunFinishedEvent
{
    public function __construct(private Run $run)
    {

    }

    /**
     * @return Run
     */
    public function getRun(): Run
    {
        return $this->run;
    }

}