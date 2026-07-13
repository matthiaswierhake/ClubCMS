<?php

declare(strict_types=1);

namespace ClubCMS;

use ClubCMS\Admin\Dashboard;
use ClubCMS\Admin\SettingsPage;
use ClubCMS\Infrastructure\OptionStorage;
use ClubCMS\Repository\CategoryRepository;
use ClubCMS\Repository\FieldDefinitionRepository;

final class Plugin
{
    private Dashboard $dashboard;

    private SettingsPage $settingsPage;

    public function register(): void
    {
        $storage = new OptionStorage();
        $categoryRepository = new CategoryRepository($storage);
        $fieldDefinitionRepository = new FieldDefinitionRepository($storage);

        $this->dashboard = new Dashboard($categoryRepository, $fieldDefinitionRepository);
        $this->settingsPage = new SettingsPage($categoryRepository, $fieldDefinitionRepository);

        add_action('init', [$this, 'registerTextDomain']);
        add_action('admin_menu', [$this, 'registerAdminMenu']);
    }

    public function registerTextDomain(): void
    {
        load_plugin_textdomain('clubcms', false, dirname(plugin_basename(CLUBCMS_FILE)) . '/languages');
    }

    public function registerAdminMenu(): void
    {
        add_menu_page(
            'ClubCMS',
            'ClubCMS',
            'manage_options',
            'clubcms',
            [$this->dashboard, 'render'],
            'dashicons-screenoptions',
            26
        );

        add_submenu_page(
            'clubcms',
            'Kategorien',
            'Kategorien',
            'manage_options',
            'clubcms-categories',
            [$this->settingsPage, 'renderCategories']
        );

        add_submenu_page(
            'clubcms',
            'Felddefinitionen',
            'Felddefinitionen',
            'manage_options',
            'clubcms-field-definitions',
            [$this->settingsPage, 'renderFieldDefinitions']
        );
    }
}
