#!/usr/bin/env php
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
$callernum = $argv[2];
$blindnum = $argv[3];

//get xfer dest context
$sql2 = "SELECT data FROM sip where keyword=\"context\" and id=\"$extension\";";
$result = @$db->getRow($sql2);
$extension_context = $result[0];

//check context of xfer dest
if($extension_context!='') {
         @$agi->exec("Set", "xfer_context=$extension_context");
} else {
         //get caller context
         $sql3 = "SELECT data FROM sip where keyword=\"context\" and id=\"$callernum\";";
         $result = @$db->getRow($sql3);
         $callerid_context = $result[0];

         //check context of caller
         if ($callerid_context!='') {
                @$agi->exec("Set", "xfer_context=$callerid_context");
         } else {
                //get blind context
                $sql4 = "SELECT data FROM sip where keyword=\"context\" and id=\"$blindnum\";";
                $result = @$db->getRow($sql4);
                $blind_context = $result[0];
                @$agi->exec("Set", "xfer_context=$blind_context");
         }
}
