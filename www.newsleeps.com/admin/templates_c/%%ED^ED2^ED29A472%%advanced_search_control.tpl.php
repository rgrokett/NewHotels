<?php /* Smarty version 2.6.19, created on 2009-12-25 14:44:23
         compiled from advanced_search_control.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'html_indent', 'advanced_search_control.tpl', 32, false),)), $this); ?>
<div align="center" id="searchControl" style="display: none; height: 0px;">
<form method="POST" id="AdvancedSearchForm" name="AdvancedSearchForm" style="padding: 0px; margin: 0px;">
<input type="hidden" name="operation" value="asearch" >
<input type="hidden" id="AdvancedSearch" name="AdvancedSearch" value="1">
<input type="hidden" id="ResetFilter" name="ResetFilter" value="0">
<table class="adv_filter">
    <tr class="adv_filter_title"><td colspan="5"><?php echo $this->_tpl_vars['Captions']->GetMessageString('AdvancedSearch'); ?>
</td></tr>
    <tr class="adv_filter_type"><td colspan="5">
        <?php echo $this->_tpl_vars['Captions']->GetMessageString('SearchFor'); ?>
:
            <input type="radio" name="SearchType" value="and"<?php if ($this->_tpl_vars['AdvancedSearchControl']->GetIsApplyAndOperator()): ?> checked<?php endif; ?>><?php echo $this->_tpl_vars['Captions']->GetMessageString('AllConditions'); ?>

            &nbsp;&nbsp;&nbsp;
            <input type="radio" name="SearchType" value="pr"<?php if (! $this->_tpl_vars['AdvancedSearchControl']->GetIsApplyAndOperator()): ?> checked<?php endif; ?>><?php echo $this->_tpl_vars['Captions']->GetMessageString('AnyCondition'); ?>

    </td></tr>
    <tr class="adv_filter_head">
        <td class="adv_filter_field_head">&nbsp;</td>
        <td class="adv_filter_not_head"><?php echo $this->_tpl_vars['Captions']->GetMessageString('Not'); ?>
</td>
        <td colspan="3" class="adv_filter_editors_head">&nbsp;</td>
    </tr>
<?php $_from = $this->_tpl_vars['AdvancedSearchControl']->GetSearchColumns(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['ColumnsIterator'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['ColumnsIterator']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['Column']):
        $this->_foreach['ColumnsIterator']['iteration']++;
?>
    <tr class="adv_filter_row">
        <td class="adv_filter_field_name_cell"><?php echo $this->_tpl_vars['Column']->GetCaption(); ?>
</td>
        <td class="adv_filter_not_cell"><input type="checkbox" name="not_<?php echo $this->_tpl_vars['Column']->GetFieldName(); ?>
"value="<?php echo $this->_tpl_vars['FilterTypeIndex']; ?>
"<?php if ($this->_tpl_vars['Column']->GetIsApplyNotOperatorIndex()): ?> checked="checked"<?php endif; ?>></td>
        <td class="adv_filter_operator_cell">
            <select class="sm_comboBox adv_filter_type" style="width: 120px;" ID="AdvSearch_FilterType_<?php echo $this->_tpl_vars['Column']->GetFieldName(); ?>
" name="AdvSearch_FilterType_<?php echo $this->_tpl_vars['Column']->GetFieldName(); ?>
" size="1"
                onchange="if (document.getElementById('AdvSearch_FilterType_<?php echo $this->_tpl_vars['Column']->GetFieldName(); ?>
').value == 'between') document.getElementById('<?php echo $this->_tpl_vars['Column']->GetFieldName(); ?>
_second').style.display = ''; else document.getElementById('<?php echo $this->_tpl_vars['Column']->GetFieldName(); ?>
_second').style.display = 'none'">
<?php $_from = $this->_tpl_vars['Column']->GetAvailableFilterTypes(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['FilterTypeIndex'] => $this->_tpl_vars['FilterTypeName']):
?>
                <option value="<?php echo $this->_tpl_vars['FilterTypeIndex']; ?>
"<?php if ($this->_tpl_vars['Column']->GetActiveFilterIndex() == $this->_tpl_vars['FilterTypeIndex']): ?> selected<?php endif; ?>><?php echo $this->_tpl_vars['FilterTypeName']; ?>
</option>
<?php endforeach; endif; unset($_from); ?>
            </select>
        </td>
        <td class="adv_filter_editor1_cell">
<?php echo smarty_function_html_indent(array('value' => 3,'text' => $this->_tpl_vars['Renderer']->Render($this->_tpl_vars['Column']->GetEditorControl())), $this);?>

        </td>
        <td class="adv_filter_editor2_cell">
            <span id="<?php echo $this->_tpl_vars['Column']->GetFieldName(); ?>
_second">
<?php echo smarty_function_html_indent(array('value' => 4,'text' => $this->_tpl_vars['Renderer']->Render($this->_tpl_vars['Column']->GetSecondEditorControl())), $this);?>

            </span>
        </td>
    </tr>
<?php endforeach; endif; unset($_from); ?>
    <tr class="adv_filter_footer">
        <td colspan="5" style="padding: 5px;">
            <input class="sm_button" type="submit" value="<?php echo $this->_tpl_vars['Captions']->GetMessageString('ApplyAdvancedFilter'); ?>
"/>
            <input class="sm_button" type="button" value="<?php echo $this->_tpl_vars['Captions']->GetMessageString('ResetAdvancedFilter'); ?>
" onclick="javascript: document.forms.AdvancedSearchForm.ResetFilter.value = '1'; document.forms.AdvancedSearchForm.submit();"/>
        </td>
    </tr>
</table>
<script language="javascript">
<?php $_from = $this->_tpl_vars['AdvancedSearchControl']->GetSearchColumns(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['ColumnsIterator'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['ColumnsIterator']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['Column']):
        $this->_foreach['ColumnsIterator']['iteration']++;
?>
    if (document.getElementById('AdvSearch_FilterType_<?php echo $this->_tpl_vars['Column']->GetFieldName(); ?>
').value == 'between')
        document.getElementById('<?php echo $this->_tpl_vars['Column']->GetFieldName(); ?>
_second').style.display = '';
    else
        document.getElementById('<?php echo $this->_tpl_vars['Column']->GetFieldName(); ?>
_second').style.display = 'none'
<?php endforeach; endif; unset($_from); ?>

<?php if ($this->_tpl_vars['AdvancedSearchControl']->IsActive()): ?>
$(document).ready(function(){
<?php $_from = $this->_tpl_vars['AdvancedSearchControl']->GetHighlightedFields(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['HighlightFields'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['HighlightFields']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['HighlightFieldName']):
        $this->_foreach['HighlightFields']['iteration']++;
?>
    HighlightTextInGrid('.grid', '<?php echo $this->_tpl_vars['HighlightFieldName']; ?>
',
        '<?php echo $this->_tpl_vars['TextsForHighlight'][($this->_foreach['HighlightFields']['iteration']-1)]; ?>
',
        '<?php echo $this->_tpl_vars['HighlightOptions'][($this->_foreach['HighlightFields']['iteration']-1)]; ?>
');
<?php endforeach; endif; unset($_from); ?>
});    
<?php endif; ?>

</script>
</form>
</div>