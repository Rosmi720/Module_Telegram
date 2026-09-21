<?php

namespace XcVm\Module\Telegram;

use XcVm\Core\Localization\Translator;

/**
 * Telegram Module Dedicated Translator.
 *
 * Provides completely self-contained localization for the Telegram Bot Module.
 * Automatically loads translations from the module's own bundled lang/ directory
 * (ar.ini, en.ini), registers them with the core Translator, and falls back to
 * bundled module strings if any key is missing or unresolved in the core system.
 *
 * @package XC_VM_Module_Telegram
 */
class TelegramTranslator
{
    private static ?array $loadedTranslations = null;
    private static string $loadedLang = '';

    /**
     * Get translated string by key.
     *
     * @param string $key
     * @param array<string, string> $replace
     * @return string
     */
    public static function get(string $key, array $replace = []): string
    {
        $currentLang = 'en';
        if (class_exists(Translator::class)) {
            $currentLang = Translator::current();
        } elseif (!empty($_COOKIE['lang'])) {
            $currentLang = (string)$_COOKIE['lang'];
        }

        self::ensureLoaded($currentLang);

        // 1. Check module's bundled translation for the current language
        $text = self::$loadedTranslations[$key] ?? null;

        // 2. If not found in module dictionary, check core system Translator
        if ($text === null) {
            if (class_exists(Translator::class)) {
                $val = Translator::get($key, $replace);
                if ($val !== '' && $val !== $key && $val !== '"' . $key . '"') {
                    return $val;
                }
            }
            // 3. Fallback to module's bundled English dictionary
            $en = self::loadIni('en');
            $text = $en[$key] ?? $key;
        }

        return $replace !== [] ? strtr($text, $replace) : $text;
    }

    /**
     * Ensure translations for the given language are loaded from the module's lang/ folder.
     */
    public static function ensureLoaded(string $lang): void
    {
        if (self::$loadedTranslations !== null && self::$loadedLang === $lang) {
            return;
        }

        self::$loadedLang = $lang;
        $data = self::loadIni($lang);

        if (empty($data) && $lang !== 'en') {
            $data = self::loadIni('en');
        }

        self::$loadedTranslations = $data;
    }

    /**
     * Load an ini file from the module's lang/ directory.
     *
     * @param string $lang
     * @return array<string, string>
     */
    public static function loadIni(string $lang): array
    {
        $file = __DIR__ . '/lang/' . $lang . '.ini';
        if (is_file($file) && is_readable($file)) {
            $parsed = parse_ini_file($file, false, INI_SCANNER_RAW);
            if (is_array($parsed)) {
                return $parsed;
            }
        }
        return [];
    }

    /**
     * Proxy static calls (such as current(), isRtl()) to the core Translator.
     */
    public static function __callStatic(string $name, array $arguments)
    {
        if (class_exists(Translator::class) && method_exists(Translator::class, $name)) {
            return forward_static_call_array([Translator::class, $name], $arguments);
        }
        return null;
    }
}
