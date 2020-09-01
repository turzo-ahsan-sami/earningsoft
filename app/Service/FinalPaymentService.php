<?php

namespace App\Service;

/**
 * FinalPaymentService Class manage final payment related logic
 *
 * @author hafij <hafij.to@gmail.com>
 */
class FinalPaymentService
{

    public static function chkFinalPaymentAccess($data)
    {
        $utype = EasyCode::getUserRole();
        $supervisorSteps = json_decode($data->final_payment_steps);

        //return count(@$supervisorSteps->value)." -- ".str_split(@$data->final_payment_status,1)[0]." -- ".$utype['branchid']." -- ".$utype['departmentid'];
        // dd($data->final_payment_status);

        if ($utype['roleid'] == 1) {
            return true;
        } else if ($utype['branchid'] == '1' && $utype['departmentid'] == '1') {
            return true;
        } else if ($data->final_payment_status == 'Initiate') {
            if (count($supervisorSteps->value) > 0 && $supervisorSteps->value[0] == $utype['positionid']) {
                return true;
            }
        } else if ($data->final_payment_status == '1st Supervisor') {
            if (!empty($supervisorSteps) && count($supervisorSteps->value) > 1 && $supervisorSteps->value[1] == $utype['positionid']) {
                return true;
            }
        } else if ($data->final_payment_status != '' && $supervisorSteps && count($supervisorSteps->value) == str_split(@$data->final_payment_status, 1)[0] && $utype['branchid'] == 1 && $utype['departmentid'] == 4) {
            return true;
        } else if ($data->final_payment_status == '2nd Supervisor') {
            if (!empty($supervisorSteps) && count($supervisorSteps->value) > 2 && $supervisorSteps->value[2] == $utype['positionid']) {
                return true;
            }
        } else if ($data->final_payment_status == '3rd Supervisor') {
            if (count($supervisorSteps->value) > 3 && $supervisorSteps->value[3] == $utype['positionid']) {
                return true;
            }
        } else if ($data->final_payment_status == '4th Supervisor') {
            if (count($supervisorSteps->value) > 4 && $supervisorSteps->value[4] == $utype['positionid']) {
                return true;
            }
        } else if ($data->final_payment_status == 'Audit' && $utype['branchid'] == 1 && $utype['departmentid'] == 2) {
            return true;
        } else if ($data->final_payment_status == 'Accounts' && $utype['branchid'] == 1 && $utype['departmentid'] == 1) {
            return true;
        } else if ($data->final_payment_status == 'HR & Accounts' && $utype['branchid'] == 1 && $utype['positionid'] == 115) {
            return true;
        } else if ($data->final_payment_status == 'Account Officer' && $utype['branchid'] == 1 && $utype['positionid'] == 109) {
            return true;
        } else if ($data->final_payment_status == 'Assistant Director' && $utype['branchid'] == 1 && $utype['positionid'] == 104) {
            return true;
        } else if ($data->final_payment_status == 'Executive Director' && $utype['branchid'] == 1 && $utype['departmentid'] == 2) {
            return true;
        }

        // For audit department
        if ($data->final_payment_status != '' &&
            !empty($supervisorSteps->value) &&
            count(@$supervisorSteps->value) == str_split(@$data->final_payment_status, 1)[0] &&
            $utype['branchid'] == 1 &&
            $utype['departmentid'] == 4) {
            return true;
        }
    }

    public static function chkFinalPaymentCompletedAccess($data)
    {
        $utype = EasyCode::getUserRole();
        $supervisorSteps = json_decode($data->final_payment_steps);

        if ($utype['roleid'] == 1) {
            return true;
        } else if ($utype['branchid'] == '1' && $utype['departmentid'] == '1') {
            return true;
        } else if ($data->final_payment_status == 'Initiate') {
            if (count($supervisorSteps->value) > 0 && $supervisorSteps->value[0] == $utype['positionid']) {
                return true;
            }
        } else if ($data->final_payment_status == '1st Supervisor') {
            if (!empty($supervisorSteps) && count($supervisorSteps->value) > 1 && $supervisorSteps->value[1] == $utype['positionid']) {
                return true;
            }
        } else if ($data->final_payment_status != '' && count(@$supervisorSteps->value) == str_split(@$data->final_payment_status, 1)[0] && $utype['branchid'] == 1 && $utype['departmentid'] == 4) {
            return true;
        } else if ($data->final_payment_status == '2nd Supervisor') {
            if (!empty($supervisorSteps) && count($supervisorSteps->value) > 2 && $supervisorSteps->value[2] == $utype['positionid']) {
                return true;
            }
        } else if ($data->final_payment_status == '3rd Supervisor') {
            if (count($supervisorSteps->value) > 3 && $supervisorSteps->value[3] == $utype['positionid']) {
                return true;
            }
        } else if ($data->final_payment_status == '4th Supervisor') {
            if (count($supervisorSteps->value) > 4 && $supervisorSteps->value[4] == $utype['positionid']) {
                return true;
            }
        } else if ($data->final_payment_status == 'Audit' && $utype['branchid'] == 1 && $utype['departmentid'] == 2) {
            return true;
        } else if ($data->final_payment_status == 'Accounts' ||
            $data->final_payment_status == 'Paid' &&
            $utype['branchid'] == 1 &&
            $utype['departmentid'] == 1) {
            return true;
        } else if ($data->final_payment_status == 'HR & Accounts' ||
            $data->final_payment_status == 'Paid' ||
            $utype['branchid'] == 1 &&
            $utype['positionid'] == 115) {
            return true;
        } else if ($data->final_payment_status == 'Account Officer' && $utype['branchid'] == 1 && $utype['positionid'] == 109) {
            return true;
        } else if ($data->final_payment_status == 'Assistant Director' && $utype['branchid'] == 1 && $utype['positionid'] == 104) {
            return true;
        } else if ($data->final_payment_status == 'Executive Director' && $utype['branchid'] == 1 && $utype['departmentid'] == 2) {
            return true;
        }
    }
}
