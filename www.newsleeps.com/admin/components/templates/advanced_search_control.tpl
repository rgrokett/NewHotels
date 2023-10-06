<div align="center" id="searchControl" style="display: none; height: 0px;">
<form method="POST" id="AdvancedSearchForm" name="AdvancedSearchForm" style="padding: 0px; margin: 0px;">
<input type="hidden" name="operation" value="asearch" >
<input type="hidden" id="AdvancedSearch" name="AdvancedSearch" value="1">
<input type="hidden" id="ResetFilter" name="ResetFilter" value="0">
<table class="adv_filter">
    <tr class="adv_filter_title"><td colspan="5">{$Captions->GetMessageString('AdvancedSearch')}</td></tr>
    <tr class="adv_filter_type"><td colspan="5">
        {$Captions->GetMessageString('SearchFor')}:
            <input type="radio" name="SearchType" value="and"{if $AdvancedSearchControl->GetIsApplyAndOperator()} checked{/if}>{$Captions->GetMessageString('AllConditions')}
            &nbsp;&nbsp;&nbsp;
            <input type="radio" name="SearchType" value="pr"{if not $AdvancedSearchControl->GetIsApplyAndOperator()} checked{/if}>{$Captions->GetMessageString('AnyCondition')}
    </td></tr>
    <tr class="adv_filter_head">
        <td class="adv_filter_field_head">&nbsp;</td>
        <td class="adv_filter_not_head">{$Captions->GetMessageString('Not')}</td>
        <td colspan="3" class="adv_filter_editors_head">&nbsp;</td>
    </tr>
{foreach item=Column from=$AdvancedSearchControl->GetSearchColumns() name=ColumnsIterator}
    <tr class="adv_filter_row">
        <td class="adv_filter_field_name_cell">{$Column->GetCaption()}</td>
        <td class="adv_filter_not_cell"><input type="checkbox" name="not_{$Column->GetFieldName()}"value="{$FilterTypeIndex}"{if $Column->GetIsApplyNotOperatorIndex()} checked="checked"{/if}></td>
        <td class="adv_filter_operator_cell">
            <select class="sm_comboBox adv_filter_type" style="width: 120px;" ID="AdvSearch_FilterType_{$Column->GetFieldName()}" name="AdvSearch_FilterType_{$Column->GetFieldName()}" size="1"
                onchange="if (document.getElementById('AdvSearch_FilterType_{$Column->GetFieldName()}').value == 'between') document.getElementById('{$Column->GetFieldName()}_second').style.display = ''; else document.getElementById('{$Column->GetFieldName()}_second').style.display = 'none'">
{foreach key=FilterTypeIndex item=FilterTypeName from=$Column->GetAvailableFilterTypes()}
                <option value="{$FilterTypeIndex}"{if $Column->GetActiveFilterIndex() == $FilterTypeIndex} selected{/if}>{$FilterTypeName}</option>
{/foreach}
            </select>
        </td>
        <td class="adv_filter_editor1_cell">
{html_indent value=3 text=$Renderer->Render($Column->GetEditorControl())}
        </td>
        <td class="adv_filter_editor2_cell">
            <span id="{$Column->GetFieldName()}_second">
{html_indent value=4 text=$Renderer->Render($Column->GetSecondEditorControl())}
            </span>
        </td>
    </tr>
{/foreach}
    <tr class="adv_filter_footer">
        <td colspan="5" style="padding: 5px;">
            <input class="sm_button" type="submit" value="{$Captions->GetMessageString('ApplyAdvancedFilter')}"/>
            <input class="sm_button" type="button" value="{$Captions->GetMessageString('ResetAdvancedFilter')}" onclick="javascript: document.forms.AdvancedSearchForm.ResetFilter.value = '1'; document.forms.AdvancedSearchForm.submit();"/>
        </td>
    </tr>
</table>
<script language="javascript">
{foreach item=Column from=$AdvancedSearchControl->GetSearchColumns() name=ColumnsIterator}
    if (document.getElementById('AdvSearch_FilterType_{$Column->GetFieldName()}').value == 'between')
        document.getElementById('{$Column->GetFieldName()}_second').style.display = '';
    else
        document.getElementById('{$Column->GetFieldName()}_second').style.display = 'none'
{/foreach}

{if $AdvancedSearchControl->IsActive()}
$(document).ready(function(){ldelim}
{foreach from=$AdvancedSearchControl->GetHighlightedFields() item=HighlightFieldName name=HighlightFields}
    HighlightTextInGrid('.grid', '{$HighlightFieldName}',
        '{$TextsForHighlight[$smarty.foreach.HighlightFields.index]}',
        '{$HighlightOptions[$smarty.foreach.HighlightFields.index]}');
{/foreach}
{rdelim});    
{/if}

</script>
</form>
</div>
