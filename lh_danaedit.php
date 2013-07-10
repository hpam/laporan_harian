<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg9.php" ?>
<?php include_once "ewmysql9.php" ?>
<?php include_once "phpfn9.php" ?>
<?php include_once "lh_danainfo.php" ?>
<?php include_once "lh_userinfo.php" ?>
<?php include_once "userfn9.php" ?>
<?php

//
// Page class
//

$lh_dana_edit = NULL; // Initialize page object first

class clh_dana_edit extends clh_dana {

	// Page ID
	var $PageID = 'edit';

	// Project ID
	var $ProjectID = "{67264FB2-6364-478B-87DD-B3E0D7A29425}";

	// Table name
	var $TableName = 'lh_dana';

	// Page object name
	var $PageObjName = 'lh_dana_edit';

	// Page name
	function PageName() {
		return ew_CurrentPage();
	}

	// Page URL
	function PageUrl() {
		$PageUrl = ew_CurrentPage() . "?";
		if ($this->UseTokenInUrl) $PageUrl .= "t=" . $this->TableVar . "&"; // Add page token
		return $PageUrl;
	}

	// Message
	function getMessage() {
		return @$_SESSION[EW_SESSION_MESSAGE];
	}

	function setMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_MESSAGE], $v);
	}

	function getFailureMessage() {
		return @$_SESSION[EW_SESSION_FAILURE_MESSAGE];
	}

	function setFailureMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_FAILURE_MESSAGE], $v);
	}

	function getSuccessMessage() {
		return @$_SESSION[EW_SESSION_SUCCESS_MESSAGE];
	}

	function setSuccessMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_SUCCESS_MESSAGE], $v);
	}

	function getWarningMessage() {
		return @$_SESSION[EW_SESSION_WARNING_MESSAGE];
	}

	function setWarningMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_WARNING_MESSAGE], $v);
	}

	// Show message
	function ShowMessage() {
		$hidden = FALSE;
		$html = "";

		// Message
		$sMessage = $this->getMessage();
		$this->Message_Showing($sMessage, "");
		if ($sMessage <> "") { // Message in Session, display
			$html .= "<p class=\"ewMessage\">" . $sMessage . "</p>";
			$_SESSION[EW_SESSION_MESSAGE] = ""; // Clear message in Session
		}

		// Warning message
		$sWarningMessage = $this->getWarningMessage();
		$this->Message_Showing($sWarningMessage, "warning");
		if ($sWarningMessage <> "") { // Message in Session, display
			$html .= "<table class=\"ewMessageTable\"><tr><td class=\"ewWarningIcon\"></td><td class=\"ewWarningMessage\">" . $sWarningMessage . "</td></tr></table>";
			$_SESSION[EW_SESSION_WARNING_MESSAGE] = ""; // Clear message in Session
		}

		// Success message
		$sSuccessMessage = $this->getSuccessMessage();
		$this->Message_Showing($sSuccessMessage, "success");
		if ($sSuccessMessage <> "") { // Message in Session, display
			$html .= "<table class=\"ewMessageTable\"><tr><td class=\"ewSuccessIcon\"></td><td class=\"ewSuccessMessage\">" . $sSuccessMessage . "</td></tr></table>";
			$_SESSION[EW_SESSION_SUCCESS_MESSAGE] = ""; // Clear message in Session
		}

		// Failure message
		$sErrorMessage = $this->getFailureMessage();
		$this->Message_Showing($sErrorMessage, "failure");
		if ($sErrorMessage <> "") { // Message in Session, display
			$html .= "<table class=\"ewMessageTable\"><tr><td class=\"ewErrorIcon\"></td><td class=\"ewErrorMessage\">" . $sErrorMessage . "</td></tr></table>";
			$_SESSION[EW_SESSION_FAILURE_MESSAGE] = ""; // Clear message in Session
		}
		echo "<div class=\"ewMessageDialog\"" . (($hidden) ? " style=\"display: none;\"" : "") . ">" . $html . "</div>";
	}
	var $PageHeader;
	var $PageFooter;

	// Show Page Header
	function ShowPageHeader() {
		$sHeader = $this->PageHeader;
		$this->Page_DataRendering($sHeader);
		if ($sHeader <> "") { // Header exists, display
			echo "<p class=\"phpmaker\">" . $sHeader . "</p>";
		}
	}

	// Show Page Footer
	function ShowPageFooter() {
		$sFooter = $this->PageFooter;
		$this->Page_DataRendered($sFooter);
		if ($sFooter <> "") { // Fotoer exists, display
			echo "<p class=\"phpmaker\">" . $sFooter . "</p>";
		}
	}

	// Validate page request
	function IsPageRequest() {
		global $objForm;
		if ($this->UseTokenInUrl) {
			if ($objForm)
				return ($this->TableVar == $objForm->GetValue("t"));
			if (@$_GET["t"] <> "")
				return ($this->TableVar == $_GET["t"]);
		} else {
			return TRUE;
		}
	}

	//
	// Page class constructor
	//
	function __construct() {
		global $conn, $Language, $UserAgent;

		// User agent
		$UserAgent = ew_UserAgent();
		$GLOBALS["Page"] = &$this;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();

		// Parent constuctor
		parent::__construct();

		// Table object (lh_dana)
		if (!isset($GLOBALS["lh_dana"])) {
			$GLOBALS["lh_dana"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["lh_dana"];
		}

		// Table object (lh_user)
		if (!isset($GLOBALS['lh_user'])) $GLOBALS['lh_user'] = new clh_user();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'edit', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'lh_dana', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect();
	}

	// 
	//  Page_Init
	//
	function Page_Init() {
		global $gsExport, $gsExportFile, $UserProfile, $Language, $Security, $objForm;

		// Security
		$Security = new cAdvancedSecurity();
		if (!$Security->IsLoggedIn()) $Security->AutoLogin();
		if (!$Security->IsLoggedIn()) {
			$Security->SaveLastUrl();
			$this->Page_Terminate("login.php");
		}
		$Security->TablePermission_Loading();
		$Security->LoadCurrentUserLevel($this->ProjectID . $this->TableName);
		$Security->TablePermission_Loaded();
		if (!$Security->IsLoggedIn()) {
			$Security->SaveLastUrl();
			$this->Page_Terminate("login.php");
		}
		if (!$Security->CanEdit()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage($Language->Phrase("NoPermission")); // Set no permission
			$this->Page_Terminate("lh_danalist.php");
		}

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"];
		$this->id_dana->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

		// Global Page Loading event (in userfn*.php)
		Page_Loading();

		// Page Load event
		$this->Page_Load();
	}

	//
	// Page_Terminate
	//
	function Page_Terminate($url = "") {
		global $conn;

		// Page Unload event
		$this->Page_Unload();

		// Global Page Unloaded event (in userfn*.php)
		Page_Unloaded();
		$this->Page_Redirecting($url);

		 // Close connection
		$conn->Close();

		// Go to URL if specified
		if ($url <> "") {
			if (!EW_DEBUG_ENABLED && ob_get_length())
				ob_end_clean();
			header("Location: " . $url);
		}
		exit();
	}
	var $DbMasterFilter;
	var $DbDetailFilter;

	// 
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsFormError;

		// Load key from QueryString
		if (@$_GET["id_dana"] <> "")
			$this->id_dana->setQueryStringValue($_GET["id_dana"]);

		// Process form if post back
		if (@$_POST["a_edit"] <> "") {
			$this->CurrentAction = $_POST["a_edit"]; // Get action code
			$this->LoadFormValues(); // Get form values
		} else {
			$this->CurrentAction = "I"; // Default action is display
		}

		// Check if valid key
		if ($this->id_dana->CurrentValue == "")
			$this->Page_Terminate("lh_danalist.php"); // Invalid key, return to list

		// Validate form if post back
		if (@$_POST["a_edit"] <> "") {
			if (!$this->ValidateForm()) {
				$this->CurrentAction = ""; // Form error, reset action
				$this->setFailureMessage($gsFormError);
				$this->EventCancelled = TRUE; // Event cancelled
				$this->RestoreFormValues();
			}
		}
		switch ($this->CurrentAction) {
			case "I": // Get a record to display
				if (!$this->LoadRow()) { // Load record based on key
					if ($this->getFailureMessage() == "") $this->setFailureMessage($Language->Phrase("NoRecord")); // No record found
					$this->Page_Terminate("lh_danalist.php"); // No matching record, return to list
				}
				break;
			Case "U": // Update
				$this->SendEmail = TRUE; // Send email on update success
				if ($this->EditRow()) { // Update record based on key
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("UpdateSuccess")); // Update success
					$sReturnUrl = $this->getReturnUrl();
					$this->Page_Terminate($sReturnUrl); // Return to caller
				} else {
					$this->EventCancelled = TRUE; // Event cancelled
					$this->RestoreFormValues(); // Restore form values if update failed
				}
		}

		// Render the record
		$this->RowType = EW_ROWTYPE_EDIT; // Render as Edit
		$this->ResetAttrs();
		$this->RenderRow();
	}

	// Get upload files
	function GetUploadFiles() {
		global $objForm;

		// Get upload data
		$index = $objForm->Index; // Save form index
		$objForm->Index = -1;
		$confirmPage = (strval($objForm->GetValue("a_confirm")) <> "");
		$objForm->Index = $index; // Restore form index
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->id_dana->FldIsDetailKey)
			$this->id_dana->setFormValue($objForm->GetValue("x_id_dana"));
		if (!$this->jumlah_dana->FldIsDetailKey) {
			$this->jumlah_dana->setFormValue($objForm->GetValue("x_jumlah_dana"));
		}
		if (!$this->periode_pembiayaan->FldIsDetailKey) {
			$this->periode_pembiayaan->setFormValue($objForm->GetValue("x_periode_pembiayaan"));
			$this->periode_pembiayaan->CurrentValue = ew_UnFormatDateTime($this->periode_pembiayaan->CurrentValue, 7);
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadRow();
		$this->id_dana->CurrentValue = $this->id_dana->FormValue;
		$this->jumlah_dana->CurrentValue = $this->jumlah_dana->FormValue;
		$this->periode_pembiayaan->CurrentValue = $this->periode_pembiayaan->FormValue;
		$this->periode_pembiayaan->CurrentValue = ew_UnFormatDateTime($this->periode_pembiayaan->CurrentValue, 7);
	}

	// Load row based on key values
	function LoadRow() {
		global $conn, $Security, $Language;
		$sFilter = $this->KeyFilter();

		// Call Row Selecting event
		$this->Row_Selecting($sFilter);

		// Load SQL based on filter
		$this->CurrentFilter = $sFilter;
		$sSql = $this->SQL();
		$res = FALSE;
		$rs = ew_LoadRecordset($sSql);
		if ($rs && !$rs->EOF) {
			$res = TRUE;
			$this->LoadRowValues($rs); // Load row values
			$rs->Close();
		}
		return $res;
	}

	// Load row values from recordset
	function LoadRowValues(&$rs) {
		global $conn;
		if (!$rs || $rs->EOF) return;

		// Call Row Selected event
		$row = &$rs->fields;
		$this->Row_Selected($row);
		$this->id_dana->setDbValue($rs->fields('id_dana'));
		$this->jumlah_dana->setDbValue($rs->fields('jumlah_dana'));
		$this->periode_pembiayaan->setDbValue($rs->fields('periode_pembiayaan'));
	}

	// Render row values based on field settings
	function RenderRow() {
		global $conn, $Security, $Language;
		global $gsLanguage;

		// Initialize URLs
		// Convert decimal values if posted back

		if ($this->jumlah_dana->FormValue == $this->jumlah_dana->CurrentValue && is_numeric(ew_StrToFloat($this->jumlah_dana->CurrentValue)))
			$this->jumlah_dana->CurrentValue = ew_StrToFloat($this->jumlah_dana->CurrentValue);

		// Call Row_Rendering event
		$this->Row_Rendering();

		// Common render codes for all row types
		// id_dana
		// jumlah_dana
		// periode_pembiayaan

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// id_dana
			$this->id_dana->ViewValue = $this->id_dana->CurrentValue;
			$this->id_dana->ViewCustomAttributes = "";

			// jumlah_dana
			$this->jumlah_dana->ViewValue = $this->jumlah_dana->CurrentValue;
			$this->jumlah_dana->ViewValue = ew_FormatNumber($this->jumlah_dana->ViewValue, 2, -1, 0, -1);
			$this->jumlah_dana->CellCssStyle .= "text-align: right;";
			$this->jumlah_dana->ViewCustomAttributes = "";

			// periode_pembiayaan
			$this->periode_pembiayaan->ViewValue = $this->periode_pembiayaan->CurrentValue;
			$this->periode_pembiayaan->ViewValue = ew_FormatDateTime($this->periode_pembiayaan->ViewValue, 7);
			$this->periode_pembiayaan->ViewCustomAttributes = "";

			// id_dana
			$this->id_dana->LinkCustomAttributes = "";
			$this->id_dana->HrefValue = "";
			$this->id_dana->TooltipValue = "";

			// jumlah_dana
			$this->jumlah_dana->LinkCustomAttributes = "";
			$this->jumlah_dana->HrefValue = "";
			$this->jumlah_dana->TooltipValue = "";

			// periode_pembiayaan
			$this->periode_pembiayaan->LinkCustomAttributes = "";
			$this->periode_pembiayaan->HrefValue = "";
			$this->periode_pembiayaan->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_EDIT) { // Edit row

			// id_dana
			$this->id_dana->EditCustomAttributes = "";
			$this->id_dana->EditValue = $this->id_dana->CurrentValue;
			$this->id_dana->ViewCustomAttributes = "";

			// jumlah_dana
			$this->jumlah_dana->EditCustomAttributes = "";
			$this->jumlah_dana->EditValue = ew_HtmlEncode($this->jumlah_dana->CurrentValue);
			if (strval($this->jumlah_dana->EditValue) <> "" && is_numeric($this->jumlah_dana->EditValue)) $this->jumlah_dana->EditValue = ew_FormatNumber($this->jumlah_dana->EditValue, -2, -1, 0, -1);

			// periode_pembiayaan
			$this->periode_pembiayaan->EditCustomAttributes = "";
			$this->periode_pembiayaan->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->periode_pembiayaan->CurrentValue, 7));

			// Edit refer script
			// id_dana

			$this->id_dana->HrefValue = "";

			// jumlah_dana
			$this->jumlah_dana->HrefValue = "";

			// periode_pembiayaan
			$this->periode_pembiayaan->HrefValue = "";
		}
		if ($this->RowType == EW_ROWTYPE_ADD ||
			$this->RowType == EW_ROWTYPE_EDIT ||
			$this->RowType == EW_ROWTYPE_SEARCH) { // Add / Edit / Search row
			$this->SetupFieldTitles();
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	// Validate form
	function ValidateForm() {
		global $Language, $gsFormError;

		// Initialize form error message
		$gsFormError = "";

		// Check if validation required
		if (!EW_SERVER_VALIDATE)
			return ($gsFormError == "");
		if (!is_null($this->jumlah_dana->FormValue) && $this->jumlah_dana->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->jumlah_dana->FldCaption());
		}
		if (!ew_CheckNumber($this->jumlah_dana->FormValue)) {
			ew_AddMessage($gsFormError, $this->jumlah_dana->FldErrMsg());
		}
		if (!is_null($this->periode_pembiayaan->FormValue) && $this->periode_pembiayaan->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->periode_pembiayaan->FldCaption());
		}
		if (!ew_CheckEuroDate($this->periode_pembiayaan->FormValue)) {
			ew_AddMessage($gsFormError, $this->periode_pembiayaan->FldErrMsg());
		}

		// Return validate result
		$ValidateForm = ($gsFormError == "");

		// Call Form_CustomValidate event
		$sFormCustomError = "";
		$ValidateForm = $ValidateForm && $this->Form_CustomValidate($sFormCustomError);
		if ($sFormCustomError <> "") {
			ew_AddMessage($gsFormError, $sFormCustomError);
		}
		return $ValidateForm;
	}

	// Update record based on key values
	function EditRow() {
		global $conn, $Security, $Language;
		$sFilter = $this->KeyFilter();
		$this->CurrentFilter = $sFilter;
		$sSql = $this->SQL();
		$conn->raiseErrorFn = 'ew_ErrorFn';
		$rs = $conn->Execute($sSql);
		$conn->raiseErrorFn = '';
		if ($rs === FALSE)
			return FALSE;
		if ($rs->EOF) {
			$EditRow = FALSE; // Update Failed
		} else {

			// Save old values
			$rsold = &$rs->fields;
			$rsnew = array();

			// jumlah_dana
			$this->jumlah_dana->SetDbValueDef($rsnew, $this->jumlah_dana->CurrentValue, 0, $this->jumlah_dana->ReadOnly);

			// periode_pembiayaan
			$this->periode_pembiayaan->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->periode_pembiayaan->CurrentValue, 7), ew_CurrentDate(), $this->periode_pembiayaan->ReadOnly);

			// Call Row Updating event
			$bUpdateRow = $this->Row_Updating($rsold, $rsnew);
			if ($bUpdateRow) {
				$conn->raiseErrorFn = 'ew_ErrorFn';
				if (count($rsnew) > 0)
					$EditRow = $this->Update($rsnew);
				else
					$EditRow = TRUE; // No field to update
				$conn->raiseErrorFn = '';
			} else {
				if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

					// Use the message, do nothing
				} elseif ($this->CancelMessage <> "") {
					$this->setFailureMessage($this->CancelMessage);
					$this->CancelMessage = "";
				} else {
					$this->setFailureMessage($Language->Phrase("UpdateCancelled"));
				}
				$EditRow = FALSE;
			}
		}

		// Call Row_Updated event
		if ($EditRow)
			$this->Row_Updated($rsold, $rsnew);
		$rs->Close();
		return $EditRow;
	}

	// Page Load event
	function Page_Load() {

		//echo "Page Load";
	}

	// Page Unload event
	function Page_Unload() {

		//echo "Page Unload";
	}

	// Page Redirecting event
	function Page_Redirecting(&$url) {

		// Example:
		//$url = "your URL";

	}

	// Message Showing event
	// $type = ''|'success'|'failure'|'warning'
	function Message_Showing(&$msg, $type) {
		if ($type == 'success') {

			//$msg = "your success message";
		} elseif ($type == 'failure') {

			//$msg = "your failure message";
		} elseif ($type == 'warning') {

			//$msg = "your warning message";
		} else {

			//$msg = "your message";
		}
	}

	// Page Data Rendering event
	function Page_DataRendering(&$header) {

		// Example:
		//$header = "your header";

	}

	// Page Data Rendered event
	function Page_DataRendered(&$footer) {

		// Example:
		//$footer = "your footer";

	}

	// Form Custom Validate event
	function Form_CustomValidate(&$CustomError) {

		// Return error message in CustomError
		return TRUE;
	}
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($lh_dana_edit)) $lh_dana_edit = new clh_dana_edit();

