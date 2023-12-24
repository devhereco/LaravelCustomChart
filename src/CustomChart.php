<?php

namespace Devhereco\CustomChart;

use Carbon\Carbon;
use Carbon\CarbonPeriod;

class CustomChart
{
    /*
     * $model => must be like => 'App\Models\Blaster\Store\Transaction',
     * $title => Chart title
     * $aggregate_function => 'in:count,sum,avg|bail',
     * $aggregate_field => model field name,
     * $filter_days => integer
     */
    public static function create(
        $model,
        $title = Null,
        $aggregate_function = 'count',
        $aggregate_field = 'id',
        $filter_days = 30,
        $show_total = False,
        $date_format = 'Y-m-d'
    ) {
        $collection = $model::get();

        if (count($collection)) {
            $data = $collection
                ->sortBy('created_at')
                ->groupBy(function ($entry) use ($date_format) {
                    if ($entry->created_at instanceof Carbon) {
                        return $entry->created_at
                            ->format($date_format);
                    } else {

                        if ($entry->created_at) {
                            return Carbon::createFromFormat(
                                'Y-m-d H:i:s',
                                $entry->created_at
                            )->format($date_format);
                        } else {
                            return '';
                        }
                    }
                })
                ->map(function ($entries) use ($aggregate_function, $aggregate_field) {
                    return $entries->{$aggregate_function}($aggregate_field);
                });
        } else {
            $data = collect([]);
        }

        $newData = collect([]);
        $today = Carbon::now();

        // This Month
        $thisMonthData = $data->filter(function ($value, $key) use ($today, $date_format) {
            return Carbon::createFromFormat($date_format, $key)->format('Y-m') === $today->format('Y-m');
        });

        // Last Month
        $lastMonthData = $data->filter(function ($value, $key) use ($today, $date_format) {
            $lastMonth = Carbon::now()->subMonth();
            return Carbon::createFromFormat($date_format, $key)->format('Y-m') === $lastMonth->format('Y-m');
        });

        // This Year
        $thisYearData = $data->filter(function ($value, $key) use ($today, $date_format) {
            return Carbon::createFromFormat($date_format, $key)->format('Y') === $today->format('Y');
        });

        // Last Year
        $lastYearData = $data->filter(function ($value, $key) use ($today, $date_format) {
            $lastYear = Carbon::now()->subYear();
            return Carbon::createFromFormat($date_format, $key)->format('Y') === $lastYear->format('Y');
        });

        // Calculate percentage change between last month and the month before it
        $monthBeforeLastMonth = Carbon::now()->subMonths(2);
        $lastMonthBeforeData = $data->filter(function ($value, $key) use ($monthBeforeLastMonth, $date_format) {
            return Carbon::createFromFormat($date_format, $key)->format('Y-m') === $monthBeforeLastMonth->format('Y-m');
        });

        CarbonPeriod::since(now()->subDays($filter_days))
            ->until(now())
            ->forEach(function (Carbon $date) use ($data, &$newData, $date_format) {
                $key = $date->format($date_format);
                $newData->put($key, $data[$key] ?? 0);
            });

        $data = [
            'name' => $title ?? Null,
            'data' => $newData,
            'this_month' => $thisMonthData->$aggregate_function(),
            'last_month' => $lastMonthData->$aggregate_function(),
            'this_year' => $thisYearData->$aggregate_function(),
            'last_year' => $lastYearData->$aggregate_function(),
        ];

        // Calculate percentage change between this month and last month
        $thisMonthPercentageChange = $data['last_month'] !== 0 ? (($data['this_month'] - $data['last_month']) / $data['last_month']) * 100 : 0;

        // Calculate percentage change between last month and the month before it
        $lastMonthPercentageChange = $lastMonthBeforeData->sum() !== 0 ? (($data['last_month'] - $lastMonthBeforeData->sum()) / $lastMonthBeforeData->sum()) * 100 : 0;

        // Calculate percentage change between this year and last year
        $thisYearPercentageChange = $data['last_year'] !== 0 ? (($data['this_year'] - $data['last_year']) / $data['last_year']) * 100 : 0;

        $percentageData['percentage_change'] = [
            'this_month' => [
                'value' => round($thisMonthPercentageChange, 2) . '%',
                'status' => $thisMonthPercentageChange > 0 ? 'positive' : ($thisMonthPercentageChange < 0 ? 'negative' : 'no change')
            ],
            'last_month' => [
                'value' => round($lastMonthPercentageChange, 2),
                'status' => $lastMonthPercentageChange > 0 ? 'positive' : ($lastMonthPercentageChange < 0 ? 'negative' : 'no change')
            ],
            'this_year' => [
                'value' => round($thisYearPercentageChange, 2) . '%',
                'status' => $thisYearPercentageChange > 0 ? 'positive' : ($thisYearPercentageChange < 0 ? 'negative' : 'no change')
            ]
        ];

        $data = array_merge($data, $percentageData);

        if ($show_total) {
            $total = 0;
            foreach ($data['data'] as $row) {
                $total += $row;
            }
            $data = array_merge($data, array('total' => $total));
        }

        return $data;
    }
}
