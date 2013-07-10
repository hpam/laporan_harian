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

$lh_gaji_view = NULL; // Initialize page object first

class clh_gaji_view extends clh_gaji {

	// Page ID
	var $PageID = 'view';

	// Project ID
	var $ProjectID = "{67264FB2-6364-478B-87DD-B3E0D7A29425}";

	// Table name
	var $TableName = 'lh_gaji';

	// Page object name
	var $PageObjName = 'lh_gaji_view';

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

	// Page URLs
	var $AddUrl;
	var $EditUrl;
	var $CopyUrl;
	var $DeleteUrl;
	var $ViewUrl;
	var $ListUrl;

	// Export URLs
	var $ExportPrintUrl;
	var $ExportHtmlUrl;
	var $ExportExcelUrl;
	var $ExportWordUrl;
	var $ExportXmlUrl;
	var $ExportCsvUrl;
	var $ExportPdfUrl;

	// Update URLs
	var $InlineAddUrl;
	var $InlineCopyUrl;
	var $InlineEditUrl;
	var $GridAddUrl;
	var $GridEditUrl;
	var $MultiDeleteUrl;
	var $MultiUpdateUrl;

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
		$KeyUrl = "";
		if (@$_GET["id_gaji"] <> "") {
			$this->RecKey["id_gaji"] = $_GET["id_gaji"];
			$KeyUrl .= "&id_gaji=" . urlencode($this->RecKey["id_gaji"]);
		}
		$this->ExportPrintUrl = $this->PageUrl() . "export=print" . $KeyUrl;
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html" . $KeyUrl;
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel" . $KeyUrl;
		$this->ExportWordUrl = $this->PageUrl() . "export=word" . $KeyUrl;
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml" . $KeyUrl;
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv" . $KeyUrl;
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf" . $KeyUrl;

		// Table object (lh_user)
		if (!isset($GLOBALS['lh_user'])) $GLOBALS['lh_user'] = new clh_user();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'view', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'lh_gaji', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect();

		// Export options
		$this->ExportOptions = new cListOptions();
		$this->ExportOptions->Tag = "span";
		$this->ExportOptions->TagClassName = "ewExportOption";
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
		if (!$Security->CanView()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage($Language->Phrase("NoPermission")); // Set no permission
			$this->Page_Terminate("lh_gajilist.php");
		}

		// Get export parameters
		if (@$_GET["export"] <> "") {
			$this->Export = $_GET["export"];
		} elseif (ew_IsHttpPost()) {
			if (@$_POST["exporttype"] <> "")
				$this->Export = $_POST["exporttype"];
		} else {
			$this->setExportReturnUrl(ew_CurrentUrl());
		}
		$gsExport = $this->Export; // Get export parameter, used in header
		$gsExportFile = $this->TableVar; // Get export file, used in header
		if (@$_GET["id_gaji"] <> "") {
			if ($gsExportFile <> "") $gsExportFile .= "_";
			$gsExportFile .= ew_StripSlashes($_GET["id_gaji"]);
		}

		// Setup export options
		$this->SetupExportOptions();
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
	var $ExportOptions; // Export options
	var $DisplayRecs = 1;
	var $StartRec;
	var $StopRec;
	var $TotalRecs = 0;
	var $RecRange = 10;
	var $RecCnt;
	var $RecKey = array();
	var $Recordset;

