<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg9.php" ?>
<?php include_once "ewmysql9.php" ?>
<?php include_once "phpfn9.php" ?>
<?php include_once "lh_gajiinfo.php" ?>
<?php include_once "lh_userinfo.php" ?>
<?php include_once "userfn9.php" ?>
<?php

//
// Page class
//

$lh_gaji_edit = NULL; // Initialize page object first

class clh_gaji_edit extends clh_gaji {

	// Page ID
	var $PageID = 'edit';

	// Project ID
	var $ProjectID = "{67264FB2-6364-478B-87DD-B3E0D7A29425}";

	// Table name
	var $TableName = 'lh_gaji';

	// Page object name
	var $PageObjName = 'lh_gaji_edit';

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

		// Table object (lh_gaji)
		if (!isset($GLOBALS["lh_gaji"])) {
			$GLOBALS["lh_gaji"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["lh_gaji"];
		}

		// Table object (lh_user)
		if (!isset($GLOBALS['lh_user'])) $GLOBALS['lh_user'] = new clh_user();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'edit', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'lh_gaji', TRUE);

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
			$this->Page_Terminate("lh_gajilist.php");
		}

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"];
		$this->id_gaji->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

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
		if (@$_GET["id_gaji"] <> "")
			$this->id_gaji->setQueryStringValue($_GET["id_gaji"]);

		// Process form if post back
		if (@$_POST["a_edit"] <> "") {
			$this->CurrentAction = $_POST["a_edit"]; // Get action code
			$this->LoadFormValues(); // Get form values
		} else {
			$this->CurrentAction = "I"; // Default action is display
		}

		// Check if valid key
		if ($this->id_gaji->CurrentValue == "")
			$this->Page_Terminate("lh_gajilist.php"); // Invalid key, return to list

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
					$this->Page_Terminate("lh_gajilist.php"); // No matching record, return to list
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
		if (!$this->id_gaji->FldIsDetailKey)
			$this->id_gaji->setFormValue($objForm->GetValue("x_id_gaji"));
		if (!$this->id_user->FldIsDetailKey) {
			$this->id_user->setFormValue($objForm->GetValue("x_id_user"));
		}
		if (!$this->status->FldIsDetailKey) {
			$this->status->setFormValue($objForm->GetValue("x_status"));
		}
		if (!$this->tanggal->FldIsDetailKey) {
			$this->tanggal->setFormValue($objForm->GetValue("x_tanggal"));
			$this->tanggal->CurrentValue = ew_UnFormatDateTime($this->tanggal->CurrentValue, 7);
		}
		if (!$this->gaji_pokok->FldIsDetailKey) {
			$this->gaji_pokok->setFormValue($objForm->GetValue("x_gaji_pokok"));
		}
		if (!$this->lembur->FldIsDetailKey) {
			$this->lembur->setFormValue($objForm->GetValue("x_lembur"));
		}
		if (!$this->tunjangan_proyek->FldIsDetailKey) {
			$this->tunjangan_proyek->setFormValue($objForm->GetValue("x_tunjangan_proyek"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadRow();
		$this->id_gaji->CurrentValue = $this->id_gaji->FormValue;
		$this->id_user->CurrentValue = $this->id_user->FormValue;
		$this->status->CurrentValue = $this->status->FormValue;
		$this->tanggal->CurrentValue = $this->tanggal->FormValue;
		$this->tanggal->CurrentValue = ew_UnFormatDateTime($this->tanggal->CurrentValue, 7);
		$this->gaji_pokok->CurrentValue = $this->gaji_pokok->FormValue;
		$this->lembur->CurrentValue = $this->lembur->FormValue;
		$this->tunjangan_proyek->CurrentValue = $this->tunjangan_proyek->FormValue;
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
		$this->id_gaji->setDbValue($rs->fields('id_gaji'));
		$this->id_user->setDbValue($rs->fields('id_user'));
		$this->status->setDbValue($rs->fields('status'));
		$this->tanggal->setDbValue($rs->fields('tanggal'));
		$this->gaji_pokok->setDbValue($rs->fields('gaji_pokok'));
		$this->lembur->setDbValue($rs->fields('lembur'));
		$this->tunjangan_proyek->setDbValue($rs->fields('tunjangan_proyek'));
	}

	// Render row values based on field settings
	function RenderRow() {
		global $conn, $Security, $Language;
		global $gsLanguage;

		// Initialize URLs
		// Convert decimal values if posted back

		if ($this->gaji_pokok->FormValue == $this->gaji_pokok->CurrentValue && is_numeric(ew_StrToFloat($this->gaji_pokok->CurrentValue)))
			$this->gaji_pokok->CurrentValue = ew_StrToFloat($this->gaji_pokok->CurrentValue);

		// Convert decimal values if posted back
		if ($this->lembur->FormValue == $this->lembur->CurrentValue && is_numeric(ew_StrToFloat($this->lembur->CurrentValue)))
			$this->lembur->CurrentValue = ew_StrToFloat($this->lembur->CurrentValue);

		// Convert decimal values if posted back
		if ($this->tunjangan_proyek->FormValue == $this->tunjangan_proyek->CurrentValue && is_numeric(ew_StrToFloat($this->tunjangan_proyek->CurrentValue)))
			$this->tunjangan_proyek->CurrentValue = ew_StrToFloat($this->tunjangan_proyek->CurrentValue);

		// Call Row_Rendering event
		$this->Row_Rendering();

		// Common render codes for all row types
		// id_gaji
		// id_user
		// status
		// tanggal
		// gaji_pokok
		// lembur
		// tunjangan_proyek

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// id_gaji
			$this->id_gaji->ViewValue = $this->id_gaji->CurrentValue;
			$this->id_gaji->ViewCustomAttributes = "";

			// id_user
			if (strval($this->id_user->CurrentValue) <> "") {
				$sFilterWrk = "`id_user`" . ew_SearchString("=", $this->id_user->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `id_user`, `nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lh_user`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->id_user->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->id_user->ViewValue = $this->id_user->CurrentValue;
				}
			} else {
				$this->id_user->ViewValue = NULL;
			}
			$this->id_user->ViewCustomAttributes = "";

			// status
			if (strval($this->status->CurrentValue) <> "") {
				switch ($this->status->CurrentValue) {
					case $this->status->FldTagValue(1):
						$this->status->ViewValue = $this->status->FldTagCaption(1) <> "" ? $this->status->FldTagCaption(1) : $this->status->CurrentValue;
						break;
					case $this->status->FldTagValue(2):
						$this->status->ViewValue = $this->status->FldTagCaption(2) <> "" ? $this->status->FldTagCaption(2) : $this->status->CurrentValue;
						break;
					default:
						$this->status->ViewValue = $this->status->CurrentValue;
				}
			} else {
				$this->status->ViewValue = NULL;
			}
			$this->status->ViewCustomAttributes = "";

			// tanggal
			$this->tanggal->ViewValue = $this->tanggal->CurrentValue;
			$this->tanggal->ViewValue = ew_FormatDateTime($this->tanggal->ViewValue, 7);
			$this->tanggal->ViewCustomAttributes = "";

			// gaji_pokok
			$this->gaji_pokok->ViewValue = $this->gaji_pokok->CurrentValue;
			$this->gaji_pokok->ViewValue = ew_FormatNumber($this->gaji_pokok->ViewValue, 2, -1, -1, -1);
			$this->gaji_pokok->CellCssStyle .= "text-align: right;";
			$this->gaji_pokok->ViewCustomAttributes = "";

			// lembur
			$this->lembur->ViewValue = $this->lembur->CurrentValue;
			$this->lembur->ViewValue = ew_FormatNumber($this->lembur->ViewValue, 2, -1, -1, -1);
			$this->lembur->CellCssStyle .= "text-align: right;";
			$this->lembur->ViewCustomAttributes = "";

			// tunjangan_proyek
			$this->tunjangan_proyek->ViewValue = $this->tunjangan_proyek->CurrentValue;
			$this->tunjangan_proyek->ViewValue = ew_FormatNumber($this->tunjangan_proyek->ViewValue, 2, -1, -1, -1);
			$this->tunjangan_proyek->CellCssStyle .= "text-align: right;";
			$this->tunjangan_proyek->ViewCustomAttributes = "";

			// id_gaji
			$this->id_gaji->LinkCustomAttributes = "";
			$this->id_gaji->HrefValue = "";
			$this->id_gaji->TooltipValue = "";

			// id_user
			$this->id_user->LinkCustomAttributes = "";
			$this->id_user->HrefValue = "";
			$this->id_user->TooltipValue = "";

			// status
			$this->status->LinkCustomAttributes = "";
			$this->status->HrefValue = "";
			$this->status->TooltipValue = "";

			// tanggal
			$this->tanggal->LinkCustomAttributes = "";
			$this->tanggal->HrefValue = "";
			$this->tanggal->TooltipValue = "";

			// gaji_pokok
			$this->gaji_pokok->LinkCustomAttributes = "";
			$this->gaji_pokok->HrefValue = "";
			$this->gaji_pokok->TooltipValue = "";

			// lembur
			$this->lembur->LinkCustomAttributes = "";
			$this->lembur->HrefValue = "";
			$this->lembur->TooltipValue = "";

			// tunjangan_proyek
			$this->tunjangan_proyek->LinkCustomAttributes = "";
			$this->tunjangan_proyek->HrefValue = "";
			$this->tunjangan_proyek->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_EDIT) { // Edit row

			// id_gaji
			$this->id_gaji->EditCustomAttributes = "";
			$this->id_gaji->EditValue = $this->id_gaji->CurrentValue;
			$this->id_gaji->ViewCustomAttributes = "";

			// id_user
			$this->id_user->EditCustomAttributes = "";
			$sFilterWrk = "";
			$sSqlWrk = "SELECT `id_user`, `nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `lh_user`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->id_user->EditValue = $arwrk;

			// status
			$this->status->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->status->FldTagValue(1), $this->status->FldTagCaption(1) <> "" ? $this->status->FldTagCaption(1) : $this->status->FldTagValue(1));
			$arwrk[] = array($this->status->FldTagValue(2), $this->status->FldTagCaption(2) <> "" ? $this->status->FldTagCaption(2) : $this->status->FldTagValue(2));
			$this->status->EditValue = $arwrk;

			// tanggal
			$this->tanggal->EditCustomAttributes = "";
			$this->tanggal->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->tanggal->CurrentValue, 7));

			// gaji_pokok
			$this->gaji_pokok->EditCustomAttributes = "";
			$this->gaji_pokok->EditValue = ew_HtmlEncode($this->gaji_pokok->CurrentValue);
			if (strval($this->gaji_pokok->EditValue) <> "" && is_numeric($this->gaji_pokok->EditValue)) $this->gaji_pokok->EditValue = ew_FormatNumber($this->gaji_pokok->EditValue, -2, -1, -1, -1);

			// lembur
			$this->lembur->EditCustomAttributes = "";
			$this->lembur->EditValue = ew_HtmlEncode($this->lembur->CurrentValue);
			if (strval($this->lembur->EditValue) <> "" && is_numeric($this->lembur->EditValue)) $this->lembur->EditValue = ew_FormatNumber($this->lembur->EditValue, -2, -1, -1, -1);

			// tunjangan_proyek
			$this->tunjangan_proyek->EditCustomAttributes = "";
			$this->tunjangan_proyek->EditValue = ew_HtmlEncode($this->tunjangan_proyek->CurrentValue);
			if (strval($this->tunjangan_proyek->EditValue) <> "" && is_numeric($this->tunjangan_proyek->EditValue)) $this->tunjangan_proyek->EditValue = ew_FormatNumber($this->tunjangan_proyek->EditValue, -2, -1, -1, -1);

			// Edit refer script
			// id_gaji

			$this->id_gaji->HrefValue = "";

			// id_user
			$this->id_user->HrefValue = "";

			// status
			$this->status->HrefValue = "";

			// tanggal
			$this->tanggal->HrefValue = "";

			// gaji_pokok
			$this->gaji_pokok->HrefValue = "";

			// lembur
			$this->lembur->HrefValue = "";

			// tunjangan_proyek
			$this->tunjangan_proyek->HrefValue = "";
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
		if (!ew_CheckInteger($this->id_gaji->FormValue)) {
			ew_AddMessage($gsFormError, $this->id_gaji->FldErrMsg());
		}
		if (!is_null($this->id_user->FormValue) && $this->id_user->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->id_user->FldCaption());
		}
		if ($this->status->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->status->FldCaption());
		}
		if (!is_null($this->tanggal->FormValue) && $this->tanggal->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->tanggal->FldCaption());
		}
		if (!ew_CheckEuroDate($this->tanggal->FormValue)) {
			ew_AddMessage($gsFormError, $this->tanggal->FldErrMsg());
		}
		if (!ew_CheckNumber($this->gaji_pokok->FormValue)) {
			ew_AddMessage($gsFormError, $this->gaji_pokok->FldErrMsg());
		}
		if (!ew_CheckNumber($this->lembur->FormValue)) {
			ew_AddMessage($gsFormError, $this->lembur->FldErrMsg());
		}
		if (!ew_CheckNumber($this->tunjangan_proyek->FormValue)) {
			ew_AddMessage($gsFormError, $this->tunjangan_proyek->FldErrMsg());
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

			// id_user
			$this->id_user->SetDbValueDef($rsnew, $this->id_user->CurrentValue, 0, $this->id_user->ReadOnly);

			// status
			$this->status->SetDbValueDef($rsnew, $this->status->CurrentValue, "", $this->status->ReadOnly);

			// tanggal
			$this->tanggal->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->tanggal->CurrentValue, 7), ew_CurrentDate(), $this->tanggal->ReadOnly);

			// gaji_pokok
			$this->gaji_pokok->SetDbValueDef($rsnew, $this->gaji_pokok->CurrentValue, NULL, $this->gaji_pokok->ReadOnly);

			// lembur
			$this->lembur->SetDbValueDef($rsnew, $this->lembur->CurrentValue, NULL, $this->lembur->ReadOnly);

			// tunjangan_proyek
			$this->tunjangan_proyek->SetDbValueDef($rsnew, $this->tunjangan_proyek->CurrentValue, NULL, $this->tunjangan_proyek->ReadOnly);

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
if (!isset($lh_gaji_edit)) $lh_gaji_edit = new clh_gaji_edit();

