<?php

namespace App\Service\Traits\Entity;

trait ShopHourTrait
{
    /**
     * choiceDay
     *
     * @return array
     */
    public function choiceDay(): array
    {
        return [
            'admin.field.monday' => 'M',
            'admin.field.tuesday' => 'TU',
            'admin.field.wednesday' => 'W',
            'admin.field.thursday' => 'TH',
            'admin.field.friday' => 'F',
            'admin.field.saturday' => 'S',
            'admin.field.sunday' => 'SU',
        ];
    }

    /**
     * getShopHourFormattedValues
     *
     * @return array
     */
    public function getShopHourFormattedValues(): array
    {
        $shop_hours = [];
        $days = ['M', 'TU', 'W', 'TH', 'F', 'S', 'SU'];

        foreach ($days as $day) {
            $add = [
                'active' => false,
                'break' => false,
                'day' => $day,
                'startTime' => "00:00:00",
                'endTime' => "00:00:00",
                'startBreakTime' => "00:00:00",
                'endBreakTime' => "00:00:00",
            ];
            array_push($shop_hours, $add);
        }

        return $shop_hours;
    }
}
