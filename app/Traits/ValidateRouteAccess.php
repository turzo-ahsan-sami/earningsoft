<?php

namespace App\Traits;

use App\User;
use DB;

trait ValidateRouteAccess
{
    public static function hasAccess($userId, $moduleId, $functionId, $subFunctionId)
    {
        if ($userId == 1) {
            return true;
        }

        $flag = 1;

        /*Check Restricted Route First*/
        $restrictedString = DB::table('gnr_user_role')->where('userIdFK', $userId)->value('restrictedFunctionalityId');
        if (strlen($restrictedString) > 0) {

            $restrictedString = str_replace(['[', ']', '"', '{', '}'], '', $restrictedString);
            $restrictedArray = explode(',', $restrictedString);

            foreach ($restrictedArray as $key => $restrictedValue) {
                $data = explode(':', $restrictedValue);
                $currentModuleId = $data[0];
                $currentFunctionId = $data[1];
                $currentSubFunctionId = $data[2];

                if ($currentModuleId == $moduleId && $currentFunctionId == $functionId && $currentSubFunctionId == $subFunctionId) {
                    return false;
                }
            }
        }
        /*End Check Restricted Route First*/

        /*Check Role Acces*/
        $userRoleId = User::find($userId)->getRole()->roleId;
        $functionalityString = DB::table('gnr_role')->where('id', $userRoleId)->value('functionalityId');

        if (strlen($functionalityString) > 0) {

            $functionalityString = str_replace(['[', ']', '"', '{', '}'], '', $functionalityString);
            $functionalityArray = explode(',', $functionalityString);

            foreach ($functionalityArray as $key => $functionalityValue) {
                $data = explode(':', $functionalityValue);
                $currentModuleId = $data[0];
                $currentFunctionId = $data[1];
                $currentSubFunctionId = $data[2];

                if ($currentModuleId == $moduleId && $currentFunctionId == $functionId && $currentSubFunctionId == $subFunctionId) {
                    return true;
                } else {
                    $flag = 0;
                }
            }
        } else {
            $flag = 0;
        }
        /*End Check Role Acces*/

        /*Check Addition Acces*/
        $additionaAccess = DB::table('gnr_user_role')->where('userIdFK', $userId)->value('additionalFunctionalityId');
        $additionaAccessType = DB::table('gnr_user_role')->where('userIdFK', $userId)->value('additionalFunctionalityTypeNDate');
        if (strlen($additionaAccess) > 0) {

            $additionaAccessString = str_replace(['[', ']', '"', '{', '}'], '', $additionaAccess);
            $additionaAccessArray = explode(',', $additionaAccessString);

            $additionaAccessTypeString = str_replace(['[', ']', '"', '{', '}'], '', $additionaAccessType);
            $additionaAccessTypeArray = explode(',', $additionaAccessTypeString);

            foreach ($additionaAccessArray as $key => $additionaAccessValue) {
                $data = explode(':', $additionaAccessValue);
                $currentModuleId = $data[0];
                $currentFunctionId = $data[1];
                $currentSubFunctionId = $data[2];

                if ($currentModuleId == $moduleId && $currentFunctionId == $functionId && $currentSubFunctionId == $subFunctionId) {
                    return true;
                }

            }
        }
        /*End Check Addition Acces*/

        if ($flag == 0) {
            return false;
        } else {
            return true;
        }

    }

    public static function hasAccessByFunctionCode($userId, $moduleId, $functionCode, $subFunctionId)
    {
        if ($userId == 1) {
            return true;
        }

        $functionId = DB::table('gnr_function')->where('functionCode',$functionCode)->value('id');

        $flag = 1;

        /*Check Restricted Route First*/
        $restrictedString = DB::table('gnr_user_role')->where('userIdFK', $userId)->value('restrictedFunctionalityId');
        if (strlen($restrictedString) > 0) {

            $restrictedString = str_replace(['[', ']', '"', '{', '}'], '', $restrictedString);
            $restrictedArray = explode(',', $restrictedString);

            foreach ($restrictedArray as $key => $restrictedValue) {
                $data = explode(':', $restrictedValue);
                $currentModuleId = $data[0];
                $currentFunctionId = $data[1];
                $currentSubFunctionId = $data[2];

                if ($currentModuleId == $moduleId && $currentFunctionId == $functionId && $currentSubFunctionId == $subFunctionId) {
                    return false;
                }
            }
        }
        /*End Check Restricted Route First*/

        /*Check Role Acces*/
        $userRoleId = User::find($userId)->getRole()->roleId;
        $functionalityString = DB::table('gnr_role')->where('id', $userRoleId)->value('functionalityId');

        if (strlen($functionalityString) > 0) {

            $functionalityString = str_replace(['[', ']', '"', '{', '}'], '', $functionalityString);
            $functionalityArray = explode(',', $functionalityString);

            foreach ($functionalityArray as $key => $functionalityValue) {
                $data = explode(':', $functionalityValue);
                $currentModuleId = $data[0];
                $currentFunctionId = $data[1];
                $currentSubFunctionId = $data[2];

                if ($currentModuleId == $moduleId && $currentFunctionId == $functionId && $currentSubFunctionId == $subFunctionId) {
                    return true;
                } else {
                    $flag = 0;
                }
            }
        } else {
            $flag = 0;
        }
        /*End Check Role Acces*/

        /*Check Addition Acces*/
        $additionaAccess = DB::table('gnr_user_role')->where('userIdFK', $userId)->value('additionalFunctionalityId');
        $additionaAccessType = DB::table('gnr_user_role')->where('userIdFK', $userId)->value('additionalFunctionalityTypeNDate');
        if (strlen($additionaAccess) > 0) {

            $additionaAccessString = str_replace(['[', ']', '"', '{', '}'], '', $additionaAccess);
            $additionaAccessArray = explode(',', $additionaAccessString);

            $additionaAccessTypeString = str_replace(['[', ']', '"', '{', '}'], '', $additionaAccessType);
            $additionaAccessTypeArray = explode(',', $additionaAccessTypeString);

            foreach ($additionaAccessArray as $key => $additionaAccessValue) {
                $data = explode(':', $additionaAccessValue);
                $currentModuleId = $data[0];
                $currentFunctionId = $data[1];
                $currentSubFunctionId = $data[2];

                if ($currentModuleId == $moduleId && $currentFunctionId == $functionId && $currentSubFunctionId == $subFunctionId) {
                    return true;
                }

            }
        }
        /*End Check Addition Acces*/

        if ($flag == 0) {
            return false;
        } else {
            return true;
        }

    }

}