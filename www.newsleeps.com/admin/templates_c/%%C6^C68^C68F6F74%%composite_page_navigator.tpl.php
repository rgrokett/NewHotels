<?php /* Smarty version 2.6.19, created on 2009-12-25 14:44:23
         compiled from list/composite_page_navigator.tpl */ ?>
<!-- <Pages> -->
<div class="page_navigator">
<?php $_from = $this->_tpl_vars['PageNavigator']->GetPageNavigators(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['SubPageNavigator']):
?>
<div class="page_navigator">
<?php echo $this->_tpl_vars['Renderer']->Render($this->_tpl_vars['SubPageNavigator']); ?>

</div>
<?php endforeach; endif; unset($_from); ?>
</div>
<!-- </Pages> -->