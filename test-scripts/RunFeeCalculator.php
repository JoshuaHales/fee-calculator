<?php

require '../vendor/autoload.php';

use Lendable\Interview\Interpolation\FeeCalculatorImpl;
use Lendable\Interview\Interpolation\Model\LoanApplication;

/**
 * Example script demonstrating the use of the FeeCalculatorImpl class
 * to calculate loan fees based on a given loan term and loan amount.
 *
 * This script showcases basic usage of the calculator.
 */

// Create an instance of the FeeCalculatorImpl class, which implements the FeeCalculator interface.
$calculator = new FeeCalculatorImpl();

// Create a loan application with a term of 24 months and a loan amount of £2750.
$application = new LoanApplication(24, 2750);

// Calculate the fee for the given loan application using the calculator.
$fee = $calculator->calculate($application);

// Output the result, demonstrating the final calculated fee for the loan.
var_dump($fee); // $fee = (float) 115.0