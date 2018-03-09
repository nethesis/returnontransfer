#!/usr/bin/php
<?php

#
#    Copyright (C) 2018 Nethesis S.r.l.
#    http://www.nethesis.it - support@nethesis.it
#
#    This file is part of ReturnOnTransfer FreePBX module.
#
#    ReturnOnTransfer module is free software: you can redistribute it and/or modify
#    it under the terms of the GNU General Public License as published by
#    the Free Software Foundation, either version 3 of the License, or any 
#    later version.
#
#    ReturnOnTransfer module is distributed in the hope that it will be useful,
#    but WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#    GNU General Public License for more details.
#
#    You should have received a copy of the GNU General Public License
#    along with ReturnOnTransfer module.  If not, see <http://www.gnu.org/licenses/>.
#

include_once ("/etc/freepbx.conf");
define("AGIBIN_DIR", "/var/lib/asterisk/agi-bin");
include(AGIBIN_DIR."/phpagi.php");

global $db;

$agi = new AGI();
$extension = $argv[1];

//get park extension
$sql = "SELECT parkext from parkplus;";
$data=@$db->getRow($sql);
$park_ext = $data[0];
if($park_ext == $extension) {
    @$agi->exec("Set", "xfer_context=from-internal");
    exit (0);
}

//get context
$sql2 = "SELECT data FROM sip where keyword=\"context\" and id=\"$extension\";";
$result = @$db->getRow($sql2);
$context = $result[0];
@$agi->exec("Set", "xfer_context=$context");

