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

$lh_dana_add = NULL; // Initialize page object first

class clh_dana_add extends clh_dana {

	// Page ID
	var $PageID = 'add';

	// Project ID
	var $ProjectID = "{67264FB2-6364-478B-87DD-B3E0D7A29425}";

	// Table name
	var $TableName = 'lh_dana';

	// Page object name
	var $PageObjName = 'lh_dana_add';

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
			define("EW_PAGE_ID", 'add', TRUE);

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
		if (!$Security->CanAdd()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage($Language->Phrase("NoPermission")); // Set no permission
			$this->Page_Terminate("lh_danalist.php");
		}

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"];

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
	var $DbMasterFilter = "";
	var $DbDetailFilter = "";
	var $Priv = 0;
	var $OldRecordset;
	var $CopyRecord;

	// 
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsFormError;

		// Process form if post back
		if (@$_POST["a_add"] <> "") {
			$this->CurrentAction = $_POST["a_add"]; // Get form action
			$this->CopyRecord = $this->LoadOldRecord(); // Load old recordset
			$this->LoadFormValues(); // Load form values
		} else { // Not post back

			// Load key values from QueryString
			$this->CopyRecord = TRUE;
			if (@$_GET["id_dana"] != "") {
				$this->id_dana->setQueryStringValue($_GET["id_dana"]);
				$this->setKey("id_dana", $this->id_dana->CurrentValue); // Set up key
			} else {
				$this->setKey("id_dana", ""); // Clear key
				$this->CopyRecord = FALSE;
			}
			if ($this->CopyRecord) {
				$this->CurrentAction = "C"; // Copy record
			} else {
				$this->CurrentAction = "I"; // Display blank record
				$this->LoadDefaultValues(); // Load default values
			}
		}

		// Validate form if post back
		if (@$_POST["a_add"] <> "") {
			if (!$this->ValidateForm()) {
				$this->CurrentAction = "I"; // Form error, reset action
				$this->EventCancelled = TRUE; // Event cancelled
				$this->RestoreFormValues(); // Restore form values
				$this->setFailureMessage($gsFormError);
			}
		}

		// Perform action based on action code
		switch ($this->CurrentAction) {
			case "I": // Blank record, no action required
				break;
			case "C": // Copy an existing record
				if (!$this->LoadRow()) { // Load record based on key
					if ($this->getFailureMessage() == "") $this->setFailureMessage($Language->Phrase("NoRecord")); // No record found
					$this->Page_Terminate("lh_danalist.php"); // No matching record, return to list
				}
				break;
			case "A": // Add new record
				$this->SendEmail = TRUE; // Send email on add success
				if ($this->AddRow($this->OldRecordset)) { // Add successful
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("AddSuccess")); // Set up success message
					$sReturnUrl = $this->getReturnUrl();
					if (ew_GetPageName($sReturnUrl) == "lh_danaview.php")
						$sReturnUrl = $this->GetViewUrl(); // View paging, return to view page with keyurl directly
					$this->Page_Terminate($sReturnUrl); // Clean up and return
				} else {
					$this->EventCancelled = TRUE; // Event cancelled
					$this->RestoreFormValues(); // Add failed, restore form values
				}
		}

		// Render row based on row type
		$this->RowType = EW_ROWTYPE_ADD;  // Render add type

		// Render row
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

	// Load default values
	function LoadDefaultValues() {
		$this->jumlah_dana->CurrentValue = NULL;
		$this->jumlah_dana->OldValue = $this->jumlah_dana->CurrentValue;
		$this->periode_pembiayaan->CurrentValue = NULL;
		$this->periode_pembiayaan->OldValue = $this->periode_pembiayaan->CurrentValue;
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
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
		$this->LoadOldRecord();
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

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("id_dana")) <> "")
			$this->id_dana->CurrentValue = $this->getKey("id_dana"); // id_dana
		else
			$bValidKey = FALSE;

		// Load old recordset
		if ($bValidKey) {
			$this->CurrentFilter = $this->KeyFilter();
			$sSql = $this->SQL();
			$this->OldRecordset = ew_LoadRecordset($sSql);
			$this->LoadRowValues($this->OldRecordset); // Load row values
		} else {
			$this->OldRecordset = NULL;
		}
		return $bValidKey;
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

			// jumlah_dana
			$this->jumlah_dana->LinkCustomAttributes = "";
			$this->jumlah_dana->HrefValue = "";
			$this->jumlah_dana->TooltipValue = "";

			// periode_pembiayaan
			$this->periode_pembiayaan->LinkCustomAttributes = "";
			$this->periode_pembiayaan->HrefValue = "";
			$this->periode_pembiayaan->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

			// jumlah_dana
			$this->jumlah_dana->EditCustomAttributes = "";
			$this->jumlah_dana->EditValue = ew_HtmlEncode($this->jumlah_dana->CurrentValue);
			if (strval($this->jumlah_dana->EditValue) <> "" && is_numeric($this->jumlah_dana->EditValue)) $this->jumlah_dana->EditValue = ew_FormatNumber($this->jumlah_dana->EditValue, -2, -1, 0, -1);

			// periode_pembiayaan
			$this->periode_pembiayaan->EditCustomAttributes = "";
			$this->periode_pembiayaan->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->periode_pembiayaan->CurrentValue, 7));

			// Edit refer script
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

	// Add record
	function AddRow($rsold = NULL) {
		global $conn, $Language, $Security;
		$rsnew = array();

		// jumlah_dana
		$this->jumlah_dana->SetDbValueDef($rsnew, $this->jumlah_dana->CurrentValue, 0, FALSE);

		// periode_pembiayaan
		$this->periode_pembiayaan->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->periode_pembiayaan->CurrentValue, 7), ew_CurrentDate(), FALSE);

		// Call Row Inserting event
		$rs = ($rsold == NULL) ? NULL : $rsold->fields;
		$bInsertRow = $this->Row_Inserting($rs, $rsnew);
		if ($bInsertRow) {
			$conn->raiseErrorFn = 'ew_ErrorFn';
			$AddRow = $this->Insert($rsnew);
			$conn->raiseErrorFn = '';
			if ($AddRow) {
			}
		} else {
			if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

				// Use the message, do nothing
			} elseif ($this->CancelMessage <> "") {
				$this->setFailureMessage($this->CancelMessage);
				$this->CancelMessage = "";
			} else {
				$this->setFailureMessage($Language->Phrase("InsertCancelled"));
			}
			$AddRow = FALSE;
		}

		// Get insert id if necessary
		if ($AddRow) {
			$this->id_dana->setDbValue($conn->Insert_ID());
			$rsnew['id_dana'] = $this->id_dana->DbValue;
		}
		if ($AddRow) {

			// Call Row Inserted event
			$rs = ($rsold == NULL) ? NULL : $rsold->fields;
			$this->Row_Inserted($rs, $rsnew);
		}
		return $AddRow;
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
if (!isset($lh_dana_add)) $lh_dana_add = new clh_dana_add();

