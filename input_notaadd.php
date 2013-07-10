<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg9.php" ?>
<?php include_once "ewmysql9.php" ?>
<?php include_once "phpfn9.php" ?>
<?php include_once "input_notainfo.php" ?>
<?php include_once "userfn9.php" ?>
<?php

//
// Page class
//

$input_nota_add = NULL; // Initialize page object first

class cinput_nota_add extends cinput_nota {

	// Page ID
	var $PageID = 'add';

	// Project ID
	var $ProjectID = "{67264FB2-6364-478B-87DD-B3E0D7A29425}";

	// Table name
	var $TableName = 'input nota';

	// Page object name
	var $PageObjName = 'input_nota_add';

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

		// Table object (input_nota)
		if (!isset($GLOBALS["input_nota"])) {
			$GLOBALS["input_nota"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["input_nota"];
		}

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'add', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'input nota', TRUE);

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
			if (@$_GET["id_nota"] != "") {
				$this->id_nota->setQueryStringValue($_GET["id_nota"]);
				$this->setKey("id_nota", $this->id_nota->CurrentValue); // Set up key
			} else {
				$this->setKey("id_nota", ""); // Clear key
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
					$this->Page_Terminate("input_notalist.php"); // No matching record, return to list
				}
				break;
			case "A": // Add new record
				$this->SendEmail = TRUE; // Send email on add success
				if ($this->AddRow($this->OldRecordset)) { // Add successful
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("AddSuccess")); // Set up success message
					$sReturnUrl = $this->getReturnUrl();
					if (ew_GetPageName($sReturnUrl) == "input_notaview.php")
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
		$this->id_dana->CurrentValue = NULL;
		$this->id_dana->OldValue = $this->id_dana->CurrentValue;
		$this->nomor_nota->CurrentValue = NULL;
		$this->nomor_nota->OldValue = $this->nomor_nota->CurrentValue;
		$this->nama_toko->CurrentValue = NULL;
		$this->nama_toko->OldValue = $this->nama_toko->CurrentValue;
		$this->tanggal_nota->CurrentValue = NULL;
		$this->tanggal_nota->OldValue = $this->tanggal_nota->CurrentValue;
		$this->jumlah_pembelian->CurrentValue = NULL;
		$this->jumlah_pembelian->OldValue = $this->jumlah_pembelian->CurrentValue;
		$this->pesan->CurrentValue = NULL;
		$this->pesan->OldValue = $this->pesan->CurrentValue;
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->id_dana->FldIsDetailKey) {
			$this->id_dana->setFormValue($objForm->GetValue("x_id_dana"));
		}
		if (!$this->nomor_nota->FldIsDetailKey) {
			$this->nomor_nota->setFormValue($objForm->GetValue("x_nomor_nota"));
		}
		if (!$this->nama_toko->FldIsDetailKey) {
			$this->nama_toko->setFormValue($objForm->GetValue("x_nama_toko"));
		}
		if (!$this->tanggal_nota->FldIsDetailKey) {
			$this->tanggal_nota->setFormValue($objForm->GetValue("x_tanggal_nota"));
			$this->tanggal_nota->CurrentValue = ew_UnFormatDateTime($this->tanggal_nota->CurrentValue, 7);
		}
		if (!$this->jumlah_pembelian->FldIsDetailKey) {
			$this->jumlah_pembelian->setFormValue($objForm->GetValue("x_jumlah_pembelian"));
		}
		if (!$this->pesan->FldIsDetailKey) {
			$this->pesan->setFormValue($objForm->GetValue("x_pesan"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadOldRecord();
		$this->id_dana->CurrentValue = $this->id_dana->FormValue;
		$this->nomor_nota->CurrentValue = $this->nomor_nota->FormValue;
		$this->nama_toko->CurrentValue = $this->nama_toko->FormValue;
		$this->tanggal_nota->CurrentValue = $this->tanggal_nota->FormValue;
		$this->tanggal_nota->CurrentValue = ew_UnFormatDateTime($this->tanggal_nota->CurrentValue, 7);
		$this->jumlah_pembelian->CurrentValue = $this->jumlah_pembelian->FormValue;
		$this->pesan->CurrentValue = $this->pesan->FormValue;
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
		$this->id_nota->setDbValue($rs->fields('id_nota'));
		$this->id_dana->setDbValue($rs->fields('id_dana'));
		$this->nomor_nota->setDbValue($rs->fields('nomor_nota'));
		$this->nama_toko->setDbValue($rs->fields('nama_toko'));
		$this->tanggal_nota->setDbValue($rs->fields('tanggal_nota'));
		$this->jumlah_pembelian->setDbValue($rs->fields('jumlah_pembelian'));
		$this->pesan->setDbValue($rs->fields('pesan'));
	}

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("id_nota")) <> "")
			$this->id_nota->CurrentValue = $this->getKey("id_nota"); // id_nota
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
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// id_nota
		// id_dana
		// nomor_nota
		// nama_toko
		// tanggal_nota
		// jumlah_pembelian
		// pesan

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// id_nota
			$this->id_nota->ViewValue = $this->id_nota->CurrentValue;
			$this->id_nota->ViewCustomAttributes = "";

			// id_dana
			if (strval($this->id_dana->CurrentValue) <> "") {
				$sFilterWrk = "`id_dana`" . ew_SearchString("=", $this->id_dana->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `id_dana`, `periode_pembiayaan` AS `DispFld`, `jumlah_dana` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lh_dana`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `periode_pembiayaan` DESC";
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->id_dana->ViewValue = ew_FormatDateTime($rswrk->fields('DispFld'), 7);
					$this->id_dana->ViewValue .= ew_ValueSeparator(1,$this->id_dana) . ew_FormatNumber($rswrk->fields('Disp2Fld'), 2, -1, 0, -1);
					$rswrk->Close();
				} else {
					$this->id_dana->ViewValue = $this->id_dana->CurrentValue;
				}
			} else {
				$this->id_dana->ViewValue = NULL;
			}
			$this->id_dana->ViewCustomAttributes = "";

			// nomor_nota
			$this->nomor_nota->ViewValue = $this->nomor_nota->CurrentValue;
			$this->nomor_nota->ViewCustomAttributes = "";

			// nama_toko
			$this->nama_toko->ViewValue = $this->nama_toko->CurrentValue;
			$this->nama_toko->ViewCustomAttributes = "";

			// tanggal_nota
			$this->tanggal_nota->ViewValue = $this->tanggal_nota->CurrentValue;
			$this->tanggal_nota->ViewValue = ew_FormatDateTime($this->tanggal_nota->ViewValue, 7);
			$this->tanggal_nota->ViewCustomAttributes = "";

			// jumlah_pembelian
			$this->jumlah_pembelian->ViewValue = $this->jumlah_pembelian->CurrentValue;
			$this->jumlah_pembelian->ViewValue = ew_FormatNumber($this->jumlah_pembelian->ViewValue, 2, -1, -1, -1);
			$this->jumlah_pembelian->CellCssStyle .= "text-align: right;";
			$this->jumlah_pembelian->ViewCustomAttributes = "";

			// pesan
			$this->pesan->ViewValue = $this->pesan->CurrentValue;
			$this->pesan->ViewCustomAttributes = "";

			// id_dana
			$this->id_dana->LinkCustomAttributes = "";
			$this->id_dana->HrefValue = "";
			$this->id_dana->TooltipValue = "";

			// nomor_nota
			$this->nomor_nota->LinkCustomAttributes = "";
			$this->nomor_nota->HrefValue = "";
			$this->nomor_nota->TooltipValue = "";

			// nama_toko
			$this->nama_toko->LinkCustomAttributes = "";
			$this->nama_toko->HrefValue = "";
			$this->nama_toko->TooltipValue = "";

			// tanggal_nota
			$this->tanggal_nota->LinkCustomAttributes = "";
			$this->tanggal_nota->HrefValue = "";
			$this->tanggal_nota->TooltipValue = "";

			// jumlah_pembelian
			$this->jumlah_pembelian->LinkCustomAttributes = "";
			$this->jumlah_pembelian->HrefValue = "";
			$this->jumlah_pembelian->TooltipValue = "";

			// pesan
			$this->pesan->LinkCustomAttributes = "";
			$this->pesan->HrefValue = "";
			$this->pesan->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

			// id_dana
			$this->id_dana->EditCustomAttributes = "";
			$sFilterWrk = "";
			$sSqlWrk = "SELECT `id_dana`, `periode_pembiayaan` AS `DispFld`, `jumlah_dana` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `lh_dana`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `periode_pembiayaan` DESC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$rowswrk = count($arwrk);
			for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
				$arwrk[$rowcntwrk][1] = ew_FormatDateTime($arwrk[$rowcntwrk][1], 7);
				$arwrk[$rowcntwrk][2] = ew_FormatNumber($arwrk[$rowcntwrk][2], 2, -1, 0, -1);
			}
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->id_dana->EditValue = $arwrk;

			// nomor_nota
			$this->nomor_nota->EditCustomAttributes = "";
			$this->nomor_nota->EditValue = ew_HtmlEncode($this->nomor_nota->CurrentValue);

			// nama_toko
			$this->nama_toko->EditCustomAttributes = "";
			$this->nama_toko->EditValue = ew_HtmlEncode($this->nama_toko->CurrentValue);

			// tanggal_nota
			$this->tanggal_nota->EditCustomAttributes = "";
			$this->tanggal_nota->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->tanggal_nota->CurrentValue, 7));

			// jumlah_pembelian
			$this->jumlah_pembelian->EditCustomAttributes = "";
			$this->jumlah_pembelian->EditValue = ew_HtmlEncode($this->jumlah_pembelian->CurrentValue);

			// pesan
			$this->pesan->EditCustomAttributes = "";
			$this->pesan->EditValue = ew_HtmlEncode($this->pesan->CurrentValue);

			// Edit refer script
			// id_dana

			$this->id_dana->HrefValue = "";

			// nomor_nota
			$this->nomor_nota->HrefValue = "";

			// nama_toko
			$this->nama_toko->HrefValue = "";

			// tanggal_nota
			$this->tanggal_nota->HrefValue = "";

			// jumlah_pembelian
			$this->jumlah_pembelian->HrefValue = "";

			// pesan
			$this->pesan->HrefValue = "";
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
		if (!is_null($this->id_dana->FormValue) && $this->id_dana->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->id_dana->FldCaption());
		}
		if (!is_null($this->nomor_nota->FormValue) && $this->nomor_nota->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->nomor_nota->FldCaption());
		}
		if (!is_null($this->nama_toko->FormValue) && $this->nama_toko->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->nama_toko->FldCaption());
		}
		if (!is_null($this->tanggal_nota->FormValue) && $this->tanggal_nota->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->tanggal_nota->FldCaption());
		}
		if (!ew_CheckEuroDate($this->tanggal_nota->FormValue)) {
			ew_AddMessage($gsFormError, $this->tanggal_nota->FldErrMsg());
		}
		if (!is_null($this->jumlah_pembelian->FormValue) && $this->jumlah_pembelian->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->jumlah_pembelian->FldCaption());
		}
		if (!ew_CheckInteger($this->jumlah_pembelian->FormValue)) {
			ew_AddMessage($gsFormError, $this->jumlah_pembelian->FldErrMsg());
		}
		if (!is_null($this->pesan->FormValue) && $this->pesan->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->pesan->FldCaption());
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

		// id_dana
		$this->id_dana->SetDbValueDef($rsnew, $this->id_dana->CurrentValue, 0, FALSE);

		// nomor_nota
		$this->nomor_nota->SetDbValueDef($rsnew, $this->nomor_nota->CurrentValue, "", FALSE);

		// nama_toko
		$this->nama_toko->SetDbValueDef($rsnew, $this->nama_toko->CurrentValue, "", FALSE);

		// tanggal_nota
		$this->tanggal_nota->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->tanggal_nota->CurrentValue, 7), ew_CurrentDate(), FALSE);

		// jumlah_pembelian
		$this->jumlah_pembelian->SetDbValueDef($rsnew, $this->jumlah_pembelian->CurrentValue, 0, FALSE);

		// pesan
		$this->pesan->SetDbValueDef($rsnew, $this->pesan->CurrentValue, "", FALSE);

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
			$this->id_nota->setDbValue($conn->Insert_ID());
			$rsnew['id_nota'] = $this->id_nota->DbValue;
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
if (!isset($input_nota_add)) $input_nota_add = new cinput_nota_add();

// Page init
$input_nota_add->Page_Init();

// Page main
$input_nota_add->Page_Main();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var input_nota_add = new ew_Page("input_nota_add");
input_nota_add.PageID = "add"; // Page ID
var EW_PAGE_ID = input_nota_add.PageID; // For backward compatibility

// Form object
var finput_notaadd = new ew_Form("finput_notaadd");

// Validate form
finput_notaadd.Validate = function(fobj) {
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
		elm = fobj.elements["x" + infix + "_id_dana"];
		if (elm && !ew_HasValue(elm))
			return ew_OnError(this, elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($input_nota->id_dana->FldCaption()) ?>");
		elm = fobj.elements["x" + infix + "_nomor_nota"];
		if (elm && !ew_HasValue(elm))
			return ew_OnError(this, elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($input_nota->nomor_nota->FldCaption()) ?>");
		elm = fobj.elements["x" + infix + "_nama_toko"];
		if (elm && !ew_HasValue(elm))
			return ew_OnError(this, elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($input_nota->nama_toko->FldCaption()) ?>");
		elm = fobj.elements["x" + infix + "_tanggal_nota"];
		if (elm && !ew_HasValue(elm))
			return ew_OnError(this, elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($input_nota->tanggal_nota->FldCaption()) ?>");
		elm = fobj.elements["x" + infix + "_tanggal_nota"];
		if (elm && !ew_CheckEuroDate(elm.value))
			return ew_OnError(this, elm, "<?php echo ew_JsEncode2($input_nota->tanggal_nota->FldErrMsg()) ?>");
		elm = fobj.elements["x" + infix + "_jumlah_pembelian"];
		if (elm && !ew_HasValue(elm))
			return ew_OnError(this, elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($input_nota->jumlah_pembelian->FldCaption()) ?>");
		elm = fobj.elements["x" + infix + "_jumlah_pembelian"];
		if (elm && !ew_CheckInteger(elm.value))
			return ew_OnError(this, elm, "<?php echo ew_JsEncode2($input_nota->jumlah_pembelian->FldErrMsg()) ?>");
		elm = fobj.elements["x" + infix + "_pesan"];
		if (elm && !ew_HasValue(elm))
			return ew_OnError(this, elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($input_nota->pesan->FldCaption()) ?>");

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
finput_notaadd.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
finput_notaadd.ValidateRequired = true;
<?php } else { ?>
finput_notaadd.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
finput_notaadd.Lists["x_id_dana"] = {"LinkField":"x_id_dana","Ajax":null,"AutoFill":false,"DisplayFields":["x_periode_pembiayaan","x_jumlah_dana","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<p><span id="ewPageCaption" class="ewTitle ewTableTitle"><?php echo $Language->Phrase("Add") ?>&nbsp;<?php echo $Language->Phrase("TblTypeVIEW") ?><?php echo $input_nota->TableCaption() ?></span></p>
<p class="phpmaker"><a href="<?php echo $input_nota->getReturnUrl() ?>" id="a_GoBack" class="ewLink"><?php echo $Language->Phrase("GoBack") ?></a></p>
<?php $input_nota_add->ShowPageHeader(); ?>
<?php
$input_nota_add->ShowMessage();
?>
<form name="finput_notaadd" id="finput_notaadd" class="ewForm" action="<?php echo ew_CurrentPage() ?>" method="post" onsubmit="return ewForms[this.id].Submit();">
<br>
<input type="hidden" name="t" value="input_nota">
<input type="hidden" name="a_add" id="a_add" value="A">
<table cellspacing="0" class="ewGrid"><tr><td class="ewGridContent">
<div class="ewGridMiddlePanel">
<table id="tbl_input_notaadd" class="ewTable">
<?php if ($input_nota->id_dana->Visible) { // id_dana ?>
	<tr id="r_id_dana"<?php echo $input_nota->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_input_nota_id_dana"><table class="ewTableHeaderBtn"><tr><td><?php echo $input_nota->id_dana->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></td></tr></table></span></td>
		<td<?php echo $input_nota->id_dana->CellAttributes() ?>><span id="el_input_nota_id_dana">
<select id="x_id_dana" name="x_id_dana"<?php echo $input_nota->id_dana->EditAttributes() ?>>
<?php
if (is_array($input_nota->id_dana->EditValue)) {
	$arwrk = $input_nota->id_dana->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($input_nota->id_dana->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
<?php if ($arwrk[$rowcntwrk][2] <> "") { ?>
<?php echo ew_ValueSeparator(1,$input_nota->id_dana) ?><?php echo $arwrk[$rowcntwrk][2] ?>
<?php } ?>
</option>
<?php
	}
}
?>
</select>
<script type="text/javascript">
finput_notaadd.Lists["x_id_dana"].Options = <?php echo (is_array($input_nota->id_dana->EditValue)) ? ew_ArrayToJson($input_nota->id_dana->EditValue, 1) : "[]" ?>;
</script>
</span><?php echo $input_nota->id_dana->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($input_nota->nomor_nota->Visible) { // nomor_nota ?>
	<tr id="r_nomor_nota"<?php echo $input_nota->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_input_nota_nomor_nota"><table class="ewTableHeaderBtn"><tr><td><?php echo $input_nota->nomor_nota->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></td></tr></table></span></td>
		<td<?php echo $input_nota->nomor_nota->CellAttributes() ?>><span id="el_input_nota_nomor_nota">
<input type="text" name="x_nomor_nota" id="x_nomor_nota" size="30" maxlength="255" value="<?php echo $input_nota->nomor_nota->EditValue ?>"<?php echo $input_nota->nomor_nota->EditAttributes() ?>>
</span><?php echo $input_nota->nomor_nota->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($input_nota->nama_toko->Visible) { // nama_toko ?>
	<tr id="r_nama_toko"<?php echo $input_nota->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_input_nota_nama_toko"><table class="ewTableHeaderBtn"><tr><td><?php echo $input_nota->nama_toko->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></td></tr></table></span></td>
		<td<?php echo $input_nota->nama_toko->CellAttributes() ?>><span id="el_input_nota_nama_toko">
<input type="text" name="x_nama_toko" id="x_nama_toko" size="30" maxlength="255" value="<?php echo $input_nota->nama_toko->EditValue ?>"<?php echo $input_nota->nama_toko->EditAttributes() ?>>
</span><?php echo $input_nota->nama_toko->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($input_nota->tanggal_nota->Visible) { // tanggal_nota ?>
	<tr id="r_tanggal_nota"<?php echo $input_nota->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_input_nota_tanggal_nota"><table class="ewTableHeaderBtn"><tr><td><?php echo $input_nota->tanggal_nota->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></td></tr></table></span></td>
		<td<?php echo $input_nota->tanggal_nota->CellAttributes() ?>><span id="el_input_nota_tanggal_nota">
<input type="text" name="x_tanggal_nota" id="x_tanggal_nota" value="<?php echo $input_nota->tanggal_nota->EditValue ?>"<?php echo $input_nota->tanggal_nota->EditAttributes() ?>>
<?php if (!$input_nota->tanggal_nota->ReadOnly && !$input_nota->tanggal_nota->Disabled && @$input_nota->tanggal_nota->EditAttrs["readonly"] == "" && @$input_nota->tanggal_nota->EditAttrs["disabled"] == "") { ?>
&nbsp;<img src="phpimages/calendar.png" id="finput_notaadd$x_tanggal_nota$" name="finput_notaadd$x_tanggal_nota$" alt="<?php echo $Language->Phrase("PickDate") ?>" title="<?php echo $Language->Phrase("PickDate") ?>" class="ewCalendar" style="border: 0;">
<script type="text/javascript">
ew_CreateCalendar("finput_notaadd", "x_tanggal_nota", "%d/%m/%Y");
</script>
<?php } ?>
</span><?php echo $input_nota->tanggal_nota->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($input_nota->jumlah_pembelian->Visible) { // jumlah_pembelian ?>
	<tr id="r_jumlah_pembelian"<?php echo $input_nota->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_input_nota_jumlah_pembelian"><table class="ewTableHeaderBtn"><tr><td><?php echo $input_nota->jumlah_pembelian->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></td></tr></table></span></td>
		<td<?php echo $input_nota->jumlah_pembelian->CellAttributes() ?>><span id="el_input_nota_jumlah_pembelian">
<input type="text" name="x_jumlah_pembelian" id="x_jumlah_pembelian" size="30" value="<?php echo $input_nota->jumlah_pembelian->EditValue ?>"<?php echo $input_nota->jumlah_pembelian->EditAttributes() ?>>
</span><?php echo $input_nota->jumlah_pembelian->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($input_nota->pesan->Visible) { // pesan ?>
	<tr id="r_pesan"<?php echo $input_nota->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_input_nota_pesan"><table class="ewTableHeaderBtn"><tr><td><?php echo $input_nota->pesan->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></td></tr></table></span></td>
		<td<?php echo $input_nota->pesan->CellAttributes() ?>><span id="el_input_nota_pesan">
<textarea name="x_pesan" id="x_pesan" cols="35" rows="4"<?php echo $input_nota->pesan->EditAttributes() ?>><?php echo $input_nota->pesan->EditValue ?></textarea>
</span><?php echo $input_nota->pesan->CustomMsg ?></td>
	</tr>
<?php } ?>
</table>
</div>
</td></tr></table>
<br>
<input type="submit" name="btnAction" id="btnAction" value="<?php echo ew_BtnCaption($Language->Phrase("AddBtn")) ?>">
</form>
<script type="text/javascript">
finput_notaadd.Init();
</script>
<?php
$input_nota_add->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$input_nota_add->Page_Terminate();
?>
