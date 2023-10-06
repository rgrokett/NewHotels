<?php

require_once 'components/page.php';

class NullUserAuthorization
{
    public function GetRoleSecurityInfo($role)
    {
        return new DataSourceSecurityInfo(true, true, true, true);
    }
    
    public function GetCurrentUser()
    {
        return null; 
    }
    
    public function GetUserRoles($userName)
    {
        return array('allOperationsRole');
    } 
    
    public function GetCurrentUserId()
    {
        return 0; 
    }    
    
    public function ApplyIdentityToConnectionOptions(&$connectoinOptions) { }
}

abstract class AbstractUserAuthorization
{
    public abstract function GetRoleSecurityInfo($role);

    public abstract function GetCurrentUserId();

    public abstract function GetCurrentUser();

    public abstract function IsCurrentUserLoggedIn();

    public abstract function GetUserRoles($userName);
    
    public function ApplyIdentityToConnectionOptions(&$connectoinOptions) { }
}

class HardCodedUserAuthorization extends AbstractUserAuthorization
{
    private $rolesSecurityInfo;
    private $userRoles;
    private $userIds;
    private $guestUserName;
    private $defaultUserName;
    
    public function __construct($rolesSecurityInfo, $userRoles, $userIds, 
    	$guestUserName = 'guest', 
    	$defaultUserName = 'defaultUser')
    {
        $this->rolesSecurityInfo = $rolesSecurityInfo;
        $this->userRoles = $userRoles;
        $this->userIds = $userIds;
        $this->guestUserName = $guestUserName;
        $this->defaultUserName = $defaultUserName;
    }

    public function GetRoleSecurityInfo($role)
    {
        return $this->rolesSecurityInfo[$role];
    }
    
    public function GetCurrentUserId()
    {
         if (isset($this->userIds[$this->GetCurrentUser()]))
            return $this->userIds[$this->GetCurrentUser()];
        else
            return null;
    }
    
    public function GetCurrentUser()
    {
        return GetCurrentUser(); 
    }
    
    public function IsCurrentUserLoggedIn()
    {
        return $this->GetCurrentUser() != 'guest';
    }
    
    public function MergeRoles($userRoles1, $userRoles2)
    {
        return array_merge($userRoles1, $userRoles2);
    }
    
    public function GetUserRoles($userName)
    {
        if (isset($this->userRoles[$userName]))
        {
            if ($this->guestUserName == $userName)
                return $this->userRoles[$userName];
            else
                return $this->MergeRoles($this->userRoles[$userName], $this->userRoles[$this->defaultUserName]);
        }
        else
        {
            return $this->userRoles[$this->defaultUserName];
        }
    }
}

class ServerSideUserAuthorization extends AbstractUserAuthorization
{
    private $rolesSecurityInfo;
    private $allRoles;
    private $guestUserName;
    private $allowGuestAccess;
    private $guestServerLogin;
    private $guestServerPassword;
    
    public function __construct($rolesSecurityInfo, $guestUserName, $allowGuestAccess, $guestServerLogin, $guestServerPassword)
    {
        $this->rolesSecurityInfo = $rolesSecurityInfo;
        $this->allRoles = array('viewRole', 'editRole', 'addRole', 'deleteRole', 'admin');
        $this->guestUserName = $guestUserName;
        $this->allowGuestAccess = $allowGuestAccess;
        $this->guestServerLogin = $guestServerLogin;
        $this->guestServerPassword = $guestServerPassword;
    }
    
    public function GetRoleSecurityInfo($role)
    {
        return $this->rolesSecurityInfo[$role];
    }

    public function GetCurrentUserId() { return null; }
    
    public function GetCurrentUser() { return GetCurrentUser(); }
    public function IsCurrentUserLoggedIn() { return $this->GetCurrentUser() != 'guest'; }
    
    public function GetUserRoles($userName)
    {
        return $this->allRoles;
    }
    
