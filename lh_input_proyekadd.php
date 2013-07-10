<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg9.php" ?>
<?php include_once "ewmysql9.php" ?>
<?php include_once "phpfn9.php" ?>
<?php include_once "lh_input_proyekinfo.php" ?>
<?php include_once "lh_userinfo.php" ?>
<?php include_once "userfn9.php" ?>
<?php

//
// Page class
//

$lh_input_proyek_add = NULL; // Initialize page object first

class clh_input_proyek_add extends clh_input_proyek {

	// Page ID
	var $PageID = 'add';

	// Project ID
	var $ProjectID = "{67264FB2-6364-478B-87DD-B3E0D7A29425}";

	// Table name
	var $TableName = 'lh_input_proyek';

	// Page object name
	var $PageObjName = 'lh_input_proyek_add';

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

		// Table object (lh_input_proyek)
		if (!isset($GLOBALS["lh_input_proyek"])) {
			$GLOBALS["lh_input_proyek"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["lh_input_proyek"];
		}

		// Table object (lh_user)
		if (!isset($GLOBALS['lh_user'])) $GLOBALS['lh_user'] = new clh_user();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'add', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'lh_input_proyek', TRUE);

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
			$this->Page_Terminate("lh_input_proyeklist.php");
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
			if (@$_GET["id_proyek"] != "") {
				$this->id_proyek->setQueryStringValue($_GET["id_proyek"]);
				$this->setKey("id_proyek", $this->id_proyek->CurrentValue); // Set up key
			} else {
				$this->setKey("id_proyek", ""); // Clear key
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
					$this->Page_Terminate("lh_input_proyeklist.php"); // No matching record, return to list
				}
				break;
			case "A": // Add new record
				$this->SendEmail = TRUE; // Send email on add success
				if ($this->AddRow($this->OldRecordset)) { // Add successful
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("AddSuccess")); // Set up success message
					$sReturnUrl = $this->getReturnUrl();
					if (ew_GetPageName($sReturnUrl) == "lh_input_proyekview.php")
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
		$this->nama_proyek->CurrentValue = NULL;
		$this->nama_proyek->OldValue = $this->nama_proyek->CurrentValue;
		$this->tanggal->CurrentValue = NULL;
		$this->tanggal->OldValue = $this->tanggal->CurrentValue;
		$this->kardus->CurrentValue = NULL;
		$this->kardus->OldValue = $this->kardus->CurrentValue;
		$this->kayu->CurrentValue = NULL;
		$this->kayu->OldValue = $this->kayu->CurrentValue;
		$this->besi->CurrentValue = NULL;
		$this->besi->OldValue = $this->besi->CurrentValue;
		$this->harga->CurrentValue = NULL;
		$this->harga->OldValue = $this->harga->CurrentValue;
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->nama_proyek->FldIsDetailKey) {
			$this->nama_proyek->setFormValue($objForm->GetValue("x_nama_proyek"));
		}
		if (!$this->tanggal->FldIsDetailKey) {
			$this->tanggal->setFormValue($objForm->GetValue("x_tanggal"));
			$this->tanggal->CurrentValue = ew_UnFormatDateTime($this->tanggal->CurrentValue, 7);
		}
		if (!$this->kardus->FldIsDetailKey) {
			$this->kardus->setFormValue($objForm->GetValue("x_kardus"));
		}
		if (!$this->kayu->FldIsDetailKey) {
			$this->kayu->setFormValue($objForm->GetValue("x_kayu"));
		}
		if (!$this->besi->FldIsDetailKey) {
			$this->besi->setFormValue($objForm->GetValue("x_besi"));
		}
		if (!$this->harga->FldIsDetailKey) {
			$this->harga->setFormValue($objForm->GetValue("x_harga"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadOldRecord();
		$this->nama_proyek->CurrentValue = $this->nama_proyek->FormValue;
		$this->tanggal->CurrentValue = $this->tanggal->FormValue;
		$this->tanggal->CurrentValue = ew_UnFormatDateTime($this->tanggal->CurrentValue, 7);
		$this->kardus->CurrentValue = $this->kardus->FormValue;
		$this->kayu->CurrentValue = $this->kayu->FormValue;
		$this->besi->CurrentValue = $this->besi->FormValue;
		$this->harga->CurrentValue = $this->harga->FormValue;
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
		$this->id_proyek->setDbValue($rs->fields('id_proyek'));
		$this->nama_proyek->setDbValue($rs->fields('nama_proyek'));
		$this->tanggal->setDbValue($rs->fields('tanggal'));
		$this->kardus->setDbValue($rs->fields('kardus'));
		$this->kayu->setDbValue($rs->fields('kayu'));
		$this->besi->setDbValue($rs->fields('besi'));
		$this->harga->setDbValue($rs->fields('harga'));
	}

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("id_proyek")) <> "")
			$this->id_proyek->CurrentValue = $this->getKey("id_proyek"); // id_proyek
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

		if ($this->harga->FormValue == $this->harga->CurrentValue && is_numeric(ew_StrToFloat($this->harga->CurrentValue)))
			$this->harga->CurrentValue = ew_StrToFloat($this->harga->CurrentValue);

		// Call Row_Rendering event
		$this->Row_Rendering();

		// Common render codes for all row types
		// id_proyek
		// nama_proyek
		// tanggal
		// kardus
		// kayu
		// besi
		// harga

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// id_proyek
			$this->id_proyek->ViewValue = $this->id_proyek->CurrentValue;
			$this->id_proyek->ViewCustomAttributes = "";

			// nama_proyek
			$this->nama_proyek->ViewValue = $this->nama_proyek->CurrentValue;
			$this->nama_proyek->ViewCustomAttributes = "";

			// tanggal
			$this->tanggal->ViewValue = $this->tanggal->CurrentValue;
			$this->tanggal->ViewValue = ew_FormatDateTime($this->tanggal->ViewValue, 7);
			$this->tanggal->ViewCustomAttributes = "";

			// kardus
			$this->kardus->ViewValue = $this->kardus->CurrentValue;
			$this->kardus->ViewCustomAttributes = "";

			// kayu
			$this->kayu->ViewValue = $this->kayu->CurrentValue;
			$this->kayu->ViewCustomAttributes = "";

			// besi
			$this->besi->ViewValue = $this->besi->CurrentValue;
			$this->besi->ViewCustomAttributes = "";

			// harga
			$this->harga->ViewValue = $this->harga->CurrentValue;
			$this->harga->ViewValue = ew_FormatNumber($this->harga->ViewValue, 2, -1, -2, -1);
			$this->harga->CellCssStyle .= "text-align: right;";
			$this->harga->ViewCustomAttributes = "";

			// nama_proyek
			$this->nama_proyek->LinkCustomAttributes = "";
			$this->nama_proyek->HrefValue = "";
			$this->nama_proyek->TooltipValue = "";

			// tanggal
			$this->tanggal->LinkCustomAttributes = "";
			$this->tanggal->HrefValue = "";
			$this->tanggal->TooltipValue = "";

			// kardus
			$this->kardus->LinkCustomAttributes = "";
			$this->kardus->HrefValue = "";
			$this->kardus->TooltipValue = "";

			// kayu
			$this->kayu->LinkCustomAttributes = "";
			$this->kayu->HrefValue = "";
			$this->kayu->TooltipValue = "";

			// besi
			$this->besi->LinkCustomAttributes = "";
			$this->besi->HrefValue = "";
			$this->besi->TooltipValue = "";

			// harga
			$this->harga->LinkCustomAttributes = "";
			$this->harga->HrefValue = "";
			$this->harga->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

			// nama_proyek
			$this->nama_proyek->EditCustomAttributes = "";
			$this->nama_proyek->EditValue = ew_HtmlEncode($this->nama_proyek->CurrentValue);

			// tanggal
			$this->tanggal->EditCustomAttributes = "";
			$this->tanggal->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->tanggal->CurrentValue, 7));

			// kardus
			$this->kardus->EditCustomAttributes = "";
			$this->kardus->EditValue = ew_HtmlEncode($this->kardus->CurrentValue);

			// kayu
			$this->kayu->EditCustomAttributes = "";
			$this->kayu->EditValue = ew_HtmlEncode($this->kayu->CurrentValue);

			// besi
			$this->besi->EditCustomAttributes = "";
			$this->besi->EditValue = ew_HtmlEncode($this->besi->CurrentValue);

			// harga
			$this->harga->EditCustomAttributes = "";
			$this->harga->EditValue = ew_HtmlEncode($this->harga->CurrentValue);
			if (strval($this->harga->EditValue) <> "" && is_numeric($this->harga->EditValue)) $this->harga->EditValue = ew_FormatNumber($this->harga->EditValue, -2, -1, -2, -1);

			// Edit refer script
			// nama_proyek

			$this->nama_proyek->HrefValue = "";

			// tanggal
			$this->tanggal->HrefValue = "";

			// kardus
			$this->kardus->HrefValue = "";

			// kayu
			$this->kayu->HrefValue = "";

			// besi
			$this->besi->HrefValue = "";

			// harga
			$this->harga->HrefValue = "";
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
		if (!is_null($this->nama_proyek->FormValue) && $this->nama_proyek->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->nama_proyek->FldCaption());
		}
		if (!is_null($this->tanggal->FormValue) && $this->tanggal->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->tanggal->FldCaption());
		}
		if (!ew_CheckEuroDate($this->tanggal->FormValue)) {
			ew_AddMessage($gsFormError, $this->tanggal->FldErrMsg());
		}
		if (!ew_CheckInteger($this->kardus->FormValue)) {
			ew_AddMessage($gsFormError, $this->kardus->FldErrMsg());
		}
		if (!ew_CheckInteger($this->kayu->FormValue)) {
			ew_AddMessage($gsFormError, $this->kayu->FldErrMsg());
		}
		if (!ew_CheckInteger($this->besi->FormValue)) {
			ew_AddMessage($gsFormError, $this->besi->FldErrMsg());
		}
		if (!is_null($this->harga->FormValue) && $this->harga->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->harga->FldCaption());
		}
		if (!ew_CheckNumber($this->harga->FormValue)) {
			ew_AddMessage($gsFormError, $this->harga->FldErrMsg());
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

		// nama_proyek
		$this->nama_proyek->SetDbValueDef($rsnew, $this->nama_proyek->CurrentValue, "", FALSE);

		// tanggal
		$this->tanggal->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->tanggal->CurrentValue, 7), ew_CurrentDate(), FALSE);

		// kardus
		$this->kardus->SetDbValueDef($rsnew, $this->kardus->CurrentValue, NULL, FALSE);

		// kayu
		$this->kayu->SetDbValueDef($rsnew, $this->kayu->CurrentValue, NULL, FALSE);

		// besi
		$this->besi->SetDbValueDef($rsnew, $this->besi->CurrentValue, NULL, FALSE);

		// harga
		$this->harga->SetDbValueDef($rsnew, $this->harga->CurrentValue, 0, FALSE);

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
			$this->id_proyek->setDbValue($conn->Insert_ID());
			$rsnew['id_proyek'] = $this->id_proyek->DbValue;
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
if (!isset($lh_input_proyek_add)) $lh_input_proyek_add = new clh_input_proyek_add();

