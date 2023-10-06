<?php /* Smarty version 2.6.19, created on 2009-12-25 14:44:58
         compiled from edit/page.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'styleoption', 'edit/page.tpl', 4, false),)), $this); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'common/page_header.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<body style="<?php echo smarty_function_styleoption(array('name' => 'margin','value' => $this->_tpl_vars['Page']->Margin), $this);?>
<?php echo smarty_function_styleoption(array('name' => 'padding','value' => $this->_tpl_vars['Page']->Padding), $this);?>
">
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'common/site_header.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<br/>
<?php echo $this->_tpl_vars['Grid']; ?>

</body>
</html>