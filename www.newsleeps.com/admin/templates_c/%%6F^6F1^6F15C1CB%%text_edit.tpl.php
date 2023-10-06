<?php /* Smarty version 2.6.19, created on 2009-12-25 14:44:23
         compiled from text_edit.tpl */ ?>
<?php if (! $this->_tpl_vars['TextEdit']->GetReadOnly()): ?><input class="sm_text" id="<?php echo $this->_tpl_vars['TextEdit']->GetName(); ?>
" name="<?php echo $this->_tpl_vars['TextEdit']->GetName(); ?>
" value="<?php echo $this->_tpl_vars['TextEdit']->GetHTMLValue(); ?>
" <?php if ($this->_tpl_vars['TextEdit']->GetSize() != null): ?>size="<?php echo $this->_tpl_vars['TextEdit']->GetSize(); ?>
" style="width: auto;"<?php endif; ?> <?php if ($this->_tpl_vars['TextEdit']->GetMaxLength() != null): ?>maxlength="<?php echo $this->_tpl_vars['TextEdit']->GetMaxLength(); ?>
"<?php endif; ?>><?php else: ?>
<?php echo $this->_tpl_vars['TextEdit']->GetValue(); ?>
<?php endif; ?>