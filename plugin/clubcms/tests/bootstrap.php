<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/Domain/FieldDefinition.php';
require_once __DIR__ . '/../src/Domain/Category.php';
require_once __DIR__ . '/../src/Domain/CardStatus.php';
require_once __DIR__ . '/../src/Domain/Visibility.php';
require_once __DIR__ . '/../src/Domain/Card.php';
require_once __DIR__ . '/../src/Support/WordPressStubs.php';
require_once __DIR__ . '/../src/Repository/CategoryRepositoryInterface.php';
require_once __DIR__ . '/../src/Repository/FieldDefinitionRepositoryInterface.php';
require_once __DIR__ . '/../src/Repository/CardRepositoryInterface.php';
require_once __DIR__ . '/../src/Admin/SettingsSubmissionHandler.php';
require_once __DIR__ . '/../src/Admin/CardSubmissionHandler.php';
require_once __DIR__ . '/../src/Rendering/LandingPageRenderer.php';
require_once __DIR__ . '/../src/Repository/CardRepository.php';
require_once __DIR__ . '/../src/Frontend/LandingPageShortcode.php';
require_once __DIR__ . '/FieldDefinitionTest.php';
require_once __DIR__ . '/CategoryTest.php';
require_once __DIR__ . '/CardTest.php';
require_once __DIR__ . '/CardSubmissionHandlerTest.php';
require_once __DIR__ . '/SettingsSubmissionHandlerTest.php';
require_once __DIR__ . '/LandingPageRendererTest.php';
require_once __DIR__ . '/LandingPageShortcodeTest.php';
require_once __DIR__ . '/LandingPageColumnShortcodeTest.php';
