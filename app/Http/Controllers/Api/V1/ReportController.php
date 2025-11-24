<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Category;

class ReportController extends Controller
{
    public function generateReport1(Request $request): JsonResponse
    {
        // --- Input Parameters ---
        $validated = $request->validate([
            'monthly_electricity_bill' => 'required|numeric|min:0',
            'monthly_water_bill' => 'required|numeric|min:0',
            'monthly_insurance_bill' => 'required|numeric|min:0',
        ]);
        if (! $validated) {
            return response()->json(['error' => 'Invalid input parameters'], 400);
        }
        $newProgramPayment = 120;
        $monthlyElectricityBill = $request->input('monthly_electricity_bill', 200);
        $monthlyWaterBill = $request->input('monthly_water_bill', 100);
        $monthlyInsuranceBill = $request->input('monthly_insurance_bill', 100);

        $electricInflationRate = 0.05; // 5%
        $waterInflationRate = 0.04;    // 4%
        $insuranceInflationRate = 0.06; // 6%
        $savingsMap = [
            'Roof' => [
                'electric' => 0.00,   // 0%
                'water' => 0.00,
                'insurance' => 0.00,
                'grid_fee' => 25,     // special rule
            ],
            'Solar' => [
                'electric' => 1.00,   // 100%
                'water' => 0.00,
                'insurance' => 0.00,
                'grid_fee' => 25,     // special rule
            ],
            'HVAC' => [
                'electric' => 0.20,
                'water' => 0.00,
                'insurance' => 0.00,
                'grid_fee' => 25,
            ],
            'Insulation' => [
                'electric' => 0.10,
                'water' => 0.00,
                'insurance' => 0.00,
                'grid_fee' => 25,
            ],
            'Windows' => [
                'electric' => 0.10,
                'water' => 0.00,
                'insurance' => 0.15,
                'grid_fee' => 25,
            ],
            'Doors' => [
                'electric' => 0.00,
                'water' => 0.00,
                'insurance' => 0.15,
                'grid_fee' => 25,
            ],
            'Water Heater' => [
                'electric' => 0.00,
                'water' => 0.40,
                'insurance' => 0.00,
                'grid_fee' => 25,
            ],
            'Other' => [
                'electric' => 0.00,
                'water' => 0.10,
                'insurance' => 0.00,
                'grid_fee' => 25,
            ],

        ];
        $category_id = $request->input('category_id');
        $electricitySavingPercentage = 0.0;
        $waterSavingPercentage = 0.0;
        $insuranceSavingPercentage = 0.0;
        if ($request->fromCart && $request->fromCart == true) {
            $category_ids = Cart::where('user_id', auth()->id())->where('appointment_id', $request->appointment_id ?? $request->id ?? null)->pluck('category_id')->toArray();
            foreach ($category_ids as $cat_id) {
                $category = Category::find($cat_id)->name ?? 'Other';
                if (isset($savingsMap[$category])) {
                    $electricitySavingPercentage += $savingsMap[$category]['electric'];
                    $waterSavingPercentage += $savingsMap[$category]['water'];
                    $insuranceSavingPercentage += $savingsMap[$category]['insurance'];
                }
            }
        } else {
            $category = Category::find($category_id)->name ?? 'Other';
            $electricitySavingPercentage = $savingsMap[$category]['electric'];
            $waterSavingPercentage = $savingsMap[$category]['water'];
            $insuranceSavingPercentage = $savingsMap[$category]['insurance'];
        }

        $fixedMonthlyFee = 25; // e.g., $25 for Solar

        // --- Calculations ---
        $years = [0, 5, 10, 15, 20, 25];
        $costProjectionRows = [];
        $adjustedCostRows = [];

        // Helper function for future value calculation
        $calculateFutureValue = function ($presentValue, $rate, $periods) {
            return $presentValue * pow((1 + $rate), $periods);
        };

        foreach ($years as $year) {
            // 1. Cost Projection Table Calculations
            $projectedElectric = $calculateFutureValue($monthlyElectricityBill, $electricInflationRate, $year);
            $projectedWater = $calculateFutureValue($monthlyWaterBill, $waterInflationRate, $year);
            $projectedInsurance = $calculateFutureValue($monthlyInsuranceBill, $insuranceInflationRate, $year);

            $costProjectionRows[] = [
                'year' => $year,
                'monthly_electricity' => round($projectedElectric, 2),
                'monthly_water' => round($projectedWater, 2),
                'monthly_insurance' => round($projectedInsurance, 2),
                'total_monthly' => round($projectedElectric + $projectedWater + $projectedInsurance, 2),
            ];

            // 2. Adjusted Cost Table Calculations

            $adjustedElectric = ($monthlyElectricityBill * (1 - $electricitySavingPercentage) * pow((1 + $electricInflationRate), $year)) + $fixedMonthlyFee;
            $adjustedWater = ($monthlyWaterBill * (1 - $waterSavingPercentage) * pow((1 + $waterInflationRate), $year));
            $adjustedInsurance = ($monthlyInsuranceBill * (1 - $insuranceSavingPercentage) * pow((1 + $insuranceInflationRate), $year));

            $adjustedCostRows[] = [
                'year' => $year,
                'adjusted_electricity' => round($adjustedElectric, 2),
                'adjusted_water' => round($adjustedWater, 2),
                'adjusted_insurance' => round($adjustedInsurance, 2),
                'total_adjusted_monthly' => round($adjustedElectric + $adjustedWater + $adjustedInsurance + $newProgramPayment, 2),
            ];
        }

        // 3. Overall 25-Year Summary Calculation
        $totalCostWithoutSavings = 0;
        $totalAdjustedCostWithSavings = 0;
        for ($i = 0; $i < 25 * 12; $i++) {
            $currentYear = floor($i / 12);
            // Calculate cost for each month over 25 years
            $projectedElectricMonthly = $calculateFutureValue($monthlyElectricityBill, $electricInflationRate, $currentYear);
            $projectedWaterMonthly = $calculateFutureValue($monthlyWaterBill, $waterInflationRate, $currentYear);
            $projectedInsuranceMonthly = $calculateFutureValue($monthlyInsuranceBill, $insuranceInflationRate, $currentYear);

            $totalCostWithoutSavings += $projectedElectricMonthly + $projectedWaterMonthly + $projectedInsuranceMonthly;

            $adjustedElectricMonthly = ($projectedElectricMonthly * (1 - $electricitySavingPercentage)) + $fixedMonthlyFee;
            $adjustedWaterMonthly = $projectedWaterMonthly * (1 - $waterSavingPercentage);
            $adjustedInsuranceMonthly = $projectedInsuranceMonthly * (1 - $insuranceSavingPercentage);

            $totalAdjustedCostWithSavings += $adjustedElectricMonthly + $adjustedWaterMonthly + $adjustedInsuranceMonthly + $newProgramPayment;
        }


        // --- JSON Response Structure ---
        $response = [
            'cost_projection_table' => [
                'title' => 'Cost Projection Over 25 Years',
                'description' => 'Projected monthly costs for electricity, water, and insurance, accounting for annual inflation.',
                'headers' => ['Year', 'Monthly Electricity Bill', 'Monthly Water Bill', 'Monthly Insurance Bill', 'Total Monthly Cost'],
                'rows' => $costProjectionRows,
            ],
            'adjusted_cost_table' => [
                'title' => 'Adjusted Monthly Cost After Savings',
                'description' => "The 'new' effective monthly cost after applying savings and adding a fixed monthly fee.",
                'headers' => ['Year', 'Adjusted Electricity Cost', 'Adjusted Water Cost', 'Adjusted Insurance Cost', 'Total Adjusted Monthly Cost'],
                'rows' => $adjustedCostRows,
            ],
            'overall_summary' => [
                'title' => '25-Year Financial Summary',
                'description' => 'A comparison of the total costs over 25 years with and without the proposed savings measures.',
                'total_cost_without_savings' => round($totalCostWithoutSavings, 2),
                'total_adjusted_cost_with_savings' => round($totalAdjustedCostWithSavings, 2),
                'total_savings_over_25_years' => round($totalCostWithoutSavings - $totalAdjustedCostWithSavings, 2),
            ],
        ];

        return response()->json($response);
    }
    public function generateReport(Request $request): JsonResponse
    {
        // --- Validate Input ---
        $validated = $request->validate([
            'monthly_electricity_bill' => 'required|numeric|min:0',
            'monthly_water_bill' => 'required|numeric|min:0',
            'monthly_insurance_bill' => 'required|numeric|min:0',
        ]);

        // Monthly bill inputs
        $electricityBill = $validated['monthly_electricity_bill'];
        $waterBill = $validated['monthly_water_bill'];
        $insuranceBill = $validated['monthly_insurance_bill'];

        // Inflation Rates
        $electricInflation = 0.05;
        $waterInflation = 0.04;
        $insuranceInflation = 0.06;

        // Savings Map
        $savingsMap = [
            'Roof' =>        ['electric' => 0.00, 'water' => 0.00, 'insurance' => 0.00],
            'Solar' =>       ['electric' => 1.00, 'water' => 0.00, 'insurance' => 0.00],
            'HVAC' =>        ['electric' => 0.20, 'water' => 0.00, 'insurance' => 0.00],
            'Insulation' =>  ['electric' => 0.10, 'water' => 0.00, 'insurance' => 0.00],
            'Windows' =>     ['electric' => 0.10, 'water' => 0.00, 'insurance' => 0.15],
            'Doors' =>       ['electric' => 0.00, 'water' => 0.00, 'insurance' => 0.15],
            'Water Heater' => ['electric' => 0.00, 'water' => 0.40, 'insurance' => 0.00],
            'Other' =>       ['electric' => 0.00, 'water' => 0.10, 'insurance' => 0.00],
        ];

        // Fixed Monthly Grid Fee
        $gridFee = 25;

        // --- Determine Applicable Categories ---
        $electricSaving = 0;
        $waterSaving = 0;
        $insuranceSaving = 0;

        if ($request->fromCart == true) {
            // MULTIPLE CATEGORIES (Cart)
            $categoryIds = Cart::where('user_id', auth()->id())
                ->where('appointment_id', $request->appointment_id ?? $request->id ?? null)
                ->pluck('category_id')
                ->toArray();

            foreach ($categoryIds as $id) {
                $categoryName = Category::find($id)->name ?? 'Other';
                if (isset($savingsMap[$categoryName])) {
                    $electricSaving += $savingsMap[$categoryName]['electric'];
                    $waterSaving += $savingsMap[$categoryName]['water'];
                    $insuranceSaving += $savingsMap[$categoryName]['insurance'];
                }
            }
        } else {
            // SINGLE CATEGORY
            $categoryName = Category::find($request->category_id)->name ?? 'Other';
            $electricSaving = $savingsMap[$categoryName]['electric'];
            $waterSaving = $savingsMap[$categoryName]['water'];
            $insuranceSaving = $savingsMap[$categoryName]['insurance'];
        }

        // --- CAP SAVINGS AT 100% ---
        $electricSaving   = min(1, $electricSaving);
        $waterSaving      = min(1, $waterSaving);
        $insuranceSaving  = min(1, $insuranceSaving);

        // --------------- Helper Function ---------------
        $futureValue = function ($present, $rate, $years) {
            return $present * pow((1 + $rate), $years);
        };

        // Years for projection
        $years = [0, 5, 10, 15, 20, 25];

        $costProjectionRows = [];
        $adjustedCostRows = [];

        // --------------- Build Tables ---------------
        foreach ($years as $year) {
            // Future projected original bills
            $projE = $futureValue($electricityBill, $electricInflation, $year);
            $projW = $futureValue($waterBill, $waterInflation, $year);
            $projI = $futureValue($insuranceBill, $insuranceInflation, $year);

            $costProjectionRows[] = [
                'year' => $year,
                'monthly_electricity' => round($projE, 2),
                'monthly_water' => round($projW, 2),
                'monthly_insurance' => round($projI, 2),
                'total_monthly' => round($projE + $projW + $projI, 2),
            ];

            // Adjusted (after savings) costs
            $adjE = ($projE * (1 - $electricSaving)) + $gridFee;
            $adjW = ($projW * (1 - $waterSaving));
            $adjI = ($projI * (1 - $insuranceSaving));

            $adjustedCostRows[] = [
                'year' => $year,
                'adjusted_electricity' => round($adjE, 2),
                'adjusted_water' => round($adjW, 2),
                'adjusted_insurance' => round($adjI, 2),
                'total_adjusted_monthly' => round($adjE + $adjW + $adjI + $newProgramPayment, 2), // +120 fixed cost?
            ];
        }

        // --------------- 25-Year Summary ---------------
        $totalOriginal = 0;
        $totalAdjusted = 0;

        for ($month = 0; $month < (25 * 12); $month++) {
            $year = floor($month / 12);

            $projE = $futureValue($electricityBill, $electricInflation, $year);
            $projW = $futureValue($waterBill, $waterInflation, $year);
            $projI = $futureValue($insuranceBill, $insuranceInflation, $year);

            $totalOriginal += ($projE + $projW + $projI);

            $adjE = ($projE * (1 - $electricSaving)) + $gridFee;
            $adjW = ($projW * (1 - $waterSaving));
            $adjI = ($projI * (1 - $insuranceSaving));

            $totalAdjusted += ($adjE + $adjW + $adjI + );
        }

        // --------------- Final Response ---------------
        return response()->json([
            'cost_projection_table' => [
                'title' => 'Cost Projection Over 25 Years',
                'description' => 'Projected monthly costs with inflation applied.',
                'headers' => ['Year', 'Monthly Electricity', 'Monthly Water', 'Monthly Insurance', 'Total Monthly'],
                'rows' => $costProjectionRows,
            ],
            'adjusted_cost_table' => [
                'title' => 'Adjusted Costs With Savings',
                'description' => 'Monthly cost after applying savings and fixed fees.',
                'headers' => ['Year', 'Electricity', 'Water', 'Insurance', 'Total Adjusted Monthly'],
                'rows' => $adjustedCostRows,
            ],
            'overall_summary' => [
                'title' => '25-Year Summary',
                'total_cost_without_savings' => round($totalOriginal, 2),
                'total_adjusted_cost_with_savings' => round($totalAdjusted, 2),
                'total_savings_over_25_years' => round($totalOriginal - $totalAdjusted, 2),
            ]
        ]);
    }
}
