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

function returnontransfer_get_config($engine){
    global $ext;
    global $asterisk_conf;
    switch($engine) {
    case "asterisk":
        $context = 'blindxfer_ringback';
        $e = '_X.';
            $ext->add($context,$e,'',new ext_noop('${BLINDTRANSFER}'));
            $ext->add($context,$e,'n', new ext_set('timeoutd','10'));           
            $ext->add($context,$e,'', new ext_set('CHANNEL(language)', '${MASTER_CHANNEL(CHANNEL(language))}'));
            $ext->add($context,$e,'n', new ext_set('xfer_exten','${EXTEN}'));           
            $ext->add($context,$e,'',new ext_noop('${xfer_exten}'));
            $ext->add($context,$e,'',new ext_agi('returnontransfer_setContext.php,${xfer_exten}'));
            $ext->add($context,$e,'',new ext_noop('${xfer_context}'));
            $ext->add($context,$e,'',new ext_dial('local/${EXTEN}@${xfer_context}','${timeoutd}'));
            $ext->add($context,$e,'',new ext_noop('${BLINDTRANSFER}'));
            $ext->add($context,$e,'n', new ext_set('foo','${CUT(BLINDTRANSFER,-,1)}'));           
            $ext->add($context,$e,'n', new ext_set('cb_exten','${CUT(foo,/,2)}')); # cut everything before the / character
            $ext->add($context,$e,'',new ext_noop('${cb_exten}'));
            $ext->add($context,$e,'',new ext_gotoif('$["${DIALSTATUS}" = "ANSWER"]','hangup:callback'));
            $ext->add($context,$e,'n', new ext_set('CALLERID(name)','RB From ${xfer_exten} ${CALLERID(name)}')); #prefix CID name with RB to indicate it is a ringback
            $ext->add($context,$e,'',new ext_agi('returnontransfer_setContext.php,${cb_exten}'));
            $ext->add($context,$e,'',new ext_noop('${xfer_context}'));
            $ext->add($context,$e,'',new ext_dial('local/${cb_exten}@${xfer_context},'));
            $ext->add($context,$e,'',new ext_hangup(''));
            $ext->add($context,'h','',new ext_hangup(''));
        break;
    }
}
