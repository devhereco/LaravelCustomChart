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
     * $filter_count => integer
     * $filter_interval => 'days', 'weeks', 'months', 'years'
     */
    public static function create(
        $model,
        $title = null,
        $aggregate_function = 'count',
        $aggregate_field = 'id',
        $filter_count = 30,
        $filter_interval = 'days',
        $show_total = false,
        $date_format = 'Y-m-d'
    ) {
        $collection = $model::get();

        if (count($collection)) {
            $data = $collection
                ->sortBy('created_at')
                ->groupBy(function ($entry) use ($date_format, $filter_interval) {
                    if ($entry->created_at instanceof Carbon) {
                        return self::formatDate($entry->created_at, $filter_interval, $date_format);
                    } else {
                        if ($entry->created_at) {
                            return self::formatDate(
                                Carbon::createFromFormat('Y-m-d H:i:s', $entry->created_at),
                                $filter_interval,
                                $date_format
                            );
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
        $startDate = self::calculateStartDate($filter_count, $filter_interval);

        CarbonPeriod::since($startDate)
            ->until(now())
            ->forEach(function (Carbon $date) use ($data, &$newData, $filter_interval, $date_format) {
                $key = self::formatDate($date, $filter_interval, $date_format);
                $newData->put($key, $data[$key] ?? 0);
            });

        $data = [
            'name' => $title ?? null,
            'data' => $newData,
            'this_month' => $newData->filter(function ($value, $key) use ($startDate, $date_format) {
                return Carbon::createFromFormat($date_format, $key)->format('Y-m') === now()->format('Y-m');
            })->sum(),
            'last_month' => $newData->filter(function ($value, $key) use ($startDate, $date_format) {
                $lastMonth = now()->subMonth();
                return Carbon::createFromFormat($date_format, $key)->format('Y-m') === $lastMonth->format('Y-m');
            })->sum(),
            'this_year' => $newData->filter(function ($value, $key) use ($startDate, $date_format) {
                return Carbon::createFromFormat($date_format, $key)->format('Y') === now()->format('Y');
            })->sum(),
            'last_year' => $newData->filter(function ($value, $key) use ($startDate, $date_format) {
                $lastYear = now()->subYear();
                return Carbon::createFromFormat($date_format, $key)->format('Y') === $lastYear->format('Y');
            })->sum(),
        ];

        // Calculate percentage changes
        $data['percentage_change'] = [
            'this_month' => self::calculatePercentageChange($data['this_month'], $data['last_month']),
            'last_month' => self::calculatePercentageChange($data['last_month'], $newData->sum() / count($newData)),
            'this_year'  => self::calculatePercentageChange($data['this_year'], $data['last_year']),
        ];

        if ($show_total) {
            $data['total'] = $newData->sum();
        }

        return $data;
    }

    private static function formatDate(Carbon $date, string $interval, string $date_format): string
    {
        switch ($interval) {
            case 'weeks':
                return $date->startOfWeek()->format($date_format);
            case 'months':
                return $date->startOfMonth()->format($date_format);
            case 'years':
                return $date->startOfYear()->format($date_format);
            case 'days':
            default:
                return $date->format($date_format);
        }
    }

    private static function calculateStartDate(int $count, string $interval): Carbon
    {
        switch ($interval) {
            case 'weeks':
                return now()->subWeeks($count);
            case 'months':
                return now()->subMonths($count);
            case 'years':
                return now()->subYears($count);
            case 'days':
            default:
                return now()->subDays($count);
        }
    }

    private static function calculatePercentageChange($current, $previous)
    {
        if ($previous == 0) {
            return [
                'value' => '0%',
                'status' => 'no change'
            ];
        }

        $change = (($current - $previous) / $previous) * 100;
        return [
            'value' => round($change, 2) . '%',
            'status' => $change > 0 ? 'positive' : ($change < 0 ? 'negative' : 'no change')
        ];
    }
}
