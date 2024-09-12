<?php

declare(strict_types=1);

namespace Lendable\Interview\Interpolation;

use Lendable\Interview\Interpolation\Model\LoanApplication;

/**
 * Class FeeCalculatorImpl
 *
 * This class implements the FeeCalculator interface and calculates loan fees
 * based on predefined fee structures for different loan terms. It supports fee
 * calculation for loans with terms of 12 or 24 months.
 *
 * The fee is determined by exact breakpoints in the fee structure, with
 * interpolation between breakpoints when necessary, and rounding up to the
 * nearest multiple of 5.
 */
class FeeCalculatorImpl implements FeeCalculator
{
    /**
     * Minimum loan amount allowed.
     */
    private const MIN_LOAN_AMOUNT = 1000;

    /**
     * Maximum loan amount allowed.
     */
    private const MAX_LOAN_AMOUNT = 20000;

    /**
     * Fee structure for loans with a 12-month term.
     */
    private array $feeStructure12 = [
        1000 => 50, 
        2000 => 90, 
        3000 => 90, 
        4000 => 115, 
        5000 => 100, 
        6000 => 120, 
        7000 => 140, 
        8000 => 160, 
        9000 => 180, 
        10000 => 200, 
        11000 => 220, 
        12000 => 240, 
        13000 => 260, 
        14000 => 280, 
        15000 => 300, 
        16000 => 320, 
        17000 => 340, 
        18000 => 360, 
        19000 => 380, 
        20000 => 400
    ];

    /**
     * Fee structure for loans with a 24-month term.
     */
    private array $feeStructure24 = [
        1000 => 70, 
        2000 => 100, 
        3000 => 120, 
        4000 => 160, 
        5000 => 200,
        6000 => 240, 
        7000 => 280,
        8000 => 320, 
        9000 => 360, 
        10000 => 400,
        11000 => 440, 
        12000 => 480, 
        13000 => 520, 
        14000 => 560, 
        15000 => 600,
        16000 => 640, 
        17000 => 680, 
        18000 => 720, 
        19000 => 760, 
        20000 => 800
    ];

    /**
     * Calculates the fee for a loan application based on the loan amount and term.
     *
     * @param LoanApplication $application The loan application containing the loan amount and term.
     * @return float The calculated fee for the loan.
     */
    public function calculate(LoanApplication $application): float
    {
        // Retrieve the loan amount and term from the loan application.
        $amount = $application->amount();
        $term = $application->term();

        // Determine the correct fee structure based on the loan term.
        $feeStructure = ($term === 12) ? $this->feeStructure12 : $this->feeStructure24;

        // Get lower and upper bounds for the loan amount
        $lowerBound = $this->getClosestLowerBound($amount, $feeStructure);
        $upperBound = $this->getClosestUpperBound($amount, $feeStructure);

        // If the amount is an exact match with a breakpoint, use that fee.
        if ($lowerBound === $upperBound) {
            $fee = $feeStructure[$lowerBound];
        } else {
            // Otherwise, interpolate between the lower and upper bounds.
            $fee = $this->interpolateFee($amount, $lowerBound, $upperBound, $feeStructure);
        }

        // Return the fee, rounded up to the nearest multiple of 5.
        return $this->roundUpToNearestFive($fee + $amount) - $amount;
    }

    /**
     * Get the closest lower bound in the fee structure for the loan amount.
     *
     * @param float $amount The loan amount.
     * @param array $structure The fee structure for the given term.
     * @return int The closest lower bound in the fee structure.
     */
    private function getClosestLowerBound(float $amount, array $structure): int
    {
        // Reverse the fee structure array so we start looking from the highest values.
        // This ensures we find the largest key (loan amount) that is still less than or equal to the given amount.
        foreach (array_reverse($structure, true) as $key => $fee) {
            // Check if the current key (loan amount) is less than or equal to the provided amount.
            if ($key <= $amount) {
                // If a match is found, return this key as the closest lower bound.
                return $key;
            }
        }
        return self::MIN_LOAN_AMOUNT; // minimum bound
    }

    /**
     * Get the closest upper bound in the fee structure for the loan amount.
     *
     * @param float $amount The loan amount.
     * @param array $structure The fee structure for the given term.
     * @return int The closest upper bound in the fee structure.
     */
    private function getClosestUpperBound(float $amount, array $structure): int
    {
        // Loop through the fee structure array, starting from the smallest loan amounts.
        foreach ($structure as $key => $fee) {
            // Check if the current key (loan amount) is greater than or equal to the provided amount.
            if ($key >= $amount) {
                // If a match is found, return this key as the closest upper bound.
                return $key;
            }
        }
        return self::MAX_LOAN_AMOUNT; // maximum bound
    }

    /**
     * Interpolates the fee for amounts between two breakpoints.
     *
     * @param float $amount The loan amount.
     * @param int $lower The closest lower bound in the fee structure.
     * @param int $upper The closest upper bound in the fee structure.
     * @param array $structure The fee structure for the given term.
     * @return float The interpolated fee.
     */
    private function interpolateFee(float $amount, int $lower, int $upper, array $structure): float
    {
        // Get the fee for the lower bound
        $lowerFee = $structure[$lower];

        // Get the fee for the upper bound
        $upperFee = $structure[$upper];

        // Linear interpolation
        // Calculate the ratio between the amount and the bounds
        $ratio = ($amount - $lower) / ($upper - $lower);

        // Interpolate the fee
        return $lowerFee + ($upperFee - $lowerFee) * $ratio;
    }

    /**
     * Rounds a value up to the nearest multiple of 5.
     *
     * @param float $value The value to be rounded.
     * @return float The value rounded up to the nearest multiple of 5.
     */
    private function roundUpToNearestFive(float $value): float
    {
        return ceil($value / 5) * 5;
    }
}