// Page init
$lh_dana_edit->Page_Init();

// Page main
$lh_dana_edit->Page_Main();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var lh_dana_edit = new ew_Page("lh_dana_edit");
lh_dana_edit.PageID = "edit"; // Page ID
var EW_PAGE_ID = lh_dana_edit.PageID; // For backward compatibility

// Form object
var flh_danaedit = new ew_Form("flh_danaedit");

// Validate form
flh_danaedit.Validate = function(fobj) {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	fobj = fobj || this.Form;
	this.PostAutoSuggest();	
	if (fobj.a_confirm && fobj.a_confirm.value == "F")
		return true;
	var elm, aelm;
	var rowcnt = 1;
	var startcnt = (rowcnt == 0) ? 0 : 1; // rowcnt == 0 => Inline-Add
	for (var i = startcnt; i <= rowcnt; i++) {
		var infix = "";
		elm = fobj.elements["x" + infix + "_jumlah_dana"];
		if (elm && !ew_HasValue(elm))
			return ew_OnError(this, elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($lh_dana->jumlah_dana->FldCaption()) ?>");
		elm = fobj.elements["x" + infix + "_jumlah_dana"];
		if (elm && !ew_CheckNumber(elm.value))
			return ew_OnError(this, elm, "<?php echo ew_JsEncode2($lh_dana->jumlah_dana->FldErrMsg()) ?>");
		elm = fobj.elements["x" + infix + "_periode_pembiayaan"];
		if (elm && !ew_HasValue(elm))
			return ew_OnError(this, elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($lh_dana->periode_pembiayaan->FldCaption()) ?>");
		elm = fobj.elements["x" + infix + "_periode_pembiayaan"];
		if (elm && !ew_CheckEuroDate(elm.value))
			return ew_OnError(this, elm, "<?php echo ew_JsEncode2($lh_dana->periode_pembiayaan->FldErrMsg()) ?>");

		// Set up row object
		ew_ElementsToRow(fobj, infix);

		// Fire Form_CustomValidate event
		if (!this.Form_CustomValidate(fobj))
			return false;
	}

	// Process detail page
	if (fobj.detailpage && fobj.detailpage.value && ewForms[fobj.detailpage.value])
		return ewForms[fobj.detailpage.value].Validate(fobj);
	return true;
}

// Form_CustomValidate event
flh_danaedit.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
flh_danaedit.ValidateRequired = true;
<?php } else { ?>
flh_danaedit.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
// Form object for search

</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<p><span id="ewPageCaption" class="ewTitle ewTableTitle"><?php echo $Language->Phrase("Edit") ?>&nbsp;<?php echo $Language->Phrase("TblTypeTABLE") ?><?php echo $lh_dana->TableCaption() ?></span></p>
<p class="phpmaker"><a href="<?php echo $lh_dana->getReturnUrl() ?>" id="a_GoBack" class="ewLink"><?php echo $Language->Phrase("GoBack") ?></a></p>
<?php $lh_dana_edit->ShowPageHeader(); ?>
<?php
$lh_dana_edit->ShowMessage();
?>
<form name="flh_danaedit" id="flh_danaedit" class="ewForm" action="<?php echo ew_CurrentPage() ?>" method="post" onsubmit="return ewForms[this.id].Submit();">
<br>
<input type="hidden" name="t" value="lh_dana">
<input type="hidden" name="a_edit" id="a_edit" value="U">
<table cellspacing="0" class="ewGrid"><tr><td class="ewGridContent">
<div class="ewGridMiddlePanel">
<table id="tbl_lh_danaedit" class="ewTable">
<?php if ($lh_dana->id_dana->Visible) { // id_dana ?>
	<tr id="r_id_dana"<?php echo $lh_dana->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_lh_dana_id_dana"><table class="ewTableHeaderBtn"><tr><td><?php echo $lh_dana->id_dana->FldCaption() ?></td></tr></table></span></td>
		<td<?php echo $lh_dana->id_dana->CellAttributes() ?>><span id="el_lh_dana_id_dana">
<span<?php echo $lh_dana->id_dana->ViewAttributes() ?>>
<?php echo $lh_dana->id_dana->EditValue ?></span>
<input type="hidden" name="x_id_dana" id="x_id_dana" value="<?php echo ew_HtmlEncode($lh_dana->id_dana->CurrentValue) ?>">
</span><?php echo $lh_dana->id_dana->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($lh_dana->jumlah_dana->Visible) { // jumlah_dana ?>
	<tr id="r_jumlah_dana"<?php echo $lh_dana->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_lh_dana_jumlah_dana"><table class="ewTableHeaderBtn"><tr><td><?php echo $lh_dana->jumlah_dana->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></td></tr></table></span></td>
		<td<?php echo $lh_dana->jumlah_dana->CellAttributes() ?>><span id="el_lh_dana_jumlah_dana">
<input type="text" name="x_jumlah_dana" id="x_jumlah_dana" size="30" value="<?php echo $lh_dana->jumlah_dana->EditValue ?>"<?php echo $lh_dana->jumlah_dana->EditAttributes() ?>>
</span><?php echo $lh_dana->jumlah_dana->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($lh_dana->periode_pembiayaan->Visible) { // periode_pembiayaan ?>
	<tr id="r_periode_pembiayaan"<?php echo $lh_dana->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_lh_dana_periode_pembiayaan"><table class="ewTableHeaderBtn"><tr><td><?php echo $lh_dana->periode_pembiayaan->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></td></tr></table></span></td>
		<td<?php echo $lh_dana->periode_pembiayaan->CellAttributes() ?>><span id="el_lh_dana_periode_pembiayaan">
<input type="text" name="x_periode_pembiayaan" id="x_periode_pembiayaan" value="<?php echo $lh_dana->periode_pembiayaan->EditValue ?>"<?php echo $lh_dana->periode_pembiayaan->EditAttributes() ?>>
<?php if (!$lh_dana->periode_pembiayaan->ReadOnly && !$lh_dana->periode_pembiayaan->Disabled && @$lh_dana->periode_pembiayaan->EditAttrs["readonly"] == "" && @$lh_dana->periode_pembiayaan->EditAttrs["disabled"] == "") { ?>
&nbsp;<img src="phpimages/calendar.png" id="flh_danaedit$x_periode_pembiayaan$" name="flh_danaedit$x_periode_pembiayaan$" alt="<?php echo $Language->Phrase("PickDate") ?>" title="<?php echo $Language->Phrase("PickDate") ?>" class="ewCalendar" style="border: 0;">
<script type="text/javascript">
ew_CreateCalendar("flh_danaedit", "x_periode_pembiayaan", "%d/%m/%Y");
</script>
<?php } ?>
</span><?php echo $lh_dana->periode_pembiayaan->CustomMsg ?></td>
	</tr>
<?php } ?>
</table>
</div>
</td></tr></table>
<br>
<input type="submit" name="btnAction" id="btnAction" value="<?php echo ew_BtnCaption($Language->Phrase("EditBtn")) ?>">
</form>
<script type="text/javascript">
flh_danaedit.Init();
</script>
<?php
$lh_dana_edit->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$lh_dana_edit->Page_Terminate();
?>
