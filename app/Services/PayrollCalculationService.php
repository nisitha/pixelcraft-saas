<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Payroll;
use App\Models\PayrollItem;
use App\Models\SalaryAdvance;
use Carbon\Carbon;
use DomainException;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class PayrollCalculationService
{
    /**
     * Generate or regenerate monthly payroll for all active employees of a tenant.
     *
     * @throws DomainException|InvalidArgumentException
     */
    public function generateMonthlyPayroll(int $companyId, string $month, int $userId): Payroll
    {
        if (! preg_match('/^\d{4}-\d{2}$/', $month)) {
            throw new InvalidArgumentException("Invalid month format [{$month}]. Expected format is YYYY-MM (e.g. 2026-10).");
        }

        return DB::transaction(function () use ($companyId, $month, $userId) {
            $existing = Payroll::withoutGlobalScopes()
                ->where('company_id', $companyId)
                ->where('month', $month)
                ->first();

            if ($existing) {
                if ($existing->isConfirmed() || $existing->isPaid()) {
                    throw new DomainException("Payroll for [{$month}] is already {$existing->status} and cannot be regenerated.");
                }

                // Delete previous draft items to regenerate fresh calculations
                $existing->items()->delete();
                $payroll = $existing;
                $payroll->update([
                    'processed_by' => $userId,
                ]);
            } else {
                $carbonMonth = Carbon::createFromFormat('Y-m', $month);
                $title = 'Payroll - '.$carbonMonth->format('F Y');

                $payroll = Payroll::create([
                    'company_id' => $companyId,
                    'month' => $month,
                    'title' => $title,
                    'status' => 'draft',
                    'processed_by' => $userId,
                ]);
            }

            // Fetch active employees
            $employees = Employee::withoutGlobalScopes()
                ->where('company_id', $companyId)
                ->where('is_active', true)
                ->get();

            foreach ($employees as $employee) {
                $basicSalary = round((float) $employee->basic_salary, 2);
                $fixedAllowance = round((float) $employee->fixed_allowance, 2);
                $additionalAllowance = 0.00;

                // Statutory calculations
                $epfEmployee = round($basicSalary * 0.08, 2);
                $epfEmployer = round($basicSalary * 0.12, 2);
                $etfEmployer = round($basicSalary * 0.03, 2);

                // Approved advances for this target deduction month
                $advanceDeduction = round((float) SalaryAdvance::withoutGlobalScopes()
                    ->where('company_id', $companyId)
                    ->where('employee_id', $employee->id)
                    ->where('status', 'approved')
                    ->where('deduct_month', $month)
                    ->sum('amount'), 2);

                $grossSalary = round($basicSalary + $fixedAllowance + $additionalAllowance, 2);
                $otherDeductions = 0.00;
                $totalDeductions = round($epfEmployee + $advanceDeduction + $otherDeductions, 2);
                $netSalary = round($grossSalary - $totalDeductions, 2);

                PayrollItem::create([
                    'company_id' => $companyId,
                    'payroll_id' => $payroll->id,
                    'employee_id' => $employee->id,
                    'basic_salary' => $basicSalary,
                    'fixed_allowance' => $fixedAllowance,
                    'additional_allowance' => $additionalAllowance,
                    'gross_salary' => $grossSalary,
                    'epf_employee' => $epfEmployee,
                    'epf_employer' => $epfEmployer,
                    'etf_employer' => $etfEmployer,
                    'advance_deduction' => $advanceDeduction,
                    'other_deductions' => $otherDeductions,
                    'total_deductions' => $totalDeductions,
                    'net_salary' => $netSalary,
                ]);
            }

            $this->recalculateTotals($payroll);

            return $payroll->fresh(['items.employee']);
        });
    }

    /**
     * Recalculate and persist aggregate totals for a payroll run.
     */
    public function recalculateTotals(Payroll $payroll): void
    {
        $items = $payroll->items()->get();

        $totalBasic = round($items->sum('basic_salary'), 2);
        $totalAllowances = round($items->sum(fn ($i) => (float) $i->fixed_allowance + (float) $i->additional_allowance), 2);
        $totalGross = round($items->sum('gross_salary'), 2);
        $totalEpfEmployee = round($items->sum('epf_employee'), 2);
        $totalEpfEmployer = round($items->sum('epf_employer'), 2);
        $totalEtfEmployer = round($items->sum('etf_employer'), 2);
        $totalAdvances = round($items->sum('advance_deduction'), 2);
        $totalDeductions = round($items->sum('total_deductions'), 2);
        $totalNetPayable = round($items->sum('net_salary'), 2);

        $payroll->updateQuietly([
            'total_basic' => $totalBasic,
            'total_allowances' => $totalAllowances,
            'total_gross' => $totalGross,
            'total_epf_employee' => $totalEpfEmployee,
            'total_epf_employer' => $totalEpfEmployer,
            'total_etf_employer' => $totalEtfEmployer,
            'total_advances' => $totalAdvances,
            'total_deductions' => $totalDeductions,
            'total_net_payable' => $totalNetPayable,
        ]);
    }

    /**
     * Confirm and finalize payroll run, transitioning linked salary advances to 'deducted'.
     *
     * @throws DomainException
     */
    public function confirmPayroll(Payroll $payroll, int $userId): void
    {
        if ($payroll->isConfirmed() || $payroll->isPaid()) {
            throw new DomainException('Payroll run has already been confirmed.');
        }

        DB::transaction(function () use ($payroll, $userId) {
            $employeeIds = $payroll->items()->pluck('employee_id');

            SalaryAdvance::withoutGlobalScopes()
                ->where('company_id', $payroll->company_id)
                ->where('status', 'approved')
                ->where('deduct_month', $payroll->month)
                ->whereIn('employee_id', $employeeIds)
                ->update(['status' => 'deducted']);

            $payroll->update([
                'status' => 'confirmed',
                'processed_by' => $userId,
            ]);

            $this->recalculateTotals($payroll);
        });
    }
}
