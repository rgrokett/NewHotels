<?php

require_once 'libs/smartylibs/Smarty.class.php';
require_once 'database_engine/insert_command.php';
require_once 'database_engine/update_command.php';
require_once 'database_engine/select_command.php';
require_once 'database_engine/delete_command.php';

require_once 'components/captions.php';
require_once 'components/grid/grid.php';
require_once 'components/grid/columns.php';
require_once 'components/grid/edit_columns.php';
require_once 'components/dataset/dataset.php';
require_once 'components/dataset/table_dataset.php';
require_once 'components/dataset/query_dataset.php';
require_once 'components/renderers/renderer.php';
require_once 'components/renderers/edit_renderer.php';
require_once 'components/renderers/list_renderer.php';
require_once 'components/renderers/view_renderer.php';
require_once 'components/renderers/print_renderer.php';
require_once 'components/renderers/insert_renderer.php';
require_once 'components/renderers/excel_renderer.php';
require_once 'components/renderers/word_renderer.php';
require_once 'components/renderers/xml_renderer.php';
require_once 'components/renderers/csv_renderer.php';
require_once 'components/renderers/pdf_renderer.php';

require_once 'components/common.php';
require_once 'components/page.php';
require_once 'components/page_navigator.php';
require_once 'components/master_detail/common.php';
require_once 'components/master_detail/master_detail_httpandler.php';
require_once 'components/simple_search_control.php';
require_once 'components/advanced_search_page.php';
require_once 'components/page_list.php';

require_once 'security/security_info.php';
require_once 'components/error_utils.php';


define('OPERATION_PARAMNAME', 'operation');
define('OPERATION_VIEW', 'view');
define('OPERATION_EDIT', 'edit');
define('OPERATION_INSERT', 'insert');
define('OPERATION_COPY', 'copy');
define('OPERATION_DELETE', 'delete');
define('OPERATION_VIEWALL', 'viewall');
define('OPERATION_COMMIT', 'commit');
define('OPERATION_COMMIT_INSERT', 'commit_new');
define('OPERATION_COMMIT_DELETE', 'commit_delete');
define('OPERATION_PRINT_ALL', 'printall');
define('OPERATION_PRINT_PAGE', 'printpage');
define('OPERATION_DELETE_SELECTED', 'delsel');

define('OPERATION_EXCEL_EXPORT', 'eexcel');
define('OPERATION_WORD_EXPORT', 'eword');
define('OPERATION_XML_EXPORT', 'exml');
define('OPERATION_CSV_EXPORT', 'ecsv');
define('OPERATION_PDF_EXPORT', 'epdf');

define('OPERATION_HTTPHANDLER_REQUEST', 'httphandler');

function GetOperation()
{
    return GetApplication()->GetOperation();
//    if(isset($_GET[OPERATION_PARAMNAME]))
//    {
//        return $_GET[OPERATION_PARAMNAME];
//    }
//    else if (isset($_POST[OPERATION_PARAMNAME]))
//        {
//            return $_POST[OPERATION_PARAMNAME];
//        }
//        else
//        {
//            return OPERATION_VIEWALL;
//        }
}

class Application
{
    private $mainPage;
    private $httpHandlers;
    private $userAuthorizationStrategy;
    private $dataSourceRecordPermissionRetrieveStrategy;

    public function __construct()
    {
        session_start();
        $this->httpHandlers = array();
        $this->userAuthorizationStrategy = new NullUserAuthorization();
        $this->dataSourceRecordPermissionRetrieveStrategy = new NullDataSourceRecordPermissionRetrieveStrategy();
        $this->SetupUncaughtedExceptionsHandler();
    }

    public function SetupUncaughtedExceptionsHandler()
    {
        
    }

    public function RefineInputValue($value)
    {
        if(1 == get_magic_quotes_gpc())
        {
            if (is_array($value))
                return $value;
            else
                return stripslashes($value);
        }
        return $value;
    }

    public function IsPOSTValueSet($name)
    {
        return isset($_POST[$name]);
    }

