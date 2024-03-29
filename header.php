<?php

// Compatibility with PHP Report Maker
if (!isset($Language)) {
	include_once "ewcfg9.php";
	include_once "ewshared9.php";
	$Language = new cLanguage();
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title><?php echo $Language->ProjectPhrase("BodyTitle") ?></title>
<?php if (@$gsExport == "") { ?>
<link rel="stylesheet" type="text/css" href="<?php echo ew_YuiHost() ?>build/container/assets/skins/sam/container.css">
<link rel="stylesheet" type="text/css" href="<?php echo ew_YuiHost() ?>build/resize/assets/skins/sam/resize.css">
<?php } ?>
<?php if (@$gsExport == "" || @$gsExport == "print") { ?>
<link rel="stylesheet" type="text/css" href="<?php echo EW_PROJECT_STYLESHEET_FILENAME ?>">
<?php if (ew_IsMobile()) { ?>
<link rel="stylesheet" type="text/css" href="phpcss/ewmobile.css">
<?php } ?>
<script type="text/javascript" src="<?php echo ew_jQueryFile("jquery-%v.min.js") ?>"></script>
<?php if (ew_IsMobile()) { ?>
<link rel="stylesheet" type="text/css" href="<?php echo ew_jQueryFile("jquery.mobile-%v.min.css") ?>">
<script type="text/javascript">
jQuery(document).bind("mobileinit", function() {
	jQuery.mobile.ajaxEnabled = false;
	jQuery.mobile.ignoreContentEnabled = true;
});
</script>
<script type="text/javascript" src="<?php echo ew_jQueryFile("jquery.mobile-%v.min.js") ?>"></script>
<?php } ?>
<script type="text/javascript" src="<?php echo ew_YuiHost() ?>build/utilities/utilities.js"></script>
<script type="text/javascript" src="<?php echo ew_YuiHost() ?>build/json/json-min.js"></script>
<?php } ?>
<?php if (@$gsExport == "") { ?>
<script type="text/javascript" src="<?php echo ew_YuiHost() ?>build/container/container-min.js"></script>
<script type="text/javascript" src="<?php echo ew_YuiHost() ?>build/datasource/datasource-min.js"></script>
<script type="text/javascript" src="<?php echo ew_YuiHost() ?>build/resize/resize-min.js"></script>
<link href="calendar/calendar-win2k-cold-1.css" rel="stylesheet" type="text/css" media="all" title="win2k-1">
<style type="text/css">.ewCalendar {cursor: pointer;}</style>
<script type="text/javascript" src="calendar/calendar.js"></script>
<script type="text/javascript" src="calendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="calendar/calendar-setup.js"></script>
<script type="text/javascript">

// Create calendar
function ew_CreateCalendar(formid, id, format) {
	if (id.indexOf("$rowindex$") > -1)
		return;
	Calendar.setup({
		inputField: ew_GetElement(id, formid), // input field
		showsTime: / %H:%M:%S$/.test(format), // shows time
		ifFormat: format, // date format
		button: ew_ConcatId(formid, id) // button ID
	});
}

// Custom event
var ewSelectDateEvent = new YAHOO.util.CustomEvent("SelectDate");
</script>
<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
<script type="text/javascript">

// update value from editor to textarea
function ew_UpdateTextArea() {
	if (typeof CKEDITOR == "undefined")	
		return;
	for (var inst in CKEDITOR.instances)
		CKEDITOR.instances[inst].updateElement();
}

// update value from textarea to editor
function ew_UpdateEditor(name) {
	if (typeof CKEDITOR == "undefined")
		return;
	var inst = CKEDITOR.instances[name];		
	if (inst)
		inst.setData(inst.element.value);
}

// focus editor
function ew_FocusEditor(name) {
	if (typeof CKEDITOR == "undefined")
		return;
	var inst = CKEDITOR.instances[name];	
	if (inst)
		inst.focus();
}

// create editor
function ew_CreateEditor(formid, name, cols, rows, readonly) {
	if (typeof CKEDITOR == "undefined" || name.indexOf("$rowindex$") > -1)
		return;
	var form = document.getElementById(formid);	
	var el = ew_GetElement(name, form);
	if (!el)
		return;
	var args = {"id": name, "form": form, "enabled": true};
	ewCreateEditorEvent.fire(args);
	if (!args.enabled)
		return;
	if (cols <= 0)
		cols = 35;
	if (rows <= 0)
		rows = 4;
	var w = cols * 20; // width multiplier
	var h = rows * 60; // height multiplier
	if (readonly) {
		new ew_ReadOnlyTextArea(el, w, h);
	} else {
		name = ew_ConcatId(formid, name);
		ewForms[formid].Editors.push(new ew_Editor(name, function() {
			var inst = CKEDITOR.replace(el, { width: w, height: h,
				autoUpdateElement: false,
				baseHref: 'ckeditor/'})
			CKEDITOR.instances[name] = inst;
			delete CKEDITOR.instances[el.id];
			this.active = true;
		}));
	}
}
</script>
<script type="text/javascript">
var EW_LANGUAGE_ID = "<?php echo $gsLanguage ?>";
var EW_DATE_SEPARATOR = "/" || "/"; // Default date separator
var EW_DECIMAL_POINT = "<?php echo $DEFAULT_DECIMAL_POINT ?>";
var EW_THOUSANDS_SEP = "<?php echo $DEFAULT_THOUSANDS_SEP ?>";
var EW_UPLOAD_ALLOWED_FILE_EXT = "gif,jpg,jpeg,bmp,png,doc,xls,pdf,zip"; // Allowed upload file extension

// Ajax settings
var EW_RECORD_DELIMITER = "\r";
var EW_FIELD_DELIMITER = "|";
var EW_LOOKUP_FILE_NAME = "ewlookup9.php"; // Lookup file name
var EW_AUTO_SUGGEST_MAX_ENTRIES = <?php echo EW_AUTO_SUGGEST_MAX_ENTRIES ?>; // Auto-Suggest max entries

// Common JavaScript messages
var EW_ADDOPT_BUTTON_SUBMIT_TEXT = "<?php echo ew_JsEncode2(ew_BtnCaption($Language->Phrase("AddBtn"))) ?>";
var EW_EMAIL_EXPORT_BUTTON_SUBMIT_TEXT = "<?php echo ew_JsEncode2(ew_BtnCaption($Language->Phrase("SendEmailBtn"))) ?>";
var EW_BUTTON_CANCEL_TEXT = "<?php echo ew_JsEncode2(ew_BtnCaption($Language->Phrase("CancelBtn"))) ?>";
var EW_DISABLE_BUTTON_ON_SUBMIT = true;
var EW_IMAGE_FOLDER = "phpimages/"; // Image folder
</script>
<?php } ?>
<?php if (@$gsExport == "" || @$gsExport == "print") { ?>
<script type="text/javascript" src="phpjs/jsrender.js"></script>
<script type="text/javascript" src="phpjs/ewp9.js"></script>
<?php } ?>
<?php if (@$gsExport == "") { ?>
<script type="text/javascript" src="phpjs/userfn9.js"></script>
<script type="text/javascript">
<?php echo $Language->ToJSON() ?>
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php } ?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="generator" content="PHPMaker v9.2.0">
</head>
<body class="yui-skin-sam">
<?php if (@$gsExport == "" || @$gsExport == "print") { ?>
<?php if (ew_IsMobile()) { ?>
<div data-role="page">
	<div data-role="header">
		<a href="mobilemenu.php"><?php echo $Language->Phrase("MobileMenu") ?></a>
		<h1 id="ewPageTitle"></h1>
	<?php if (IsLoggedIn()) { ?>
		<a href="logout.php"><?php echo $Language->Phrase("Logout") ?></a>
	<?php } elseif (substr(ew_ScriptName(), 0 - strlen("login.php")) <> "login.php") { ?>
		<a href="login.php"><?php echo $Language->Phrase("Login") ?></a>
	<?php } ?>
	</div>
<?php } ?>
<?php } ?>
<?php if (@!$gbSkipHeaderFooter) { ?>
<?php if (@$gsExport == "") { ?>
<div class="ewLayout">
<?php if (!ew_IsMobile()) { ?>
	<!-- header (begin) --><!-- *** Note: Only licensed users are allowed to change the logo *** -->
  <div class="ewHeaderRow"><img src="phpimages/phpmkrlogo9.png" alt="" style="border: 0;"></div>
	<!-- header (end) -->
<?php } ?>
<?php if (ew_IsMobile()) { ?>
	<div data-role="content" data-enhance="false">
	<table class="ewContentTable">
		<tr>
<?php } else { ?>
	<!-- content (begin) -->
	<table cellspacing="0" class="ewContentTable">
		<tr>	
			<td class="ewMenuColumn">
			<!-- left column (begin) -->
<?php include_once "ewmenu.php" ?>
			<!-- left column (end) -->
			</td>
<?php } ?>
	    <td class="ewContentColumn">
			<!-- right column (begin) -->
				<p><span class="ewSiteTitle"><?php echo $Language->ProjectPhrase("BodyTitle") ?></span></p>
<?php } ?>
<?php } ?>
