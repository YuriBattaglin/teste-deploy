<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Models\Traffic;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ManagerPanelController extends Controller
{
    public function index(Request $request)
    {
        [$dateFrom, $dateTo] = $this->resolvePeriod($request);
        $partnerIds = $this->normalizePartnerIds($request->query('partner_ids', []));
        $groupByWeek = $this->booleanValue($request->query('group_by_week', true));
        $includeComparison = $this->booleanValue($request->query('include_comparison', true));

        $currentTraffic = $this->loadTraffic($dateFrom, $dateTo, $partnerIds);
        $summary = $this->buildSummaryMetrics($currentTraffic, $request);
        $secondary = $this->buildSecondaryMetrics($currentTraffic);

        if ($includeComparison) {
            $comparisonPeriodLength = $dateFrom->diffInDays($dateTo) + 1;
            $comparisonEnd = $dateFrom->copy()->subDay()->endOfDay();
            $comparisonStart = $comparisonEnd->copy()->subDays($comparisonPeriodLength - 1)->startOfDay();

            $comparisonTraffic = $this->loadTraffic($comparisonStart, $comparisonEnd, $partnerIds);
            $comparisonSummary = $this->buildSummaryMetrics($comparisonTraffic, $request, true);
            $comparisonSecondary = $this->buildSecondaryMetrics($comparisonTraffic);

            $summary['deltas'] = $this->calculateSummaryDeltas($summary, $comparisonSummary);
            $secondary['deltas'] = $this->calculateSecondaryDeltas($secondary, $comparisonSecondary);
        } else {
            $summary['deltas'] = $this->emptySummaryDeltas();
            $secondary['deltas'] = $this->emptySecondaryDeltas();
        }

        $table = $this->buildPartnerTable($currentTraffic, $groupByWeek);

        return response()->json([
            'summary' => $summary,
            'table' => $table,
            'secondary_metrics' => $secondary,
        ]);
    }

    protected function resolvePeriod(Request $request): array
    {
        $monthPeriod = $this->resolveMonthYearPeriod($request);
        if ($monthPeriod !== null) {
            return $monthPeriod;
        }

        $defaultEnd = Carbon::today()->endOfDay();
        $defaultStart = $defaultEnd->copy()->subMonth()->addDay()->startOfDay();

        $dateTo = $this->parseDate($request->query('date_to'), $defaultEnd)->endOfDay();
        $dateFrom = $this->parseDate($request->query('date_from'), $defaultStart)->startOfDay();

        if ($dateFrom->gt($dateTo)) {
            [$dateFrom, $dateTo] = [$dateTo->copy()->startOfDay(), $dateFrom->copy()->endOfDay()];
        }

        return [$dateFrom, $dateTo];
    }

    protected function resolveMonthYearPeriod(Request $request): ?array
    {
        $monthYear = $request->query('month_year');

        if (!empty($monthYear)) {
            $startOfMonth = $this->parseMonthYear($monthYear);
            if ($startOfMonth !== null) {
                $start = $startOfMonth->copy()->startOfDay();
                $end = $startOfMonth->copy()->endOfMonth()->endOfDay();
                return [$start, $end];
            }
        }

        $month = $request->query('month');
        $year = $request->query('year');

        if ($month !== null && $year !== null) {
            $month = (int) $month;
            $year = (int) $year;

            if ($month >= 1 && $month <= 12 && $year > 0) {
                try {
                    $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfDay();
                    $endOfMonth = $startOfMonth->copy()->endOfMonth()->endOfDay();
                    return [$startOfMonth, $endOfMonth];
                } catch (\Throwable $exception) {
                }
            }
        }

        return null;
    }

    protected function parseDate($value, Carbon $default): Carbon
    {
        if (empty($value)) {
            return $default->copy();
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable $exception) {
            return $default->copy();
        }
    }

    protected function parseMonthYear($value): ?Carbon
    {
        if (empty($value)) {
            return null;
        }

        $value = trim((string) $value);
        $formats = [
            'Y-m', 'Y/m', 'Y m', 'Ym',
            'm-Y', 'm/Y', 'm Y',
            'n-Y', 'n/Y', 'n Y',
            'F Y', 'Y F',
        ];

        foreach ($formats as $format) {
            try {
                $date = Carbon::createFromFormat($format, $value);
                if ($date !== false) {
                    return $date->startOfMonth();
                }
            } catch (\Throwable $exception) {
            }
        }

        try {
            return Carbon::parse($value)->startOfMonth();
        } catch (\Throwable $exception) {
            return null;
        }
    }

    protected function normalizePartnerIds($partnerIds): array
    {
        if (is_string($partnerIds)) {
            $partnerIds = array_filter(explode(',', $partnerIds));
        }

        if (!is_array($partnerIds)) {
            return [];
        }

        return array_values(array_unique(array_filter(array_map('intval', $partnerIds))));
    }

    protected function booleanValue($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            $normalized = strtolower($value);
            if (in_array($normalized, ['1', 'true', 'yes', 'on'], true)) {
                return true;
            }
            if (in_array($normalized, ['0', 'false', 'no', 'off'], true)) {
                return false;
            }
        }

        return (bool) $value;
    }

    protected function loadTraffic(Carbon $dateFrom, Carbon $dateTo, array $partnerIds): Collection
    {
        $query = Traffic::query()->with('partner');

        if (!empty($partnerIds)) {
            $query->whereIn('partner_id', $partnerIds);
        }

        $start = $dateFrom->copy()->startOfDay();
        $end   = $dateTo->copy()->endOfDay();

        $query->where(function ($q) use ($start, $end) {
            $q->where(function ($qq) use ($start, $end) {
                $qq->whereNotNull('start_date')
                   ->whereNotNull('end_date')
                   ->where('start_date', '<=', $end)  
                   ->where('end_date', '>=', $start);  
            })
            ->orWhere(function ($qq) use ($end) {
                $qq->whereNotNull('start_date')
                   ->whereNull('end_date')
                   ->where('start_date', '<=', $end);
            })
            ->orWhere(function ($qq) use ($start, $end) {
                $qq->whereNull('start_date')
                   ->whereBetween('created_at', [$start, $end]);
            });
        });

        return $query->get();
    }

    protected function buildSummaryMetrics(Collection $traffic, Request $request, bool $isComparison = false): array
    {
        $totalInvestment = (float) $traffic->sum(function ($row) {
            return (float) ($row->ad_investment ?? 0);
        });

        $totalLeads = (int) $traffic->sum(function ($row) {
            return (int) ($row->leads ?? 0);
        });

        $avgCpl = $totalLeads > 0 ? $totalInvestment / $totalLeads : null;
        $roas = $this->resolveRoas($traffic, $totalInvestment, $request, $isComparison);

        return [
            'total_investment' => $this->roundValue($totalInvestment),
            'total_leads' => $totalLeads,
            'avg_cpl' => $avgCpl !== null ? $this->roundValue($avgCpl) : null,
            'roas' => $roas !== null ? $this->roundValue($roas, 2) : null,
        ];
    }

    protected function buildSecondaryMetrics(Collection $traffic): array
    {
        $totalImpressions = (int) $traffic->sum(function ($row) {
            return (int) ($row->impressions ?? 0);
        });

        $totalClicks = (int) $traffic->sum(function ($row) {
            return (int) ($row->clicks ?? 0);
        });

        $totalInvestment = (float) $traffic->sum(function ($row) {
            return (float) ($row->ad_investment ?? 0);
        });

        $cpm = $totalImpressions > 0 ? ($totalInvestment / $totalImpressions) * 1000 : null;
        $ctr = $totalImpressions > 0 ? ($totalClicks / $totalImpressions) * 100 : null;
        $cpc = $totalClicks > 0 ? $totalInvestment / $totalClicks : null;
        $clicksToLeadsRatio = $traffic->sum(function ($row) {
            return (int) ($row->leads ?? 0);
        });
        $clicksToLeadsRatio = $clicksToLeadsRatio > 0 ? $totalClicks / $clicksToLeadsRatio : null;

        return [
            'impressions_total' => $totalImpressions,
            'cpm' => $cpm !== null ? $this->roundValue($cpm) : null,
            'ctr' => $ctr !== null ? $this->roundValue($ctr, 2) : null,
            'cpc' => $cpc !== null ? $this->roundValue($cpc) : null,
            'clicks_to_leads_ratio' => $clicksToLeadsRatio !== null ? $this->roundValue($clicksToLeadsRatio, 2) : null,
        ];
    }

    protected function resolveRoas(Collection $traffic, float $totalInvestment, Request $request, bool $isComparison = false): ?float
    {
        $explicitRoas = $isComparison
            ? $request->query('comparison_roas')
            : $request->query('roas');

        if ($explicitRoas !== null && $explicitRoas !== '') {
            return (float) $explicitRoas;
        }

        $revenueParam = null;

        if ($isComparison) {
            $revenueParam = $request->query('comparison_total_revenue', $request->query('comparison_revenue', $request->query('total_revenue_previous')));
        } else {
            $revenueParam = $request->query('total_revenue', $request->query('revenue'));
        }

        if ($revenueParam !== null && $revenueParam !== '' && $totalInvestment > 0) {
            return (float) $revenueParam / $totalInvestment;
        }

        $roasValues = $traffic
            ->map(function ($row) {
                return isset($row->roas) ? (float) $row->roas : null;
            })
            ->filter(function ($v) {
                return $v !== null; 
            });

        if ($roasValues->isNotEmpty()) {
            return $roasValues->avg();
        }

        return null;
    }

    protected function calculateSummaryDeltas(array $current, array $previous): array
    {
        return [
            'total_investment_pct' => $this->calculateDelta($current['total_investment'], $previous['total_investment']),
            'total_leads_pct' => $this->calculateDelta($current['total_leads'], $previous['total_leads']),
            'avg_cpl_pct' => $this->calculateDelta($current['avg_cpl'], $previous['avg_cpl']),
            'roas_pct' => $this->calculateDelta($current['roas'], $previous['roas']),
        ];
    }

    protected function calculateSecondaryDeltas(array $current, array $previous): array
    {
        return [
            'impressions_total_pct' => $this->calculateDelta($current['impressions_total'], $previous['impressions_total']),
            'cpm_pct' => $this->calculateDelta($current['cpm'], $previous['cpm']),
            'ctr_pct' => $this->calculateDelta($current['ctr'], $previous['ctr']),
            'cpc_pct' => $this->calculateDelta($current['cpc'], $previous['cpc']),
            'clicks_to_leads_ratio_pct' => $this->calculateDelta($current['clicks_to_leads_ratio'], $previous['clicks_to_leads_ratio']),
        ];
    }

    protected function calculateDelta($current, $previous): ?float
    {
        if ($previous === null || (is_numeric($previous) && (float) $previous === 0.0)) {
            return null;
        }

        if ($current === null) {
            return null;
        }

        $previousValue = (float) $previous;
        $currentValue = (float) $current;

        if ($previousValue == 0.0) {
            return null;
        }

        return $this->roundValue((($currentValue - $previousValue) / abs($previousValue)) * 100, 1);
    }

    protected function emptySummaryDeltas(): array
    {
        return [
            'total_investment_pct' => null,
            'total_leads_pct' => null,
            'avg_cpl_pct' => null,
            'roas_pct' => null,
        ];
    }

    protected function emptySecondaryDeltas(): array
    {
        return [
            'impressions_total_pct' => null,
            'cpm_pct' => null,
            'ctr_pct' => null,
            'cpc_pct' => null,
            'clicks_to_leads_ratio_pct' => null,
        ];
    }

    protected function buildPartnerTable(Collection $traffic, bool $groupByWeek): array
    {
        if ($traffic->isEmpty()) {
            return [];
        }

        $partnerIds = $traffic->pluck('partner_id')->filter()->unique()->all();
        $partners = Partner::whereIn('id', $partnerIds)->get()->keyBy('id');

        return $traffic->groupBy('partner_id')->map(function (Collection $items, $partnerId) use ($partners, $groupByWeek) {
            $partner = $partners->get((int) $partnerId);
            $partnerName = $partner->description ?? 'Unknown Partner';

            $weeks = $this->buildTimeBuckets($items, $groupByWeek);
            $totalInvestment = (float) $items->sum(function ($row) {
                return (float) ($row->ad_investment ?? 0);
            });
            $totalLeads = (int) $items->sum(function ($row) {
                return (int) ($row->leads ?? 0);
            });

            return [
                'partner_id' => (int) $partnerId,
                'partner_name' => $partnerName,
                'weeks' => $weeks,
                'month_cpl' => $totalLeads > 0 ? $this->roundValue($totalInvestment / $totalLeads) : null,
            ];
        })->values()->all();
    }

    protected function buildTimeBuckets(Collection $items, bool $groupByWeek): array
    {
        $buckets = $items->groupBy(function ($row) use ($groupByWeek) {
            $date = $this->resolveEntryDate($row);
            if (!$date) {
                return null;
            }

            $carbonDate = Carbon::parse($date);

            return $groupByWeek
                ? $carbonDate->copy()->startOfWeek(Carbon::MONDAY)->toDateString()
                : $carbonDate->copy()->startOfMonth()->toDateString();
        })->filter(function ($_, $key) {
            return $key !== null;
        });

        $weeks = $buckets->map(function (Collection $rows, $key) {
            $totalLeads = (int) $rows->sum(function ($row) {
                return (int) ($row->leads ?? 0);
            });
            $totalInvestment = (float) $rows->sum(function ($row) {
                return (float) ($row->ad_investment ?? 0);
            });

            return [
                'week' => $key,
                'leads' => $totalLeads > 0 ? $totalLeads : null,
                'cpl' => $totalLeads > 0 ? $this->roundValue($totalInvestment / $totalLeads) : null,
            ];
        })->sortBy('week')->values()->all();

        return $weeks;
    }

    protected function resolveEntryDate($row): ?string
    {
        if (!empty($row->start_date)) {
            return $row->start_date;
        }

        if (!empty($row->end_date)) {
            return $row->end_date;
        }

        if (!empty($row->created_at)) {
            return Carbon::parse($row->created_at)->toDateString();
        }

        return null;
    }

    protected function roundValue(float $value, int $precision = 2): float
    {
        return round($value, $precision);
    }
}
