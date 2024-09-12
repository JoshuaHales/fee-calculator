<?php

declare(strict_types=1);

namespace Lendable\Interview\Interpolation;

use Lendable\Interview\Interpolation\Model\LoanApplication;

interface FeeCalculator
{
    /**
     * @return float The calculated total fee.
     */
    public function calculate(LoanApplication $application): float;
}
