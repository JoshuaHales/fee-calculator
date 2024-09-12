<?php


declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Lendable\Interview\Interpolation\FeeCalculatorImpl;
use Lendable\Interview\Interpolation\Model\LoanApplication;

/**
 * Class FeeCalculatorTest
 *
 * This test class evaluates the correctness of the FeeCalculatorImpl class.
 * It ensures that the loan fee calculation works for exact breakpoints, interpolation,
 * and rounding rules, with example test cases.
 */
class FeeCalculatorTest extends TestCase
{
    /**
     * Test case for calculating the fee for an exact breakpoint.
     *
     * This test checks that when a loan amount falls exactly on a defined fee breakpoint,
     * the calculator returns the expected fee without interpolation.
     */
    public function testCalculateExactBreakpoint()
    {
        $calculator = new FeeCalculatorImpl();
        $application = new LoanApplication(12, 1000);

        // Assert that the fee for a 12-month loan of £1000 is exactly £50
        $this->assertEquals(50, $calculator->calculate($application));
    }

    /**
     * Test case for calculating the fee using linear interpolation between two breakpoints.
     *
     * This test ensures that for loan amounts between defined breakpoints, the fee is calculated
     * by interpolating between the lower and upper bounds.
     */
    public function testCalculateInterpolation()
    {
        $calculator = new FeeCalculatorImpl();
        $application = new LoanApplication(12, 1500);

        // Assert that the interpolated fee for a 12-month loan of £1500 is £70
        $this->assertEquals(70, $calculator->calculate($application)); // Interpolated fee
    }

    /**
     * Test case for rounding up the fee so that the sum of loan amount and fee is a multiple of 5.
     *
     * This test verifies that the calculated fee is correctly rounded up
     * so that the loan amount plus fee is divisible by 5.
     */
    public function testRoundUpToNearestFive()
    {
        $calculator = new FeeCalculatorImpl();
        $application = new LoanApplication(24, 2750);

        // Assert that the fee for a 24-month loan of £2750 is rounded to £115
        $this->assertEquals(115, $calculator->calculate($application)); // Rounded up to 5 multiple
    }

    /**
     * Test case based on the example provided in the challenge documentation.
     *
     * This test checks the fee calculation for a loan amount of £2750 with a 24-month term,
     * verifying that the fee calculation aligns with the example provided.
     */
    public function testCalculateExampleProvided()
    {
        // Instantiate the concrete FeeCalculatorImpl class
        $calculator = new FeeCalculatorImpl();
        
        // Create a loan application with term = 24 months and amount = 2750
        $application = new LoanApplication(24, 2750);
        
        // Calculate the fee
        $fee = $calculator->calculate($application);
        
        // Assert the fee is as expected (should be rounded to the nearest 5)
        $this->assertEquals(115.0, $fee);
    }

    /**
     * Test case for running multiple loan fee calculations using a data provider.
     */
    #[DataProvider('loanApplicationDataProvider')]
    public function testFeeCalculation(int $term, float $amount, float $expectedFee)
    {
        $calculator = new FeeCalculatorImpl();
        $application = new LoanApplication($term, $amount);
        
        // Assert that the fee calculation matches the expected fee
        $this->assertEquals($expectedFee, $calculator->calculate($application));
    }

    /**
     * Data provider for testFeeCalculation().
     *
     * Provides different loan term and amount combinations, along with the expected fee.
     */
    public static function loanApplicationDataProvider(): array
    {
        return [
            'Exact Breakpoint' => [12, 1000, 50],  // Exact breakpoint
            'Interpolated Fee' => [12, 1500, 70],  // Interpolated fee
            'Rounded to Nearest 5' => [24, 2750, 115],  // Rounded to nearest 5
        ];
    }
}