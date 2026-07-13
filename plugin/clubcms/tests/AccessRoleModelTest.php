<?php

declare(strict_types=1);

namespace ClubCMS\Tests;

use ClubCMS\Security\AccessRoleModel;
use ClubCMS\Domain\Visibility;
use RuntimeException;

final class AccessRoleModelTest
{
    public function run(): void
    {
        $this->itDistinguishesAdminsEditorsAndLoggedInUsers();
    }

    private function itDistinguishesAdminsEditorsAndLoggedInUsers(): void
    {
        $model = new AccessRoleModel(
            static function (string $capability) {
                return $capability === 'manage_options';
            },
            static fn (): bool => true
        );

        $this->assertTrue($model->isAdmin(), 'Admins should be detected.');
        $this->assertFalse($model->isEditor(), 'Admin-only access should not be treated as editor access.');
        $this->assertTrue($model->isLoggedIn(), 'Logged-in state should be detected.');
        $this->assertTrue($model->canAccessEditor(), 'Admins should access the editor.');
        $this->assertTrue($model->canSeeFrontendControls(), 'Admins should see frontend controls.');

        $editorModel = new AccessRoleModel(
            static function (string $capability) {
                return $capability === 'edit_posts';
            },
            static fn (): bool => false
        );

        $this->assertFalse($editorModel->isAdmin(), 'Editor-only access should not be treated as admin access.');
        $this->assertTrue($editorModel->isEditor(), 'Editors should be detected.');
        $this->assertTrue($editorModel->canAccessEditor(), 'Editors should access the editor.');
        $this->assertTrue($editorModel->canSeeFrontendControls(), 'Editors should see frontend controls.');
        $this->assertTrue($editorModel->canSeeVisibility(Visibility::Public), 'Visitors can always see public cards.');
        $this->assertFalse($editorModel->canSeeVisibility(Visibility::Members), 'Visitors should not see members-only cards.');
        $this->assertTrue($editorModel->canSeeVisibility(Visibility::Editorial), 'Editors should see editorial cards.');

        $memberModel = new AccessRoleModel(
            static function (string $capability) {
                return false;
            },
            static fn (): bool => true
        );

        $this->assertTrue($memberModel->canSeeVisibility(Visibility::Members), 'Logged-in users should see members cards.');
        $this->assertFalse($memberModel->canSeeFrontendControls(), 'Logged-in non-editors should not see frontend controls.');
        $this->assertFalse($memberModel->canSeeVisibility(Visibility::Editorial), 'Logged-in non-editors should not see editorial cards.');
    }

    private function assertTrue(bool $condition, string $message): void
    {
        if (! $condition) {
            throw new RuntimeException($message);
        }
    }

    private function assertFalse(bool $condition, string $message): void
    {
        if ($condition) {
            throw new RuntimeException($message);
        }
    }
}
