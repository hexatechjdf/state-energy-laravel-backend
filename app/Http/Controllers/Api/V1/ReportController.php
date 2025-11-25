<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{

    public function generateReport(Request $request): JsonResponse
    {
        // --- Validate Input ---
        $validated = $request->validate([
            'monthly_electricity_bill' => 'required|numeric|min:0',
            'monthly_water_bill' => 'required|numeric|min:0',
            'monthly_insurance_bill' => 'required|numeric|min:0',
        ]);
        $newProgramPayment = isset($request->new_program_payment) ? $request->new_program_payment : 550; // per month
        $programTerm = isset($request->program_term) ? $request->program_term : 50; // in months
        // Monthly bill inputs
        $electricityBill = $validated['monthly_electricity_bill'];
        $waterBill = $validated['monthly_water_bill'];
        $insuranceBill = $validated['monthly_insurance_bill'];

        // Inflation Rates
        $electricInflation = 0.05;
        $waterInflation = 0.04;
        $insuranceInflation = 0.06;
        $hasSolar = false;
        // Savings Map
        $savingsMap = [
            'Roof' =>        ['electric' => 0.00, 'water' => 0.00, 'insurance' => 0.15],
            'Solar' =>       ['electric' => 1.00, 'water' => 0.00, 'insurance' => 0.00],
            'HVAC' =>        ['electric' => 0.20, 'water' => 0.00, 'insurance' => 0.00],
            'Insulation' =>  ['electric' => 0.10, 'water' => 0.00, 'insurance' => 0.00],
            'Windows' =>     ['electric' => 0.10, 'water' => 0.00, 'insurance' => 0.15],
            'Doors' =>       ['electric' => 0.10, 'water' => 0.00, 'insurance' => 0.15],
            'Water Heater' => ['electric' => 0.00, 'water' => 0.40, 'insurance' => 0.00],
            'Other' =>       ['electric' => 0.00, 'water' => 0.10, 'insurance' => 0.00],
        ];

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
                if ($categoryName === 'Solar') {
                    $hasSolar = true;
                }
                if (isset($savingsMap[$categoryName])) {
                    $electricSaving += $savingsMap[$categoryName]['electric'];
                    $waterSaving += $savingsMap[$categoryName]['water'];
                    $insuranceSaving += $savingsMap[$categoryName]['insurance'];
                }
            }
        } else {
            // SINGLE CATEGORY
            $categoryName = Category::find($request->category_id)->name ?? 'Other';
            if ($categoryName === 'Solar') {
                $hasSolar = true;
            }
            $electricSaving = $savingsMap[$categoryName]['electric'];
            $waterSaving = $savingsMap[$categoryName]['water'];
            $insuranceSaving = $savingsMap[$categoryName]['insurance'];
        }
        $gridFee = $hasSolar ? 25 : 0;
        $annualGridFee = $hasSolar ? 300 : 0; // 25 * 12
        // --- CAP SAVINGS AT 100% ---
        $electricSaving   = min(1, $electricSaving);
        $waterSaving      = min(1, $waterSaving);
        $insuranceSaving  = min(1, $insuranceSaving);
        $annualElectricityBill = $electricityBill * 12;
        $annualWaterBill = $waterBill * 12;
        $annualInsuranceBill = $insuranceBill * 12;

        $actual25yearElectricityCost = $annualElectricityBill * (pow((1 + $electricInflation), 25) - 1) / $electricInflation;
        $actual25yearWaterCost = $annualWaterBill * (pow((1 + $waterInflation), 25) - 1) / $waterInflation;
        $actual25yearInsuranceCost = $annualInsuranceBill * (pow((1 + $insuranceInflation), 25) - 1) / $insuranceInflation;
        // Inflation Rates
        $totalBaseCost = $actual25yearElectricityCost + $actual25yearWaterCost + $actual25yearInsuranceCost;

        $ajd25yearElectricityCost = $actual25yearElectricityCost * (1 - $electricSaving) + (25 * 300);
        $ajd25yearWaterCost = $actual25yearWaterCost * (1 - $waterSaving);
        $ajd25yearInsuranceCost = $actual25yearInsuranceCost * (1 - $insuranceSaving);
        $totalAdjustedCost = $ajd25yearElectricityCost + $ajd25yearWaterCost + $ajd25yearInsuranceCost + ($newProgramPayment * $programTerm);

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
                'total_cost_without_savings' => round($totalBaseCost, 2),
                'total_adjusted_cost_with_savings' => round($totalAdjustedCost, 2),
                'total_savings_over_25_years' => round($totalBaseCost - $totalAdjustedCost, 2),
            ]
        ]);
    }
}
