<?php

namespace NouTools\Domains\Schedules\Actions;

use App\Models\StudentSchedule;
use Illuminate\Support\Facades\DB;
use NouTools\Domains\Schedules\DataTransferObjects\ScheduleCustomizationUpsertData;
use NouTools\Domains\Schedules\ViewModels\ScheduleCustomizationPageViewModel;

final class UpdateScheduleCustomization
{
    public function __invoke(StudentSchedule $schedule, ScheduleCustomizationUpsertData $input): StudentSchedule
    {
        return DB::transaction(function () use ($schedule, $input) {
            $schedule->display_options = ScheduleCustomizationPageViewModel::normalizeDisplayOptions($input->displayOptions);
            $schedule->custom_links = ScheduleCustomizationPageViewModel::normalizeCustomLinks($input->customLinks);
            $schedule->saveOrFail();

            return $schedule;
        });
    }
}