    public function GetPOSTValue($name)
    {
        return $this->RefineInputValue($_POST[$name]);
    }

    public function IsGETValueSet($name)
    {
        return isset($_GET[$name]);
    }

    public function GetGETValue($name)
    {
        return $this->RefineInputValue($_GET[$name]);
    }

    public function IsSessionVariableSet($name)
    {
        return isset($_SESSION[$name]);
    }

    public function SetSessionVariable($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    public function GetSessionVariable($name)
    {
        return $_SESSION[$name];
    }

    public function UnSetSessionVariable($name)
    {
        unset($_SESSION[$name]);
    }

    public function Run()
    {
        if (isset($_GET['hname']))
        {
            $this->ProcessHTTPHandlers();
        }
        else
        {
            $this->mainPage->BeginRender();
            $this->mainPage->EndRender();
        }
    }

    public function SetMainPage($page)
    {
        $this->mainPage = $page;
    }

    function RegisterHTTPHandler($HTTPHandler)
    {
        $this->httpHandlers[] = $HTTPHandler;
    }

    function GetHTTPHandlerByName($Name)
    {
        foreach($this->httpHandlers as $HTTPHandler)
        {
            if ($HTTPHandler->GetName() == $Name)
                return $HTTPHandler;
        }
        return null;
    }

    function ProcessHTTPHandlers()
    {
        $renderer = new ViewAllRenderer($this->mainPage->GetLocalizerCaptions());
        $HTTPHandler = $this->GetHTTPHandlerByName($_GET['hname']);
        if (isset($HTTPHandler))
        {
            echo $HTTPHandler->Render($renderer);
        }
    }

    function HasPostGetRequestParameters()
    {
        if (count($_POST) == 0 && count($_GET) == 0)
        {
            return false;
        }
        elseif (count($_POST) == 0 && (count($_GET) == 1 && isset($_GET['hname'])))
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    function GetRoleSecurityInfo($role)
    {
        return $this->userAuthorizationStrategy->GetRoleSecurityInfo($role);
    }

    function MergeRoles($roles)
    {
        $viewGrant = false;
        $editGrant = false;
        $addGrant = false;
        $deleteGrant = false;
        foreach($roles as $role)
        {
            $currentRoleSecurityInfo = $this->GetRoleSecurityInfo($role);
            if ($currentRoleSecurityInfo->AdminGrant())
            {
                return new AdminDataSourceSecurityInfo();
            }
            if ($currentRoleSecurityInfo->HasViewGrant())
                $viewGrant = true;
            if ($currentRoleSecurityInfo->HasEditGrant())
                $editGrant = true;
            if ($currentRoleSecurityInfo->HasAddGrant())
                $addGrant = true;
            if ($currentRoleSecurityInfo->HasDeleteGrant())
                $deleteGrant = true;
        }
        $result = new DataSourceSecurityInfo($viewGrant, $editGrant, $addGrant, $deleteGrant);
        return $result;
    }

    function GetCurrentUser()
    {
        return $this->userAuthorizationStrategy->GetCurrentUser();
    }

    function IsCurrentUserLoggedIn()
    {
        return $this->userAuthorizationStrategy->IsCurrentUserLoggedIn();
    }

    function GetUserRoles($userName)
    {
        return $this->userAuthorizationStrategy->GetUserRoles($userName);
    }

    function GetCurrentUserGrants()
    {
        $currentUser = $this->GetCurrentUser();
        $roles = $this->GetUserRoles($currentUser);
        return $this->MergeRoles($roles);
    }

    public function GetUserAuthorizationStrategy()
    {
        return $this->userAuthorizationStrategy;
    }

    public function SetUserAuthorizationStrategy($userAuthorizationStrategy)
    {
        $this->userAuthorizationStrategy = $userAuthorizationStrategy;
    }

    public function GetCurrentUserId()
    {
        return $this->userAuthorizationStrategy->GetCurrentUserId();
    }

    public function SetDataSourceRecordPermissionRetrieveStrategy($value)
    { $this->dataSourceRecordPermissionRetrieveStrategy = $value; }
    public function GetDataSourceRecordPermissionRetrieveStrategy()
    { return $this->dataSourceRecordPermissionRetrieveStrategy; }

    public function GetCurrentUserRecordPermissionsForDataSource($dataSourceName)
    {
        if ($this->GetCurrentUserGrants()->AdminGrant())
            return new AdminRecordPermissions();
        else
            return $this->GetUserRecordPermissionsForDataSource($dataSourceName, $this->GetCurrentUserId());
    }

    public function GetUserRecordPermissionsForDataSource($dataSourceName, $userId)
    {
        return $this->GetDataSourceRecordPermissionRetrieveStrategy()->
        GetUserRecordPermissionsForDataSource($dataSourceName, $userId);
    }

    private $settedOperation = null;

    function SetOperation($value)
    {
        $this->settedOperation = $value;
    }

    function GetOperation()
    {
        if (isset($this->settedOperation))
            return $this->settedOperation;
        else
        {
            if(isset($_GET[OPERATION_PARAMNAME]))
            {
                return $_GET[OPERATION_PARAMNAME];
            }
            else if (isset($_POST[OPERATION_PARAMNAME]))
                {
                    return $_POST[OPERATION_PARAMNAME];
                }
                else
                {
                    return OPERATION_VIEWALL;
                }
        }
    }
}

abstract class Page
{
    protected $dataset;
    private $grid;
    private $pageNavigator;
    protected $renderer;
    private $pageFileName;
    private $httpHandlerName;
    private $caption;
    private $shortCaption;
    private $securityInfo;
    private $header;
    private $footer;
    private $contentEncoding;
    private $gridHeader;
    private $message;
    private $errorMessage;

    private $pageNavigatorStack;

    private $showPageList;
    private $exportToExcelAvailable;
    private $exportToWordAvailable;
    private $exportToXmlAvailable;
    private $exportToCsvAvailable;
    private $exportToPdfAvailable;
    private $printerFriendlyAvailable;
    private $simpleSearchAvailable;
    private $advancedSearchAvailable;
    private $visualEffectsEnabled;


    public $Margin;
    public $Padding;
    public $AdvancedSearchControl;

    public $BeforePageRender;
    public $OnCustomHTMLHeader;



    public function GetCustomPageHeader()
    {
        $result = '';
        $this->OnCustomHTMLHeader->Fire(array(&$this, &$result));
        return $result;
    }

    protected abstract function CreateGrid();
    protected function CreatePageNavigator()
    {
        return null;
    }

    protected function AddPageNavigatorToStack($pageNavigator)
    {
        $this->pageNavigatorStack[] = $pageNavigator;
    }

    protected function FillPageNavigatorStack()
    {
    }

    public function GetThirdPartyLibsPath()
    { return 'libs/'; }
    public function GetPathToCss()
    { return 'phpgen.css'; }
    public function GetPathToJavaScriptFile()
    { return 'phpgen.css'; }

    protected function DoBeforeCreate()
    { }

    private function CreateComponents()
    {
        $this->grid = $this->CreateGrid();
        $this->pageNavigator = $this->CreatePageNavigator();
        $this->httpHandlerName = null;
        $this->FillPageNavigatorStack();
    }

    function __construct($pageFileName, $caption = null, $dataSourceSecurityInfo = null, $contentEncoding=null)
    {
        $this->BeforePageRender = new Event();
        $this->OnCustomHTMLHeader = new Event();

        $this->contentEncoding = $contentEncoding;
        $this->securityInfo = $dataSourceSecurityInfo;
        $this->pageFileName = $pageFileName;
        $this->caption = $caption;
        $this->shortCaption = $caption;
        $this->showPageList = true;
        $this->exportToExcelAvailable = true;
        $this->exportToWordAvailable = true;
        $this->exportToXmlAvailable = true;
        $this->exportToCsvAvailable = true;
        $this->exportToPdfAvailable = true;
        $this->printerFriendlyAvailable = true;
        $this->simpleSearchAvailable = true;
        $this->advancedSearchAvailable = true;
        $this->visualEffectsEnabled = true;

        $this->BeforeCreate();
        $this->CreateComponents();
        $this->gridHeader = '';
        $this->recordPermission = null;
        $this->message = null;
        $this->pageNavigatorStack = array();
    }

    private $localizerCaptions;

    function BeforeCreate()
    {
        try
        {
            $this->DoBeforeCreate();
        }
        catch(Exception $e)
        {
            $message = $this->GetLocalizerCaptions()->GetMessageString('GuestAccessDenied');
            ShowSecurityErrorPage($this, $message);
            die();
        }
    }

    function GetLocalizerCaptions()
    {
        if (!isset($this->localizerCaptions))
            $this->localizerCaptions = new Captions($this->GetContentEncoding());
        return $this->localizerCaptions;
    }

    function GetConnection()
    {
        $this->dataset->Connect();
        return $this->dataset->GetConnection();
    }

    private $showUserAuthBar = false;

    function GetShowUserAuthBar()
    { return $this->showUserAuthBar; }
    function SetShowUserAuthBar($value)
    { $this->showUserAuthBar = $value; }

    function IsCurrentUserLoggedIn()
    {
        return GetApplication()->IsCurrentUserLoggedIn();
    }

    function GetCurrentUserName()
    {
        return GetApplication()->GetCurrentUser();
    }

    public function SetErrorMessage($value) { $this->errorMessage = $value; }
    public function GetErrorMessage() { return $this->errorMessage; }

    protected function DoGetGridHeader()
    { return ''; }
    public function GetGridHeader()
    { return $this->RenderText($this->DoGetGridHeader()); }

    public function SetMessage($value)
    { $this->message = $value; }
    public function GetMessage()
    { return $this->RenderText($this->message); }

    public function GetContentEncoding()
    { return $this->contentEncoding; }
    public function SetContentEncoding($value)
    { $this->contentEncoding = $value; }

    public function GetShortCaption()
    { return $this->RenderText($this->shortCaption); }
    public function SetShortCaption($value)
    { $this->shortCaption = $value; }

    public function GetCaption()
    { return $this->RenderText($this->caption); }
    public function SetCaption($value)
    { $this->caption = $value; }

    public function GetHeader()
    { return $this->RenderText($this->header); }
    public function SetHeader($value)
    { $this->header = $value; }

    public function GetFooter()
    { return $this->RenderText($this->footer); }
    public function SetFooter($value)
    { $this->footer = $value; }

    protected function GetSecurityInfo()
    { return $this->securityInfo; }

    private $recordPermission;

    public function GetRecordPermission()
    { return $this->recordPermission; }
    public function SetRecordPermission($value)
    { $this->recordPermission = $value; }

    public function RenderText($text)
    {
        return ConvertTextToEncoding($text, GetAnsiEncoding(), $this->GetContentEncoding());
    //return ConvertTextToEncoding($text, null, $this->GetContentEncoding());
    }

    public function PrepareTestForSQL($text)
    {
        return ConvertTextToEncoding($text, GetAnsiEncoding(), $this->GetContentEncoding());
    }

    function RaiseSecurityError($condition, $operation)
    {
        if ($condition)
        {
            if ($operation === OPERATION_EDIT)
                $message = $this->GetLocalizerCaptions()->GetMessageString('EditOperationNotPermitted');
            elseif ($operation === OPERATION_VIEW)
                $message = $this->GetLocalizerCaptions()->GetMessageString('ViewOperationNotPermitted');
            elseif ($operation === OPERATION_DELETE)
                $message = $this->GetLocalizerCaptions()->GetMessageString('DeleteOperationNotPermitted');
            elseif ($operation === OPERATION_INSERT)
                $message = $this->GetLocalizerCaptions()->GetMessageString('InsertOperationNotPermitted');
            else
                $message = $this->GetLocalizerCaptions()->GetMessageString('OperationNotPermitted');
            ShowSecurityErrorPage($this, $message);
            exit;
        }
    }

    function CheckOperationPermitted()
    {
        $operation = GetOperation();
        if ($this->securityInfo->AdminGrant())
            return true;
        switch ($operation)
        {
            case OPERATION_EDIT:
                $this->RaiseSecurityError(!$this->securityInfo->HasEditGrant(), OPERATION_EDIT);
                break;
            case OPERATION_VIEW:
            case OPERATION_PRINT_ALL:
            case OPERATION_PRINT_PAGE:
            case OPERATION_EXCEL_EXPORT:
            case OPERATION_WORD_EXPORT:
            case OPERATION_XML_EXPORT:
            case OPERATION_CSV_EXPORT:
            case OPERATION_PDF_EXPORT:
                $this->RaiseSecurityError(!$this->securityInfo->HasViewGrant(), OPERATION_VIEW);
                break;
            case OPERATION_DELETE:
            case OPERATION_DELETE_SELECTED:
                $this->RaiseSecurityError(!$this->securityInfo->HasDeleteGrant(), OPERATION_DELETE);
                break;
            case OPERATION_INSERT:
            case OPERATION_COPY:
                $this->RaiseSecurityError(!$this->securityInfo->HasAddGrant(), OPERATION_INSERT);
                break;
            default:
                $this->RaiseSecurityError(!$this->securityInfo->HasViewGrant(), OPERATION_VIEW);
                break;
        }
    }

    function SelectRenderer()
    {
        switch (GetOperation())
        {
            case OPERATION_EDIT :
                if (!$this->securityInfo->AdminGrant())
                    $this->RaiseSecurityError(!$this->securityInfo->HasEditGrant(), 'Cannot edit');
                $this->renderer = new EditRenderer($this->GetLocalizerCaptions());
                break;
            case OPERATION_VIEW:
                if (!$this->securityInfo->AdminGrant())
                    $this->RaiseSecurityError(!$this->securityInfo->HasViewGrant(), 'Cannot view');
                $this->renderer = new ViewRenderer($this->GetLocalizerCaptions());
                break;
            case OPERATION_DELETE:
                if (!$this->securityInfo->AdminGrant())
                    $this->RaiseSecurityError(!$this->securityInfo->HasDeleteGrant(), 'Cannot delete');
                $this->renderer = new DeleteRenderer($this->GetLocalizerCaptions());
                break;
            case OPERATION_INSERT:
                if (!$this->securityInfo->AdminGrant())
                    $this->RaiseSecurityError(!$this->securityInfo->HasAddGrant(), 'Cannot add');
                $this->renderer = new InsertRenderer($this->GetLocalizerCaptions());
                break;
            case OPERATION_COPY:
                if (!$this->securityInfo->AdminGrant())
                    $this->RaiseSecurityError(!$this->securityInfo->HasAddGrant(), 'Cannot add');
                $this->renderer = new InsertRenderer($this->GetLocalizerCaptions());
                break;
            case OPERATION_PRINT_ALL:
                if (!$this->securityInfo->AdminGrant())
                    $this->RaiseSecurityError(!$this->securityInfo->HasViewGrant(), 'Cannot view');
                $this->renderer = new PrintRenderer($this->GetLocalizerCaptions());
                break;
            case OPERATION_PRINT_PAGE:
                if (!$this->securityInfo->AdminGrant())
                    $this->RaiseSecurityError(!$this->securityInfo->HasViewGrant(), 'Cannot view');
                $this->renderer = new PrintRenderer($this->GetLocalizerCaptions());
                break;
            case OPERATION_EXCEL_EXPORT:
                if (!$this->securityInfo->AdminGrant())
                    $this->RaiseSecurityError(!$this->securityInfo->HasViewGrant(), 'Cannot view');
                $this->renderer = new ExcelRenderer($this->GetLocalizerCaptions());
                break;
            case OPERATION_WORD_EXPORT:
                if (!$this->securityInfo->AdminGrant())
                    $this->RaiseSecurityError(!$this->securityInfo->HasViewGrant(), 'Cannot view');
                $this->renderer = new WordRenderer($this->GetLocalizerCaptions());
                break;
            case OPERATION_XML_EXPORT:
                if (!$this->securityInfo->AdminGrant())
                    $this->RaiseSecurityError(!$this->securityInfo->HasViewGrant(), 'Cannot view');
                $this->renderer = new XmlRenderer($this->GetLocalizerCaptions());
                break;
            case OPERATION_CSV_EXPORT:
                if (!$this->securityInfo->AdminGrant())
                    $this->RaiseSecurityError(!$this->securityInfo->HasViewGrant(), 'Cannot view');
                $this->renderer = new CsvRenderer($this->GetLocalizerCaptions());
                break;
            case OPERATION_PDF_EXPORT:
                if (!$this->securityInfo->AdminGrant())
                    $this->RaiseSecurityError(!$this->securityInfo->HasViewGrant(), 'Cannot view');
                $this->renderer = new PdfRenderer($this->GetLocalizerCaptions());
                break;
            case OPERATION_DELETE_SELECTED:
                if (!$this->securityInfo->AdminGrant())
                    $this->RaiseSecurityError(!$this->securityInfo->HasDeleteGrant(), 'Cannot delete');
                $this->renderer = new ViewAllRenderer($this->GetLocalizerCaptions());
                break;
            default:
                if (!$this->securityInfo->AdminGrant())
                    $this->RaiseSecurityError(!$this->securityInfo->HasViewGrant(), 'Cannot view');
                $this->renderer = new ViewAllRenderer($this->GetLocalizerCaptions());
                break;
        }
    }

    function DoProcessMessages()
    {
        $this->grid->SetState(GetOperation());
        if (isset($this->AdvancedSearchControl))
            $this->AdvancedSearchControl->ProcessMessages();
        $this->grid->ProcessMessages();
        if (isset($this->pageNavigator))
            $this->pageNavigator->ProcessMessages();
    }

    function ProcessMessages()
    {
        try
        {
            $this->DoProcessMessages();
        }
        catch(Exception $e)
        {
            $this->DisplayErrorPage($e);
            die();
        }
    }

    function BeginRender()
    {
        $this->BeforeBeginRenderPage();
        $this->ProcessMessages();
    }

    function EndRender()
    {
        try
        {
            $this->CheckOperationPermitted();
            $this->SelectRenderer();
            $this->BeforeRenderPageRender();
            echo $this->renderer->Render($this);
        }
        catch(Exception $e)
        {
            $this->DisplayErrorPage($e);
            die();
        }
    }

    function BeforeBeginRenderPage()
    { }

    function BeforeRenderPageRender()
    { }

    function DisplayErrorPage($exception)
    {
        $errorStateRenderer = new ErrorStateRenderer($this->GetLocalizerCaptions(), $exception);
        echo $errorStateRenderer->Render($this);
    }

    function Accept($visitor)
    {
        $visitor->RenderPage($this);
    }

    function GetDataset()
    { return $this->dataset; }
    function GetGrid()
    { return $this->grid; }
    function GetPageNavigator()
    { return $this->pageNavigator; }
    function GetPageNavigatorStack()
    { return $this->pageNavigatorStack; }

    function GetPageFileName()
    { return $this->pageFileName; }
    function SetHttpHandlerName($name)
    { $this->httpHandlerName = $name; }

    function CreateLinkBuilder()
    {
        $result = new LinkBuilder($this->GetPageFileName());

        if (isset($this->httpHandlerName))
            $result->AddParameter('hname', $this->httpHandlerName);

        return $result;
    }

    function GetPrintAllLink()
    {
        return $this->GetOperationLink(OPERATION_PRINT_ALL);
    }

    function GetPrintCurrentPageLink()
    {
        return $this->GetOperationLink(OPERATION_PRINT_PAGE, true);
    }

    function GetOperationLink($operationName, $operationForAllPages = false)
    {
        $result = $this->CreateLinkBuilder();
        $result->AddParameter(OPERATION_PARAMNAME, $operationName);
        if ($operationForAllPages)
            if (isset($this->pageNavigator))
                $this->pageNavigator->AddCurrentPageParameters($result);
        return $result->GetLink();

    }

    function GetExportToExcelLink()
    {
        return $this->GetOperationLink(OPERATION_EXCEL_EXPORT);
    }

    function GetExportToWordLink()
    {
        return $this->GetOperationLink(OPERATION_WORD_EXPORT);
    }

    function GetExportToXmlLink()
    {
        return $this->GetOperationLink(OPERATION_XML_EXPORT);
    }

    function GetExportToCsvLink()
    {
        return $this->GetOperationLink(OPERATION_CSV_EXPORT);
    }

    function GetExportToPdfLink()
    {
        return $this->GetOperationLink(OPERATION_PDF_EXPORT);
    }

    private $showTopPageNavigator = true;
    private $showBottomPageNavigator = false;

    function GetShowTopPageNavigator()
    { return $this->showTopPageNavigator; }
    function SetShowTopPageNavigator($value)
    { $this->showTopPageNavigator = $value; }

    function GetShowBottomPageNavigator()
    { return $this->showBottomPageNavigator; }
    function SetShowBottomPageNavigator($value)
    { $this->showBottomPageNavigator = $value; }

    function GetShowPageList()
    { return $this->showPageList; }
    function GetExportToExcelAvailable()
    { return $this->exportToExcelAvailable; }
    function GetExportToWordAvailable()
    { return $this->exportToWordAvailable; }
    function GetExportToXmlAvailable()
    { return $this->exportToXmlAvailable; }
    function GetExportToCsvAvailable()
    { return $this->exportToCsvAvailable; }
    function GetExportToPdfAvailable()
    { return $this->exportToPdfAvailable; }
    function GetPrinterFriendlyAvailable()
    { return $this->printerFriendlyAvailable; }
    function GetSimpleSearchAvailable()
    { return $this->simpleSearchAvailable; }
    function GetAdvancedSearchAvailable()
    { return $this->advancedSearchAvailable; }
    function GetVisualEffectsEnabled()
    { return $this->visualEffectsEnabled; }

    function SetShowPageList($value)
    { $this->showPageList = $value; }
    function SetExportToExcelAvailable($value)
    { $this->exportToExcelAvailable = $value; }
    function SetExportToWordAvailable($value)
    { $this->exportToWordAvailable = $value; }
    function SetExportToXmlAvailable($value)
    { $this->exportToXmlAvailable = $value; }
    function SetExportToCsvAvailable($value)
    { $this->exportToCsvAvailable = $value; }
    function SetExportToPdfAvailable($value)
    { $this->exportToPdfAvailable = $value; }
    function SetPrinterFriendlyAvailable($value)
    { $this->printerFriendlyAvailable = $value; }
    function SetSimpleSearchAvailable($value)
    { $this->simpleSearchAvailable = $value; }
    function SetAdvancedSearchAvailable($value)
    { $this->advancedSearchAvailable = $value; }
    function SetVisualEffectsEnabled($value)
    { $this->visualEffectsEnabled = $value; }
}

abstract class DetailPage extends Page
{
    private $foreingKeyValues;
    private $foreingKeyFields;
    private $recordLimit;
    private $totalRowCount;
    private $fullViewHandlerName;

    public $DetailRowNumber;

    public function __construct($caption, $shortCaption, $foreingKeyFields, $dataSourceSecurityInfo, $contentEncoding = null, $recordLimit = 0, $fullViewHandlerName)
    {
        parent::__construct('', $caption, $dataSourceSecurityInfo, $contentEncoding);
        $this->foreingKeyFields = $foreingKeyFields;
        $this->SetShortCaption($shortCaption);
        $this->recordLimit = $recordLimit;
        $this->fullViewHandlerName = $fullViewHandlerName;
    }

    public function ProcessMessages()
    {
        if ($this->recordLimit)
        {
            $this->dataset->SetUpLimit(0);
            $this->dataset->SetLimit($this->recordLimit);
        }
        $this->DetailRowNumber = $_GET['detailrow'];
        $this->renderer = new ViewAllRenderer($this->GetLocalizerCaptions());
        for($i = 0; $i < count($this->foreingKeyFields); $i++)
        {
            $this->dataset->AddFieldFilter($this->foreingKeyFields[$i], new FieldFilter($_GET['fk' . $i], '='));
            $this->foreingKeyValues[] = $_GET['fk' . $i];
        }
        $this->totalRowCount = $this->dataset->GetTotalRowCount();
        $this->GetGrid()->SetShowUpdateLink(false);
    }

    protected function CreatePageNavigator()
    { }

    function CreateLinkBuilder()
    {
        $result = parent::CreateLinkBuilder();
        for($i = 0; $i < count($this->foreingKeyValues); $i++)
            $result->AddParameter('fk' . $i, $this->foreingKeyValues[$i]);
        return $result;
    }

    public function GetFullRecordCount()
    { return $this->totalRowCount; }
    public function GetRecordLimit()
    { return $this->recordLimit; }
    public function GetFullViewLink()
    {
        $result = $this->CreateLinkBuilder();
        $result->AddParameter('hname', $this->fullViewHandlerName);
        return $result->GetLink();
    }

    function Accept($visitor)
    {
        $visitor->RenderDetailPage($this);
    }

    function EndRender()
    {
        echo $this->renderer->Render($this);
    }
}

abstract class DetailPageEdit extends Page
{
    private $foreingKeyValues;
    private $foreingKeyFields;
    private $masterKeyFields;
    private $masterDataset;
    private $masterGrid;
    private $parentPage;

    public function __construct($parentPage, $foreingKeyFields, $masterKeyFields, $masterGrid, $masterDataset, $dataSourceSecurityInfo, $contentEncoding = null)
    {
        parent::__construct('', '', $dataSourceSecurityInfo, $contentEncoding);
        $this->foreingKeyFields = $foreingKeyFields;
        $this->masterKeyFields = $masterKeyFields;
        $this->masterGrid = $masterGrid;
        $this->masterDataset = $masterDataset;
        $this->foreingKeyValues = array();
        $this->parentPage = $parentPage;
    }

    public function GetMasterGrid()
    { return $this->masterGrid; }

    public function ProcessMessages()
    {
        for($i = 0; $i < count($this->foreingKeyFields); $i++)
        {
            $this->dataset->AddFieldFilter($this->foreingKeyFields[$i], new FieldFilter($_GET['fk' . $i], '='));
            $this->dataset->SetMasterFieldValue($this->foreingKeyFields[$i], $_GET['fk' . $i]);
            $this->foreingKeyValues[] = $_GET['fk' . $i];
        }
        for($i = 0; $i < count($this->masterKeyFields); $i++)
            $this->masterDataset->AddFieldFilter($this->masterKeyFields[$i], new FieldFilter($_GET['fk' . $i], '='));
        parent::ProcessMessages();
    }

    function Accept($visitor)
    {
        $visitor->RenderDetailPageEdit($this);
    }

    function GetParentPageLink()
    {
        return $this->parentPage->CreateLinkBuilder()->GetLink();
    }

    function CreateLinkBuilder()
    {
        $result = parent::CreateLinkBuilder();
        for($i = 0; $i < count($this->foreingKeyValues); $i++)
            $result->AddParameter('fk' . $i, $this->foreingKeyValues[$i]);
        return $result;
    }

    function GetOperationLink($operationName, $operationForAllPages = false)
    {
        $result = $this->CreateLinkBuilder();
        $result->AddParameter(OPERATION_PARAMNAME, $operationName);

        for($i = 0; $i < count($this->foreingKeyValues); $i++)
            $result->AddParameter('fk' . $i, $this->foreingKeyValues[$i]);

        if (isset($this->pageNavigator))
            $result->AddParameter('page', $this->pageNavigator->CurrentPageNumber());
        return $result->GetLink();

    }
}

class CustomLoginPage
{
    public function __construct()
    { }

    public function GetCustomPageHeader()
    {
        return '';
    }

    public function RenderText($text)
    {
        return ConvertTextToEncoding($text, GetAnsiEncoding(), $this->GetContentEncoding());
    }
}

$Application = new Application();

function GetApplication()
{
    global $Application;
    return $Application;
}

?>
