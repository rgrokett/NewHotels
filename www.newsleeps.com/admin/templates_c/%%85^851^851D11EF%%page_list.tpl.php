<?php /* Smarty version 2.6.19, created on 2009-12-25 14:44:23
         compiled from page_list.tpl */ ?>
    <h3><?php echo $this->_tpl_vars['Captions']->GetMessageString('PageList'); ?>
</h3>
    <ul>
<?php $_from = $this->_tpl_vars['PageList']->GetPages(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['PageLink']):
?>        
<?php if ($this->_tpl_vars['PageLink']->GetShowAsText()): ?>
        <li><b><span title="<?php echo $this->_tpl_vars['PageLink']->GetHint(); ?>
"><?php echo $this->_tpl_vars['PageLink']->GetCaption(); ?>
</span></b></li>
<?php else: ?>
        <li><a href="<?php echo $this->_tpl_vars['PageLink']->GetLink(); ?>
" title="<?php echo $this->_tpl_vars['PageLink']->GetHint(); ?>
"><span title="<?php echo $this->_tpl_vars['PageLink']->GetHint(); ?>
"><?php echo $this->_tpl_vars['PageLink']->GetCaption(); ?>
</span></a></li>
<?php endif; ?>
<?php endforeach; endif; unset($_from); ?>
    </ul>