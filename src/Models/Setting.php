<?php

namespace TelegramNotificationBot\App\Models;

class Setting
{
    public const SETTING_FILE = __DIR__ . '/../../storage/json/tg-setting.json';

    public const SETTING_PREFIX = 'stg.';

    public const SETTING_IS_NOTIFIED = self::SETTING_PREFIX . 'is_notified';

    public const SETTING_ALL_EVENTS_NOTIFY = self::SETTING_PREFIX . 'all_events_notify';

    public const SETTING_CUSTOM_EVENTS = self::SETTING_PREFIX . 'cus';

    public const SETTING_GITHUB_EVENTS = self::SETTING_CUSTOM_EVENTS . Event::GITHUB_EVENT_SEPARATOR;

    public const SETTING_GITLAB_EVENTS = self::SETTING_CUSTOM_EVENTS . Event::GITLAB_EVENT_SEPARATOR;

    public const SETTING_BACK = self::SETTING_PREFIX . 'back.';

    public array $settings = [];

    public function __construct()
    {
        if (file_exists(self::SETTING_FILE)) {
            $this->setSettingConfig();
        }
    }

    /**
     * Set settings
     *
     * @return void
     */
    private function setSettingConfig(): void
    {
        $json = file_get_contents(self::SETTING_FILE);
        $this->settings = json_decode($json, true);
    }

    /**
     * @return bool
     */
    public function allEventsNotifyStatus(): bool
    {
        if (!empty($this->settings) && $this->settings['all_events_notify'] === true) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isNotified(): bool
    {
        if (!empty($this->settings) && $this->settings['is_notified'] === true) {
            return true;
        }

        return false;
    }

    /**
     * Update setting item value and save to file
     *
     * @param string $settingName
     * @param $settingValue
     * @return bool
     */
    public function updateSettingItem(string $settingName, $settingValue = null): bool
    {
        $settingKeys = explode('.', $settingName);
        $lastKey = array_pop($settingKeys);
        $nestedSettings = &$this->settings;

        foreach ($settingKeys as $key) {
            if (!isset($nestedSettings[$key]) || !is_array($nestedSettings[$key])) {
                return false;
            }
            $nestedSettings = &$nestedSettings[$key];
        }

        if (isset($nestedSettings[$lastKey])) {
            $newValue = $settingValue ?? !$nestedSettings[$lastKey]; // if value is null, then toggle value
            $nestedSettings[$lastKey] = $newValue;

            return $this->saveSettingsToFile();
        }

        return false;
    }

    /**
     * Save settings to json file
     *
     * @return bool
     */
    private function saveSettingsToFile(): bool
    {
        if (file_exists(self::SETTING_FILE)) {
            $json = json_encode($this->settings, JSON_PRETTY_PRINT);
            file_put_contents(self::SETTING_FILE, $json, LOCK_EX);

            return true;
        }

        return false;
    }
}