// Page init
$lh_dana_add->Page_Init();

// Page main
$lh_dana_add->Page_Main();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var lh_dana_add = new ew_Page("lh_dana_add");
lh_dana_add.PageID = "add"; // Page ID
var EW_PAGE_ID = lh_dana_add.PageID; // For backward compatibility

// Form object
var flh_danaadd = new ew_Form("flh_danaadd");

// Validate form
flh_danaadd.Validate = function(fobj) {
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
flh_danaadd.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
flh_danaadd.ValidateRequired = true;
<?php } else { ?>
flh_danaadd.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
// Form object for search

</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<p><span id="ewPageCaption" class="ewTitle ewTableTitle"><?php echo $Language->Phrase("Add") ?>&nbsp;<?php echo $Language->Phrase("TblTypeTABLE") ?><?php echo $lh_dana->TableCaption() ?></span></p>
<p class="phpmaker"><a href="<?php echo $lh_dana->getReturnUrl() ?>" id="a_GoBack" class="ewLink"><?php echo $Language->Phrase("GoBack") ?></a></p>
<?php $lh_dana_add->ShowPageHeader(); ?>
<?php
$lh_dana_add->ShowMessage();
?>
<form name="flh_danaadd" id="flh_danaadd" class="ewForm" action="<?php echo ew_CurrentPage() ?>" method="post" onsubmit="return ewForms[this.id].Submit();">
<br>
<input type="hidden" name="t" value="lh_dana">
<input type="hidden" name="a_add" id="a_add" value="A">
<table cellspacing="0" class="ewGrid"><tr><td class="ewGridContent">
<div class="ewGridMiddlePanel">
<table id="tbl_lh_danaadd" class="ewTable">
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
&nbsp;<img src="phpimages/calendar.png" id="flh_danaadd$x_periode_pembiayaan$" name="flh_danaadd$x_periode_pembiayaan$" alt="<?php echo $Language->Phrase("PickDate") ?>" title="<?php echo $Language->Phrase("PickDate") ?>" class="ewCalendar" style="border: 0;">
<script type="text/javascript">
ew_CreateCalendar("flh_danaadd", "x_periode_pembiayaan", "%d/%m/%Y");
</script>
<?php } ?>
</span><?php echo $lh_dana->periode_pembiayaan->CustomMsg ?></td>
	</tr>
<?php } ?>
</table>
</div>
</td></tr></table>
<br>
<input type="submit" name="btnAction" id="btnAction" value="<?php echo ew_BtnCaption($Language->Phrase("AddBtn")) ?>">
</form>
<script type="text/javascript">
flh_danaadd.Init();
</script>
<?php
$lh_dana_add->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$lh_dana_add->Page_Terminate();
?>
