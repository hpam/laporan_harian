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

$lh_dana_list = NULL; // Initialize page object first

class clh_dana_list extends clh_dana {

	// Page ID
	var $PageID = 'list';

	// Project ID
	var $ProjectID = "{67264FB2-6364-478B-87DD-B3E0D7A29425}";

	// Table name
	var $TableName = 'lh_dana';

	// Page object name
	var $PageObjName = 'lh_dana_list';

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

		// Table object (lh_dana)
		if (!isset($GLOBALS["lh_dana"])) {
			$GLOBALS["lh_dana"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["lh_dana"];
		}

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html";
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml";
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";
		$this->AddUrl = "lh_danaadd.php";
		$this->InlineAddUrl = $this->PageUrl() . "a=add";
		$this->GridAddUrl = $this->PageUrl() . "a=gridadd";
		$this->GridEditUrl = $this->PageUrl() . "a=gridedit";
		$this->MultiDeleteUrl = "lh_danadelete.php";
		$this->MultiUpdateUrl = "lh_danaupdate.php";

		// Table object (lh_user)
		if (!isset($GLOBALS['lh_user'])) $GLOBALS['lh_user'] = new clh_user();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'list', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'lh_dana', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect();

		// List options
		$this->ListOptions = new cListOptions();
		$this->ListOptions->TableVar = $this->TableVar;

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
		if (!$Security->CanList()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage($Language->Phrase("NoPermission")); // Set no permission
			$this->Page_Terminate("login.php");
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

		// Get grid add count
		$gridaddcnt = @$_GET[EW_TABLE_GRID_ADD_ROW_COUNT];
		if (is_numeric($gridaddcnt) && $gridaddcnt > 0)
			$this->GridAddRowCount = $gridaddcnt;

		// Set up list options
		$this->SetupListOptions();

		// Setup export options
		$this->SetupExportOptions();
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

	// Class variables
	var $ListOptions; // List options
	var $ExportOptions; // Export options
	var $DisplayRecs = 20;
	var $StartRec;
	var $StopRec;
	var $TotalRecs = 0;
	var $RecRange = 10;
	var $Pager;
	var $SearchWhere = ""; // Search WHERE clause
	var $RecCnt = 0; // Record count
	var $EditRowCnt;
	var $StartRowCnt = 1;
	var $RowCnt = 0;
	var $Attrs = array(); // Row attributes and cell attributes
	var $RowIndex = 0; // Row index
	var $KeyCount = 0; // Key count
	var $RowAction = ""; // Row action
	var $RowOldKey = ""; // Row old key (for copy)
	var $RecPerRow = 0;
	var $ColCnt = 0;
	var $DbMasterFilter = ""; // Master filter
	var $DbDetailFilter = ""; // Detail filter
	var $MasterRecordExists;	
	var $MultiSelectKey;
	var $Command;
	var $Recordset;
	var $OldRecordset;

	//
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsFormError, $gsSearchError, $Security;

		// Search filters
		$sSrchAdvanced = ""; // Advanced search filter
		$sSrchBasic = ""; // Basic search filter
		$sFilter = "";

		// Get command
		$this->Command = strtolower(@$_GET["cmd"]);
		if ($this->IsPageRequest()) { // Validate request

			// Handle reset command
			$this->ResetCmd();

			// Hide all options
			if ($this->Export <> "" ||
				$this->CurrentAction == "gridadd" ||
				$this->CurrentAction == "gridedit") {
				$this->ListOptions->HideAllOptions();
				$this->ExportOptions->HideAllOptions();
			}

			// Set up sorting order
			$this->SetUpSortOrder();
		}

		// Restore display records
		if ($this->getRecordsPerPage() <> "") {
			$this->DisplayRecs = $this->getRecordsPerPage(); // Restore from Session
		} else {
			$this->DisplayRecs = 20; // Load default
		}

		// Load Sorting Order
		$this->LoadSortOrder();

		// Build filter
		$sFilter = "";
		if (!$Security->CanList())
			$sFilter = "(0=1)"; // Filter all records
		ew_AddFilter($sFilter, $this->DbDetailFilter);
		ew_AddFilter($sFilter, $this->SearchWhere);

		// Set up filter in session
		$this->setSessionWhere($sFilter);
		$this->CurrentFilter = "";

		// Export data only
		if (in_array($this->Export, array("html","word","excel","xml","csv","email","pdf"))) {
			$this->ExportData();
			if ($this->Export == "email")
				$this->Page_Terminate($this->ExportReturnUrl());
			else
				$this->Page_Terminate(); // Terminate response
			exit();
		}
	}

	// Build filter for all keys
	function BuildKeyFilter() {
		global $objForm;
		$sWrkFilter = "";

		// Update row index and get row key
		$rowindex = 1;
		$objForm->Index = $rowindex;
		$sThisKey = strval($objForm->GetValue("k_key"));
		while ($sThisKey <> "") {
			if ($this->SetupKeyValues($sThisKey)) {
				$sFilter = $this->KeyFilter();
				if ($sWrkFilter <> "") $sWrkFilter .= " OR ";
				$sWrkFilter .= $sFilter;
			} else {
				$sWrkFilter = "0=1";
				break;
			}

			// Update row index and get row key
			$rowindex++; // next row
			$objForm->Index = $rowindex;
			$sThisKey = strval($objForm->GetValue("k_key"));
		}
		return $sWrkFilter;
	}

	// Set up key values
	function SetupKeyValues($key) {
		$arrKeyFlds = explode($GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"], $key);
		if (count($arrKeyFlds) >= 1) {
			$this->id_dana->setFormValue($arrKeyFlds[0]);
			if (!is_numeric($this->id_dana->FormValue))
				return FALSE;
		}
		return TRUE;
	}

	// Set up sort parameters
	function SetUpSortOrder() {

		// Check for "order" parameter
		if (@$_GET["order"] <> "") {
			$this->CurrentOrder = ew_StripSlashes(@$_GET["order"]);
			$this->CurrentOrderType = @$_GET["ordertype"];
			$this->UpdateSort($this->id_dana); // id_dana
			$this->UpdateSort($this->jumlah_dana); // jumlah_dana
			$this->UpdateSort($this->periode_pembiayaan); // periode_pembiayaan
			$this->setStartRecordNumber(1); // Reset start position
		}
	}

	// Load sort order parameters
	function LoadSortOrder() {
		$sOrderBy = $this->getSessionOrderBy(); // Get ORDER BY from Session
		if ($sOrderBy == "") {
			if ($this->SqlOrderBy() <> "") {
				$sOrderBy = $this->SqlOrderBy();
				$this->setSessionOrderBy($sOrderBy);
				$this->periode_pembiayaan->setSort("DESC");
			}
		}
	}

	// Reset command
	// cmd=reset (Reset search parameters)
	// cmd=resetall (Reset search and master/detail parameters)
	// cmd=resetsort (Reset sort parameters)
	function ResetCmd() {

		// Check if reset command
		if (substr($this->Command,0,5) == "reset") {

			// Reset sorting order
			if ($this->Command == "resetsort") {
				$sOrderBy = "";
				$this->setSessionOrderBy($sOrderBy);
				$this->id_dana->setSort("");
				$this->jumlah_dana->setSort("");
				$this->periode_pembiayaan->setSort("");
			}

			// Reset start position
			$this->StartRec = 1;
			$this->setStartRecordNumber($this->StartRec);
		}
	}

	// Set up list options
	function SetupListOptions() {
		global $Security, $Language;

		// "view"
		$item = &$this->ListOptions->Add("view");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = $Security->CanView();
		$item->OnLeft = FALSE;

		// "edit"
		$item = &$this->ListOptions->Add("edit");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = $Security->CanEdit();
		$item->OnLeft = FALSE;

		// "copy"
		$item = &$this->ListOptions->Add("copy");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = $Security->CanAdd();
		$item->OnLeft = FALSE;

		// "delete"
		$item = &$this->ListOptions->Add("delete");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = $Security->CanDelete();
		$item->OnLeft = FALSE;

		// Call ListOptions_Load event
		$this->ListOptions_Load();
	}

	// Render list options
	function RenderListOptions() {
		global $Security, $Language, $objForm;
		$this->ListOptions->LoadDefault();

		// "view"
		$oListOpt = &$this->ListOptions->Items["view"];
		if ($Security->CanView())
			$oListOpt->Body = "<a class=\"ewRowLink\" href=\"" . $this->ViewUrl . "\">" . $Language->Phrase("ViewLink") . "</a>";

		// "edit"
		$oListOpt = &$this->ListOptions->Items["edit"];
		if ($Security->CanEdit()) {
			$oListOpt->Body = "<a class=\"ewRowLink\" href=\"" . $this->EditUrl . "\">" . $Language->Phrase("EditLink") . "</a>";
		}

		// "copy"
		$oListOpt = &$this->ListOptions->Items["copy"];
		if ($Security->CanAdd()) {
			$oListOpt->Body = "<a class=\"ewRowLink\" href=\"" . $this->CopyUrl . "\">" . $Language->Phrase("CopyLink") . "</a>";
		}

		// "delete"
		$oListOpt = &$this->ListOptions->Items["delete"];
		if ($Security->CanDelete())
			$oListOpt->Body = "<a class=\"ewRowLink\"" . "" . " href=\"" . $this->DeleteUrl . "\">" . $Language->Phrase("DeleteLink") . "</a>";
		$this->RenderListOptionsExt();

		// Call ListOptions_Rendered event
		$this->ListOptions_Rendered();
	}

	function RenderListOptionsExt() {
		global $Security, $Language;
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
		$this->ViewUrl = $this->GetViewUrl();
		$this->EditUrl = $this->GetEditUrl();
		$this->InlineEditUrl = $this->GetInlineEditUrl();
		$this->CopyUrl = $this->GetCopyUrl();
		$this->InlineCopyUrl = $this->GetInlineCopyUrl();
		$this->DeleteUrl = $this->GetDeleteUrl();

		// Convert decimal values if posted back
		if ($this->jumlah_dana->FormValue == $this->jumlah_dana->CurrentValue && is_numeric(ew_StrToFloat($this->jumlah_dana->CurrentValue)))
			$this->jumlah_dana->CurrentValue = ew_StrToFloat($this->jumlah_dana->CurrentValue);

		// Call Row_Rendering event
		$this->Row_Rendering();

		// Common render codes for all row types
		// id_dana
		// jumlah_dana
		// periode_pembiayaan
		// Accumulate aggregate value

		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT && $this->RowType <> EW_ROWTYPE_AGGREGATE) {
			if (is_numeric($this->jumlah_dana->CurrentValue))
				$this->jumlah_dana->Total += $this->jumlah_dana->CurrentValue; // Accumulate total
		}
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
		} elseif ($this->RowType == EW_ROWTYPE_AGGREGATEINIT) { // Initialize aggregate row
			$this->jumlah_dana->Total = 0; // Initialize total
		} elseif ($this->RowType == EW_ROWTYPE_AGGREGATE) { // Aggregate row
			$this->jumlah_dana->CurrentValue = $this->jumlah_dana->Total;
			$this->jumlah_dana->ViewValue = $this->jumlah_dana->CurrentValue;
			$this->jumlah_dana->ViewValue = ew_FormatNumber($this->jumlah_dana->ViewValue, 2, -1, 0, -1);
			$this->jumlah_dana->CellCssStyle .= "text-align: right;";
			$this->jumlah_dana->ViewCustomAttributes = "";
			$this->jumlah_dana->HrefValue = ""; // Clear href value
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
		$item->Body = "<a id=\"emf_lh_dana\" href=\"javascript:void(0);\" onclick=\"ew_EmailDialogShow({lnk:'emf_lh_dana',hdr:ewLanguage.Phrase('ExportToEmail'),f:document.flh_danalist,sel:false});\">" . $Language->Phrase("ExportToEmail") . "</a>";
		$item->Visible = FALSE;

		// Hide options for export/action
		if ($this->Export <> "" || $this->CurrentAction <> "")
			$this->ExportOptions->HideAllOptions();
	}

	// Export data in HTML/CSV/Word/Excel/XML/Email/PDF format
	function ExportData() {
		$utf8 = (strtolower(EW_CHARSET) == "utf-8");
		$bSelectLimit = EW_SELECT_LIMIT;

		// Load recordset
		if ($bSelectLimit) {
			$this->TotalRecs = $this->SelectRecordCount();
		} else {
			if ($rs = $this->LoadRecordset())
				$this->TotalRecs = $rs->RecordCount();
		}
		$this->StartRec = 1;

		// Export all
		if ($this->ExportAll) {
			set_time_limit(EW_EXPORT_ALL_TIME_LIMIT);
			$this->DisplayRecs = $this->TotalRecs;
			$this->StopRec = $this->TotalRecs;
		} else { // Export one page only
			$this->SetUpStartRec(); // Set up start record position

			// Set the last record to display
			if ($this->DisplayRecs <= 0) {
				$this->StopRec = $this->TotalRecs;
			} else {
				$this->StopRec = $this->StartRec + $this->DisplayRecs - 1;
			}
		}
		if ($bSelectLimit)
			$rs = $this->LoadRecordset($this->StartRec-1, $this->DisplayRecs <= 0 ? $this->TotalRecs : $this->DisplayRecs);
		if (!$rs) {
			header("Content-Type:"); // Remove header
			header("Content-Disposition:");
			$this->ShowMessage();
			return;
		}
		$ExportDoc = ew_ExportDocument($this, "h");
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
		$this->ExportDocument($ExportDoc, $rs, $StartRec, $StopRec, "");
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

	// Form Custom Validate event
	function Form_CustomValidate(&$CustomError) {

		// Return error message in CustomError
		return TRUE;
	}

	// ListOptions Load event
	function ListOptions_Load() {

		// Example:
		//$opt = &$this->ListOptions->Add("new");
		//$opt->Header = "xxx";
		//$opt->OnLeft = TRUE; // Link on left
		//$opt->MoveTo(0); // Move to first column

	}

	// ListOptions Rendered event
	function ListOptions_Rendered() {

		// Example: 
		//$this->ListOptions->Items["new"]->Body = "xxx";

	}
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($lh_dana_list)) $lh_dana_list = new clh_dana_list();

// Page init
$lh_dana_list->Page_Init();

// Page main
$lh_dana_list->Page_Main();
?>
<?php include_once "header.php" ?>
<?php if ($lh_dana->Export == "") { ?>
<script type="text/javascript">

// Page object
var lh_dana_list = new ew_Page("lh_dana_list");
lh_dana_list.PageID = "list"; // Page ID
var EW_PAGE_ID = lh_dana_list.PageID; // For backward compatibility

// Form object
var flh_danalist = new ew_Form("flh_danalist");

// Form_CustomValidate event
flh_danalist.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
flh_danalist.ValidateRequired = true;
<?php } else { ?>
flh_danalist.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
// Form object for search

</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php } ?>
<?php
	$bSelectLimit = EW_SELECT_LIMIT;
	if ($bSelectLimit) {
		$lh_dana_list->TotalRecs = $lh_dana->SelectRecordCount();
	} else {
		if ($lh_dana_list->Recordset = $lh_dana_list->LoadRecordset())
			$lh_dana_list->TotalRecs = $lh_dana_list->Recordset->RecordCount();
	}
	$lh_dana_list->StartRec = 1;
	if ($lh_dana_list->DisplayRecs <= 0 || ($lh_dana->Export <> "" && $lh_dana->ExportAll)) // Display all records
		$lh_dana_list->DisplayRecs = $lh_dana_list->TotalRecs;
	if (!($lh_dana->Export <> "" && $lh_dana->ExportAll))
		$lh_dana_list->SetUpStartRec(); // Set up start record position
	if ($bSelectLimit)
		$lh_dana_list->Recordset = $lh_dana_list->LoadRecordset($lh_dana_list->StartRec-1, $lh_dana_list->DisplayRecs);
?>
<p style="white-space: nowrap;"><span id="ewPageCaption" class="ewTitle ewTableTitle"><?php echo $Language->Phrase("TblTypeTABLE") ?><?php echo $lh_dana->TableCaption() ?>&nbsp;&nbsp;</span>
<?php $lh_dana_list->ExportOptions->Render("body"); ?>
</p>
<?php $lh_dana_list->ShowPageHeader(); ?>
<?php
$lh_dana_list->ShowMessage();
?>
<br>
<table cellspacing="0" class="ewGrid"><tr><td class="ewGridContent">
<form name="flh_danalist" id="flh_danalist" class="ewForm" action="" method="post">
<input type="hidden" name="t" value="lh_dana">
<div id="gmp_lh_dana" class="ewGridMiddlePanel">
<?php if ($lh_dana_list->TotalRecs > 0) { ?>
<table id="tbl_lh_danalist" class="ewTable ewTableSeparate">
<?php echo $lh_dana->TableCustomInnerHtml ?>
<thead><!-- Table header -->
	<tr class="ewTableHeader">
<?php

// Render list options
$lh_dana_list->RenderListOptions();

// Render list options (header, left)
$lh_dana_list->ListOptions->Render("header", "left");
?>
<?php if ($lh_dana->id_dana->Visible) { // id_dana ?>
	<?php if ($lh_dana->SortUrl($lh_dana->id_dana) == "") { ?>
		<td><span id="elh_lh_dana_id_dana" class="lh_dana_id_dana"><table class="ewTableHeaderBtn"><thead><tr><td><?php echo $lh_dana->id_dana->FldCaption() ?></td></tr></thead></table></span></td>
	<?php } else { ?>
		<td><div onmousedown="ew_Sort(event,'<?php echo $lh_dana->SortUrl($lh_dana->id_dana) ?>',1);"><span id="elh_lh_dana_id_dana" class="lh_dana_id_dana">
			<table class="ewTableHeaderBtn"><thead><tr><td class="ewTableHeaderCaption"><?php echo $lh_dana->id_dana->FldCaption() ?></td><td class="ewTableHeaderSort"><?php if ($lh_dana->id_dana->getSort() == "ASC") { ?><img src="phpimages/sortup.gif" width="10" height="9" alt="" style="border: 0;"><?php } elseif ($lh_dana->id_dana->getSort() == "DESC") { ?><img src="phpimages/sortdown.gif" width="10" height="9" alt="" style="border: 0;"><?php } ?></td></tr></thead></table>
		</span></div></td>		
	<?php } ?>
<?php } ?>		
<?php if ($lh_dana->jumlah_dana->Visible) { // jumlah_dana ?>
	<?php if ($lh_dana->SortUrl($lh_dana->jumlah_dana) == "") { ?>
		<td><span id="elh_lh_dana_jumlah_dana" class="lh_dana_jumlah_dana"><table class="ewTableHeaderBtn"><thead><tr><td><?php echo $lh_dana->jumlah_dana->FldCaption() ?></td></tr></thead></table></span></td>
	<?php } else { ?>
		<td><div onmousedown="ew_Sort(event,'<?php echo $lh_dana->SortUrl($lh_dana->jumlah_dana) ?>',1);"><span id="elh_lh_dana_jumlah_dana" class="lh_dana_jumlah_dana">
			<table class="ewTableHeaderBtn"><thead><tr><td class="ewTableHeaderCaption"><?php echo $lh_dana->jumlah_dana->FldCaption() ?></td><td class="ewTableHeaderSort"><?php if ($lh_dana->jumlah_dana->getSort() == "ASC") { ?><img src="phpimages/sortup.gif" width="10" height="9" alt="" style="border: 0;"><?php } elseif ($lh_dana->jumlah_dana->getSort() == "DESC") { ?><img src="phpimages/sortdown.gif" width="10" height="9" alt="" style="border: 0;"><?php } ?></td></tr></thead></table>
		</span></div></td>		
	<?php } ?>
<?php } ?>		
<?php if ($lh_dana->periode_pembiayaan->Visible) { // periode_pembiayaan ?>
	<?php if ($lh_dana->SortUrl($lh_dana->periode_pembiayaan) == "") { ?>
		<td><span id="elh_lh_dana_periode_pembiayaan" class="lh_dana_periode_pembiayaan"><table class="ewTableHeaderBtn"><thead><tr><td><?php echo $lh_dana->periode_pembiayaan->FldCaption() ?></td></tr></thead></table></span></td>
	<?php } else { ?>
		<td><div onmousedown="ew_Sort(event,'<?php echo $lh_dana->SortUrl($lh_dana->periode_pembiayaan) ?>',1);"><span id="elh_lh_dana_periode_pembiayaan" class="lh_dana_periode_pembiayaan">
			<table class="ewTableHeaderBtn"><thead><tr><td class="ewTableHeaderCaption"><?php echo $lh_dana->periode_pembiayaan->FldCaption() ?></td><td class="ewTableHeaderSort"><?php if ($lh_dana->periode_pembiayaan->getSort() == "ASC") { ?><img src="phpimages/sortup.gif" width="10" height="9" alt="" style="border: 0;"><?php } elseif ($lh_dana->periode_pembiayaan->getSort() == "DESC") { ?><img src="phpimages/sortdown.gif" width="10" height="9" alt="" style="border: 0;"><?php } ?></td></tr></thead></table>
		</span></div></td>		
	<?php } ?>
<?php } ?>		
<?php

// Render list options (header, right)
$lh_dana_list->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
if ($lh_dana->ExportAll && $lh_dana->Export <> "") {
	$lh_dana_list->StopRec = $lh_dana_list->TotalRecs;
} else {

	// Set the last record to display
	if ($lh_dana_list->TotalRecs > $lh_dana_list->StartRec + $lh_dana_list->DisplayRecs - 1)
		$lh_dana_list->StopRec = $lh_dana_list->StartRec + $lh_dana_list->DisplayRecs - 1;
	else
		$lh_dana_list->StopRec = $lh_dana_list->TotalRecs;
}
$lh_dana_list->RecCnt = $lh_dana_list->StartRec - 1;
if ($lh_dana_list->Recordset && !$lh_dana_list->Recordset->EOF) {
	$lh_dana_list->Recordset->MoveFirst();
	if (!$bSelectLimit && $lh_dana_list->StartRec > 1)
		$lh_dana_list->Recordset->Move($lh_dana_list->StartRec - 1);
} elseif (!$lh_dana->AllowAddDeleteRow && $lh_dana_list->StopRec == 0) {
	$lh_dana_list->StopRec = $lh_dana->GridAddRowCount;
}

// Initialize aggregate
$lh_dana->RowType = EW_ROWTYPE_AGGREGATEINIT;
$lh_dana->ResetAttrs();
$lh_dana_list->RenderRow();
while ($lh_dana_list->RecCnt < $lh_dana_list->StopRec) {
	$lh_dana_list->RecCnt++;
	if (intval($lh_dana_list->RecCnt) >= intval($lh_dana_list->StartRec)) {
		$lh_dana_list->RowCnt++;

		// Set up key count
		$lh_dana_list->KeyCount = $lh_dana_list->RowIndex;

		// Init row class and style
		$lh_dana->ResetAttrs();
		$lh_dana->CssClass = "";
		if ($lh_dana->CurrentAction == "gridadd") {
		} else {
			$lh_dana_list->LoadRowValues($lh_dana_list->Recordset); // Load row values
		}
		$lh_dana->RowType = EW_ROWTYPE_VIEW; // Render view

		// Set up row id / data-rowindex
		$lh_dana->RowAttrs = array_merge($lh_dana->RowAttrs, array('data-rowindex'=>$lh_dana_list->RowCnt, 'id'=>'r' . $lh_dana_list->RowCnt . '_lh_dana', 'data-rowtype'=>$lh_dana->RowType));

		// Render row
		$lh_dana_list->RenderRow();

		// Render list options
		$lh_dana_list->RenderListOptions();
?>
	<tr<?php echo $lh_dana->RowAttributes() ?>>
<?php

// Render list options (body, left)
$lh_dana_list->ListOptions->Render("body", "left", $lh_dana_list->RowCnt);
?>
	<?php if ($lh_dana->id_dana->Visible) { // id_dana ?>
		<td<?php echo $lh_dana->id_dana->CellAttributes() ?>><span id="el<?php echo $lh_dana_list->RowCnt ?>_lh_dana_id_dana" class="lh_dana_id_dana">
<span<?php echo $lh_dana->id_dana->ViewAttributes() ?>>
<?php echo $lh_dana->id_dana->ListViewValue() ?></span>
</span><a id="<?php echo $lh_dana_list->PageObjName . "_row_" . $lh_dana_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($lh_dana->jumlah_dana->Visible) { // jumlah_dana ?>
		<td<?php echo $lh_dana->jumlah_dana->CellAttributes() ?>><span id="el<?php echo $lh_dana_list->RowCnt ?>_lh_dana_jumlah_dana" class="lh_dana_jumlah_dana">
<span<?php echo $lh_dana->jumlah_dana->ViewAttributes() ?>>
<?php echo $lh_dana->jumlah_dana->ListViewValue() ?></span>
</span><a id="<?php echo $lh_dana_list->PageObjName . "_row_" . $lh_dana_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($lh_dana->periode_pembiayaan->Visible) { // periode_pembiayaan ?>
		<td<?php echo $lh_dana->periode_pembiayaan->CellAttributes() ?>><span id="el<?php echo $lh_dana_list->RowCnt ?>_lh_dana_periode_pembiayaan" class="lh_dana_periode_pembiayaan">
<span<?php echo $lh_dana->periode_pembiayaan->ViewAttributes() ?>>
<?php echo $lh_dana->periode_pembiayaan->ListViewValue() ?></span>
</span><a id="<?php echo $lh_dana_list->PageObjName . "_row_" . $lh_dana_list->RowCnt ?>"></a></td>
	<?php } ?>
<?php

// Render list options (body, right)
$lh_dana_list->ListOptions->Render("body", "right", $lh_dana_list->RowCnt);
?>
	</tr>
<?php
	}
	if ($lh_dana->CurrentAction <> "gridadd")
		$lh_dana_list->Recordset->MoveNext();
}
?>
</tbody>
<?php

// Render aggregate row
$lh_dana->RowType = EW_ROWTYPE_AGGREGATE;
$lh_dana->ResetAttrs();
$lh_dana_list->RenderRow();
?>
<?php if ($lh_dana_list->TotalRecs > 0 && ($lh_dana->CurrentAction <> "gridadd" && $lh_dana->CurrentAction <> "gridedit")) { ?>
<tfoot><!-- Table footer -->
	<tr class="ewTableFooter">
<?php

// Render list options
$lh_dana_list->RenderListOptions();

// Render list options (footer, left)
$lh_dana_list->ListOptions->Render("footer", "left");
?>
	<?php if ($lh_dana->id_dana->Visible) { // id_dana ?>
		<td><span id="elf_lh_dana_id_dana" class="lh_dana_id_dana">
		&nbsp;
		</span></td>
	<?php } ?>
	<?php if ($lh_dana->jumlah_dana->Visible) { // jumlah_dana ?>
		<td><span id="elf_lh_dana_jumlah_dana" class="lh_dana_jumlah_dana">
<?php echo $Language->Phrase("TOTAL") ?>: 
<?php echo $lh_dana->jumlah_dana->ViewValue ?>
		</span></td>
	<?php } ?>
	<?php if ($lh_dana->periode_pembiayaan->Visible) { // periode_pembiayaan ?>
		<td><span id="elf_lh_dana_periode_pembiayaan" class="lh_dana_periode_pembiayaan">
		&nbsp;
		</span></td>
	<?php } ?>
<?php

// Render list options (footer, right)
$lh_dana_list->ListOptions->Render("footer", "right");
?>
	</tr>
</tfoot>	
<?php } ?>
</table>
<?php } ?>
<?php if ($lh_dana->CurrentAction == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
</div>
</form>
<?php

// Close recordset
if ($lh_dana_list->Recordset)
	$lh_dana_list->Recordset->Close();
?>
<?php if ($lh_dana->Export == "") { ?>
<div class="ewGridLowerPanel">
<?php if ($lh_dana->CurrentAction <> "gridadd" && $lh_dana->CurrentAction <> "gridedit") { ?>
<form name="ewpagerform" id="ewpagerform" class="ewForm" action="<?php echo ew_CurrentPage() ?>">
<table class="ewPager"><tr><td>
<?php if (!isset($lh_dana_list->Pager)) $lh_dana_list->Pager = new cPrevNextPager($lh_dana_list->StartRec, $lh_dana_list->DisplayRecs, $lh_dana_list->TotalRecs) ?>
<?php if ($lh_dana_list->Pager->RecordCount > 0) { ?>
	<table cellspacing="0" class="ewStdTable"><tbody><tr><td><span class="phpmaker"><?php echo $Language->Phrase("Page") ?>&nbsp;</span></td>
<!--first page button-->
	<?php if ($lh_dana_list->Pager->FirstButton->Enabled) { ?>
	<td><a href="<?php echo $lh_dana_list->PageUrl() ?>start=<?php echo $lh_dana_list->Pager->FirstButton->Start ?>"><img src="phpimages/first.gif" alt="<?php echo $Language->Phrase("PagerFirst") ?>" width="16" height="16" style="border: 0;"></a></td>
	<?php } else { ?>
	<td><img src="phpimages/firstdisab.gif" alt="<?php echo $Language->Phrase("PagerFirst") ?>" width="16" height="16" style="border: 0;"></td>
	<?php } ?>
<!--previous page button-->
	<?php if ($lh_dana_list->Pager->PrevButton->Enabled) { ?>
	<td><a href="<?php echo $lh_dana_list->PageUrl() ?>start=<?php echo $lh_dana_list->Pager->PrevButton->Start ?>"><img src="phpimages/prev.gif" alt="<?php echo $Language->Phrase("PagerPrevious") ?>" width="16" height="16" style="border: 0;"></a></td>
	<?php } else { ?>
	<td><img src="phpimages/prevdisab.gif" alt="<?php echo $Language->Phrase("PagerPrevious") ?>" width="16" height="16" style="border: 0;"></td>
	<?php } ?>
<!--current page number-->
	<td><input type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" id="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $lh_dana_list->Pager->CurrentPage ?>" size="4"></td>
<!--next page button-->
	<?php if ($lh_dana_list->Pager->NextButton->Enabled) { ?>
	<td><a href="<?php echo $lh_dana_list->PageUrl() ?>start=<?php echo $lh_dana_list->Pager->NextButton->Start ?>"><img src="phpimages/next.gif" alt="<?php echo $Language->Phrase("PagerNext") ?>" width="16" height="16" style="border: 0;"></a></td>	
	<?php } else { ?>
	<td><img src="phpimages/nextdisab.gif" alt="<?php echo $Language->Phrase("PagerNext") ?>" width="16" height="16" style="border: 0;"></td>
	<?php } ?>
<!--last page button-->
	<?php if ($lh_dana_list->Pager->LastButton->Enabled) { ?>
	<td><a href="<?php echo $lh_dana_list->PageUrl() ?>start=<?php echo $lh_dana_list->Pager->LastButton->Start ?>"><img src="phpimages/last.gif" alt="<?php echo $Language->Phrase("PagerLast") ?>" width="16" height="16" style="border: 0;"></a></td>	
	<?php } else { ?>
	<td><img src="phpimages/lastdisab.gif" alt="<?php echo $Language->Phrase("PagerLast") ?>" width="16" height="16" style="border: 0;"></td>
	<?php } ?>
	<td><span class="phpmaker">&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $lh_dana_list->Pager->PageCount ?></span></td>
	</tr></tbody></table>
	</td>	
	<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
	<td>
	<span class="phpmaker"><?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $lh_dana_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $lh_dana_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $lh_dana_list->Pager->RecordCount ?></span>
<?php } else { ?>
	<?php if ($Security->CanList()) { ?>
	<?php if ($lh_dana_list->SearchWhere == "0=101") { ?>
	<span class="phpmaker"><?php echo $Language->Phrase("EnterSearchCriteria") ?></span>
	<?php } else { ?>
	<span class="phpmaker"><?php echo $Language->Phrase("NoRecord") ?></span>
	<?php } ?>
	<?php } else { ?>
	<span class="phpmaker"><?php echo $Language->Phrase("NoPermission") ?></span>
	<?php } ?>
<?php } ?>
	</td>
</tr></table>
</form>
<?php } ?>
<span class="phpmaker">
<?php if ($Security->CanAdd()) { ?>
<?php if ($lh_dana_list->AddUrl <> "") { ?>
<a class="ewGridLink" href="<?php echo $lh_dana_list->AddUrl ?>"><?php echo $Language->Phrase("AddLink") ?></a>&nbsp;&nbsp;
<?php } ?>
<?php } ?>
</span>
</div>
<?php } ?>
</td></tr></table>
<?php if ($lh_dana->Export == "") { ?>
<script type="text/javascript">
flh_danalist.Init();
</script>
<?php } ?>
<?php
$lh_dana_list->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<?php if ($lh_dana->Export == "") { ?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php } ?>
<?php include_once "footer.php" ?>
<?php
$lh_dana_list->Page_Terminate();
?>
