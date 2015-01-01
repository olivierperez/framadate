<?php
use Framadate\Migration\From_0_0_to_0_8_Migration;
use Framadate\Migration\From_0_8_to_0_9_Migration;
use Framadate\Migration\Migration;
use Framadate\Utils;

include_once __DIR__ . '/app/inc/init.php';

function output($msg) {
    echo $msg . '<br/>';
}

// List a Migration sub classes to execute
$migrations = [
    new From_0_0_to_0_8_Migration(),
    new From_0_8_to_0_9_Migration()
];
// ---------------------------------------

// Check if MIGRATION_TABLE already exists
$tables = $connect->allTables();
$pdo = $connect->getPDO();
$prefixedMigrationTable = Utils::table(MIGRATION_TABLE);

if (!in_array($prefixedMigrationTable, $tables)) {
    $pdo->exec('
CREATE TABLE IF NOT EXISTS `' . $prefixedMigrationTable . '` (
  `id`   INT(11)  UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` TEXT              NOT NULL,
  `execute_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;');

    output('Table ' . $prefixedMigrationTable . ' created.');
}

$selectStmt = $pdo->prepare('SELECT id FROM ' . $prefixedMigrationTable . ' WHERE name=?');
$insertStmt = $pdo->prepare('INSERT INTO ' . $prefixedMigrationTable . ' (name) VALUES (?)');
$countSucceeded = 0;
$countFailed = 0;
$countSkipped = 0;

// Loop on every Migration sub classes
foreach ($migrations as $migration) {
    $className = get_class($migration);

    // Check if $className is a Migration sub class
    if (!$migration instanceof Migration) {
        output('The class ' . $className . ' is not a sub class of Framadate\\Migration\\Migration.');
        exit;
    }

    // Check if the Migration is already executed
    $selectStmt->execute([$className]);
    $executed = $selectStmt->rowCount();
    $selectStmt->closeCursor();

    if (!$executed) {
        $migration->execute($pdo);
        if ($insertStmt->execute([$className])) {
            $countSucceeded++;
            output('Migration done: ' . $className);
        } else {
            $countFailed++;
            output('Migration failed: ' . $className);
        }
    } else {
        $countSkipped++;
    }

}

$countTotal = $countSucceeded + $countFailed + $countSkipped;

output('Summary<hr/>');
output('Success: ' . $countSucceeded . ' / ' . $countTotal);
output('Fail: ' . $countFailed . ' / ' . $countTotal);
output('Skipped: ' . $countSkipped . ' / ' . $countTotal);