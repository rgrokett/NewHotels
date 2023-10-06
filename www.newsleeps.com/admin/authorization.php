<?php

require_once 'components/page.php';
require_once 'components/security/security_info.php';

$fixedRoles = array(
    'viewRole'      => new DataSourceSecurityInfo(true, false, false, false),
    'editRole'      => new DataSourceSecurityInfo(false, true, false, false),
    'addRole'       => new DataSourceSecurityInfo(false, false, true, false),
    'deleteRole'    => new DataSourceSecurityInfo(false, false, false, true),
    'admin'         => new AdminDataSourceSecurityInfo()
);

$users = array('admin' => 'yourAdminPWD');

$userRoles = array('guest' => array(),
    'defaultUser' => array('viewRole'),
    'guest' => array(),
    'root' => array('admin', 'viewRole', 'editRole', 'addRole', 'deleteRole'));

$usersIds = array('root' => -1);

$dataSourceRecordPermissions = array();

function SetUpUserAuthorization()
{
    global $fixedRoles;
    global $userRoles;
    global $usersIds;
    global $dataSourceRecordPermissions;
    $userAuthorizationStrategy = new HardCodedUserAuthorization($fixedRoles, $userRoles, $usersIds);
    GetApplication()->SetUserAuthorizationStrategy($userAuthorizationStrategy);

GetApplication()->SetDataSourceRecordPermissionRetrieveStrategy(
    new HardCodedDataSourceRecordPermissionRetrieveStrategy($dataSourceRecordPermissions));
}

function GetIdentityCheckStrategy()
{
    global $users;
    return new SimpleIdentityCheckStrategy($users, ENCRYPTION_NONE);
}

?>