// Page init
$lh_gaji_edit->Page_Init();

// Page main
$lh_gaji_edit->Page_Main();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var lh_gaji_edit = new ew_Page("lh_gaji_edit");
lh_gaji_edit.PageID = "edit"; // Page ID
var EW_PAGE_ID = lh_gaji_edit.PageID; // For backward compatibility

// Form object
var flh_gajiedit = new ew_Form("flh_gajiedit");

// Validate form
flh_gajiedit.Validate = function(fobj) {
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
		elm = fobj.elements["x" + infix + "_id_gaji"];
		if (elm && !ew_CheckInteger(elm.value))
			return ew_OnError(this, elm, "<?php echo ew_JsEncode2($lh_gaji->id_gaji->FldErrMsg()) ?>");
		elm = fobj.elements["x" + infix + "_id_user"];
		if (elm && !ew_HasValue(elm))
			return ew_OnError(this, elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($lh_gaji->id_user->FldCaption()) ?>");
		elm = fobj.elements["x" + infix + "_status"];
		if (elm && !ew_HasValue(elm))
			return ew_OnError(this, elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($lh_gaji->status->FldCaption()) ?>");
		elm = fobj.elements["x" + infix + "_tanggal"];
		if (elm && !ew_HasValue(elm))
			return ew_OnError(this, elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($lh_gaji->tanggal->FldCaption()) ?>");
		elm = fobj.elements["x" + infix + "_tanggal"];
		if (elm && !ew_CheckEuroDate(elm.value))
			return ew_OnError(this, elm, "<?php echo ew_JsEncode2($lh_gaji->tanggal->FldErrMsg()) ?>");
		elm = fobj.elements["x" + infix + "_gaji_pokok"];
		if (elm && !ew_CheckNumber(elm.value))
			return ew_OnError(this, elm, "<?php echo ew_JsEncode2($lh_gaji->gaji_pokok->FldErrMsg()) ?>");
		elm = fobj.elements["x" + infix + "_lembur"];
		if (elm && !ew_CheckNumber(elm.value))
			return ew_OnError(this, elm, "<?php echo ew_JsEncode2($lh_gaji->lembur->FldErrMsg()) ?>");
		elm = fobj.elements["x" + infix + "_tunjangan_proyek"];
		if (elm && !ew_CheckNumber(elm.value))
			return ew_OnError(this, elm, "<?php echo ew_JsEncode2($lh_gaji->tunjangan_proyek->FldErrMsg()) ?>");

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
flh_gajiedit.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
flh_gajiedit.ValidateRequired = true;
<?php } else { ?>
flh_gajiedit.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
flh_gajiedit.Lists["x_id_user"] = {"LinkField":"x_id_user","Ajax":null,"AutoFill":false,"DisplayFields":["x_nama","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<p><span id="ewPageCaption" class="ewTitle ewTableTitle"><?php echo $Language->Phrase("Edit") ?>&nbsp;<?php echo $Language->Phrase("TblTypeTABLE") ?><?php echo $lh_gaji->TableCaption() ?></span></p>
<p class="phpmaker"><a href="<?php echo $lh_gaji->getReturnUrl() ?>" id="a_GoBack" class="ewLink"><?php echo $Language->Phrase("GoBack") ?></a></p>
<?php $lh_gaji_edit->ShowPageHeader(); ?>
<?php
$lh_gaji_edit->ShowMessage();
?>
<form name="flh_gajiedit" id="flh_gajiedit" class="ewForm" action="<?php echo ew_CurrentPage() ?>" method="post" onsubmit="return ewForms[this.id].Submit();">
<br>
<input type="hidden" name="t" value="lh_gaji">
<input type="hidden" name="a_edit" id="a_edit" value="U">
<table cellspacing="0" class="ewGrid"><tr><td class="ewGridContent">
<div class="ewGridMiddlePanel">
<table id="tbl_lh_gajiedit" class="ewTable">
<?php if ($lh_gaji->id_gaji->Visible) { // id_gaji ?>
	<tr id="r_id_gaji"<?php echo $lh_gaji->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_lh_gaji_id_gaji"><table class="ewTableHeaderBtn"><tr><td><?php echo $lh_gaji->id_gaji->FldCaption() ?></td></tr></table></span></td>
		<td<?php echo $lh_gaji->id_gaji->CellAttributes() ?>><span id="el_lh_gaji_id_gaji">
<span<?php echo $lh_gaji->id_gaji->ViewAttributes() ?>>
<?php echo $lh_gaji->id_gaji->EditValue ?></span>
<input type="hidden" name="x_id_gaji" id="x_id_gaji" value="<?php echo ew_HtmlEncode($lh_gaji->id_gaji->CurrentValue) ?>">
</span><?php echo $lh_gaji->id_gaji->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($lh_gaji->id_user->Visible) { // id_user ?>
	<tr id="r_id_user"<?php echo $lh_gaji->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_lh_gaji_id_user"><table class="ewTableHeaderBtn"><tr><td><?php echo $lh_gaji->id_user->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></td></tr></table></span></td>
		<td<?php echo $lh_gaji->id_user->CellAttributes() ?>><span id="el_lh_gaji_id_user">
<select id="x_id_user" name="x_id_user"<?php echo $lh_gaji->id_user->EditAttributes() ?>>
<?php
if (is_array($lh_gaji->id_user->EditValue)) {
	$arwrk = $lh_gaji->id_user->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($lh_gaji->id_user->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
?>
</select>
<script type="text/javascript">
flh_gajiedit.Lists["x_id_user"].Options = <?php echo (is_array($lh_gaji->id_user->EditValue)) ? ew_ArrayToJson($lh_gaji->id_user->EditValue, 1) : "[]" ?>;
</script>
</span><?php echo $lh_gaji->id_user->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($lh_gaji->status->Visible) { // status ?>
	<tr id="r_status"<?php echo $lh_gaji->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_lh_gaji_status"><table class="ewTableHeaderBtn"><tr><td><?php echo $lh_gaji->status->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></td></tr></table></span></td>
		<td<?php echo $lh_gaji->status->CellAttributes() ?>><span id="el_lh_gaji_status">
<div id="tp_x_status" class="<?php echo EW_ITEM_TEMPLATE_CLASSNAME ?>"><input type="radio" name="x_status" id="x_status" value="{value}"<?php echo $lh_gaji->status->EditAttributes() ?>></div>
<div id="dsl_x_status" data-repeatcolumn="5" class="ewItemList">
<?php
$arwrk = $lh_gaji->status->EditValue;
if (is_array($arwrk)) {
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($lh_gaji->status->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " checked=\"checked\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;

		// Note: No spacing within the LABEL tag
?>
<?php echo ew_RepeatColumnTable($rowswrk, $rowcntwrk, 5, 1) ?>
<label><input type="radio" name="x_status" id="x_status" value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?><?php echo $lh_gaji->status->EditAttributes() ?>><?php echo $arwrk[$rowcntwrk][1] ?></label>
<?php echo ew_RepeatColumnTable($rowswrk, $rowcntwrk, 5, 2) ?>
<?php
	}
}
?>
</div>
</span><?php echo $lh_gaji->status->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($lh_gaji->tanggal->Visible) { // tanggal ?>
	<tr id="r_tanggal"<?php echo $lh_gaji->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_lh_gaji_tanggal"><table class="ewTableHeaderBtn"><tr><td><?php echo $lh_gaji->tanggal->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></td></tr></table></span></td>
		<td<?php echo $lh_gaji->tanggal->CellAttributes() ?>><span id="el_lh_gaji_tanggal">
<input type="text" name="x_tanggal" id="x_tanggal" value="<?php echo $lh_gaji->tanggal->EditValue ?>"<?php echo $lh_gaji->tanggal->EditAttributes() ?>>
<?php if (!$lh_gaji->tanggal->ReadOnly && !$lh_gaji->tanggal->Disabled && @$lh_gaji->tanggal->EditAttrs["readonly"] == "" && @$lh_gaji->tanggal->EditAttrs["disabled"] == "") { ?>
&nbsp;<img src="phpimages/calendar.png" id="flh_gajiedit$x_tanggal$" name="flh_gajiedit$x_tanggal$" alt="<?php echo $Language->Phrase("PickDate") ?>" title="<?php echo $Language->Phrase("PickDate") ?>" class="ewCalendar" style="border: 0;">
<script type="text/javascript">
ew_CreateCalendar("flh_gajiedit", "x_tanggal", "%d/%m/%Y");
</script>
<?php } ?>
</span><?php echo $lh_gaji->tanggal->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($lh_gaji->gaji_pokok->Visible) { // gaji_pokok ?>
	<tr id="r_gaji_pokok"<?php echo $lh_gaji->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_lh_gaji_gaji_pokok"><table class="ewTableHeaderBtn"><tr><td><?php echo $lh_gaji->gaji_pokok->FldCaption() ?></td></tr></table></span></td>
		<td<?php echo $lh_gaji->gaji_pokok->CellAttributes() ?>><span id="el_lh_gaji_gaji_pokok">
<input type="text" name="x_gaji_pokok" id="x_gaji_pokok" size="30" value="<?php echo $lh_gaji->gaji_pokok->EditValue ?>"<?php echo $lh_gaji->gaji_pokok->EditAttributes() ?>>
</span><?php echo $lh_gaji->gaji_pokok->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($lh_gaji->lembur->Visible) { // lembur ?>
	<tr id="r_lembur"<?php echo $lh_gaji->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_lh_gaji_lembur"><table class="ewTableHeaderBtn"><tr><td><?php echo $lh_gaji->lembur->FldCaption() ?></td></tr></table></span></td>
		<td<?php echo $lh_gaji->lembur->CellAttributes() ?>><span id="el_lh_gaji_lembur">
<input type="text" name="x_lembur" id="x_lembur" size="30" value="<?php echo $lh_gaji->lembur->EditValue ?>"<?php echo $lh_gaji->lembur->EditAttributes() ?>>
</span><?php echo $lh_gaji->lembur->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($lh_gaji->tunjangan_proyek->Visible) { // tunjangan_proyek ?>
	<tr id="r_tunjangan_proyek"<?php echo $lh_gaji->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_lh_gaji_tunjangan_proyek"><table class="ewTableHeaderBtn"><tr><td><?php echo $lh_gaji->tunjangan_proyek->FldCaption() ?></td></tr></table></span></td>
		<td<?php echo $lh_gaji->tunjangan_proyek->CellAttributes() ?>><span id="el_lh_gaji_tunjangan_proyek">
<input type="text" name="x_tunjangan_proyek" id="x_tunjangan_proyek" size="30" value="<?php echo $lh_gaji->tunjangan_proyek->EditValue ?>"<?php echo $lh_gaji->tunjangan_proyek->EditAttributes() ?>>
</span><?php echo $lh_gaji->tunjangan_proyek->CustomMsg ?></td>
	</tr>
<?php } ?>
</table>
</div>
</td></tr></table>
<br>
<input type="submit" name="btnAction" id="btnAction" value="<?php echo ew_BtnCaption($Language->Phrase("EditBtn")) ?>">
</form>
<script type="text/javascript">
flh_gajiedit.Init();
</script>
<?php
$lh_gaji_edit->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$lh_gaji_edit->Page_Terminate();
?>
