<?php /* Smarty version 2.6.19, created on 2009-12-25 14:44:58
         compiled from datetime_edit.tpl */ ?>
<input type="text" name="<?php echo $this->_tpl_vars['DateTimeEdit']->GetName(); ?>
" id="<?php echo $this->_tpl_vars['DateTimeEdit']->GetName(); ?>
" value="<?php echo $this->_tpl_vars['DateTimeEdit']->GetValue(); ?>
">
<button type="reset" id="<?php echo $this->_tpl_vars['DateTimeEdit']->GetName(); ?>
_trigger">...</button>
<script type="text/javascript">
    Calendar.setup({
        inputField     :    "<?php echo $this->_tpl_vars['DateTimeEdit']->GetName(); ?>
",
        ifFormat       :    "<?php echo $this->_tpl_vars['DateTimeEdit']->GetFormat(); ?>
",
        showsTime      :    <?php if ($this->_tpl_vars['DateTimeEdit']->GetShowsTime()): ?>true<?php else: ?>false<?php endif; ?>,
        button         :    "<?php echo $this->_tpl_vars['DateTimeEdit']->GetName(); ?>
_trigger",
        singleClick    :    true,
        step           :    1
    });
</script>