// Page init
$lh_input_proyek_add->Page_Init();

// Page main
$lh_input_proyek_add->Page_Main();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var lh_input_proyek_add = new ew_Page("lh_input_proyek_add");
lh_input_proyek_add.PageID = "add"; // Page ID
var EW_PAGE_ID = lh_input_proyek_add.PageID; // For backward compatibility

// Form object
var flh_input_proyekadd = new ew_Form("flh_input_proyekadd");

// Validate form
flh_input_proyekadd.Validate = function(fobj) {
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
		elm = fobj.elements["x" + infix + "_nama_proyek"];
		if (elm && !ew_HasValue(elm))
			return ew_OnError(this, elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($lh_input_proyek->nama_proyek->FldCaption()) ?>");
		elm = fobj.elements["x" + infix + "_tanggal"];
		if (elm && !ew_HasValue(elm))
			return ew_OnError(this, elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($lh_input_proyek->tanggal->FldCaption()) ?>");
		elm = fobj.elements["x" + infix + "_tanggal"];
		if (elm && !ew_CheckEuroDate(elm.value))
			return ew_OnError(this, elm, "<?php echo ew_JsEncode2($lh_input_proyek->tanggal->FldErrMsg()) ?>");
		elm = fobj.elements["x" + infix + "_kardus"];
		if (elm && !ew_CheckInteger(elm.value))
			return ew_OnError(this, elm, "<?php echo ew_JsEncode2($lh_input_proyek->kardus->FldErrMsg()) ?>");
		elm = fobj.elements["x" + infix + "_kayu"];
		if (elm && !ew_CheckInteger(elm.value))
			return ew_OnError(this, elm, "<?php echo ew_JsEncode2($lh_input_proyek->kayu->FldErrMsg()) ?>");
		elm = fobj.elements["x" + infix + "_besi"];
		if (elm && !ew_CheckInteger(elm.value))
			return ew_OnError(this, elm, "<?php echo ew_JsEncode2($lh_input_proyek->besi->FldErrMsg()) ?>");
		elm = fobj.elements["x" + infix + "_harga"];
		if (elm && !ew_HasValue(elm))
			return ew_OnError(this, elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($lh_input_proyek->harga->FldCaption()) ?>");
		elm = fobj.elements["x" + infix + "_harga"];
		if (elm && !ew_CheckNumber(elm.value))
			return ew_OnError(this, elm, "<?php echo ew_JsEncode2($lh_input_proyek->harga->FldErrMsg()) ?>");

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
flh_input_proyekadd.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
flh_input_proyekadd.ValidateRequired = true;
<?php } else { ?>
flh_input_proyekadd.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
// Form object for search

</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<p><span id="ewPageCaption" class="ewTitle ewTableTitle"><?php echo $Language->Phrase("Add") ?>&nbsp;<?php echo $Language->Phrase("TblTypeVIEW") ?><?php echo $lh_input_proyek->TableCaption() ?></span></p>
<p class="phpmaker"><a href="<?php echo $lh_input_proyek->getReturnUrl() ?>" id="a_GoBack" class="ewLink"><?php echo $Language->Phrase("GoBack") ?></a></p>
<?php $lh_input_proyek_add->ShowPageHeader(); ?>
<?php
$lh_input_proyek_add->ShowMessage();
?>
<form name="flh_input_proyekadd" id="flh_input_proyekadd" class="ewForm" action="<?php echo ew_CurrentPage() ?>" method="post" onsubmit="return ewForms[this.id].Submit();">
<br>
<input type="hidden" name="t" value="lh_input_proyek">
<input type="hidden" name="a_add" id="a_add" value="A">
<table cellspacing="0" class="ewGrid"><tr><td class="ewGridContent">
<div class="ewGridMiddlePanel">
<table id="tbl_lh_input_proyekadd" class="ewTable">
<?php if ($lh_input_proyek->nama_proyek->Visible) { // nama_proyek ?>
	<tr id="r_nama_proyek"<?php echo $lh_input_proyek->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_lh_input_proyek_nama_proyek"><table class="ewTableHeaderBtn"><tr><td><?php echo $lh_input_proyek->nama_proyek->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></td></tr></table></span></td>
		<td<?php echo $lh_input_proyek->nama_proyek->CellAttributes() ?>><span id="el_lh_input_proyek_nama_proyek">
<input type="text" name="x_nama_proyek" id="x_nama_proyek" size="30" maxlength="255" value="<?php echo $lh_input_proyek->nama_proyek->EditValue ?>"<?php echo $lh_input_proyek->nama_proyek->EditAttributes() ?>>
</span><?php echo $lh_input_proyek->nama_proyek->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($lh_input_proyek->tanggal->Visible) { // tanggal ?>
	<tr id="r_tanggal"<?php echo $lh_input_proyek->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_lh_input_proyek_tanggal"><table class="ewTableHeaderBtn"><tr><td><?php echo $lh_input_proyek->tanggal->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></td></tr></table></span></td>
		<td<?php echo $lh_input_proyek->tanggal->CellAttributes() ?>><span id="el_lh_input_proyek_tanggal">
<input type="text" name="x_tanggal" id="x_tanggal" value="<?php echo $lh_input_proyek->tanggal->EditValue ?>"<?php echo $lh_input_proyek->tanggal->EditAttributes() ?>>
<?php if (!$lh_input_proyek->tanggal->ReadOnly && !$lh_input_proyek->tanggal->Disabled && @$lh_input_proyek->tanggal->EditAttrs["readonly"] == "" && @$lh_input_proyek->tanggal->EditAttrs["disabled"] == "") { ?>
&nbsp;<img src="phpimages/calendar.png" id="flh_input_proyekadd$x_tanggal$" name="flh_input_proyekadd$x_tanggal$" alt="<?php echo $Language->Phrase("PickDate") ?>" title="<?php echo $Language->Phrase("PickDate") ?>" class="ewCalendar" style="border: 0;">
<script type="text/javascript">
ew_CreateCalendar("flh_input_proyekadd", "x_tanggal", "%d/%m/%Y");
</script>
<?php } ?>
</span><?php echo $lh_input_proyek->tanggal->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($lh_input_proyek->kardus->Visible) { // kardus ?>
	<tr id="r_kardus"<?php echo $lh_input_proyek->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_lh_input_proyek_kardus"><table class="ewTableHeaderBtn"><tr><td><?php echo $lh_input_proyek->kardus->FldCaption() ?></td></tr></table></span></td>
		<td<?php echo $lh_input_proyek->kardus->CellAttributes() ?>><span id="el_lh_input_proyek_kardus">
<input type="text" name="x_kardus" id="x_kardus" size="30" value="<?php echo $lh_input_proyek->kardus->EditValue ?>"<?php echo $lh_input_proyek->kardus->EditAttributes() ?>>
</span><?php echo $lh_input_proyek->kardus->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($lh_input_proyek->kayu->Visible) { // kayu ?>
	<tr id="r_kayu"<?php echo $lh_input_proyek->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_lh_input_proyek_kayu"><table class="ewTableHeaderBtn"><tr><td><?php echo $lh_input_proyek->kayu->FldCaption() ?></td></tr></table></span></td>
		<td<?php echo $lh_input_proyek->kayu->CellAttributes() ?>><span id="el_lh_input_proyek_kayu">
<input type="text" name="x_kayu" id="x_kayu" size="30" value="<?php echo $lh_input_proyek->kayu->EditValue ?>"<?php echo $lh_input_proyek->kayu->EditAttributes() ?>>
</span><?php echo $lh_input_proyek->kayu->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($lh_input_proyek->besi->Visible) { // besi ?>
	<tr id="r_besi"<?php echo $lh_input_proyek->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_lh_input_proyek_besi"><table class="ewTableHeaderBtn"><tr><td><?php echo $lh_input_proyek->besi->FldCaption() ?></td></tr></table></span></td>
		<td<?php echo $lh_input_proyek->besi->CellAttributes() ?>><span id="el_lh_input_proyek_besi">
<input type="text" name="x_besi" id="x_besi" size="30" value="<?php echo $lh_input_proyek->besi->EditValue ?>"<?php echo $lh_input_proyek->besi->EditAttributes() ?>>
</span><?php echo $lh_input_proyek->besi->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($lh_input_proyek->harga->Visible) { // harga ?>
	<tr id="r_harga"<?php echo $lh_input_proyek->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_lh_input_proyek_harga"><table class="ewTableHeaderBtn"><tr><td><?php echo $lh_input_proyek->harga->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></td></tr></table></span></td>
		<td<?php echo $lh_input_proyek->harga->CellAttributes() ?>><span id="el_lh_input_proyek_harga">
<input type="text" name="x_harga" id="x_harga" size="30" value="<?php echo $lh_input_proyek->harga->EditValue ?>"<?php echo $lh_input_proyek->harga->EditAttributes() ?>>
</span><?php echo $lh_input_proyek->harga->CustomMsg ?></td>
	</tr>
<?php } ?>
</table>
</div>
</td></tr></table>
<br>
<input type="submit" name="btnAction" id="btnAction" value="<?php echo ew_BtnCaption($Language->Phrase("AddBtn")) ?>">
</form>
<script type="text/javascript">
flh_input_proyekadd.Init();
</script>
<?php
$lh_input_proyek_add->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$lh_input_proyek_add->Page_Terminate();
?>
