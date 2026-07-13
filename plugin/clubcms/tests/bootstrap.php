<?php

declare(strict_types=1);

if (! defined('ABSPATH')) {
    define('ABSPATH', __DIR__);
}

require_once __DIR__ . '/../src/Domain/FieldDefinition.php';
require_once __DIR__ . '/../src/Domain/Category.php';
require_once __DIR__ . '/../src/Domain/CardStatus.php';
require_once __DIR__ . '/../src/Domain/Visibility.php';
require_once __DIR__ . '/../src/Domain/Card.php';
require_once __DIR__ . '/../src/Support/WordPressStubs.php';
require_once __DIR__ . '/../src/Infrastructure/EditorSettingsStorageInterface.php';
require_once __DIR__ . '/../src/Infrastructure/EditorSettingsStorage.php';
require_once __DIR__ . '/../src/Repository/CategoryRepositoryInterface.php';
require_once __DIR__ . '/../src/Repository/FieldDefinitionRepositoryInterface.php';
require_once __DIR__ . '/../src/Repository/CardRepositoryInterface.php';
require_once __DIR__ . '/../src/Admin/SettingsSubmissionHandler.php';
require_once __DIR__ . '/../src/Admin/CardSubmissionHandler.php';
require_once __DIR__ . '/../src/Rendering/LandingPageRenderer.php';
require_once __DIR__ . '/../src/Security/AdminAccessGuard.php';
require_once __DIR__ . '/../src/Security/AdminBarGuard.php';
require_once __DIR__ . '/../src/Security/EditorAccessGuard.php';
require_once __DIR__ . '/../src/Repository/CardRepository.php';
require_once __DIR__ . '/../src/Frontend/LandingPageShortcode.php';
require_once __DIR__ . '/../src/Frontend/EditorShortcode.php';
require_once __DIR__ . '/../clubcms.php';
require_once __DIR__ . '/FieldDefinitionTest.php';
require_once __DIR__ . '/CategoryTest.php';
require_once __DIR__ . '/CardTest.php';
require_once __DIR__ . '/CardSubmissionHandlerTest.php';
require_once __DIR__ . '/AdminAccessGuardTest.php';
require_once __DIR__ . '/AdminBarGuardTest.php';
require_once __DIR__ . '/AdminBarCssTest.php';
require_once __DIR__ . '/DashboardTest.php';
require_once __DIR__ . '/EditorAccessGuardTest.php';
require_once __DIR__ . '/EditorShortcodeTest.php';
require_once __DIR__ . '/SettingsSubmissionHandlerTest.php';
require_once __DIR__ . '/LandingPageRendererTest.php';
require_once __DIR__ . '/LandingPageShortcodeTest.php';
require_once __DIR__ . '/LandingPageColumnShortcodeTest.php';