    public function ApplyIdentityToConnectionOptions(&$connectoinOptions)
    {
        if ($this->GetCurrentUser() == $this->guestUserName)
        {
            if ($this->allowGuestAccess)
            {
                $connectoinOptions['username'] = $this->guestServerLogin;
                $connectoinOptions['password'] = $this->guestServerPassword;
            }
            else
                RaiseError(GetCaptions()->GetMessageString('GuestAccessDenied'));
        }
        else
        {
            $connectoinOptions['username'] = $this->GetCurrentUser();
            $connectoinOptions['password'] = $_COOKIE['password'];
        }
    }
}

class AdminDataSourceSecurityInfo
{
    public function __construct()
    {}

    public function HasEditGrant() { return true; }
    public function HasViewGrant() { return true; }
    public function HasDeleteGrant() { return true; }
    public function HasAddGrant() { return true; }
    public function AdminGrant() { return true; }
}

class DataSourceSecurityInfo
{
    private $viewGrant;
    private $editGrant;
    private $addGrant;
    private $deleteGrant;
 
    public function __construct($viewGrant, $editGrant, $addGrant, $deleteGrant)
    {
        $this->viewGrant = $viewGrant;
        $this->editGrant = $editGrant;
        $this->addGrant = $addGrant;
        $this->deleteGrant = $deleteGrant;
    }
 
    public function HasEditGrant() { return $this->editGrant; }
    public function HasViewGrant() { return $this->viewGrant; }
    public function HasDeleteGrant() { return $this->deleteGrant; }
    public function HasAddGrant() { return $this->addGrant; }
    public function AdminGrant() { return false; }
}

define('ENCRYPTION_NONE', 0);
define('ENCRYPTION_MD5', 1);
define('ENCRYPTION_SHA1', 2);

class IdentityCheckStrategy
{
    function ApplyIdentityToConnectionOptions($connectionOptions) { }
}

class SimpleIdentityCheckStrategy extends IdentityCheckStrategy
{
    private $userInfos;
    private $passwordEncryption;

    public function __construct($userInfos, $passwordEncryption = ENCRYPTION_NONE)
    {
        $this->userInfos = $userInfos;
        $this->passwordEncryption = $passwordEncryption;
    }

    private function CheckPasswordEquals($actualPassword, $expectedPassword)
    {
        if ($this->passwordEncryption == ENCRYPTION_NONE)
            return $actualPassword == $expectedPassword;
        else if ($this->passwordEncryption == ENCRYPTION_MD5)
            return md5($actualPassword) == $expectedPassword;
        else if ($this->passwordEncryption == ENCRYPTION_SHA1)
            return sha1($actualPassword) == $expectedPassword;
        else
            return false;
    }

    public function CheckUsernameAndPassword($username, $password, &$errorMessage)
    {
        if (isset($this->userInfos[$username]) && $this->CheckPasswordEquals($password, $this->userInfos[$username]))
        {
            $errorMessage = null;
            return true;
        }
        else
        {
            $errorMessage = 'The username/password combination you entered was invalid.';
            return false;
        }
    }
}

class ServerSideIdentityCheckStrategy
{
    private $connectionFactory;
    private $connectionOptions;

    public function __construct($connectionFactory, $connectionOptions)
    {
        $this->connectionFactory = $connectionFactory;
        $this->connectionOptions = $connectionOptions;
    }

    public function CheckUsernameAndPassword($username, $password, &$errorMessage)
    {
        $this->connectionOptions['username'] = $username;
        $this->connectionOptions['password'] = $password;
        
        $connection = $this->connectionFactory->CreateConnection($this->connectionOptions);
        $connection->Connect();
        if ($connection->Connected())
        {
            $errorMessage = null;
            $connection->Disconnect();
            return true;            
        }
        else
        {   
            $errorMessage = $connection->LastError();//'The username/password combination you entered was invalid.';
            return false;
        }
    }
}



function GetCurrentUser()
{
    if (isset($_COOKIE['username']))
        return $_COOKIE['username'];
    else
        return 'guest';
}

function GetUserGrantInfo($username, $tableName)
{
    global $userGrants;
    if (isset($userGrants[$username]))
        if (isset($userGrants[$username][$tableName]))
            return $userGrants[$username][$tableName];
}

function GetCurrentUserGrantForDataSource($dataSourceName)
{
    return GetApplication()->GetCurrentUserGrants();
}

