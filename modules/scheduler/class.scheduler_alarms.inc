<?php
/**
 * This file is part of the Achievo distribution.
 * Detailed copyright and licensing information can be found
 * in the doc/COPYRIGHT and doc/LICENSE files which should be
 * included in the distribution.
 *
 * @package achievo
 * @subpackage scheduler
 *
 * @copyright (c)2008 Sandy Pleyte
 * @copyright (c)2008 Ibuildings B.V.
 * @license http://www.achievo.org/licensing Achievo Open Source License
 *
 * @version $Revision$
 * $Id$
 */
useattrib("atknumberattribute");
useattrib("atkupdatedbyattribute");
useattrib("atklistattribute");
useattrib("atkboolattribute");
useattrib("scheduler.schedulerdurationattribute");

/**
 * Class for managing scheduler alarms
 * @author Sandy Pleyte <sandy@achievo.org>
 * @package achievo
 * @subpackage scheduler
 * @since 1.3.0
 */
class scheduler_alarms extends atkNode
{

    function scheduler_alarms()
    {
        //$users = $this->getUsers();
        $users = array("-1" => "All users", atkGetUserId() => "sandy");
        $this->atkNode("scheduler_alarms");
        $this->add(new atkNumberAttribute("scheduler_id", AF_PRIMARY | AF_HIDE));
        $this->add(new atkNumberAttribute("startdate", AF_PRIMARY | AF_READONLY));
        $this->add(new schedulerDurationAttribute("duration"));
        $this->add(new atkNumberAttribute("senddate", AF_HIDE));
        $this->add(new atkBoolAttribute("send", AF_HIDE, 1));
        $this->add(new atkAttribute("type", AF_HIDE));
        $this->add(new atkListAttribute("userid", array_values($users), array_keys($users), AF_PRIMARY));
        $this->add(new atkUpdatedByAttribute("owner"));
        $this->setTable("scheduler_alarms");
        $this->setOrder("scheduler_alarms.startdate,scheduler_alarms.duration");
        $this->addFilter("scheduler_alarms.startdate>=" . time());
        $this->setSecurityAlias("scheduler.scheduler");
    }

    function initial_values()
    {
        $sessionmanager = &atkGetSessionManager();
        $startdate = $sessionmanager->stackVar("itemdate");

        return array("startdate" => $startdate,
            "userid" => -1,
            "type" => "email",
            "send" => 0,
            "duration" => 60);
    }

    function startdate_display($rec)
    {
        return date("d-m-Y H:i", $rec["startdate"]);
    }

    function preAdd(&$rec)
    {
        // Pre calculate the senddate
        $rec["senddate"] = $this->_calculateSendDate($rec["startdate"], $rec["duration"]);
        return true;
    }

    function preUpdate(&$rec)
    {
        // Pre calculate the senddate
        $rec["senddate"] = $this->_calculateSendDate($rec["startdate"], $rec["duration"]);
        return true;
    }

    function _calculateSendDate($startdate, $duration)
    {
        return ($startdate - $duration);
    }

    function duration_validate(&$rec)
    {
        if ($rec["senddate"] < time()) {
            triggerError($rec, "duration", "no_alarms_in_the_past");
        }
    }

}

?>