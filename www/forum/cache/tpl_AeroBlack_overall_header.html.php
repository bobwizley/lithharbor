<?php if (!defined('IN_PHPBB')) exit; ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo (isset($this->_rootref['S_CONTENT_DIRECTION'])) ? $this->_rootref['S_CONTENT_DIRECTION'] : ''; ?>" lang="<?php echo (isset($this->_rootref['S_USER_LANG'])) ? $this->_rootref['S_USER_LANG'] : ''; ?>" xml:lang="<?php echo (isset($this->_rootref['S_USER_LANG'])) ? $this->_rootref['S_USER_LANG'] : ''; ?>">
<head>
<?php echo (isset($this->_rootref['SEO_BASE_HREF'])) ? $this->_rootref['SEO_BASE_HREF'] : ''; ?>

<meta http-equiv="content-type" content="text/html; charset=<?php echo (isset($this->_rootref['S_CONTENT_ENCODING'])) ? $this->_rootref['S_CONTENT_ENCODING'] : ''; ?>" />
<meta http-equiv="content-language" content="<?php echo (isset($this->_rootref['S_USER_LANG'])) ? $this->_rootref['S_USER_LANG'] : ''; ?>" />
<meta http-equiv="content-style-type" content="text/css" />
<meta http-equiv="imagetoolbar" content="no" />
<meta name="resource-type" content="document" />
<meta name="distribution" content="global" />
<meta name="copyright" content="2000, 2002, 2005, 2007 phpBB Group" />
<meta name="keywords" content="" />
<meta name="description" content="" />
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
<?php echo (isset($this->_rootref['META'])) ? $this->_rootref['META'] : ''; ?>
<title><?php echo (isset($this->_rootref['SITENAME'])) ? $this->_rootref['SITENAME'] : ''; ?> &bull; <?php if ($this->_rootref['S_IN_MCP']) {  echo ((isset($this->_rootref['L_MCP'])) ? $this->_rootref['L_MCP'] : ((isset($user->lang['MCP'])) ? $user->lang['MCP'] : '{ MCP }')); ?> &bull; <?php } else if ($this->_rootref['S_IN_UCP']) {  echo ((isset($this->_rootref['L_UCP'])) ? $this->_rootref['L_UCP'] : ((isset($user->lang['UCP'])) ? $user->lang['UCP'] : '{ UCP }')); ?> &bull; <?php } echo (isset($this->_rootref['PAGE_TITLE'])) ? $this->_rootref['PAGE_TITLE'] : ''; ?></title>

<link rel="stylesheet" href="<?php echo (isset($this->_rootref['T_STYLESHEET_LINK'])) ? $this->_rootref['T_STYLESHEET_LINK'] : ''; ?>" type="text/css" />

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-31101476-1', 'auto');
  ga('send', 'pageview');
  ga('create', 'UA-42649475-1', {'name' : 'tracker2'});
  ga('tracker2.send', 'pageview');

</script>

<script type="text/javascript">
// <![CDATA[
<?php if ($this->_rootref['S_USER_PM_POPUP']) {  ?>
	if (<?php echo (isset($this->_rootref['S_NEW_PM'])) ? $this->_rootref['S_NEW_PM'] : ''; ?>)
	{
		popup('<?php echo (isset($this->_rootref['UA_POPUP_PM'])) ? $this->_rootref['UA_POPUP_PM'] : ''; ?>', 480, 180, '_phpbbprivmsg');
	}
<?php } ?>

function popup(url, width, height, name)
{
	if (!name)
	{
		name = '_popup';
	}

	window.open(url.replace(/&amp;/g, '&'), name, 'height=' + height + ',resizable=yes,scrollbars=yes,width=' + width);
	return false;
}

