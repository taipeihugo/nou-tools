<?php

namespace NouTools\Domains\Schedules\Actions;

use App\Models\StudentSchedule;
use NouTools\Domains\Schedules\ViewModels\ScheduleCustomizationPageViewModel;

final class BuildScheduleCustomizationPage
{
    public function __invoke(StudentSchedule $schedule): ScheduleCustomizationPageViewModel
    {
        return new ScheduleCustomizationPageViewModel(
            schedule: $schedule,
            displayOptions: ScheduleCustomizationPageViewModel::normalizeDisplayOptions($schedule->display_options),
            customLinks: ScheduleCustomizationPageViewModel::normalizeCustomLinks($schedule->custom_links),
        );
    }
}
