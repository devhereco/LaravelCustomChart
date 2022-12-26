<?php

namespace Devhereco\CustomChart;

use Carbon\Carbon;
use Carbon\CarbonPeriod;

class CustomChart
{
    /*
     * $model => must be like => 'App\Models\Blaster\Store\Transaction',
     * $aggregate_function => 'in:count,sum,avg|bail',
     * $aggregate_field => model field name,
     * $filter_days => integer
     */
    public static function create(
        $model, 
        $aggregate_function, 
        $aggregate_field, 
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

        CarbonPeriod::since(now()->subDays($filter_days))
            ->until(now())
            ->forEach(function (Carbon $date) use ($data, &$newData, $date_format) {
                $key = $date->format($date_format);
                $newData->put($key, $data[$key] ?? 0);
            });

        $data = [
            'name' => 'Transactions by Days',
            'data' => $newData,
        ];

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