// www.phpBB-SEO.com SEO TOOLKIT BEGIN
function jumpto() {
	var page = prompt('<?php echo ((isset($this->_rootref['LA_JUMP_PAGE'])) ? $this->_rootref['LA_JUMP_PAGE'] : ((isset($this->_rootref['L_JUMP_PAGE'])) ? addslashes($this->_rootref['L_JUMP_PAGE']) : ((isset($user->lang['JUMP_PAGE'])) ? addslashes($user->lang['JUMP_PAGE']) : '{ JUMP_PAGE }'))); ?>:', '<?php echo (isset($this->_rootref['ON_PAGE'])) ? $this->_rootref['ON_PAGE'] : ''; ?>');
	var perpage = '<?php echo (isset($this->_rootref['PER_PAGE'])) ? $this->_rootref['PER_PAGE'] : ''; ?>';
	var base_url = '<?php echo (isset($this->_rootref['A_BASE_URL'])) ? $this->_rootref['A_BASE_URL'] : ''; ?>';
	var seo_delim_start = '<?php echo (isset($this->_rootref['SEO_START_DELIM'])) ? $this->_rootref['SEO_START_DELIM'] : ''; ?>';
	var seo_static_pagination = '<?php echo (isset($this->_rootref['SEO_SATIC_PAGE'])) ? $this->_rootref['SEO_SATIC_PAGE'] : ''; ?>';
	var seo_ext_pagination = '<?php echo (isset($this->_rootref['SEO_EXT_PAGE'])) ? $this->_rootref['SEO_EXT_PAGE'] : ''; ?>';
	if (page !== null && !isNaN(page) && page == Math.floor(page) && page > 0) {
		var seo_page = (page - 1) * perpage;
		var anchor = '';
		var anchor_parts = base_url.split('#');
		if ( anchor_parts[1] ) {
			base_url = anchor_parts[0];
			anchor = '#' + anchor_parts[1];
		}
		if ( seo_page > 0 ) {
			var phpEXtest = false;
			if ( base_url.indexOf('?') >= 0 || ( phpEXtest = base_url.match(/\.php$/i))) {
				document.location.href = base_url.replace(/&amp;/g, '&') + (phpEXtest ? '?' : '&') + 'start=' + seo_page + anchor;
			} else {
				var ext = base_url.match(/\.[a-z0-9]+$/i);
				if (ext) {
					// location.ext => location-xx.ext
					document.location.href = base_url.replace(/\.[a-z0-9]+$/i, '') + seo_delim_start + seo_page + ext + anchor;
				} else {
					// location and location/ to location/pagexx.html
					var slash = base_url.match(/\/$/) ? '' : '/';
					document.location.href = base_url + slash + seo_static_pagination + seo_page + seo_ext_pagination + anchor;
				}
			}
		} else {
			document.location.href = base_url + anchor;
		}
	}
}
var seo_external = <?php echo (isset($this->_rootref['SEO_EXTERNAL'])) ? $this->_rootref['SEO_EXTERNAL'] : ''; ?>;
var seo_external_sub = <?php echo (isset($this->_rootref['SEO_EXTERNAL_SUB'])) ? $this->_rootref['SEO_EXTERNAL_SUB'] : ''; ?>;
var seo_ext_classes = <?php echo (isset($this->_rootref['SEO_EXT_CLASSES'])) ? $this->_rootref['SEO_EXT_CLASSES'] : ''; ?>;
var seo_hashfix = <?php echo (isset($this->_rootref['SEO_HASHFIX'])) ? $this->_rootref['SEO_HASHFIX'] : ''; ?>;
/**
*  phpbb_seo_href()
*  Fixes href="#something" links with virtual directories
*  Optionally open external or marked with a css class links in a new window
*  in a XHTML 1.x compliant way.
*/
function phpbb_seo_href() {
	var current_domain = document.domain.toLowerCase();
	if (!current_domain || !document.getElementsByTagName) return;
	if (seo_external_sub && current_domain.indexOf('.') >= 0) {
		current_domain = current_domain.replace(new RegExp(/^[a-z0-9_-]+\.([a-z0-9_-]+\.([a-z]{2,6}|[a-z]{2,3}\.[a-z]{2,3}))$/i), '$1');
	}
	if (seo_ext_classes) {
		var extclass = new RegExp("(^|\s)(" + seo_ext_classes + ")(\s|$)");
	}
	if (seo_hashfix) {
		var basehref = document.getElementsByTagName('base')[0];
		if (basehref) {
			basehref = basehref.href;
			var hashtest = new RegExp("^(" + basehref + "|)#[a-z0-9_-]+$");
			var current_href = document.location.href.replace(/#[a-z0-9_-]+$/i, "");
		} else {
			seo_hashfix = false;
		}
	}
	var hrefels = document.getElementsByTagName("a");
	var hrefelslen = hrefels.length;
	for (var i = 0; i < hrefelslen; i++) {
		var el = hrefels[i];
		var hrefinner = el.innerHTML.toLowerCase();
		if (el.onclick || (el.href == '') || (el.href.indexOf('javascript') >=0 ) || (el.href.indexOf('mailto') >=0 ) || (hrefinner.indexOf('<a') >= 0) ) {
			continue;
		}
		if (seo_hashfix && el.hash && hashtest.test(el.href)) {
			el.href = current_href + el.hash;
		}
		if (seo_external) {
			if ((el.href.indexOf(current_domain) >= 0) && !(seo_ext_classes && extclass.test(el.className))) {
				continue;
			}
			el.onclick = function () { window.open(this.href); return false; };
		}
	}
}
window.onload = function() {
	if (seo_external || seo_hashfix) {
		phpbb_seo_href();
	}
	// here you can add other window.onload events
}
// www.phpBB-SEO.com SEO TOOLKIT END

/**
* Find a member
*/
function find_username(url)
{
	popup(url, 870, 570, '_usersearch');
	return false;
}

/**
* Mark/unmark checklist
* id = ID of parent container, name = name prefix, state = state [true/false]
*/
function marklist(id, name, state)
{
	var parent = document.getElementById(id);
	if (!parent)
	{
		eval('parent = document.' + id);
	}

	if (!parent)
	{
		return;
	}

	var rb = parent.getElementsByTagName('input');
	
	for (var r = 0; r < rb.length; r++)
	{
		if (rb[r].name.substr(0, name.length) == name)
		{
			rb[r].checked = state;
		}
	}
}

<?php if (sizeof($this->_tpldata['_file'])) {  ?>

	/**
	* Play quicktime file by determining it's width/height
	* from the displayed rectangle area
	*
	* Only defined if there is a file block present.
	*/
	function play_qt_file(obj)
	{
		var rectangle = obj.GetRectangle();

		if (rectangle)
		{
			rectangle = rectangle.split(',')
			var x1 = parseInt(rectangle[0]);
			var x2 = parseInt(rectangle[2]);
			var y1 = parseInt(rectangle[1]);
			var y2 = parseInt(rectangle[3]);

			var width = (x1 < 0) ? (x1 * -1) + x2 : x2 - x1;
			var height = (y1 < 0) ? (y1 * -1) + y2 : y2 - y1;
		}
		else
		{
			var width = 200;
			var height = 0;
		}

		obj.width = width;
		obj.height = height + 16;

		obj.SetControllerVisible(true);

		obj.Play();
	}
<?php } ?>

function selectCode(a)
{
   // Get ID of code block
   var e = a.parentNode.parentNode.getElementsByTagName('CODE')[0];

   // Not IE
   if (window.getSelection)
   {
      var s = window.getSelection();
      // Safari
      if (s.setBaseAndExtent)
      {
         s.setBaseAndExtent(e, 0, e, e.innerText.length - 1);
      }
      // Firefox and Opera
      else
      {
         var r = document.createRange();
         r.selectNodeContents(e);
         s.removeAllRanges();
         s.addRange(r);
      }
   }
   // Some older browsers
   else if (document.getSelection)
   {
      var s = document.getSelection();
      var r = document.createRange();
      r.selectNodeContents(e);
      s.removeAllRanges();
      s.addRange(r);
   }
   // IE
   else if (document.selection)
   {
      var r = document.body.createTextRange();
      r.moveToElementText(e);
      r.select();
   }
}

// ]]>
</script>
</head>
<body class="<?php echo (isset($this->_rootref['S_CONTENT_DIRECTION'])) ? $this->_rootref['S_CONTENT_DIRECTION'] : ''; ?>">

<a name="top"></a>

<table class="wrap" width="100%" cellpadding="0" cellspacing="0">
<tr><td>

<div id="wrapheader">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td width="18" height="18"><img src="<?php echo (isset($this->_rootref['T_THEME_PATH'])) ? $this->_rootref['T_THEME_PATH'] : ''; ?>/images/tl.png" width="18" height="18" alt="" /></td>
    <td height="18" style="background:url('<?php echo (isset($this->_rootref['T_THEME_PATH'])) ? $this->_rootref['T_THEME_PATH'] : ''; ?>/images/tm.png');"></td>
    <td width="18" height="18"><img src="<?php echo (isset($this->_rootref['T_THEME_PATH'])) ? $this->_rootref['T_THEME_PATH'] : ''; ?>/images/tr.png" width="18" height="18" alt="" /></td>
  </tr>
  <tr>
    <td width="18" style="background:url('<?php echo (isset($this->_rootref['T_THEME_PATH'])) ? $this->_rootref['T_THEME_PATH'] : ''; ?>/images/ml.png');"></td>
    <td>
  <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td width="303" height="80" style="background:url('<?php echo (isset($this->_rootref['T_THEME_PATH'])) ? $this->_rootref['T_THEME_PATH'] : ''; ?>/images/midl.png');"><div class="logo"><a class="headw" href="<?php echo (isset($this->_rootref['U_INDEX'])) ? $this->_rootref['U_INDEX'] : ''; ?>"><?php echo (isset($this->_rootref['SITENAME'])) ? $this->_rootref['SITENAME'] : ''; ?></a><br /><span class="genmedw"><?php echo (isset($this->_rootref['SITE_DESCRIPTION'])) ? $this->_rootref['SITE_DESCRIPTION'] : ''; ?></span></div></td>
<td height="80" style="background:url('<?php echo (isset($this->_rootref['T_THEME_PATH'])) ? $this->_rootref['T_THEME_PATH'] : ''; ?>/images/midm.png');">&nbsp;</td>
<td width="470" height="80" align="right" valign="top" style="background:url('<?php echo (isset($this->_rootref['T_THEME_PATH'])) ? $this->_rootref['T_THEME_PATH'] : ''; ?>/images/midr.png');">
<?php if ($this->_rootref['S_DISPLAY_SEARCH'] && ! $this->_rootref['S_IN_SEARCH']) {  ?>
<div class="midlink">
<div id="search-box">
<form action="<?php echo (isset($this->_rootref['U_SEARCH'])) ? $this->_rootref['U_SEARCH'] : ''; ?>" method="post" id="search">
            <fieldset class="nb">
               <input name="keywords" style="opacity:0.8;filter:alpha(opacity=80)" onmouseover="this.style.opacity=1;this.filters.alpha.opacity=100" onmouseout="this.style.opacity=0.8;this.filters.alpha.opacity=80" type="text" class="inputbox search" id="keywords" title="<?php echo ((isset($this->_rootref['L_SEARCH_KEYWORDS'])) ? $this->_rootref['L_SEARCH_KEYWORDS'] : ((isset($user->lang['SEARCH_KEYWORDS'])) ? $user->lang['SEARCH_KEYWORDS'] : '{ SEARCH_KEYWORDS }')); ?>" onblur="if(this.value=='')this.value='<?php echo ((isset($this->_rootref['LA_SEARCH_MINI'])) ? $this->_rootref['LA_SEARCH_MINI'] : ((isset($this->_rootref['L_SEARCH_MINI'])) ? addslashes($this->_rootref['L_SEARCH_MINI']) : ((isset($user->lang['SEARCH_MINI'])) ? addslashes($user->lang['SEARCH_MINI']) : '{ SEARCH_MINI }'))); ?>';" onclick="if(this.value=='<?php echo ((isset($this->_rootref['LA_SEARCH_MINI'])) ? $this->_rootref['LA_SEARCH_MINI'] : ((isset($this->_rootref['L_SEARCH_MINI'])) ? addslashes($this->_rootref['L_SEARCH_MINI']) : ((isset($user->lang['SEARCH_MINI'])) ? addslashes($user->lang['SEARCH_MINI']) : '{ SEARCH_MINI }'))); ?>')this.value='';" value="<?php if ($this->_rootref['SEARCH_WORDS']) {  echo (isset($this->_rootref['SEARCH_WORDS'])) ? $this->_rootref['SEARCH_WORDS'] : ''; } else { echo ((isset($this->_rootref['L_SEARCH_MINI'])) ? $this->_rootref['L_SEARCH_MINI'] : ((isset($user->lang['SEARCH_MINI'])) ? $user->lang['SEARCH_MINI'] : '{ SEARCH_MINI }')); } ?>" maxlength="50" />
               <input class="nb" name="submit" type="image" src="<?php echo (isset($this->_rootref['T_THEME_PATH'])) ? $this->_rootref['T_THEME_PATH'] : ''; ?>/images/search.png" align="absmiddle" /><br />
<a class="genmedw" href="<?php echo (isset($this->_rootref['U_SEARCH'])) ? $this->_rootref['U_SEARCH'] : ''; ?>" title="<?php echo ((isset($this->_rootref['L_SEARCH_ADV_EXPLAIN'])) ? $this->_rootref['L_SEARCH_ADV_EXPLAIN'] : ((isset($user->lang['SEARCH_ADV_EXPLAIN'])) ? $user->lang['SEARCH_ADV_EXPLAIN'] : '{ SEARCH_ADV_EXPLAIN }')); ?>"><?php echo ((isset($this->_rootref['L_SEARCH_ADV'])) ? $this->_rootref['L_SEARCH_ADV'] : ((isset($user->lang['SEARCH_ADV'])) ? $user->lang['SEARCH_ADV'] : '{ SEARCH_ADV }')); ?></a> <?php echo (isset($this->_rootref['S_SEARCH_HIDDEN_FIELDS'])) ? $this->_rootref['S_SEARCH_HIDDEN_FIELDS'] : ''; ?>
            </fieldset>
</form>
</div>
</div>
<?php } ?>
      </td>
  </tr>
  </table>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
<td height="24" align="left" style="background:url('<?php echo (isset($this->_rootref['T_THEME_PATH'])) ? $this->_rootref['T_THEME_PATH'] : ''; ?>/images/bottomm.png');">

<div class="buttonwrapper">
<div class="navspacel"></div>
<?php if ($this->_rootref['S_USER_LOGGED_IN']) {  } else if ($this->_rootref['S_REGISTER_ENABLED']) {  ?>
<a class="squarebuttonl" href="<?php echo (isset($this->_rootref['U_REGISTER'])) ? $this->_rootref['U_REGISTER'] : ''; ?>"><span><?php echo ((isset($this->_rootref['L_REGISTER'])) ? $this->_rootref['L_REGISTER'] : ((isset($user->lang['REGISTER'])) ? $user->lang['REGISTER'] : '{ REGISTER }')); ?></span></a><div class="navspacel"></div>
<?php } if ($this->_rootref['S_USER_LOGGED_IN']) {  ?>
<a class="squarebuttonl" href="<?php echo (isset($this->_rootref['U_PROFILE'])) ? $this->_rootref['U_PROFILE'] : ''; ?>"><span><?php echo ((isset($this->_rootref['L_PROFILE'])) ? $this->_rootref['L_PROFILE'] : ((isset($user->lang['PROFILE'])) ? $user->lang['PROFILE'] : '{ PROFILE }')); ?></span></a><div class="navspacel"></div>
<?php if ($this->_rootref['S_DISPLAY_PM']) {  ?>
<a class="squarebuttonl" href="<?php echo (isset($this->_rootref['U_PRIVATEMSGS'])) ? $this->_rootref['U_PRIVATEMSGS'] : ''; ?>"><span><?php echo (isset($this->_rootref['PRIVATE_MESSAGE_INFO'])) ? $this->_rootref['PRIVATE_MESSAGE_INFO'] : ''; ?></span></a><div class="navspacel"></div>
<?php } } if ($this->_rootref['U_RESTORE_PERMISSIONS']) {  ?>
<a class="squarebuttonl" href="<?php echo (isset($this->_rootref['U_RESTORE_PERMISSIONS'])) ? $this->_rootref['U_RESTORE_PERMISSIONS'] : ''; ?>"><span><?php echo ((isset($this->_rootref['L_RESTORE_PERMISSIONS'])) ? $this->_rootref['L_RESTORE_PERMISSIONS'] : ((isset($user->lang['RESTORE_PERMISSIONS'])) ? $user->lang['RESTORE_PERMISSIONS'] : '{ RESTORE_PERMISSIONS }')); ?></span></a>
<?php } ?>
</div>

</td>
<td width="431" height="24" align="right" style="background:url('<?php echo (isset($this->_rootref['T_THEME_PATH'])) ? $this->_rootref['T_THEME_PATH'] : ''; ?>/images/bottomr.png');">

<div class="buttonwrapper">
<div class="navspacer"></div>
<?php if (! $this->_rootref['S_IS_BOT']) {  ?>
<a class="squarebuttonr" href="<?php echo (isset($this->_rootref['U_LOGIN_LOGOUT'])) ? $this->_rootref['U_LOGIN_LOGOUT'] : ''; ?>"><span><?php echo ((isset($this->_rootref['L_LOGIN_LOGOUT'])) ? $this->_rootref['L_LOGIN_LOGOUT'] : ((isset($user->lang['LOGIN_LOGOUT'])) ? $user->lang['LOGIN_LOGOUT'] : '{ LOGIN_LOGOUT }')); ?></span></a><div class="navspacer"></div>
<?php if ($this->_rootref['S_DISPLAY_MEMBERLIST']) {  ?>
<a class="squarebuttonr" href="<?php echo (isset($this->_rootref['U_MEMBERLIST'])) ? $this->_rootref['U_MEMBERLIST'] : ''; ?>"><span><?php echo ((isset($this->_rootref['L_MEMBERLIST'])) ? $this->_rootref['L_MEMBERLIST'] : ((isset($user->lang['MEMBERLIST'])) ? $user->lang['MEMBERLIST'] : '{ MEMBERLIST }')); ?></span></a><div class="navspacer"></div>
<?php } } ?>
<a class="squarebuttonr" href="<?php echo (isset($this->_rootref['U_FAQ'])) ? $this->_rootref['U_FAQ'] : ''; ?>"><span><?php echo ((isset($this->_rootref['L_FAQ'])) ? $this->_rootref['L_FAQ'] : ((isset($user->lang['FAQ'])) ? $user->lang['FAQ'] : '{ FAQ }')); ?></span></a>
</div>

</td>
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
</div>

	<div id="datebar">
		<table width="100%" cellspacing="0">
			<?php if ($this->_rootref['S_BOARD_DISABLED']) {  ?>
            <tr><td class="gensmall">
			<span class="error"><?php echo ((isset($this->_rootref['L_BOARD_DISABLED'])) ? $this->_rootref['L_BOARD_DISABLED'] : ((isset($user->lang['BOARD_DISABLED'])) ? $user->lang['BOARD_DISABLED'] : '{ BOARD_DISABLED }')); ?></span>
            </td></tr>
            <?php } ?>
            <tr>
			<td class="gensmall"><?php if ($this->_rootref['S_USER_LOGGED_IN']) {  echo (isset($this->_rootref['LAST_VISIT_DATE'])) ? $this->_rootref['LAST_VISIT_DATE'] : ''; } ?></td>
			<td class="gensmall" align="<?php echo (isset($this->_rootref['S_CONTENT_FLOW_END'])) ? $this->_rootref['S_CONTENT_FLOW_END'] : ''; ?>"><?php echo (isset($this->_rootref['CURRENT_TIME'])) ? $this->_rootref['CURRENT_TIME'] : ''; ?><br /></td>
		</tr>
		</table>
	</div>

	<?php if ($this->_rootref['S_DISPLAY_SEARCH']) {  ?>
	<p class="searchbar">
		<span style="float: <?php echo (isset($this->_rootref['S_CONTENT_FLOW_BEGIN'])) ? $this->_rootref['S_CONTENT_FLOW_BEGIN'] : ''; ?>;"><a href="<?php echo (isset($this->_rootref['U_SEARCH_UNANSWERED'])) ? $this->_rootref['U_SEARCH_UNANSWERED'] : ''; ?>"><?php echo ((isset($this->_rootref['L_SEARCH_UNANSWERED'])) ? $this->_rootref['L_SEARCH_UNANSWERED'] : ((isset($user->lang['SEARCH_UNANSWERED'])) ? $user->lang['SEARCH_UNANSWERED'] : '{ SEARCH_UNANSWERED }')); ?></a> | <a href="<?php echo (isset($this->_rootref['U_SEARCH_ACTIVE_TOPICS'])) ? $this->_rootref['U_SEARCH_ACTIVE_TOPICS'] : ''; ?>"><?php echo ((isset($this->_rootref['L_SEARCH_ACTIVE_TOPICS'])) ? $this->_rootref['L_SEARCH_ACTIVE_TOPICS'] : ((isset($user->lang['SEARCH_ACTIVE_TOPICS'])) ? $user->lang['SEARCH_ACTIVE_TOPICS'] : '{ SEARCH_ACTIVE_TOPICS }')); ?></a></span>
		<?php if ($this->_rootref['S_USER_LOGGED_IN']) {  ?>
		<span style="float: <?php echo (isset($this->_rootref['S_CONTENT_FLOW_END'])) ? $this->_rootref['S_CONTENT_FLOW_END'] : ''; ?>;"><a href="<?php echo (isset($this->_rootref['U_SEARCH_NEW'])) ? $this->_rootref['U_SEARCH_NEW'] : ''; ?>"><?php echo ((isset($this->_rootref['L_SEARCH_NEW'])) ? $this->_rootref['L_SEARCH_NEW'] : ((isset($user->lang['SEARCH_NEW'])) ? $user->lang['SEARCH_NEW'] : '{ SEARCH_NEW }')); ?></a> | <a href="<?php echo (isset($this->_rootref['U_SEARCH_SELF'])) ? $this->_rootref['U_SEARCH_SELF'] : ''; ?>"><?php echo ((isset($this->_rootref['L_SEARCH_SELF'])) ? $this->_rootref['L_SEARCH_SELF'] : ((isset($user->lang['SEARCH_SELF'])) ? $user->lang['SEARCH_SELF'] : '{ SEARCH_SELF }')); ?></a></span>
		<?php } ?>
	</p>
	<?php } ?>

	<br style="clear: both;" />
	
	<?php $this->_tpl_include('breadcrumbs.html'); ?>

	<br />