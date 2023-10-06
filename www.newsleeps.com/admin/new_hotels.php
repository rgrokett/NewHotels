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
    
    class new_hotelsPage extends Page
    {
        protected function DoBeforeCreate()
        {
            $this->dataset = new TableDataset(
                new MySqlIConnectionFactory(),
                GetConnectionOptions(),
                '`new_hotels`');
            $field = new IntegerField('id', null, null, true);
            $this->dataset->AddField($field, true);
            $field = new StringField('name');
            $this->dataset->AddField($field, false);
            $field = new StringField('website');
            $this->dataset->AddField($field, false);
            $field = new StringField('url');
            $this->dataset->AddField($field, false);
            $field = new StringField('address1');
            $this->dataset->AddField($field, false);
            $field = new StringField('address2');
            $this->dataset->AddField($field, false);
            $field = new StringField('city');
            $this->dataset->AddField($field, false);
            $field = new StringField('state_prov');
            $this->dataset->AddField($field, false);
            $field = new StringField('country');
            $this->dataset->AddField($field, false);
            $field = new StringField('postal_code');
            $this->dataset->AddField($field, false);
            $field = new StringField('map_url');
            $this->dataset->AddField($field, false);
            $field = new StringField('phone');
            $this->dataset->AddField($field, false);
            $field = new StringField('hotel_chain');
            $this->dataset->AddField($field, false);
            $field = new StringField('hotel_type');
            $this->dataset->AddField($field, false);
            $field = new DateField('open_date');
            $this->dataset->AddField($field, false);
            $field = new IntegerField('rating');
            $this->dataset->AddField($field, false);
            $field = new StringField('photo_url');
            $this->dataset->AddField($field, false);
            $field = new DateTimeField('last_verified_date');
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
            return $result;
        }
    
        protected function CreateGridSearchControl($grid)
        {
            $grid->UseFilter = true;
            $grid->SearchControl = new SimpleSearch('new_hotelsssearch', $this->dataset,
                array('id', 'name', 'website', 'url', 'address1', 'address2', 'city', 'state_prov', 'country', 'postal_code', 'map_url', 'phone', 'hotel_chain', 'hotel_type', 'open_date', 'rating', 'photo_url', 'last_verified_date'),
                array($this->RenderText('Id'), $this->RenderText('Name'), $this->RenderText('Website'), $this->RenderText('Url'), $this->RenderText('Address1'), $this->RenderText('Address2'), $this->RenderText('City'), $this->RenderText('State Prov'), $this->RenderText('Country'), $this->RenderText('Postal Code'), $this->RenderText('Map Url'), $this->RenderText('Phone'), $this->RenderText('Hotel Chain'), $this->RenderText('Hotel Type'), $this->RenderText('Open Date'), $this->RenderText('Rating'), $this->RenderText('Photo Url'), $this->RenderText('Last Verified Date')),
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
            $this->AdvancedSearchControl = new AdvancedSearchControl('new_hotelsasearch', $this->dataset);
            $this->AdvancedSearchControl->AddSearchColumn(new StringSearchColumn('id', $this->RenderText('Id'), $this->GetLocalizerCaptions()));
            $this->AdvancedSearchControl->AddSearchColumn(new StringSearchColumn('name', $this->RenderText('Name'), $this->GetLocalizerCaptions()));
            $this->AdvancedSearchControl->AddSearchColumn(new StringSearchColumn('website', $this->RenderText('Website'), $this->GetLocalizerCaptions()));
            $this->AdvancedSearchControl->AddSearchColumn(new StringSearchColumn('url', $this->RenderText('Url'), $this->GetLocalizerCaptions()));
            $this->AdvancedSearchControl->AddSearchColumn(new StringSearchColumn('address1', $this->RenderText('Address1'), $this->GetLocalizerCaptions()));
            $this->AdvancedSearchControl->AddSearchColumn(new StringSearchColumn('address2', $this->RenderText('Address2'), $this->GetLocalizerCaptions()));
            $this->AdvancedSearchControl->AddSearchColumn(new StringSearchColumn('city', $this->RenderText('City'), $this->GetLocalizerCaptions()));
            $this->AdvancedSearchControl->AddSearchColumn(new StringSearchColumn('state_prov', $this->RenderText('State Prov'), $this->GetLocalizerCaptions()));
            $this->AdvancedSearchControl->AddSearchColumn(new StringSearchColumn('country', $this->RenderText('Country'), $this->GetLocalizerCaptions()));
            $this->AdvancedSearchControl->AddSearchColumn(new StringSearchColumn('postal_code', $this->RenderText('Postal Code'), $this->GetLocalizerCaptions()));
            $this->AdvancedSearchControl->AddSearchColumn(new StringSearchColumn('map_url', $this->RenderText('Map Url'), $this->GetLocalizerCaptions()));
            $this->AdvancedSearchControl->AddSearchColumn(new StringSearchColumn('phone', $this->RenderText('Phone'), $this->GetLocalizerCaptions()));
            $this->AdvancedSearchControl->AddSearchColumn(new StringSearchColumn('hotel_chain', $this->RenderText('Hotel Chain'), $this->GetLocalizerCaptions()));
            $this->AdvancedSearchControl->AddSearchColumn(new StringSearchColumn('hotel_type', $this->RenderText('Hotel Type'), $this->GetLocalizerCaptions()));
            $this->AdvancedSearchControl->AddSearchColumn(new StringSearchColumn('open_date', $this->RenderText('Open Date'), $this->GetLocalizerCaptions()));
            $this->AdvancedSearchControl->AddSearchColumn(new StringSearchColumn('rating', $this->RenderText('Rating'), $this->GetLocalizerCaptions()));
            $this->AdvancedSearchControl->AddSearchColumn(new StringSearchColumn('photo_url', $this->RenderText('Photo Url'), $this->GetLocalizerCaptions()));
            $this->AdvancedSearchControl->AddSearchColumn(new StringSearchColumn('last_verified_date', $this->RenderText('Last Verified Date'), $this->GetLocalizerCaptions()));
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
            // View column for name field
            //
            $column = new TextViewColumn('name', 'Name', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('name_handler');
            $grid->AddViewColumn($column);
            
            //
            // View column for website field
            //
            $column = new TextViewColumn('website', 'Website', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('website_handler');
            $grid->AddViewColumn($column);
            
            //
            // View column for url field
            //
            $column = new TextViewColumn('url', 'Url', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('url_handler');
            $grid->AddViewColumn($column);
            
            //
            // View column for address1 field
            //
            $column = new TextViewColumn('address1', 'Address1', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('address1_handler');
            $grid->AddViewColumn($column);
            
            //
            // View column for address2 field
            //
            $column = new TextViewColumn('address2', 'Address2', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('address2_handler');
            $grid->AddViewColumn($column);
            
            //
            // View column for city field
            //
            $column = new TextViewColumn('city', 'City', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddViewColumn($column);
            
            //
            // View column for state_prov field
            //
            $column = new TextViewColumn('state_prov', 'State Prov', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddViewColumn($column);
            
            //
            // View column for country field
            //
            $column = new TextViewColumn('country', 'Country', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddViewColumn($column);
            
            //
            // View column for postal_code field
            //
            $column = new TextViewColumn('postal_code', 'Postal Code', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddViewColumn($column);
            
            //
            // View column for map_url field
            //
            $column = new TextViewColumn('map_url', 'Map Url', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('map_url_handler');
            $grid->AddViewColumn($column);
            
            //
            // View column for phone field
            //
            $column = new TextViewColumn('phone', 'Phone', $this->dataset);
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
            // View column for hotel_type field
            //
            $column = new TextViewColumn('hotel_type', 'Hotel Type', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddViewColumn($column);
            
            //
            // View column for open_date field
            //
            $column = new DateTimeViewColumn('open_date', 'Open Date', $this->dataset);
            $column->SetDateTimeFormat('Y-m-d H:i:s');
            $column->SetOrderable(true);
            $grid->AddViewColumn($column);
            
            //
            // View column for rating field
            //
            $column = new TextViewColumn('rating', 'Rating', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddViewColumn($column);
            
            //
            // View column for photo_url field
            //
            $column = new TextViewColumn('photo_url', 'Photo Url', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('photo_url_handler');
            $grid->AddViewColumn($column);
            
            //
            // View column for last_verified_date field
            //
            $column = new DateTimeViewColumn('last_verified_date', 'Last Verified Date', $this->dataset);
            $column->SetDateTimeFormat('Y-m-d H:i:s');
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
            // View column for name field
            //
            $column = new TextViewColumn('name', 'Name', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('name_handler');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for website field
            //
            $column = new TextViewColumn('website', 'Website', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('website_handler');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for url field
            //
            $column = new TextViewColumn('url', 'Url', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('url_handler');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for address1 field
            //
            $column = new TextViewColumn('address1', 'Address1', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('address1_handler');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for address2 field
            //
            $column = new TextViewColumn('address2', 'Address2', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('address2_handler');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for city field
            //
            $column = new TextViewColumn('city', 'City', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for state_prov field
            //
            $column = new TextViewColumn('state_prov', 'State Prov', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for country field
            //
            $column = new TextViewColumn('country', 'Country', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for postal_code field
            //
            $column = new TextViewColumn('postal_code', 'Postal Code', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for map_url field
            //
            $column = new TextViewColumn('map_url', 'Map Url', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('map_url_handler');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for phone field
            //
            $column = new TextViewColumn('phone', 'Phone', $this->dataset);
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
            // View column for hotel_type field
            //
            $column = new TextViewColumn('hotel_type', 'Hotel Type', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for open_date field
            //
            $column = new DateTimeViewColumn('open_date', 'Open Date', $this->dataset);
            $column->SetDateTimeFormat('Y-m-d H:i:s');
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for rating field
            //
            $column = new TextViewColumn('rating', 'Rating', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for photo_url field
            //
            $column = new TextViewColumn('photo_url', 'Photo Url', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('photo_url_handler');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for last_verified_date field
            //
            $column = new DateTimeViewColumn('last_verified_date', 'Last Verified Date', $this->dataset);
            $column->SetDateTimeFormat('Y-m-d H:i:s');
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
        }
    
        protected function AddEditColumns($grid)
        {
            //
            // Edit column for name field
            //
            $editor = new TextEdit('name_edit');
            $editor->SetSize(80);
            $editColumn = new CustomEditColumn('Name', 'name', $editor, $this->dataset);
            $validator = new NotEmptyValidator(sprintf($this->GetLocalizerCaptions()->GetMessageString('FieldValueRequiredErrorMsg'), 'Name'));
            $editColumn->AddValidator($validator);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for website field
            //
            $editor = new TextEdit('website_edit');
            $editor->SetSize(80);
            $editColumn = new CustomEditColumn('Website', 'website', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for url field
            //
            $editor = new TextAreaEdit('url_edit', 50, 8);
            $editColumn = new CustomEditColumn('Url', 'url', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for address1 field
            //
            $editor = new TextEdit('address1_edit');
            $editor->SetSize(100);
            $editColumn = new CustomEditColumn('Address1', 'address1', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for address2 field
            //
            $editor = new TextEdit('address2_edit');
            $editor->SetSize(100);
            $editColumn = new CustomEditColumn('Address2', 'address2', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for city field
            //
            $editor = new TextEdit('city_edit');
            $editor->SetSize(40);
            $editColumn = new CustomEditColumn('City', 'city', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for state_prov field
            //
            $editor = new TextEdit('state_prov_edit');
            $editor->SetSize(40);
            $editColumn = new CustomEditColumn('State Prov', 'state_prov', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for country field
            //
            $editor = new TextEdit('country_edit');
            $editor->SetSize(40);
            $editColumn = new CustomEditColumn('Country', 'country', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for postal_code field
            //
            $editor = new TextEdit('postal_code_edit');
            $editor->SetSize(20);
            $editColumn = new CustomEditColumn('Postal Code', 'postal_code', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for map_url field
            //
            $editor = new TextAreaEdit('map_url_edit', 50, 8);
            $editColumn = new CustomEditColumn('Map Url', 'map_url', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for phone field
            //
            $editor = new TextEdit('phone_edit');
            $editor->SetSize(20);
            $editColumn = new CustomEditColumn('Phone', 'phone', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for hotel_chain field
            //
            $editor = new TextEdit('hotel_chain_edit');
            $editor->SetSize(80);
            $editColumn = new CustomEditColumn('Hotel Chain', 'hotel_chain', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for hotel_type field
            //
            $editor = new TextEdit('hotel_type_edit');
            $editor->SetSize(20);
            $editColumn = new CustomEditColumn('Hotel Type', 'hotel_type', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for open_date field
            //
            $editor = new DateTimeEdit('open_date_edit', true, 'Y-m-d H:i:s');
            $editColumn = new CustomEditColumn('Open Date', 'open_date', $editor, $this->dataset);
            $validator = new NotEmptyValidator(sprintf($this->GetLocalizerCaptions()->GetMessageString('FieldValueRequiredErrorMsg'), 'Open Date'));
            $editColumn->AddValidator($validator);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for rating field
            //
            $editor = new TextEdit('rating_edit');
            $editColumn = new CustomEditColumn('Rating', 'rating', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for photo_url field
            //
            $editor = new TextAreaEdit('photo_url_edit', 50, 8);
            $editColumn = new CustomEditColumn('Photo Url', 'photo_url', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for last_verified_date field
            //
            $editor = new DateTimeEdit('last_verified_date_edit', true, 'Y-m-d H:i:s');
            $editColumn = new CustomEditColumn('Last Verified Date', 'last_verified_date', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $grid->AddEditColumn($editColumn);
        }
    
        protected function AddInsertColumns($grid)
        {
            //
            // Edit column for name field
            //
            $editor = new TextEdit('name_edit');
            $editor->SetSize(80);
            $editColumn = new CustomEditColumn('Name', 'name', $editor, $this->dataset);
            $validator = new NotEmptyValidator(sprintf($this->GetLocalizerCaptions()->GetMessageString('FieldValueRequiredErrorMsg'), 'Name'));
            $editColumn->AddValidator($validator);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for website field
            //
            $editor = new TextEdit('website_edit');
            $editor->SetSize(80);
            $editColumn = new CustomEditColumn('Website', 'website', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for url field
            //
            $editor = new TextAreaEdit('url_edit', 50, 8);
            $editColumn = new CustomEditColumn('Url', 'url', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for address1 field
            //
            $editor = new TextEdit('address1_edit');
            $editor->SetSize(100);
            $editColumn = new CustomEditColumn('Address1', 'address1', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for address2 field
            //
            $editor = new TextEdit('address2_edit');
            $editor->SetSize(100);
            $editColumn = new CustomEditColumn('Address2', 'address2', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for city field
            //
            $editor = new TextEdit('city_edit');
            $editor->SetSize(40);
            $editColumn = new CustomEditColumn('City', 'city', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for state_prov field
            //
            $editor = new TextEdit('state_prov_edit');
            $editor->SetSize(40);
            $editColumn = new CustomEditColumn('State Prov', 'state_prov', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for country field
            //
            $editor = new TextEdit('country_edit');
            $editor->SetSize(40);
            $editColumn = new CustomEditColumn('Country', 'country', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for postal_code field
            //
            $editor = new TextEdit('postal_code_edit');
            $editor->SetSize(20);
            $editColumn = new CustomEditColumn('Postal Code', 'postal_code', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for map_url field
            //
            $editor = new TextAreaEdit('map_url_edit', 50, 8);
            $editColumn = new CustomEditColumn('Map Url', 'map_url', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for phone field
            //
            $editor = new TextEdit('phone_edit');
            $editor->SetSize(20);
            $editColumn = new CustomEditColumn('Phone', 'phone', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for hotel_chain field
            //
            $editor = new TextEdit('hotel_chain_edit');
            $editor->SetSize(80);
            $editColumn = new CustomEditColumn('Hotel Chain', 'hotel_chain', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for hotel_type field
            //
            $editor = new TextEdit('hotel_type_edit');
            $editor->SetSize(20);
            $editColumn = new CustomEditColumn('Hotel Type', 'hotel_type', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for open_date field
            //
            $editor = new DateTimeEdit('open_date_edit', true, 'Y-m-d H:i:s');
            $editColumn = new CustomEditColumn('Open Date', 'open_date', $editor, $this->dataset);
            $editColumn->SetAllowSetToDefault(true);
            $validator = new NotEmptyValidator(sprintf($this->GetLocalizerCaptions()->GetMessageString('FieldValueRequiredErrorMsg'), 'Open Date'));
            $editColumn->AddValidator($validator);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for rating field
            //
            $editor = new TextEdit('rating_edit');
            $editColumn = new CustomEditColumn('Rating', 'rating', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for photo_url field
            //
            $editor = new TextAreaEdit('photo_url_edit', 50, 8);
            $editColumn = new CustomEditColumn('Photo Url', 'photo_url', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for last_verified_date field
            //
            $editor = new DateTimeEdit('last_verified_date_edit', true, 'Y-m-d H:i:s');
            $editColumn = new CustomEditColumn('Last Verified Date', 'last_verified_date', $editor, $this->dataset);
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
            // View column for name field
            //
            $column = new TextViewColumn('name', 'Name', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for website field
            //
            $column = new TextViewColumn('website', 'Website', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for url field
            //
            $column = new TextViewColumn('url', 'Url', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for address1 field
            //
            $column = new TextViewColumn('address1', 'Address1', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for address2 field
            //
            $column = new TextViewColumn('address2', 'Address2', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for city field
            //
            $column = new TextViewColumn('city', 'City', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for state_prov field
            //
            $column = new TextViewColumn('state_prov', 'State Prov', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for country field
            //
            $column = new TextViewColumn('country', 'Country', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for postal_code field
            //
            $column = new TextViewColumn('postal_code', 'Postal Code', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for map_url field
            //
            $column = new TextViewColumn('map_url', 'Map Url', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for phone field
            //
            $column = new TextViewColumn('phone', 'Phone', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for hotel_chain field
            //
            $column = new TextViewColumn('hotel_chain', 'Hotel Chain', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for hotel_type field
            //
            $column = new TextViewColumn('hotel_type', 'Hotel Type', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for open_date field
            //
            $column = new DateTimeViewColumn('open_date', 'Open Date', $this->dataset);
            $column->SetDateTimeFormat('Y-m-d H:i:s');
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for rating field
            //
            $column = new TextViewColumn('rating', 'Rating', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for photo_url field
            //
            $column = new TextViewColumn('photo_url', 'Photo Url', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for last_verified_date field
            //
            $column = new DateTimeViewColumn('last_verified_date', 'Last Verified Date', $this->dataset);
            $column->SetDateTimeFormat('Y-m-d H:i:s');
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
            // View column for name field
            //
            $column = new TextViewColumn('name', 'Name', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for website field
            //
            $column = new TextViewColumn('website', 'Website', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for url field
            //
            $column = new TextViewColumn('url', 'Url', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for address1 field
            //
            $column = new TextViewColumn('address1', 'Address1', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for address2 field
            //
            $column = new TextViewColumn('address2', 'Address2', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for city field
            //
            $column = new TextViewColumn('city', 'City', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for state_prov field
            //
            $column = new TextViewColumn('state_prov', 'State Prov', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for country field
            //
            $column = new TextViewColumn('country', 'Country', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for postal_code field
            //
            $column = new TextViewColumn('postal_code', 'Postal Code', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for map_url field
            //
            $column = new TextViewColumn('map_url', 'Map Url', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for phone field
            //
            $column = new TextViewColumn('phone', 'Phone', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for hotel_chain field
            //
            $column = new TextViewColumn('hotel_chain', 'Hotel Chain', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for hotel_type field
            //
            $column = new TextViewColumn('hotel_type', 'Hotel Type', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for open_date field
            //
            $column = new DateTimeViewColumn('open_date', 'Open Date', $this->dataset);
            $column->SetDateTimeFormat('Y-m-d H:i:s');
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for rating field
            //
            $column = new TextViewColumn('rating', 'Rating', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for photo_url field
            //
            $column = new TextViewColumn('photo_url', 'Photo Url', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for last_verified_date field
            //
            $column = new DateTimeViewColumn('last_verified_date', 'Last Verified Date', $this->dataset);
            $column->SetDateTimeFormat('Y-m-d H:i:s');
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
            $result = new Grid($this, $this->dataset, 'new_hotelsGrid');
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
            // View column for name field
            //
            $column = new TextViewColumn('name', 'Name', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'name_handler', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            //
            // View column for website field
            //
            $column = new TextViewColumn('website', 'Website', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'website_handler', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            //
            // View column for url field
            //
            $column = new TextViewColumn('url', 'Url', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'url_handler', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            //
            // View column for address1 field
            //
            $column = new TextViewColumn('address1', 'Address1', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'address1_handler', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            //
            // View column for address2 field
            //
            $column = new TextViewColumn('address2', 'Address2', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'address2_handler', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            //
            // View column for map_url field
            //
            $column = new TextViewColumn('map_url', 'Map Url', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'map_url_handler', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            //
            // View column for hotel_chain field
            //
            $column = new TextViewColumn('hotel_chain', 'Hotel Chain', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'hotel_chain_handler', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            //
            // View column for photo_url field
            //
            $column = new TextViewColumn('photo_url', 'Photo Url', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'photo_url_handler', $column);
            GetApplication()->RegisterHTTPHandler($handler);//
            // View column for name field
            //
            $column = new TextViewColumn('name', 'Name', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'name_handler', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            //
            // View column for website field
            //
            $column = new TextViewColumn('website', 'Website', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'website_handler', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            //
            // View column for url field
            //
            $column = new TextViewColumn('url', 'Url', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'url_handler', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            //
            // View column for address1 field
            //
            $column = new TextViewColumn('address1', 'Address1', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'address1_handler', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            //
            // View column for address2 field
            //
            $column = new TextViewColumn('address2', 'Address2', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'address2_handler', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            //
            // View column for map_url field
            //
            $column = new TextViewColumn('map_url', 'Map Url', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'map_url_handler', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            //
            // View column for hotel_chain field
            //
            $column = new TextViewColumn('hotel_chain', 'Hotel Chain', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'hotel_chain_handler', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            //
            // View column for photo_url field
            //
            $column = new TextViewColumn('photo_url', 'Photo Url', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'photo_url_handler', $column);
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
        $Page = new new_hotelsPage("new_hotels.php", "new_hotels", GetCurrentUserGrantForDataSource("new_hotels"), 'UTF-8');
        $Page->SetShortCaption('New Hotels');
        $Page->SetHeader(GetPagesHeader());
        $Page->SetFooter(GetPagesFooter());
        $Page->SetCaption('New Hotels');
        $Page->SetRecordPermission(GetCurrentUserRecordPermissionsForDataSource("new_hotels"));

        GetApplication()->SetMainPage($Page);
        GetApplication()->Run();
    }
    catch(Exception $e)
    {
        ShowErrorPage($e->getMessage());
    }

?>