	//
	// Page main
	//
	function Page_Main() {
		global $Language;

		// Load current record
		$bLoadCurrentRecord = FALSE;
		$sReturnUrl = "";
		$bMatchRecord = FALSE;
		if ($this->IsPageRequest()) { // Validate request
			if (@$_GET["id_gaji"] <> "") {
				$this->id_gaji->setQueryStringValue($_GET["id_gaji"]);
				$this->RecKey["id_gaji"] = $this->id_gaji->QueryStringValue;
			} else {
				$sReturnUrl = "lh_gajilist.php"; // Return to list
			}

			// Get action
			$this->CurrentAction = "I"; // Display form
			switch ($this->CurrentAction) {
				case "I": // Get a record to display
					if (!$this->LoadRow()) { // Load record based on key
						if ($this->getSuccessMessage() == "" && $this->getFailureMessage() == "")
							$this->setFailureMessage($Language->Phrase("NoRecord")); // Set no record message
						$sReturnUrl = "lh_gajilist.php"; // No matching record, return to list
					}
			}

			// Export data only
			if (in_array($this->Export, array("html","word","excel","xml","csv","email","pdf"))) {
				if ($this->Export == "email" && $this->ExportReturnUrl() == ew_CurrentPage()) // Default return page
					$this->setExportReturnUrl($this->GetViewUrl()); // Add key
				$this->ExportData();
				if ($this->Export == "email")
					$this->Page_Terminate($this->ExportReturnUrl());
				else
					$this->Page_Terminate(); // Terminate response
				exit();
			}
		} else {
			$sReturnUrl = "lh_gajilist.php"; // Not page request, return to list
		}
		if ($sReturnUrl <> "")
			$this->Page_Terminate($sReturnUrl);

		// Render row
		$this->RowType = EW_ROWTYPE_VIEW;
		$this->ResetAttrs();
		$this->RenderRow();
	}

	// Set up starting record parameters
	function SetUpStartRec() {
		if ($this->DisplayRecs == 0)
			return;
		if ($this->IsPageRequest()) { // Validate request
			if (@$_GET[EW_TABLE_START_REC] <> "") { // Check for "start" parameter
				$this->StartRec = $_GET[EW_TABLE_START_REC];
				$this->setStartRecordNumber($this->StartRec);
			} elseif (@$_GET[EW_TABLE_PAGE_NO] <> "") {
				$PageNo = $_GET[EW_TABLE_PAGE_NO];
				if (is_numeric($PageNo)) {
					$this->StartRec = ($PageNo-1)*$this->DisplayRecs+1;
					if ($this->StartRec <= 0) {
						$this->StartRec = 1;
					} elseif ($this->StartRec >= intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1) {
						$this->StartRec = intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1;
					}
					$this->setStartRecordNumber($this->StartRec);
				}
			}
		}
		$this->StartRec = $this->getStartRecordNumber();

		// Check if correct start record counter
		if (!is_numeric($this->StartRec) || $this->StartRec == "") { // Avoid invalid start record counter
			$this->StartRec = 1; // Reset start record counter
			$this->setStartRecordNumber($this->StartRec);
		} elseif (intval($this->StartRec) > intval($this->TotalRecs)) { // Avoid starting record > total records
			$this->StartRec = intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1; // Point to last page first record
			$this->setStartRecordNumber($this->StartRec);
		} elseif (($this->StartRec-1) % $this->DisplayRecs <> 0) {
			$this->StartRec = intval(($this->StartRec-1)/$this->DisplayRecs)*$this->DisplayRecs+1; // Point to page boundary
			$this->setStartRecordNumber($this->StartRec);
		}
	}

