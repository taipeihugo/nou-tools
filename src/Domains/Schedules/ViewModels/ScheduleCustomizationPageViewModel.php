<?php

namespace NouTools\Domains\Schedules\ViewModels;

use App\Models\StudentSchedule;

final readonly class ScheduleCustomizationPageViewModel
{
    /**
     * @param  array<string, bool>  $displayOptions
     * @param  array<int, array{title: string, url: string}>  $customLinks
     */
    public function __construct(
        public StudentSchedule $schedule,
        public array $displayOptions,
        public array $customLinks,
    ) {}

    /**
     * @return array<string, bool>
     */
    public static function defaultDisplayOptions(): array
    {
        return [
            'show_greeting' => true,
            'show_schedule_items' => true,
            'show_common_links' => true,
            'show_class_dates' => true,
            'show_school_calendar' => true,
            'show_exam_info' => true,
            'show_share_section' => true,
            'show_print_button' => true,
        ];
    }

    /**
     * @param  array<string, bool|int|string|null>|null  $displayOptions
     * @return array<string, bool>
     */
    public static function normalizeDisplayOptions(?array $displayOptions): array
    {
        $defaults = self::defaultDisplayOptions();

        if (! is_array($displayOptions)) {
            return $defaults;
        }

        foreach ($defaults as $key => $defaultValue) {
            $rawValue = $displayOptions[$key] ?? $defaultValue;
            $defaults[$key] = filter_var($rawValue, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? $defaultValue;
        }

        return $defaults;
    }

    /**
     * @param  array<int, array{title?: string|null, url?: string|null}>|null  $customLinks
     * @return array<int, array{title: string, url: string}>
     */
    public static function normalizeCustomLinks(?array $customLinks): array
    {
        if (! is_array($customLinks)) {
            return [];
        }

        return collect($customLinks)
            ->filter(fn ($link) => is_array($link))
            ->map(function (array $link): array {
                return [
                    'title' => trim((string) ($link['title'] ?? '')),
                    'url' => trim((string) ($link['url'] ?? '')),
                ];
            })
            ->filter(fn (array $link): bool => $link['title'] !== '' && $link['url'] !== '')
            ->take(20)
            ->values()
            ->all();
    }
}
