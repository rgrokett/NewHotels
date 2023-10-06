<form method="post" name="SearchForm" style="padding: 0px; margin: 0px; vertical-align: middle;">
    <input type="hidden" name="operation" value="ssearch">
    <input type="hidden" name="ResetFilter" value="0">
    <b>{$Captions->GetMessageString('SearchFor')}: </b> &nbsp;&nbsp;&nbsp
    <select class="sfilter_comboBox" name="SearchField" id="SearchField">
{foreach key=FieldIndex item=FieldName from=$SearchControl->GetFilteredFields()}
        <option value="{$FieldIndex}"{if $SearchControl->GetActiveFieldName() == $FieldIndex} selected{/if}>{$FieldName}</option>
{/foreach}
    </select>
&nbsp;
    <select class="sfilter_comboBox" name="FilterType" id="FilterType">
{foreach key=FilterTypeIndex item=FilterTypeName from=$SearchControl->GetFilterTypes()}
        <option value="{$FilterTypeIndex}"{if $SearchControl->GetActiveFilterTypeName() == $FilterTypeIndex} selected{/if}>{$FilterTypeName}</option>
{/foreach}
    </select>
    &nbsp;&nbsp;&nbsp;&nbsp;
    <input class="sfilter_text" type="text" size="20" name="FilterText" id="FilterText" value="{$SearchControl->GetActiveFilterText()}">
    &nbsp;
    <input type="submit" class="sm_button" value="{$Captions->GetMessageString('ApplySimpleFilter')}"></span>
    &nbsp;
    <input type="button" class="sm_button" value="{$Captions->GetMessageString('ResetSimpleFilter')}" onclick="javascript: document.forms.SearchForm.ResetFilter.value = '1'; document.forms.SearchForm.submit();">
</form>

<script>
    {if $SearchControl->UseTextHighlight() != ''}
    $(document).ready(function(){ldelim}
    {foreach from=$SearchControl->GetHighlightedFields() item=HighlightFieldName}
        HighlightTextInGrid('.grid', '{$HighlightFieldName}', '{$SearchControl->GetTextForHighlight()}', '{$SearchControl->GetHighlightOption()}');
    {/foreach}
    {rdelim});
    {/if}
</script>