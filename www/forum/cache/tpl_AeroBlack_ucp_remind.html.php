<?php if (!defined('IN_PHPBB')) exit; $this->_tpl_include('overall_header.html'); ?>

<div align="center">

<form action="<?php echo (isset($this->_rootref['S_PROFILE_ACTION'])) ? $this->_rootref['S_PROFILE_ACTION'] : ''; ?>" method="post">
<table border="0" width="50%" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td width="18" height="18"><img src="<?php echo (isset($this->_rootref['T_THEME_PATH'])) ? $this->_rootref['T_THEME_PATH'] : ''; ?>/images/tl.png" width="18" height="18" alt="" /></td>
    <td height="18" style="background:url('<?php echo (isset($this->_rootref['T_THEME_PATH'])) ? $this->_rootref['T_THEME_PATH'] : ''; ?>/images/tm.png');"></td>
    <td width="18" height="18"><img src="<?php echo (isset($this->_rootref['T_THEME_PATH'])) ? $this->_rootref['T_THEME_PATH'] : ''; ?>/images/tr.png" width="18" height="18" alt="" /></td>
  </tr>
  <tr>
    <td width="18" style="background:url('<?php echo (isset($this->_rootref['T_THEME_PATH'])) ? $this->_rootref['T_THEME_PATH'] : ''; ?>/images/ml.png');"></td>
    <td>

<table class="tablebg" width="100%" cellspacing="0" cellpadding="4" border="0">
<tr> 
	<th colspan="2"><?php echo ((isset($this->_rootref['L_SEND_PASSWORD'])) ? $this->_rootref['L_SEND_PASSWORD'] : ((isset($user->lang['SEND_PASSWORD'])) ? $user->lang['SEND_PASSWORD'] : '{ SEND_PASSWORD }')); ?></th>
</tr>
<tr> 
	<td class="row1" width="38%"><b class="genmed"><?php echo ((isset($this->_rootref['L_USERNAME'])) ? $this->_rootref['L_USERNAME'] : ((isset($user->lang['USERNAME'])) ? $user->lang['USERNAME'] : '{ USERNAME }')); ?>: </b></td>
	<td class="row2"><input type="text" class="post" name="username" size="25" value="<?php echo (isset($this->_rootref['USERNAME'])) ? $this->_rootref['USERNAME'] : ''; ?>" /></td>
</tr>
<tr> 
	<td class="row1"><b class="genmed"><?php echo ((isset($this->_rootref['L_EMAIL_ADDRESS'])) ? $this->_rootref['L_EMAIL_ADDRESS'] : ((isset($user->lang['EMAIL_ADDRESS'])) ? $user->lang['EMAIL_ADDRESS'] : '{ EMAIL_ADDRESS }')); ?>: </b><br /><span class="gensmall"><?php echo ((isset($this->_rootref['L_EMAIL_REMIND'])) ? $this->_rootref['L_EMAIL_REMIND'] : ((isset($user->lang['EMAIL_REMIND'])) ? $user->lang['EMAIL_REMIND'] : '{ EMAIL_REMIND }')); ?></span></td>
	<td class="row2"><input type="text" class="post" name="email" size="25" maxlength="100" value="<?php echo (isset($this->_rootref['EMAIL'])) ? $this->_rootref['EMAIL'] : ''; ?>" /></td>
</tr>
<tr> 
	<td class="catb" colspan="2" align="center"><?php echo (isset($this->_rootref['S_HIDDEN_FIELDS'])) ? $this->_rootref['S_HIDDEN_FIELDS'] : ''; ?><input type="submit" name="submit" value="<?php echo ((isset($this->_rootref['L_SUBMIT'])) ? $this->_rootref['L_SUBMIT'] : ((isset($user->lang['SUBMIT'])) ? $user->lang['SUBMIT'] : '{ SUBMIT }')); ?>" class="btnmain" />&nbsp;&nbsp;<input type="reset" value="<?php echo ((isset($this->_rootref['L_RESET'])) ? $this->_rootref['L_RESET'] : ((isset($user->lang['RESET'])) ? $user->lang['RESET'] : '{ RESET }')); ?>" name="reset" class="btnlite" /></td>
</tr>
</table>
</td>
    <td width="18" style="background:url('<?php echo (isset($this->_rootref['T_THEME_PATH'])) ? $this->_rootref['T_THEME_PATH'] : ''; ?>/images/mr.png');"></td>
  </tr>
  <tr>
    <td width="18" height="18" align="right"><img src="<?php echo (isset($this->_rootref['T_THEME_PATH'])) ? $this->_rootref['T_THEME_PATH'] : ''; ?>/images/bl.png" width="18" height="18" alt="" /></td>
    <td height="18" style="background:url('<?php echo (isset($this->_rootref['T_THEME_PATH'])) ? $this->_rootref['T_THEME_PATH'] : ''; ?>/images/bm.png');"></td>
    <td width="18" height="18"><img src="<?php echo (isset($this->_rootref['T_THEME_PATH'])) ? $this->_rootref['T_THEME_PATH'] : ''; ?>/images/br.png" width="18" height="18" alt="" /></td>
  </tr>
</table>
<?php echo (isset($this->_rootref['S_FORM_TOKEN'])) ? $this->_rootref['S_FORM_TOKEN'] : ''; ?>
</form>

</div>

<?php $this->_tpl_include('overall_footer.html'); ?>