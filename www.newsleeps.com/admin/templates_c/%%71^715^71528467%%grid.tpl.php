<?php /* Smarty version 2.6.19, created on 2009-12-25 14:44:23
         compiled from list/grid.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'counter', 'list/grid.tpl', 25, false),)), $this); ?>
<?php if ($this->_tpl_vars['UseFilter']): ?>
<?php echo '<div class="grid grid_menu" style="width: auto; padding: 10px; margin-top: 10px;">'; ?><?php echo $this->_tpl_vars['SearchControl']; ?><?php echo '</div><br/>'; ?>

<?php endif; ?>

<?php if ($this->_tpl_vars['AllowDeleteSelected']): ?>
<form name="selectedRecords" method="POST" action="<?php echo $this->_tpl_vars['Grid']->GetDeleteSelectedLink(); ?>
">
    <input type="hidden" name="operation" value="delsel">
    <input type="hidden" name="recordCount" value="<?php echo $this->_tpl_vars['RecordCount']; ?>
">
<?php endif; ?>

<?php if ($this->_tpl_vars['Grid']->GetHighlightRowAtHover()): ?><script> EnableHighlightRowAtHover('.grid'); </script><?php endif; ?>


<table class="grid">
<?php if ($this->_tpl_vars['Grid']->GetShowAddButton() || $this->_tpl_vars['AllowDeleteSelected'] || $this->_tpl_vars['Grid']->GetShowUpdateLink()): ?>
    <tr>
        <?php echo '<td colspan="'; ?><?php echo $this->_tpl_vars['ColumnCount']; ?><?php echo '" class="grid_menu">'; ?><?php echo smarty_function_counter(array('start' => 0,'assign' => 'grid_menu_links'), $this);?><?php echo ''; ?><?php if ($this->_tpl_vars['Grid']->GetShowAddButton()): ?><?php echo ''; ?><?php if ($this->_tpl_vars['grid_menu_links'] > 0): ?><?php echo '|'; ?><?php endif; ?><?php echo '<a class="grid_menu_link" href="'; ?><?php echo $this->_tpl_vars['Grid']->GetAddRecordLink(); ?><?php echo '">'; ?><?php echo $this->_tpl_vars['Captions']->GetMessageString('AddNewRecord'); ?><?php echo '</a>'; ?><?php echo smarty_function_counter(array('assign' => 'grid_menu_links'), $this);?><?php echo ''; ?><?php endif; ?><?php echo ''; ?><?php if ($this->_tpl_vars['AllowDeleteSelected']): ?><?php echo ''; ?><?php if ($this->_tpl_vars['grid_menu_links'] > 0): ?><?php echo '|'; ?><?php endif; ?><?php echo '<a class="grid_menu_link" href="" onclick="if (confirm(\'Delete records?\')) document.selectedRecords.submit(); return false;">'; ?><?php echo $this->_tpl_vars['Captions']->GetMessageString('DeleteSelected'); ?><?php echo '</a>'; ?><?php echo smarty_function_counter(array('assign' => 'grid_menu_links'), $this);?><?php echo ''; ?><?php endif; ?><?php echo ''; ?><?php if ($this->_tpl_vars['Grid']->GetShowUpdateLink()): ?><?php echo ''; ?><?php if ($this->_tpl_vars['grid_menu_links'] > 0): ?><?php echo '|'; ?><?php endif; ?><?php echo '<a class="grid_menu_link" href="'; ?><?php echo $this->_tpl_vars['Grid']->GetUpdateLink(); ?><?php echo '">'; ?><?php echo $this->_tpl_vars['Captions']->GetMessageString('Refresh'); ?><?php echo '</a>'; ?><?php echo smarty_function_counter(array('assign' => 'grid_menu_links'), $this);?><?php echo ''; ?><?php endif; ?><?php echo '</td>'; ?>

    </tr>
<?php endif; ?>
    <?php if ($this->_tpl_vars['Grid']->GetErrorMessage() != ''): ?>
    <tr><td class="odd grid_error_row" colspan="<?php echo $this->_tpl_vars['ColumnCount']; ?>
" >
        <div class="grid_error_message">
        <strong><?php echo $this->_tpl_vars['Captions']->GetMessageString('ErrorsDuringDeleteProcess'); ?>
</strong><br><br>
        <?php echo $this->_tpl_vars['Grid']->GetErrorMessage(); ?>

        </div>
    </td></tr>
    <?php endif; ?>

    <!-- <Grid Head> -->
    <tr>
        <?php if ($this->_tpl_vars['AllowDeleteSelected']): ?>
            <th class="odd">
                <input type="checkbox" name="rec<?php echo ($this->_foreach['RowsGrid']['iteration']-1); ?>
" onClick="var i; for(i = 0; i < <?php echo $this->_tpl_vars['RecordCount']; ?>
; i++) document.getElementById('rec' + i).checked = this.checked">
            </th>
        <?php endif; ?>
        <!-- <Grid Head Columns> -->
        <?php $_from = $this->_tpl_vars['Columns']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['Header'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['Header']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['Column']):
        $this->_foreach['Header']['iteration']++;
?>
            <?php echo '<th class="'; ?><?php if (!(1 & ($this->_foreach['Header']['iteration']-1))): ?><?php echo 'even'; ?><?php else: ?><?php echo 'odd'; ?><?php endif; ?><?php echo '"'; ?><?php if ($this->_tpl_vars['HeadColumnsStyles'][($this->_foreach['Header']['iteration']-1)] != ''): ?><?php echo ' style="'; ?><?php echo $this->_tpl_vars['HeadColumnsStyles'][($this->_foreach['Header']['iteration']-1)]; ?><?php echo '"'; ?><?php endif; ?><?php echo '>'; ?><?php echo $this->_tpl_vars['Renderer']->Render($this->_tpl_vars['Column']->GetHeaderControl()); ?><?php echo '</th>'; ?>

        <?php endforeach; endif; unset($_from); ?>
        <!-- </Grid Head Columns> -->
    </tr>

    <!-- </Grid Head> -->
<?php if (count ( $this->_tpl_vars['Rows'] ) > 0): ?>
    <?php $_from = $this->_tpl_vars['Rows']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['RowsGrid'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['RowsGrid']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['Row']):
        $this->_foreach['RowsGrid']['iteration']++;
?>
    <tr class="<?php if (!(1 & ($this->_foreach['RowsGrid']['iteration']-1))): ?>even<?php else: ?>odd<?php endif; ?>"<?php if ($this->_tpl_vars['RowCssStyles'][($this->_foreach['RowsGrid']['iteration']-1)] != ''): ?> style="<?php echo $this->_tpl_vars['RowCssStyles'][($this->_foreach['RowsGrid']['iteration']-1)]; ?>
"<?php endif; ?>>
        <?php if ($this->_tpl_vars['AllowDeleteSelected']): ?>
        <?php echo '<td class="odd" '; ?><?php if ($this->_tpl_vars['RowCssStyles'][($this->_foreach['RowsGrid']['iteration']-1)] != ''): ?><?php echo ' style="'; ?><?php echo $this->_tpl_vars['RowCssStyles'][($this->_foreach['RowsGrid']['iteration']-1)]; ?><?php echo '"'; ?><?php endif; ?><?php echo '><input type="checkbox" name="rec'; ?><?php echo ($this->_foreach['RowsGrid']['iteration']-1); ?><?php echo '" id="rec'; ?><?php echo ($this->_foreach['RowsGrid']['iteration']-1); ?><?php echo '">'; ?><?php $_from = $this->_tpl_vars['RowPrimaryKeys'][($this->_foreach['RowsGrid']['iteration']-1)]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['CPkValues'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['CPkValues']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['PkValue']):
        $this->_foreach['CPkValues']['iteration']++;
?><?php echo '<input type="hidden" name="rec'; ?><?php echo ($this->_foreach['RowsGrid']['iteration']-1); ?><?php echo '_pk'; ?><?php echo ($this->_foreach['CPkValues']['iteration']-1); ?><?php echo '" value="'; ?><?php echo $this->_tpl_vars['PkValue']; ?><?php echo '">'; ?><?php endforeach; endif; unset($_from); ?><?php echo '</td>'; ?>

        <?php endif; ?>

        <?php $_from = $this->_tpl_vars['Row']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['RowColumns'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['RowColumns']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['RowColumn']):
        $this->_foreach['RowColumns']['iteration']++;
?>
        <?php echo '<td data-column-name="'; ?><?php echo $this->_tpl_vars['ColumnsNames'][($this->_foreach['RowColumns']['iteration']-1)]; ?><?php echo '" char="'; ?><?php echo $this->_tpl_vars['RowColumnsChars'][($this->_foreach['RowsGrid']['iteration']-1)][($this->_foreach['RowColumns']['iteration']-1)]; ?><?php echo '" class="'; ?><?php if (!(1 & ($this->_foreach['RowColumns']['iteration']-1))): ?><?php echo 'even'; ?><?php else: ?><?php echo 'odd'; ?><?php endif; ?><?php echo '"'; ?><?php if ($this->_tpl_vars['RowColumnsCssStyles'][($this->_foreach['RowsGrid']['iteration']-1)][($this->_foreach['RowColumns']['iteration']-1)] != ''): ?><?php echo ' style="'; ?><?php echo $this->_tpl_vars['RowColumnsCssStyles'][($this->_foreach['RowsGrid']['iteration']-1)][($this->_foreach['RowColumns']['iteration']-1)]; ?><?php echo '"'; ?><?php endif; ?><?php echo '>'; ?><?php echo $this->_tpl_vars['RowColumn']; ?><?php echo '</td>'; ?>

        <?php endforeach; endif; unset($_from); ?>
    </tr>
    
    <?php echo '<tr style="border: none; height: 0px;"><td colspan="'; ?><?php echo $this->_tpl_vars['ColumnCount']; ?><?php echo '" style="border: none; padding: 0px; height: 0px;">'; ?><?php $_from = $this->_tpl_vars['AfterRows'][($this->_foreach['RowsGrid']['iteration']-1)]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['AfterRow']):
?><?php echo ''; ?><?php echo $this->_tpl_vars['AfterRow']; ?><?php echo ''; ?><?php endforeach; endif; unset($_from); ?><?php echo '</td></tr>'; ?>

    
    <?php endforeach; endif; unset($_from); ?>
<?php else: ?> <?php echo '<tr><td colspan="'; ?><?php echo $this->_tpl_vars['ColumnCount']; ?><?php echo '" class="emplygrid">'; ?><?php echo $this->_tpl_vars['Captions']->GetMessageString('NoDataToDisplay'); ?><?php echo '</td></tr>'; ?>

<?php endif; ?> <?php if ($this->_tpl_vars['Grid']->GetShowAddButton() || $this->_tpl_vars['AllowDeleteSelected'] || $this->_tpl_vars['Grid']->GetShowUpdateLink()): ?>
    <tr>
        <?php echo '<td colspan="'; ?><?php echo $this->_tpl_vars['ColumnCount']; ?><?php echo '" class="grid_menu">'; ?><?php echo smarty_function_counter(array('start' => 0,'assign' => 'grid_menu_links'), $this);?><?php echo ''; ?><?php if ($this->_tpl_vars['Grid']->GetShowAddButton()): ?><?php echo ''; ?><?php if ($this->_tpl_vars['grid_menu_links'] > 0): ?><?php echo '|'; ?><?php endif; ?><?php echo '<a class="grid_menu_link" href="'; ?><?php echo $this->_tpl_vars['Grid']->GetAddRecordLink(); ?><?php echo '">'; ?><?php echo $this->_tpl_vars['Captions']->GetMessageString('AddNewRecord'); ?><?php echo '</a>'; ?><?php echo smarty_function_counter(array('assign' => 'grid_menu_links'), $this);?><?php echo ''; ?><?php endif; ?><?php echo ''; ?><?php if ($this->_tpl_vars['AllowDeleteSelected']): ?><?php echo ''; ?><?php if ($this->_tpl_vars['grid_menu_links'] > 0): ?><?php echo '|'; ?><?php endif; ?><?php echo '<a class="grid_menu_link" href="" onclick="if (confirm(\'Delete records?\')) document.selectedRecords.submit(); return false;">'; ?><?php echo $this->_tpl_vars['Captions']->GetMessageString('DeleteSelected'); ?><?php echo '</a>'; ?><?php echo smarty_function_counter(array('assign' => 'grid_menu_links'), $this);?><?php echo ''; ?><?php endif; ?><?php echo ''; ?><?php if ($this->_tpl_vars['Grid']->GetShowUpdateLink()): ?><?php echo ''; ?><?php if ($this->_tpl_vars['grid_menu_links'] > 0): ?><?php echo '|'; ?><?php endif; ?><?php echo '<a class="grid_menu_link" href="'; ?><?php echo $this->_tpl_vars['Grid']->GetUpdateLink(); ?><?php echo '">'; ?><?php echo $this->_tpl_vars['Captions']->GetMessageString('Refresh'); ?><?php echo '</a>'; ?><?php echo smarty_function_counter(array('assign' => 'grid_menu_links'), $this);?><?php echo ''; ?><?php endif; ?><?php echo '</td>'; ?>

    </tr>
<?php endif; ?>
</table>

<?php if ($this->_tpl_vars['AllowDeleteSelected']): ?></form><?php endif; ?>