<?php
out(_('Creating the database table'));
//Database
$dbh = \FreePBX::Database();
try {
    $sql = "CREATE TABLE IF NOT EXISTS `returnontransfer` (
        `value` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`keyword` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
	PRIMARY KEY (`keyword`));";
    $sth = $dbh->prepare($sql);
    $result = $sth->execute(array());
} catch (PDOException $e) {
    $result = $e->getMessage();
}

$sql = array();
$sql[] = 'INSERT IGNORE INTO `returnontransfer` (`keyword`,`value`) VALUES ("timeout","15")';
$sql[] = 'INSERT IGNORE INTO `returnontransfer` (`keyword`,`value`) VALUES ("prefix","RT: ${xfer_exten} ${CALLERID(name)}")';
$sql[] = 'INSERT IGNORE INTO `returnontransfer` (`keyword`,`value`) VALUES ("alertinfo","")';
$sql[] = 'INSERT IGNORE INTO `returnontransfer` (`keyword`,`value`) VALUES ("enabled","true")';
foreach ($sql as $query) {
    $sth = $dbh->prepare($query);
    $result = $sth->execute();
}

// get enabled state
$sql = "SELECT `value` FROM `returnontransfer` WHERE `keyword` = 'enabled'";
$sth = $dbh->prepare($sql);
$sth->execute(array());
$res = $sth->fetchAll()[0][0];
if ($res === "true") {
    $sth = $dbh->prepare("UPDATE `freepbx_settings` SET `value`='blindxfer_ringback' WHERE `keyword`='TRANSFER_CONTEXT'");
    $result = $sth->execute();
}
