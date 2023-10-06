<?php /* Smarty version 2.6.19, created on 2009-12-25 14:44:57
         compiled from insert/grid.tpl */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'common/edit_validation_script.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div align="center" style="width: 100%">
    <form name="insertform" enctype="multipart/form-data" method="POST" action="<?php echo $this->_tpl_vars['Grid']->GetEditPageAction(); ?>
">
<?php $_from = $this->_tpl_vars['HiddenValues']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['HiddenValueName'] => $this->_tpl_vars['HiddenValue']):
?>
        <input type="hidden" name="<?php echo $this->_tpl_vars['HiddenValueName']; ?>
" value="<?php echo $this->_tpl_vars['HiddenValue']; ?>
" />
<?php endforeach; endif; unset($_from); ?>
		<table class="grid" style="width: auto">
            <tr><th class="even" colspan="4">
                <?php echo $this->_tpl_vars['Title']; ?>
: <?php echo $this->_tpl_vars['Captions']->GetMessageString('InsertRecord'); ?>

            </th></tr>
            <?php if ($this->_tpl_vars['Grid']->GetErrorMessage() != ''): ?>
            <tr><td class="odd grid_error_row" colspan=4>
                <div class="grid_error_message">
                <strong><?php echo $this->_tpl_vars['Captions']->GetMessageString('ErrorsDuringInsertProcess'); ?>
</strong><br><br>
                <?php echo $this->_tpl_vars['Grid']->GetErrorMessage(); ?>

                </div>
            </td></tr>
            <?php endif; ?>
<?php $_from = $this->_tpl_vars['Grid']->GetInsertColumns(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['Columns'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['Columns']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['column']):
        $this->_foreach['Columns']['iteration']++;
?>
            <tr class="<?php if (!(1 & ($this->_foreach['Columns']['iteration']-1))): ?>even<?php else: ?>odd<?php endif; ?>">
                <td class="even" style="padding-left:20px; font-weight: bold;"><?php echo $this->_tpl_vars['column']->GetCaption(); ?>
<?php if (! $this->_tpl_vars['column']->GetAllowSetToNull()): ?><font color="#FF0000">*</font><?php endif; ?></td>
                <td class="odd" style="border-right-width: 0px; text-align: left; padding-left: 15px;"><?php echo $this->_tpl_vars['Renderer']->Render($this->_tpl_vars['column']); ?>
</td>
                <td class="odd" style="border-right-width: 0px; white-space: nowrap; padding-right: 5px;">
<?php if ($this->_tpl_vars['column']->GetAllowSetToNull()): ?>
    <input type="checkbox" value="1" id="<?php echo $this->_tpl_vars['column']->GetFieldName(); ?>
_null" name="<?php echo $this->_tpl_vars['column']->GetFieldName(); ?>
_null"<?php if ($this->_tpl_vars['column']->IsValueNull()): ?> checked="checked"<?php endif; ?>/><?php echo $this->_tpl_vars['Captions']->GetMessageString('SetNull'); ?>
</div>
<?php endif; ?>
                </td>
                <td class="odd" style="border-right-width: 0px; white-space: nowrap; padding-right: 5px;">
<?php if ($this->_tpl_vars['column']->GetAllowSetToDefault()): ?>
    <input type="checkbox" value="1" name="<?php echo $this->_tpl_vars['column']->GetFieldName(); ?>
_def" id="<?php echo $this->_tpl_vars['column']->GetFieldName(); ?>
_def" <?php if ($this->_tpl_vars['column']->IsValueSetToDefault()): ?> checked="checked"<?php endif; ?>/><?php echo $this->_tpl_vars['Captions']->GetMessageString('SetDefault'); ?>
</div>
<?php endif; ?>
            </td>
            </tr>
<?php endforeach; endif; unset($_from); ?>
            <tr class="editor_buttons">
                <td colspan="4" style="text-align: left" valign="middle">
                    <font color="#FF0000">*</font> - <?php echo $this->_tpl_vars['Captions']->GetMessageString('RequiredField'); ?>

                </td>
            </tr>
            <tr id="errorMessagesRow" style="display: none;"><td class="odd grid_error_row" colspan="4">
                <div class="grid_error_message">
                <strong><?php echo $this->_tpl_vars['Captions']->GetMessageString('ClientValidationsErrors'); ?>
</strong><br>
                <div id="errorMessages"></div>
                </div>
            </td></tr>
            <tr height="40" class="editor_buttons"><td colspan="4" align="center" valign="top">
                <input class="sm_button" type="button" value="<?php echo $this->_tpl_vars['Captions']->GetMessageString('SaveNewRecord'); ?>
" name="submit1" onclick="if (ValidateControls()) document.insertform.submit();"/>
                <input class="sm_button" type="reset" value="<?php echo $this->_tpl_vars['Captions']->GetMessageString('BackToList'); ?>
" onclick="window.location.href='<?php echo $this->_tpl_vars['Grid']->GetReturnUrl(); ?>
'"/>
            </td></tr>
        </table>
    </form>
</div>