#!/usr/bin/env php
<?php
/**
 * Birthday email CLI for cron / DirectAdmin.
 * Usage: php birthday-cli.php
 *
 * Does not go through BirthdayController's web auth gate.
 */
require_once __DIR__ . '/app/config/bootstrap_env.php';
require_once __DIR__ . '/app/config/config.php';
require_once __DIR__ . '/app/config/database.php';
require_once __DIR__ . '/app/config/mail.php';
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}
require_once __DIR__ . '/core/Autoloader.php';
require_once CORE_PATH . '/Controller.php';
require_once APP_PATH . '/controllers/BirthdayController.php';

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "CLI only.\n");
    exit(1);
}

// Lightweight CLI runner that reuses BirthdayController batch logic
// without Session::requireAuth() from the normal constructor.
final class BirthdayCliRunner extends BirthdayController
{
    public function __construct()
    {
        // Skip web auth; initialize dependencies the same way.
        $ref = new ReflectionClass(BirthdayController::class);
        $parent = $ref->getParentClass(); // Controller — no-op

        $memberProp = $ref->getProperty('memberModel');
        $memberProp->setAccessible(true);
        $settingsProp = $ref->getProperty('settings');
        $settingsProp->setAccessible(true);
        $mailerProp = $ref->getProperty('mailer');
        $mailerProp->setAccessible(true);

        // Construct without parent BirthdayController::__construct
        // by not calling it — PHP requires explicit parent call only if we invoke it.
        require_once APP_PATH . '/models/MemberModel.php';
        require_once APP_PATH . '/models/SettingsModel.php';
        require_once APP_PATH . '/services/MailerService.php';

        $memberProp->setValue($this, new MemberModel());
        $settingsProp->setValue($this, new SettingsModel());
        $mailerProp->setValue($this, new MailerService());
    }
}

$runner = new BirthdayCliRunner();
$runner->sendDueCli();
