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
useattrib("atkboolattribute");
useattrib("atklistattribute");
useattrib("atknumberattribute");
userelation("atkmanytoonerelation");

/**
 * Class for managing scheduler attendees
 * @author Sandy Pleyte <sandy@achievo.org>
 * @package achievo
 * @subpackage scheduler
 * @since 1.3.0
 */
class scheduler_attendees extends atkNode
{

    function scheduler_attendees()
    {
        $this->atkNode("scheduler_attendees");

        $this->add(new atkNumberAttribute("scheduler_id", AF_PRIMARY | AF_HIDE | AF_FORCE_LOAD));
        $this->add(new atkManyToOneRelation("person_id", "person.select_person", AF_PRIMARY | AF_LARGE));
        $this->add(new atkListAttribute("status", array("no_response", "accepted", "rejected"), array(), AF_OBLIGATORY));

        $this->setTable("scheduler_attendees");
        $this->setSecurityAlias("scheduler.scheduler");
        $this->addSecurityMap(array("accept", "reject"), "view");
    }

    function descriptor_def()
    {
        return "[person_id.lastname], [person_id.firstname]";
    }

    function preAdd(&$record)
    {
        if ($record["person_id"]["role"] != "employee")
            $record["status"] = "accepted";
        return true;
    }

    function recordActions($rec, &$actions, &$mraactions)
    {
        if ($rec["person_id"]["id"] == atkGetUserId()) {
            if ($rec["status"] != "accepted")
                $actions["accept"] = session_url(dispatch_url($this->atknodetype(), "accept", array("scheduler_id" => $rec["scheduler_id"], "person_id" => $rec["person_id"]["id"])));
            if ($rec["status"] != "rejected")
                $actions["reject"] = session_url(dispatch_url($this->atknodetype(), "reject", array("scheduler_id" => $rec["scheduler_id"], "person_id" => $rec["person_id"]["id"])));
        }
    }

    function action_accept(&$handler, $record = array(), $mode = "")
    {
        $this->updateUserStatus('accepted');
        $location = "";
        if (atkLevel() <= 0) {
            $this->setFeedback("accept", ACTION_SUCCESS);
            $location = $this->feedbackUrl("accept", ACTION_SUCCESS, "", atktext("scheduled_item_accepted"));
            $this->redirect($location);
        }
        $this->redirect($location);
    }

    function action_reject(&$handler, $record = array(), $mode = "")
    {
        $this->updateUserStatus('rejected');
        $location = "";
        if (atkLevel() <= 0) {
            $this->setFeedback("reject", ACTION_SUCCESS);
            $location = $this->feedbackUrl("reject", ACTION_SUCCESS, "", atktext("scheduled_item_rejected"));
            $this->redirect($location);
        }
        $this->redirect($location);
    }

    function updateUserStatus($status)
    {
        $primkey = 'scheduler_id=\'' . $this->m_postvars["scheduler_id"] . '\' AND person_id.id=\'' . $this->m_postvars["person_id"] . '\'';
        $rows = $this->selectDb($primkey, "", "", "", "", "edit");
        if (count($rows) == 1) {
            // It seems updatedb needs the complete record
            $rec = $rows[0];

            //$rec['atkprimkey'] = $primkey;
            $rec['status'] = $status;
            $ret = $this->updateDb($rec, false);
            if ($rec["person_id"]["role"] == "employee") {
                $this->sendEmail("user_notification", $rec["scheduler_id"], $rec["person_id"]["id"], "date", $status, true);
            }
        }
        return true;
    }

    function postAdd($record)
    {
        // Only for employees
        if ($record["person_id"]["role"] == "employee")
            $this->sendEmail("add_event", $record["scheduler_id"], $record["person_id"]["id"], "date", "invited");

        return true;
    }

    function postDelete($record)
    {
        if ($record["person_id"]["role"] == "employee")
            $this->sendEmail("disinvited_user", $record["scheduler_id"], $record["person_id"]["id"], "date", "disinvited");
        return true;
    }

    /**
     * Send mail to user
     * @todo: Fix mail
     *
     * @param string $action
     * @param int $scheduler_id
     * @param int $person_id
     * @param string $date
     * @param string $trigger_action
     * @param boolean $sendtoowner
     * @return boolean
     */
    function sendEmail($action, $scheduler_id, $person_id, $date, $trigger_action = "", $sendtoowner = false)
    {
        $template = &atknew("modules.scheduler.scheduler_email_template");
        $template->initialize($action, $person_id);
        $template->loadData($action, $scheduler_id, $date, $trigger_action);
        $template->send();
    }

}

?>