	// Load recordset
	function LoadRecordset($offset = -1, $rowcnt = -1) {
		global $conn;

		// Call Recordset Selecting event
		$this->Recordset_Selecting($this->CurrentFilter);

		// Load List page SQL
		$sSql = $this->SelectSQL();
		if ($offset > -1 && $rowcnt > -1)
			$sSql .= " LIMIT $rowcnt OFFSET $offset";

		// Load recordset
		$rs = ew_LoadRecordset($sSql);

		// Call Recordset Selected event
		$this->Recordset_Selected($rs);
		return $rs;
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
		$this->AddUrl = $this->GetAddUrl();
		$this->EditUrl = $this->GetEditUrl();
		$this->CopyUrl = $this->GetCopyUrl();
		$this->DeleteUrl = $this->GetDeleteUrl();
		$this->ListUrl = $this->GetListUrl();

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
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	// Set up export options
	function SetupExportOptions() {
		global $Language;

		// Printer friendly
		$item = &$this->ExportOptions->Add("print");
		$item->Body = "<a href=\"" . $this->ExportPrintUrl . "\">" . $Language->Phrase("PrinterFriendly") . "</a>";
		$item->Visible = FALSE;

		// Export to Excel
		$item = &$this->ExportOptions->Add("excel");
		$item->Body = "<a href=\"" . $this->ExportExcelUrl . "\">" . $Language->Phrase("ExportToExcel") . "</a>";
		$item->Visible = TRUE;

		// Export to Word
		$item = &$this->ExportOptions->Add("word");
		$item->Body = "<a href=\"" . $this->ExportWordUrl . "\">" . $Language->Phrase("ExportToWord") . "</a>";
		$item->Visible = FALSE;

		// Export to Html
		$item = &$this->ExportOptions->Add("html");
		$item->Body = "<a href=\"" . $this->ExportHtmlUrl . "\">" . $Language->Phrase("ExportToHtml") . "</a>";
		$item->Visible = FALSE;

		// Export to Xml
		$item = &$this->ExportOptions->Add("xml");
		$item->Body = "<a href=\"" . $this->ExportXmlUrl . "\">" . $Language->Phrase("ExportToXml") . "</a>";
		$item->Visible = FALSE;

		// Export to Csv
		$item = &$this->ExportOptions->Add("csv");
		$item->Body = "<a href=\"" . $this->ExportCsvUrl . "\">" . $Language->Phrase("ExportToCsv") . "</a>";
		$item->Visible = FALSE;

		// Export to Pdf
		$item = &$this->ExportOptions->Add("pdf");
		$item->Body = "<a href=\"" . $this->ExportPdfUrl . "\">" . $Language->Phrase("ExportToPDF") . "</a>";
		$item->Visible = FALSE;

		// Export to Email
		$item = &$this->ExportOptions->Add("email");
		$item->Body = "<a id=\"emf_lh_gaji\" href=\"javascript:void(0);\" onclick=\"ew_EmailDialogShow({lnk:'emf_lh_gaji',hdr:ewLanguage.Phrase('ExportToEmail'),key:" . ew_ArrayToJsonAttr($this->RecKey) . ",sel:false});\">" . $Language->Phrase("ExportToEmail") . "</a>";
		$item->Visible = FALSE;

		// Hide options for export/action
		if ($this->Export <> "")
			$this->ExportOptions->HideAllOptions();
	}

	// Export data in HTML/CSV/Word/Excel/XML/Email/PDF format
	function ExportData() {
		$utf8 = (strtolower(EW_CHARSET) == "utf-8");
		$bSelectLimit = FALSE;

		// Load recordset
		if ($bSelectLimit) {
			$this->TotalRecs = $this->SelectRecordCount();
		} else {
			if ($rs = $this->LoadRecordset())
				$this->TotalRecs = $rs->RecordCount();
		}
		$this->StartRec = 1;
		$this->SetUpStartRec(); // Set up start record position

		// Set the last record to display
		if ($this->DisplayRecs <= 0) {
			$this->StopRec = $this->TotalRecs;
		} else {
			$this->StopRec = $this->StartRec + $this->DisplayRecs - 1;
		}
		if (!$rs) {
			header("Content-Type:"); // Remove header
			header("Content-Disposition:");
			$this->ShowMessage();
			return;
		}
		$ExportDoc = ew_ExportDocument($this, "v");
		$ParentTable = "";
		if ($bSelectLimit) {
			$StartRec = 1;
			$StopRec = $this->DisplayRecs <= 0 ? $this->TotalRecs : $this->DisplayRecs;
		} else {
			$StartRec = $this->StartRec;
			$StopRec = $this->StopRec;
		}
		$sHeader = $this->PageHeader;
		$this->Page_DataRendering($sHeader);
		$ExportDoc->Text .= $sHeader;
		$this->ExportDocument($ExportDoc, $rs, $StartRec, $StopRec, "view");
		$sFooter = $this->PageFooter;
		$this->Page_DataRendered($sFooter);
		$ExportDoc->Text .= $sFooter;

		// Close recordset
		$rs->Close();

		// Export header and footer
		$ExportDoc->ExportHeaderAndFooter();

		// Clean output buffer
		if (!EW_DEBUG_ENABLED && ob_get_length())
			ob_end_clean();

		// Write debug message if enabled
		if (EW_DEBUG_ENABLED)
			echo ew_DebugMsg();

		// Output data
		$ExportDoc->Export();
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
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($lh_gaji_view)) $lh_gaji_view = new clh_gaji_view();

// Page init
$lh_gaji_view->Page_Init();

// Page main
$lh_gaji_view->Page_Main();
?>
<?php include_once "header.php" ?>
<?php if ($lh_gaji->Export == "") { ?>
<script type="text/javascript">

// Page object
var lh_gaji_view = new ew_Page("lh_gaji_view");
lh_gaji_view.PageID = "view"; // Page ID
var EW_PAGE_ID = lh_gaji_view.PageID; // For backward compatibility

// Form object
var flh_gajiview = new ew_Form("flh_gajiview");

// Form_CustomValidate event
flh_gajiview.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
flh_gajiview.ValidateRequired = true;
<?php } else { ?>
flh_gajiview.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
flh_gajiview.Lists["x_id_user"] = {"LinkField":"x_id_user","Ajax":null,"AutoFill":false,"DisplayFields":["x_nama","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php } ?>
<p><span id="ewPageCaption" class="ewTitle ewTableTitle"><?php echo $Language->Phrase("View") ?>&nbsp;<?php echo $Language->Phrase("TblTypeTABLE") ?><?php echo $lh_gaji->TableCaption() ?>&nbsp;&nbsp;</span><?php $lh_gaji_view->ExportOptions->Render("body"); ?>
</p>
<?php if ($lh_gaji->Export == "") { ?>
<p class="phpmaker">
<a href="<?php echo $lh_gaji_view->ListUrl ?>" id="a_BackToList" class="ewLink"><?php echo $Language->Phrase("BackToList") ?></a>&nbsp;
<?php if ($Security->CanAdd()) { ?>
<?php if ($lh_gaji_view->AddUrl <> "") { ?>
<a href="<?php echo $lh_gaji_view->AddUrl ?>" id="a_AddLink" class="ewLink"><?php echo $Language->Phrase("ViewPageAddLink") ?></a>&nbsp;
<?php } ?>
<?php } ?>
<?php if ($Security->CanEdit()) { ?>
<?php if ($lh_gaji_view->EditUrl <> "") { ?>
<a href="<?php echo $lh_gaji_view->EditUrl ?>" id="a_EditLink" class="ewLink"><?php echo $Language->Phrase("ViewPageEditLink") ?></a>&nbsp;
<?php } ?>
<?php } ?>
<?php if ($Security->CanAdd()) { ?>
<?php if ($lh_gaji_view->CopyUrl <> "") { ?>
<a href="<?php echo $lh_gaji_view->CopyUrl ?>" id="a_CopyLink" class="ewLink"><?php echo $Language->Phrase("ViewPageCopyLink") ?></a>&nbsp;
<?php } ?>
<?php } ?>
<?php if ($Security->CanDelete()) { ?>
<?php if ($lh_gaji_view->DeleteUrl <> "") { ?>
<a href="<?php echo $lh_gaji_view->DeleteUrl ?>" id="a_DeleteLink" class="ewLink"><?php echo $Language->Phrase("ViewPageDeleteLink") ?></a>&nbsp;
<?php } ?>
<?php } ?>
</p>
<?php } ?>
<?php $lh_gaji_view->ShowPageHeader(); ?>
<?php
$lh_gaji_view->ShowMessage();
?>
<form name="flh_gajiview" id="flh_gajiview" class="ewForm" action="" method="post">
<input type="hidden" name="t" value="lh_gaji">
<table cellspacing="0" class="ewGrid"><tr><td class="ewGridContent">
<div class="ewGridMiddlePanel">
<table id="tbl_lh_gajiview" class="ewTable">
<?php if ($lh_gaji->id_gaji->Visible) { // id_gaji ?>
	<tr id="r_id_gaji"<?php echo $lh_gaji->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_lh_gaji_id_gaji"><table class="ewTableHeaderBtn"><tr><td><?php echo $lh_gaji->id_gaji->FldCaption() ?></td></tr></table></span></td>
		<td<?php echo $lh_gaji->id_gaji->CellAttributes() ?>><span id="el_lh_gaji_id_gaji">
<span<?php echo $lh_gaji->id_gaji->ViewAttributes() ?>>
<?php echo $lh_gaji->id_gaji->ViewValue ?></span>
</span></td>
	</tr>
<?php } ?>
<?php if ($lh_gaji->id_user->Visible) { // id_user ?>
	<tr id="r_id_user"<?php echo $lh_gaji->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_lh_gaji_id_user"><table class="ewTableHeaderBtn"><tr><td><?php echo $lh_gaji->id_user->FldCaption() ?></td></tr></table></span></td>
		<td<?php echo $lh_gaji->id_user->CellAttributes() ?>><span id="el_lh_gaji_id_user">
<span<?php echo $lh_gaji->id_user->ViewAttributes() ?>>
<?php echo $lh_gaji->id_user->ViewValue ?></span>
</span></td>
	</tr>
<?php } ?>
<?php if ($lh_gaji->status->Visible) { // status ?>
	<tr id="r_status"<?php echo $lh_gaji->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_lh_gaji_status"><table class="ewTableHeaderBtn"><tr><td><?php echo $lh_gaji->status->FldCaption() ?></td></tr></table></span></td>
		<td<?php echo $lh_gaji->status->CellAttributes() ?>><span id="el_lh_gaji_status">
<span<?php echo $lh_gaji->status->ViewAttributes() ?>>
<?php echo $lh_gaji->status->ViewValue ?></span>
</span></td>
	</tr>
<?php } ?>
<?php if ($lh_gaji->tanggal->Visible) { // tanggal ?>
	<tr id="r_tanggal"<?php echo $lh_gaji->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_lh_gaji_tanggal"><table class="ewTableHeaderBtn"><tr><td><?php echo $lh_gaji->tanggal->FldCaption() ?></td></tr></table></span></td>
		<td<?php echo $lh_gaji->tanggal->CellAttributes() ?>><span id="el_lh_gaji_tanggal">
<span<?php echo $lh_gaji->tanggal->ViewAttributes() ?>>
<?php echo $lh_gaji->tanggal->ViewValue ?></span>
</span></td>
	</tr>
<?php } ?>
<?php if ($lh_gaji->gaji_pokok->Visible) { // gaji_pokok ?>
	<tr id="r_gaji_pokok"<?php echo $lh_gaji->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_lh_gaji_gaji_pokok"><table class="ewTableHeaderBtn"><tr><td><?php echo $lh_gaji->gaji_pokok->FldCaption() ?></td></tr></table></span></td>
		<td<?php echo $lh_gaji->gaji_pokok->CellAttributes() ?>><span id="el_lh_gaji_gaji_pokok">
<span<?php echo $lh_gaji->gaji_pokok->ViewAttributes() ?>>
<?php echo $lh_gaji->gaji_pokok->ViewValue ?></span>
</span></td>
	</tr>
<?php } ?>
<?php if ($lh_gaji->lembur->Visible) { // lembur ?>
	<tr id="r_lembur"<?php echo $lh_gaji->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_lh_gaji_lembur"><table class="ewTableHeaderBtn"><tr><td><?php echo $lh_gaji->lembur->FldCaption() ?></td></tr></table></span></td>
		<td<?php echo $lh_gaji->lembur->CellAttributes() ?>><span id="el_lh_gaji_lembur">
<span<?php echo $lh_gaji->lembur->ViewAttributes() ?>>
<?php echo $lh_gaji->lembur->ViewValue ?></span>
</span></td>
	</tr>
<?php } ?>
<?php if ($lh_gaji->tunjangan_proyek->Visible) { // tunjangan_proyek ?>
	<tr id="r_tunjangan_proyek"<?php echo $lh_gaji->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_lh_gaji_tunjangan_proyek"><table class="ewTableHeaderBtn"><tr><td><?php echo $lh_gaji->tunjangan_proyek->FldCaption() ?></td></tr></table></span></td>
		<td<?php echo $lh_gaji->tunjangan_proyek->CellAttributes() ?>><span id="el_lh_gaji_tunjangan_proyek">
<span<?php echo $lh_gaji->tunjangan_proyek->ViewAttributes() ?>>
<?php echo $lh_gaji->tunjangan_proyek->ViewValue ?></span>
</span></td>
	</tr>
<?php } ?>
</table>
</div>
</td></tr></table>
</form>
<br>
<script type="text/javascript">
flh_gajiview.Init();
</script>
<?php
$lh_gaji_view->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<?php if ($lh_gaji->Export == "") { ?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php } ?>
<?php include_once "footer.php" ?>
<?php
$lh_gaji_view->Page_Terminate();
?>
