<?php
// 2019-06-03 PHP 7.0 mysqli
    require_once 'database_engine/mysql_engine.php';
    require_once 'components/page.php';
    require_once 'settings.php';
    require_once 'authorization.php';

    function GetConnectionOptions()
    {
        $result = GetGlobalConnectionOptions();
        $result['client_encoding'] = 'utf8';
        GetApplication()->GetUserAuthorizationStrategy()->ApplyIdentityToConnectionOptions($result);
        return $result;
    }

    
    ?><?php
    
    ?><?php
    
    class affiliate_dataPage extends Page
    {
        protected function DoBeforeCreate()
        {
            $this->dataset = new TableDataset(
                new MySqlIConnectionFactory(),
                GetConnectionOptions(),
                '`affiliate_data`');
            $field = new IntegerField('id', null, null, true);
            $this->dataset->AddField($field, true);
            $field = new StringField('hotel_chain');
            $this->dataset->AddField($field, false);
            $field = new StringField('new_openings_url');
            $this->dataset->AddField($field, false);
            $field = new StringField('script_id');
            $this->dataset->AddField($field, false);
            $field = new DateTimeField('last_verified_date');
            $this->dataset->AddField($field, false);
            $field = new StringField('affiliate_url');
            $this->dataset->AddField($field, false);
            $field = new StringField('login');
            $this->dataset->AddField($field, false);
            $field = new StringField('pwd');
            $this->dataset->AddField($field, false);
        }
    
        protected function CreatePageNavigator()
        {
            $result = new CompositePageNavigator($this);
            
            $partitionNavigator = new PageNavigator('pnav', $this, $this->dataset);
            $partitionNavigator->SetRowsPerPage(20);
            $result->AddPageNavigator($partitionNavigator);
            
            return $result;
        }
    
        public function GetPageList()
        {
            $currentPageCaption = $this->GetShortCaption();
            $result = new PageList();
            $result->AddPage(new PageLink($this->RenderText('Affiliate Data'), 'affiliate_data.php', $this->RenderText('Affiliate Data'), $currentPageCaption == $this->RenderText('Affiliate Data')));
            $result->AddPage(new PageLink($this->RenderText('New Hotels'), 'new_hotels.php', $this->RenderText('New Hotels'), $currentPageCaption == $this->RenderText('New Hotels')));
            $result->AddPage(new PageLink($this->RenderText('Load new_hotels.csv Data'), 'data/load_new_hotels.php', $this->RenderText('Load new_hotels.csv Data'), $currentPageCaption == $this->RenderText('Load new_hotels.csv Data')));
            $result->AddPage(new PageLink($this->RenderText('Load Geo LatLng Data'), 'data/geolocate.php', $this->RenderText('Load Geo LatLng Data'), $currentPageCaption == $this->RenderText('Load Geo LatLng Data')));
            $result->AddPage(new PageLink($this->RenderText('Load affiliate_data.csv'), 'data/load_affiliate.php', $this->RenderText('Load affiliate_data.csv'), $currentPageCaption == $this->RenderText('Load affiliate_data.csv')));
            $result->AddPage(new PageLink($this->RenderText('Update EAN HotelID'), 'data/update_hotelid.php', $this->RenderText('Update EAN HotelID'), $currentPageCaption == $this->RenderText('Update EAN HotelID')));
            $result->AddPage(new PageLink($this->RenderText('Update EAN Photo URLs'), 'data/update_images.php', $this->RenderText('Update EAN Photo URLs'), $currentPageCaption == $this->RenderText('Update EAN Photo URLs')));
            $result->AddPage(new PageLink($this->RenderText('Load EAN Renovations'), 'data/load_renovations.php', $this->RenderText('Load EAN Renovations'), $currentPageCaption == $this->RenderText('Load EAN Renovations')));
            $result->AddPage(new PageLink($this->RenderText('Update EAN Renovations'), 'data/update_renovations.php', $this->RenderText('Update EAN Renovations'), $currentPageCaption == $this->RenderText('Update EAN Renovations')));
            return $result;
        }
    
        protected function CreateGridSearchControl($grid)
        {
            $grid->UseFilter = true;
            $grid->SearchControl = new SimpleSearch('affiliate_datassearch', $this->dataset,
                array('id', 'hotel_chain', 'new_openings_url', 'script_id', 'last_verified_date', 'affiliate_url', 'login', 'pwd'),
                array($this->RenderText('Id'), $this->RenderText('Hotel Chain'), $this->RenderText('New Openings Url'), $this->RenderText('Script Id'), $this->RenderText('Last Verified Date'), $this->RenderText('Affiliate Url'), $this->RenderText('Login'), $this->RenderText('Pwd')),
                array(
                    '=' => $this->GetLocalizerCaptions()->GetMessageString('equals'),
                    '<>' => $this->GetLocalizerCaptions()->GetMessageString('doesNotEquals'),
                    '<' => $this->GetLocalizerCaptions()->GetMessageString('isLessThan'),
                    '<=' => $this->GetLocalizerCaptions()->GetMessageString('isLessThanOrEqualsTo'),
                    '>' => $this->GetLocalizerCaptions()->GetMessageString('isGreaterThan'),
                    '>=' => $this->GetLocalizerCaptions()->GetMessageString('isGreaterThanOrEqualsTo'),
                    'LIKE' => $this->GetLocalizerCaptions()->GetMessageString('Like'),
                    'STARTS' => $this->GetLocalizerCaptions()->GetMessageString('StartsWith'),
                    'ENDS' => $this->GetLocalizerCaptions()->GetMessageString('EndsWith'),
                    'CONTAINS' => $this->GetLocalizerCaptions()->GetMessageString('Contains')
                    ), $this->GetLocalizerCaptions()
                );
        }
    
        protected function CreateGridAdvancedSearchControl($grid)
        {
            $this->AdvancedSearchControl = new AdvancedSearchControl('affiliate_dataasearch', $this->dataset);
            $this->AdvancedSearchControl->AddSearchColumn(new StringSearchColumn('id', $this->RenderText('Id'), $this->GetLocalizerCaptions()));
            $this->AdvancedSearchControl->AddSearchColumn(new StringSearchColumn('hotel_chain', $this->RenderText('Hotel Chain'), $this->GetLocalizerCaptions()));
            $this->AdvancedSearchControl->AddSearchColumn(new StringSearchColumn('new_openings_url', $this->RenderText('New Openings Url'), $this->GetLocalizerCaptions()));
            $this->AdvancedSearchControl->AddSearchColumn(new StringSearchColumn('script_id', $this->RenderText('Script Id'), $this->GetLocalizerCaptions()));
            $this->AdvancedSearchControl->AddSearchColumn(new StringSearchColumn('last_verified_date', $this->RenderText('Last Verified Date'), $this->GetLocalizerCaptions()));
            $this->AdvancedSearchControl->AddSearchColumn(new StringSearchColumn('affiliate_url', $this->RenderText('Affiliate Url'), $this->GetLocalizerCaptions()));
            $this->AdvancedSearchControl->AddSearchColumn(new StringSearchColumn('login', $this->RenderText('Login'), $this->GetLocalizerCaptions()));
            $this->AdvancedSearchControl->AddSearchColumn(new StringSearchColumn('pwd', $this->RenderText('Pwd'), $this->GetLocalizerCaptions()));
        }
    
        protected function AddOperationsColumns($grid)
        {
            if ($this->GetSecurityInfo()->HasViewGrant())
              $grid->AddViewColumn(new RowOperationByLinkColumn($this->GetLocalizerCaptions()->GetMessageString('View'), OPERATION_VIEW, $this->dataset));
            if ($this->GetSecurityInfo()->HasEditGrant())
            {
              $column = $grid->AddViewColumn(new RowOperationByLinkColumn($this->GetLocalizerCaptions()->GetMessageString('Edit'), OPERATION_EDIT, $this->dataset));
              $column->OnShow->AddListener('ShowEditButtonHandler', $this);
            }
            if ($this->GetSecurityInfo()->HasDeleteGrant())
            {
              $column = $grid->AddViewColumn(new RowOperationByLinkColumn($this->GetLocalizerCaptions()->GetMessageString('Delete'), OPERATION_DELETE, $this->dataset));
            $column->OnShow->AddListener('ShowDeleteButtonHandler', $this);
            }
            if ($this->GetSecurityInfo()->HasAddGrant())
              $grid->AddViewColumn(new RowOperationByLinkColumn($this->GetLocalizerCaptions()->GetMessageString('Copy'), OPERATION_COPY, $this->dataset));
            $grid->AddVericalLine('solid 2px');
        }
    
        protected function AddFieldColumns($grid)
        {
            //
            // View column for id field
            //
            $column = new TextViewColumn('id', 'Id', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddViewColumn($column);
            
            //
            // View column for hotel_chain field
            //
            $column = new TextViewColumn('hotel_chain', 'Hotel Chain', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('hotel_chain_handler');
            $grid->AddViewColumn($column);
            
            //
            // View column for new_openings_url field
            //
            $column = new TextViewColumn('new_openings_url', 'New Openings Url', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('new_openings_url_handler');
            $grid->AddViewColumn($column);
            
            //
            // View column for script_id field
            //
            $column = new TextViewColumn('script_id', 'Script Id', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddViewColumn($column);
            
            //
            // View column for last_verified_date field
            //
            $column = new DateTimeViewColumn('last_verified_date', 'Last Verified Date', $this->dataset);
            $column->SetDateTimeFormat('Y-m-d H:i:s');
            $column->SetOrderable(true);
            $grid->AddViewColumn($column);
            
            //
            // View column for affiliate_url field
            //
            $column = new TextViewColumn('affiliate_url', 'Affiliate Url', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('affiliate_url_handler');
            $grid->AddViewColumn($column);
            
            //
            // View column for login field
            //
            $column = new TextViewColumn('login', 'Login', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddViewColumn($column);
            
            //
            // View column for pwd field
            //
            $column = new TextViewColumn('pwd', 'Pwd', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddViewColumn($column);
        }
    
        protected function AddSingleRecordViewColumns($grid)
        {
            //
            // View column for id field
            //
            $column = new TextViewColumn('id', 'Id', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for hotel_chain field
            //
            $column = new TextViewColumn('hotel_chain', 'Hotel Chain', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('hotel_chain_handler');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for new_openings_url field
            //
            $column = new TextViewColumn('new_openings_url', 'New Openings Url', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('new_openings_url_handler');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for script_id field
            //
            $column = new TextViewColumn('script_id', 'Script Id', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for last_verified_date field
            //
            $column = new DateTimeViewColumn('last_verified_date', 'Last Verified Date', $this->dataset);
            $column->SetDateTimeFormat('Y-m-d H:i:s');
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for affiliate_url field
            //
            $column = new TextViewColumn('affiliate_url', 'Affiliate Url', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('affiliate_url_handler');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for login field
            //
            $column = new TextViewColumn('login', 'Login', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for pwd field
            //
            $column = new TextViewColumn('pwd', 'Pwd', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
        }
    
        protected function AddEditColumns($grid)
        {
            //
            // Edit column for hotel_chain field
            //
            $editor = new TextEdit('hotel_chain_edit');
            $editor->SetSize(80);
            $editColumn = new CustomEditColumn('Hotel Chain', 'hotel_chain', $editor, $this->dataset);
            $validator = new NotEmptyValidator(sprintf($this->GetLocalizerCaptions()->GetMessageString('FieldValueRequiredErrorMsg'), 'Hotel Chain'));
            $editColumn->AddValidator($validator);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for new_openings_url field
            //
            $editor = new TextAreaEdit('new_openings_url_edit', 50, 8);
            $editColumn = new CustomEditColumn('New Openings Url', 'new_openings_url', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for script_id field
            //
            $editor = new TextEdit('script_id_edit');
            $editor->SetSize(20);
            $editColumn = new CustomEditColumn('Script Id', 'script_id', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for last_verified_date field
            //
            $editor = new DateTimeEdit('last_verified_date_edit', true, 'Y-m-d H:i:s');
            $editColumn = new CustomEditColumn('Last Verified Date', 'last_verified_date', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for affiliate_url field
            //
            $editor = new TextAreaEdit('affiliate_url_edit', 50, 8);
            $editColumn = new CustomEditColumn('Affiliate Url', 'affiliate_url', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for login field
            //
            $editor = new TextEdit('login_edit');
            $editor->SetSize(20);
            $editColumn = new CustomEditColumn('Login', 'login', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for pwd field
            //
            $editor = new TextEdit('pwd_edit');
            $editor->SetSize(20);
            $editColumn = new CustomEditColumn('Pwd', 'pwd', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $grid->AddEditColumn($editColumn);
        }
    
        protected function AddInsertColumns($grid)
        {
            //
            // Edit column for hotel_chain field
            //
            $editor = new TextEdit('hotel_chain_edit');
            $editor->SetSize(80);
            $editColumn = new CustomEditColumn('Hotel Chain', 'hotel_chain', $editor, $this->dataset);
            $validator = new NotEmptyValidator(sprintf($this->GetLocalizerCaptions()->GetMessageString('FieldValueRequiredErrorMsg'), 'Hotel Chain'));
            $editColumn->AddValidator($validator);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for new_openings_url field
            //
            $editor = new TextAreaEdit('new_openings_url_edit', 50, 8);
            $editColumn = new CustomEditColumn('New Openings Url', 'new_openings_url', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for script_id field
            //
            $editor = new TextEdit('script_id_edit');
            $editor->SetSize(20);
            $editColumn = new CustomEditColumn('Script Id', 'script_id', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for last_verified_date field
            //
            $editor = new DateTimeEdit('last_verified_date_edit', true, 'Y-m-d H:i:s');
            $editColumn = new CustomEditColumn('Last Verified Date', 'last_verified_date', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for affiliate_url field
            //
            $editor = new TextAreaEdit('affiliate_url_edit', 50, 8);
            $editColumn = new CustomEditColumn('Affiliate Url', 'affiliate_url', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for login field
            //
            $editor = new TextEdit('login_edit');
            $editor->SetSize(20);
            $editColumn = new CustomEditColumn('Login', 'login', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for pwd field
            //
            $editor = new TextEdit('pwd_edit');
            $editor->SetSize(20);
            $editColumn = new CustomEditColumn('Pwd', 'pwd', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $grid->AddInsertColumn($editColumn);
            if ($this->GetSecurityInfo()->HasAddGrant())
                $grid->SetShowAddButton(true);
            else
                $grid->SetShowAddButton(false);
        }
    
        protected function AddPrintColumns($grid)
        {
            //
            // View column for id field
            //
            $column = new TextViewColumn('id', 'Id', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for hotel_chain field
            //
            $column = new TextViewColumn('hotel_chain', 'Hotel Chain', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for new_openings_url field
            //
            $column = new TextViewColumn('new_openings_url', 'New Openings Url', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for script_id field
            //
            $column = new TextViewColumn('script_id', 'Script Id', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for last_verified_date field
            //
            $column = new DateTimeViewColumn('last_verified_date', 'Last Verified Date', $this->dataset);
            $column->SetDateTimeFormat('Y-m-d H:i:s');
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for affiliate_url field
            //
            $column = new TextViewColumn('affiliate_url', 'Affiliate Url', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for login field
            //
            $column = new TextViewColumn('login', 'Login', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for pwd field
            //
            $column = new TextViewColumn('pwd', 'Pwd', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
        }
    
        protected function AddExportColumns($grid)
        {
            //
            // View column for id field
            //
            $column = new TextViewColumn('id', 'Id', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for hotel_chain field
            //
            $column = new TextViewColumn('hotel_chain', 'Hotel Chain', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for new_openings_url field
            //
            $column = new TextViewColumn('new_openings_url', 'New Openings Url', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for script_id field
            //
            $column = new TextViewColumn('script_id', 'Script Id', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for last_verified_date field
            //
            $column = new DateTimeViewColumn('last_verified_date', 'Last Verified Date', $this->dataset);
            $column->SetDateTimeFormat('Y-m-d H:i:s');
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for affiliate_url field
            //
            $column = new TextViewColumn('affiliate_url', 'Affiliate Url', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for login field
            //
            $column = new TextViewColumn('login', 'Login', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for pwd field
            //
            $column = new TextViewColumn('pwd', 'Pwd', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
        }
    
        
        public function ShowEditButtonHandler($show)
        {
            if ($this->GetRecordPermission() != null)
                $show = $this->GetRecordPermission()->HasEditGrant($this->GetDataset());
        }
        public function ShowDeleteButtonHandler($show)
        {
            if ($this->GetRecordPermission() != null)
                $show = $this->GetRecordPermission()->HasDeleteGrant($this->GetDataset());
        }
    
        protected function CreateGrid()
        {
            $result = new Grid($this, $this->dataset, 'affiliate_dataGrid');
            if ($this->GetSecurityInfo()->HasDeleteGrant())
               $result->SetAllowDeleteSelected(false);
            else
               $result->SetAllowDeleteSelected(false);   
            
            ApplyCommonPageSettings($this, $result);
            
            $result->SetHighlightRowAtHover(false);
            $this->CreateGridSearchControl($result);
            $this->CreateGridAdvancedSearchControl($result);
            $this->AddOperationsColumns($result);
            $this->AddFieldColumns($result);
            $this->AddSingleRecordViewColumns($result);
            $this->AddEditColumns($result);
            $this->AddInsertColumns($result);
            $this->AddPrintColumns($result);
            $this->AddExportColumns($result);
    
            $this->SetShowPageList(true);
            $this->SetExportToExcelAvailable(false);
            $this->SetExportToWordAvailable(false);
            $this->SetExportToXmlAvailable(false);
            $this->SetExportToCsvAvailable(false);
            $this->SetExportToPdfAvailable(false);
            $this->SetPrinterFriendlyAvailable(false);
            $this->SetSimpleSearchAvailable(true);
            $this->SetAdvancedSearchAvailable(false);
            $this->SetVisualEffectsEnabled(true);
            $this->SetShowTopPageNavigator(true);
            $this->SetShowBottomPageNavigator(true);
    
            //
            // Http Handlers
            //
            //
            // View column for hotel_chain field
            //
            $column = new TextViewColumn('hotel_chain', 'Hotel Chain', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'hotel_chain_handler', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            //
            // View column for new_openings_url field
            //
            $column = new TextViewColumn('new_openings_url', 'New Openings Url', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'new_openings_url_handler', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            //
            // View column for affiliate_url field
            //
            $column = new TextViewColumn('affiliate_url', 'Affiliate Url', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'affiliate_url_handler', $column);
            GetApplication()->RegisterHTTPHandler($handler);//
            // View column for hotel_chain field
            //
            $column = new TextViewColumn('hotel_chain', 'Hotel Chain', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'hotel_chain_handler', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            //
            // View column for new_openings_url field
            //
            $column = new TextViewColumn('new_openings_url', 'New Openings Url', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'new_openings_url_handler', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            //
            // View column for affiliate_url field
            //
            $column = new TextViewColumn('affiliate_url', 'Affiliate Url', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'affiliate_url_handler', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            return $result;
        }
        
        protected function DoGetGridHeader()
        {
            return '';
        }
    }

    SetUpUserAuthorization(GetApplication());

    try
    {
        $Page = new affiliate_dataPage("affiliate_data.php", "affiliate_data", GetCurrentUserGrantForDataSource("affiliate_data"), 'UTF-8');
        $Page->SetShortCaption('Affiliate Data');
        $Page->SetHeader(GetPagesHeader());
        $Page->SetFooter(GetPagesFooter());
        $Page->SetCaption('Affiliate Data');
        $Page->SetRecordPermission(GetCurrentUserRecordPermissionsForDataSource("affiliate_data"));

        GetApplication()->SetMainPage($Page);
        GetApplication()->Run();
    }
    catch(Exception $e)
    {
        ShowErrorPage($e->getMessage());
    }

?>
