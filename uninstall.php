<?php

$dbh = \FreePBX::Database();
out(_('Removing the database table'));
$table = 'returnontransfer';
try {
    $sth = $dbh->prepare("UPDATE `freepbx_settings` SET `value`='from-internal-xfer' WHERE `keyword`='TRANSFER_CONTEXT'");
    $result = $sth->execute();
    $sql = "DROP TABLE IF EXISTS $table;";
    $sth = $dbh->prepare($sql);
    $result = $sth->execute();
} catch (PDOException $e) {
    $result = $e->getMessage();
}
if ($result === true) {
    out(_('Table Deleted'));
} else {
    out(_('Something went wrong'));
    out($result);
}