function GetCurrentUserRecordPermissionsForDataSource($dataSourceName)
{
    return GetApplication()->GetCurrentUserRecordPermissionsForDataSource($dataSourceName);
}

class AdminRecordPermissions
{
    public function CanAllUsersViewRecords()
    {
    	return true;
    }    
    
    public function HasEditGrant($dataset) 
    {
        return true;
    }
    
    public function HasViewGrant($dataset) 
    { 
        return true;
    }
    
    public function HasDeleteGrant($dataset) 
    { 
        return true;
    }        
}

class DataSourceRecordPermission
{
    private $canAllView, $canAllDelete, $canAllEdit;
    private $canOwnerView, $canOwnerDelete, $canOwnerEdit;
    private $ownerIdField;
 
    public function __construct($ownerIdField, $canAllView, $canAllDelete, $canAllEdit, 
        $canOwnerView, $canOwnerDelete, $canOwnerEdit)
    {
        $this->ownerIdField = $ownerIdField;
        $this->canAllView = $canAllView;
        $this->canAllDelete = $canAllDelete;
        $this->canAllEdit = $canAllEdit;
        $this->canOwnerView = $canOwnerView;
        $this->canOwnerDelete = $canOwnerDelete;
        $this->canOwnerEdit = $canOwnerEdit;
    }
    
    public function CanAllUsersViewRecords()
    {
    	return $this->canAllView;
    }
    
    public function HasEditGrant($dataset, $userId) 
    {
        $ownerId = $dataset->GetFieldValueByName($this->ownerIdField);
        return $ownerId == $userId ? $this->canOwnerEdit : $this->canAllEdit;
    }
    
    public function HasViewGrant($dataset, $userId) 
    { 
        $ownerId = $dataset->GetFieldValueByName($this->ownerIdField);
        return $ownerId == $userId ? $this->canOwnerView : $this->canAllView;
    }
    
    public function HasDeleteGrant($dataset, $userId) 
    { 
        $ownerId = $dataset->GetFieldValueByName($this->ownerIdField);
        return $ownerId == $userId ? $this->canOwnerDelete : $this->canAllDelete;
    }
}

class UserDataSourceRecordPermission
{
    private $userId;
    private $dataSourceRecordPermission;
 
    public function __construct($userId, $dataSourceRecordPermission)
    {
        $this->userId = $userId;
        $this->dataSourceRecordPermission = $dataSourceRecordPermission;
    }

    public function CanAllUsersViewRecords()
    {
    	return $this->dataSourceRecordPermission->CanAllUsersViewRecords();
    }    
    
    public function HasEditGrant($dataset) 
    {
        return $this->dataSourceRecordPermission->HasEditGrant($dataset, $this->userId);
    }
    
    public function HasViewGrant($dataset) 
    { 
        return $this->dataSourceRecordPermission->HasViewGrant($dataset, $this->userId);
    }
    
    public function HasDeleteGrant($dataset) 
    { 
        return $this->dataSourceRecordPermission->HasDeleteGrant($dataset, $this->userId);
    }        
}

class NullUserDataSourceRecordPermission
{
    public function HasEditGrant($dataset) 
    {
        return true;
    }
    
    public function HasViewGrant($dataset) 
    { 
        return true;
    }
    
    public function HasDeleteGrant($dataset) 
    { 
        return true;
    }         
}

class HardCodedDataSourceRecordPermissionRetrieveStrategy 
{
    private $dataSourceRecordPermissions;
 
    public function __construct($dataSourceRecordPermissions)
    {
        $this->dataSourceRecordPermissions = $dataSourceRecordPermissions;
    }
 
    public function GetUserRecordPermissionsForDataSource($dataSourceName, $userId)     
    {
        if (isset($this->dataSourceRecordPermissions[$dataSourceName]))
            return new UserDataSourceRecordPermission($userId, $this->dataSourceRecordPermissions[$dataSourceName]);
        else
            return null;
    }
}

class NullDataSourceRecordPermissionRetrieveStrategy 
{
    public function GetUserRecordPermissionsForDataSource($dataSourceName, $userId)     
    {
        return new NullUserDataSourceRecordPermission();
    }
}

?>
