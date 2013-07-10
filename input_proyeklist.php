<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg9.php" ?>
<?php include_once "ewmysql9.php" ?>
<?php include_once "phpfn9.php" ?>
<?php include_once "input_proyekinfo.php" ?>
<?php include_once "userfn9.php" ?>
<?php

//
// Page class
//

$input_proyek_list = NULL; // Initialize page object first

class cinput_proyek_list extends cinput_proyek {

	// Page ID
	var $PageID = 'list';

	// Project ID
	var $ProjectID = "{67264FB2-6364-478B-87DD-B3E0D7A29425}";

	// Table name
	var $TableName = 'input proyek';

	// Page object name
	var $PageObjName = 'input_proyek_list';

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

		// Table object (input_proyek)
		if (!isset($GLOBALS["input_proyek"])) {
			$GLOBALS["input_proyek"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["input_proyek"];
		}

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html";
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml";
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";
		$this->AddUrl = "input_proyekadd.php";
		$this->InlineAddUrl = $this->PageUrl() . "a=add";
		$this->GridAddUrl = $this->PageUrl() . "a=gridadd";
		$this->GridEditUrl = $this->PageUrl() . "a=gridedit";
		$this->MultiDeleteUrl = "input_proyekdelete.php";
		$this->MultiUpdateUrl = "input_proyekupdate.php";

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'list', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'input proyek', TRUE);

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
		$this->id_proyek->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

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

			// Get basic search values
			$this->LoadBasicSearchValues();

			// Restore search parms from Session if not searching / reset
			if ($this->Command <> "search" && $this->Command <> "reset" && $this->Command <> "resetall")
				$this->RestoreSearchParms();

			// Call Recordset SearchValidated event
			$this->Recordset_SearchValidated();

			// Set up sorting order
			$this->SetUpSortOrder();

			// Get basic search criteria
			if ($gsSearchError == "")
				$sSrchBasic = $this->BasicSearchWhere();
		}

		// Restore display records
		if ($this->getRecordsPerPage() <> "") {
			$this->DisplayRecs = $this->getRecordsPerPage(); // Restore from Session
		} else {
			$this->DisplayRecs = 20; // Load default
		}

		// Load Sorting Order
		$this->LoadSortOrder();

		// Load search default if no existing search criteria
		if (!$this->CheckSearchParms()) {

			// Load basic search from default
			$this->BasicSearch->LoadDefault();
			if ($this->BasicSearch->Keyword != "")
				$sSrchBasic = $this->BasicSearchWhere();
		}

		// Build search criteria
		ew_AddFilter($this->SearchWhere, $sSrchAdvanced);
		ew_AddFilter($this->SearchWhere, $sSrchBasic);

		// Call Recordset_Searching event
		$this->Recordset_Searching($this->SearchWhere);

		// Save search criteria
		if ($this->Command == "search") {
			$this->setSearchWhere($this->SearchWhere); // Save to Session
			$this->StartRec = 1; // Reset start record counter
			$this->setStartRecordNumber($this->StartRec);
		} else {
			$this->SearchWhere = $this->getSearchWhere();
		}

		// Build filter
		$sFilter = "";
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
			$this->id_proyek->setFormValue($arrKeyFlds[0]);
			if (!is_numeric($this->id_proyek->FormValue))
				return FALSE;
		}
		return TRUE;
	}

	// Return basic search SQL
	function BasicSearchSQL($Keyword) {
		$sKeyword = ew_AdjustSql($Keyword);
		$sWhere = "";
		$this->BuildBasicSearchSQL($sWhere, $this->nama_proyek, $Keyword);
		return $sWhere;
	}

	// Build basic search SQL
	function BuildBasicSearchSql(&$Where, &$Fld, $Keyword) {
		if ($Keyword == EW_NULL_VALUE) {
			$sWrk = $Fld->FldExpression . " IS NULL";
		} elseif ($Keyword == EW_NOT_NULL_VALUE) {
			$sWrk = $Fld->FldExpression . " IS NOT NULL";
		} else {
			$sFldExpression = ($Fld->FldVirtualExpression <> $Fld->FldExpression) ? $Fld->FldVirtualExpression : $Fld->FldBasicSearchExpression;
			$sWrk = $sFldExpression . ew_Like(ew_QuotedValue("%" . $Keyword . "%", EW_DATATYPE_STRING));
		}
		if ($Where <> "") $Where .= " OR ";
		$Where .= $sWrk;
	}

	// Return basic search WHERE clause based on search keyword and type
	function BasicSearchWhere() {
		global $Security;
		$sSearchStr = "";
		$sSearchKeyword = $this->BasicSearch->Keyword;
		$sSearchType = $this->BasicSearch->Type;
		if ($sSearchKeyword <> "") {
			$sSearch = trim($sSearchKeyword);
			if ($sSearchType <> "=") {
				while (strpos($sSearch, "  ") !== FALSE)
					$sSearch = str_replace("  ", " ", $sSearch);
				$arKeyword = explode(" ", trim($sSearch));
				foreach ($arKeyword as $sKeyword) {
					if ($sSearchStr <> "") $sSearchStr .= " " . $sSearchType . " ";
					$sSearchStr .= "(" . $this->BasicSearchSQL($sKeyword) . ")";
				}
			} else {
				$sSearchStr = $this->BasicSearchSQL($sSearch);
			}
			$this->Command = "search";
		}
		if ($this->Command == "search") {
			$this->BasicSearch->setKeyword($sSearchKeyword);
			$this->BasicSearch->setType($sSearchType);
		}
		return $sSearchStr;
	}

	// Check if search parm exists
	function CheckSearchParms() {

		// Check basic search
		if ($this->BasicSearch->IssetSession())
			return TRUE;
		return FALSE;
	}

	// Clear all search parameters
	function ResetSearchParms() {

		// Clear search WHERE clause
		$this->SearchWhere = "";
		$this->setSearchWhere($this->SearchWhere);

		// Clear basic search parameters
		$this->ResetBasicSearchParms();
	}

	// Load advanced search default values
	function LoadAdvancedSearchDefault() {
		return FALSE;
	}

	// Clear all basic search parameters
	function ResetBasicSearchParms() {
		$this->BasicSearch->UnsetSession();
	}

	// Restore all search parameters
	function RestoreSearchParms() {

		// Restore basic search values
		$this->BasicSearch->Load();
	}

	// Set up sort parameters
	function SetUpSortOrder() {

		// Check for "order" parameter
		if (@$_GET["order"] <> "") {
			$this->CurrentOrder = ew_StripSlashes(@$_GET["order"]);
			$this->CurrentOrderType = @$_GET["ordertype"];
			$this->UpdateSort($this->id_proyek); // id_proyek
			$this->UpdateSort($this->nama_proyek); // nama_proyek
			$this->UpdateSort($this->tanggal); // tanggal
			$this->UpdateSort($this->kardus); // kardus
			$this->UpdateSort($this->kayu); // kayu
			$this->UpdateSort($this->besi); // besi
			$this->UpdateSort($this->harga); // harga
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

			// Reset search criteria
			if ($this->Command == "reset" || $this->Command == "resetall")
				$this->ResetSearchParms();

			// Reset sorting order
			if ($this->Command == "resetsort") {
				$sOrderBy = "";
				$this->setSessionOrderBy($sOrderBy);
				$this->id_proyek->setSort("");
				$this->nama_proyek->setSort("");
				$this->tanggal->setSort("");
				$this->kardus->setSort("");
				$this->kayu->setSort("");
				$this->besi->setSort("");
				$this->harga->setSort("");
			}

			// Reset start position
			$this->StartRec = 1;
			$this->setStartRecordNumber($this->StartRec);
		}
	}

	// Set up list options
	function SetupListOptions() {
		global $Security, $Language;

		// Call ListOptions_Load event
		$this->ListOptions_Load();
	}

	// Render list options
	function RenderListOptions() {
		global $Security, $Language, $objForm;
		$this->ListOptions->LoadDefault();
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

	// Load basic search values
	function LoadBasicSearchValues() {
		$this->BasicSearch->Keyword = @$_GET[EW_TABLE_BASIC_SEARCH];
		if ($this->BasicSearch->Keyword <> "") $this->Command = "search";
		$this->BasicSearch->Type = @$_GET[EW_TABLE_BASIC_SEARCH_TYPE];
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
		$this->ViewUrl = $this->GetViewUrl();
		$this->EditUrl = $this->GetEditUrl();
		$this->InlineEditUrl = $this->GetInlineEditUrl();
		$this->CopyUrl = $this->GetCopyUrl();
		$this->InlineCopyUrl = $this->GetInlineCopyUrl();
		$this->DeleteUrl = $this->GetDeleteUrl();

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
		// Accumulate aggregate value

		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT && $this->RowType <> EW_ROWTYPE_AGGREGATE) {
			if (is_numeric($this->kardus->CurrentValue))
				$this->kardus->Total += $this->kardus->CurrentValue; // Accumulate total
			if (is_numeric($this->kayu->CurrentValue))
				$this->kayu->Total += $this->kayu->CurrentValue; // Accumulate total
			if (is_numeric($this->besi->CurrentValue))
				$this->besi->Total += $this->besi->CurrentValue; // Accumulate total
			if (is_numeric($this->harga->CurrentValue))
				$this->harga->Total += $this->harga->CurrentValue; // Accumulate total
		}
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
			$this->harga->ViewValue = ew_FormatNumber($this->harga->ViewValue, 2, -1, -1, -1);
			$this->harga->CellCssStyle .= "text-align: right;";
			$this->harga->ViewCustomAttributes = "";

			// id_proyek
			$this->id_proyek->LinkCustomAttributes = "";
			$this->id_proyek->HrefValue = "";
			$this->id_proyek->TooltipValue = "";

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
		} elseif ($this->RowType == EW_ROWTYPE_AGGREGATEINIT) { // Initialize aggregate row
			$this->kardus->Total = 0; // Initialize total
			$this->kayu->Total = 0; // Initialize total
			$this->besi->Total = 0; // Initialize total
			$this->harga->Total = 0; // Initialize total
		} elseif ($this->RowType == EW_ROWTYPE_AGGREGATE) { // Aggregate row
			$this->kardus->CurrentValue = $this->kardus->Total;
			$this->kardus->ViewValue = $this->kardus->CurrentValue;
			$this->kardus->ViewCustomAttributes = "";
			$this->kardus->HrefValue = ""; // Clear href value
			$this->kayu->CurrentValue = $this->kayu->Total;
			$this->kayu->ViewValue = $this->kayu->CurrentValue;
			$this->kayu->ViewCustomAttributes = "";
			$this->kayu->HrefValue = ""; // Clear href value
			$this->besi->CurrentValue = $this->besi->Total;
			$this->besi->ViewValue = $this->besi->CurrentValue;
			$this->besi->ViewCustomAttributes = "";
			$this->besi->HrefValue = ""; // Clear href value
			$this->harga->CurrentValue = $this->harga->Total;
			$this->harga->ViewValue = $this->harga->CurrentValue;
			$this->harga->ViewValue = ew_FormatNumber($this->harga->ViewValue, 2, -1, -1, -1);
			$this->harga->CellCssStyle .= "text-align: right;";
			$this->harga->ViewCustomAttributes = "";
			$this->harga->HrefValue = ""; // Clear href value
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
		$item->Body = "<a id=\"emf_input_proyek\" href=\"javascript:void(0);\" onclick=\"ew_EmailDialogShow({lnk:'emf_input_proyek',hdr:ewLanguage.Phrase('ExportToEmail'),f:document.finput_proyeklist,sel:false});\">" . $Language->Phrase("ExportToEmail") . "</a>";
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
if (!isset($input_proyek_list)) $input_proyek_list = new cinput_proyek_list();

// Page init
$input_proyek_list->Page_Init();

// Page main
$input_proyek_list->Page_Main();
?>
<?php include_once "header.php" ?>
<?php if ($input_proyek->Export == "") { ?>
<script type="text/javascript">

// Page object
var input_proyek_list = new ew_Page("input_proyek_list");
input_proyek_list.PageID = "list"; // Page ID
var EW_PAGE_ID = input_proyek_list.PageID; // For backward compatibility

// Form object
var finput_proyeklist = new ew_Form("finput_proyeklist");

// Form_CustomValidate event
finput_proyeklist.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
finput_proyeklist.ValidateRequired = true;
<?php } else { ?>
finput_proyeklist.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
// Form object for search

var finput_proyeklistsrch = new ew_Form("finput_proyeklistsrch");
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php } ?>
<?php
	$bSelectLimit = EW_SELECT_LIMIT;
	if ($bSelectLimit) {
		$input_proyek_list->TotalRecs = $input_proyek->SelectRecordCount();
	} else {
		if ($input_proyek_list->Recordset = $input_proyek_list->LoadRecordset())
			$input_proyek_list->TotalRecs = $input_proyek_list->Recordset->RecordCount();
	}
	$input_proyek_list->StartRec = 1;
	if ($input_proyek_list->DisplayRecs <= 0 || ($input_proyek->Export <> "" && $input_proyek->ExportAll)) // Display all records
		$input_proyek_list->DisplayRecs = $input_proyek_list->TotalRecs;
	if (!($input_proyek->Export <> "" && $input_proyek->ExportAll))
		$input_proyek_list->SetUpStartRec(); // Set up start record position
	if ($bSelectLimit)
		$input_proyek_list->Recordset = $input_proyek_list->LoadRecordset($input_proyek_list->StartRec-1, $input_proyek_list->DisplayRecs);
?>
<p style="white-space: nowrap;"><span id="ewPageCaption" class="ewTitle ewTableTitle"><?php echo $Language->Phrase("TblTypeVIEW") ?><?php echo $input_proyek->TableCaption() ?>&nbsp;&nbsp;</span>
<?php $input_proyek_list->ExportOptions->Render("body"); ?>
</p>
<?php if ($input_proyek->Export == "" && $input_proyek->CurrentAction == "") { ?>
<form name="finput_proyeklistsrch" id="finput_proyeklistsrch" class="ewForm" action="<?php echo ew_CurrentPage() ?>">
<a href="javascript:finput_proyeklistsrch.ToggleSearchPanel();" style="text-decoration: none;"><img id="finput_proyeklistsrch_SearchImage" src="phpimages/collapse.gif" alt="" width="9" height="9" style="border: 0;"></a><span class="phpmaker">&nbsp;<?php echo $Language->Phrase("Search") ?></span><br>
<div id="finput_proyeklistsrch_SearchPanel">
<input type="hidden" name="cmd" value="search">
<input type="hidden" name="t" value="input_proyek">
<div class="ewBasicSearch">
<div id="xsr_1" class="ewRow">
	<input type="text" name="<?php echo EW_TABLE_BASIC_SEARCH ?>" id="<?php echo EW_TABLE_BASIC_SEARCH ?>" size="20" value="<?php echo ew_HtmlEncode($input_proyek_list->BasicSearch->getKeyword()) ?>">
	<input type="submit" name="btnsubmit" id="btnsubmit" value="<?php echo ew_BtnCaption($Language->Phrase("QuickSearchBtn")) ?>">&nbsp;
	<a href="<?php echo $input_proyek_list->PageUrl() ?>cmd=reset" id="a_ShowAll" class="ewLink"><?php echo $Language->Phrase("ShowAll") ?></a>&nbsp;
</div>
<div id="xsr_2" class="ewRow">
	<label><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="="<?php if ($input_proyek_list->BasicSearch->getType() == "=") { ?> checked="checked"<?php } ?>><?php echo $Language->Phrase("ExactPhrase") ?></label>&nbsp;&nbsp;<label><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="AND"<?php if ($input_proyek_list->BasicSearch->getType() == "AND") { ?> checked="checked"<?php } ?>><?php echo $Language->Phrase("AllWord") ?></label>&nbsp;&nbsp;<label><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="OR"<?php if ($input_proyek_list->BasicSearch->getType() == "OR") { ?> checked="checked"<?php } ?>><?php echo $Language->Phrase("AnyWord") ?></label>
</div>
</div>
</div>
</form>
<?php } ?>
<?php $input_proyek_list->ShowPageHeader(); ?>
<?php
$input_proyek_list->ShowMessage();
?>
<br>
<table cellspacing="0" class="ewGrid"><tr><td class="ewGridContent">
<form name="finput_proyeklist" id="finput_proyeklist" class="ewForm" action="" method="post">
<input type="hidden" name="t" value="input_proyek">
<div id="gmp_input_proyek" class="ewGridMiddlePanel">
<?php if ($input_proyek_list->TotalRecs > 0) { ?>
<table id="tbl_input_proyeklist" class="ewTable ewTableSeparate">
<?php echo $input_proyek->TableCustomInnerHtml ?>
<thead><!-- Table header -->
	<tr class="ewTableHeader">
<?php

// Render list options
$input_proyek_list->RenderListOptions();

// Render list options (header, left)
$input_proyek_list->ListOptions->Render("header", "left");
?>
<?php if ($input_proyek->id_proyek->Visible) { // id_proyek ?>
	<?php if ($input_proyek->SortUrl($input_proyek->id_proyek) == "") { ?>
		<td><span id="elh_input_proyek_id_proyek" class="input_proyek_id_proyek"><table class="ewTableHeaderBtn"><thead><tr><td><?php echo $input_proyek->id_proyek->FldCaption() ?></td></tr></thead></table></span></td>
	<?php } else { ?>
		<td><div onmousedown="ew_Sort(event,'<?php echo $input_proyek->SortUrl($input_proyek->id_proyek) ?>',1);"><span id="elh_input_proyek_id_proyek" class="input_proyek_id_proyek">
			<table class="ewTableHeaderBtn"><thead><tr><td class="ewTableHeaderCaption"><?php echo $input_proyek->id_proyek->FldCaption() ?></td><td class="ewTableHeaderSort"><?php if ($input_proyek->id_proyek->getSort() == "ASC") { ?><img src="phpimages/sortup.gif" width="10" height="9" alt="" style="border: 0;"><?php } elseif ($input_proyek->id_proyek->getSort() == "DESC") { ?><img src="phpimages/sortdown.gif" width="10" height="9" alt="" style="border: 0;"><?php } ?></td></tr></thead></table>
		</span></div></td>		
	<?php } ?>
<?php } ?>		
<?php if ($input_proyek->nama_proyek->Visible) { // nama_proyek ?>
	<?php if ($input_proyek->SortUrl($input_proyek->nama_proyek) == "") { ?>
		<td><span id="elh_input_proyek_nama_proyek" class="input_proyek_nama_proyek"><table class="ewTableHeaderBtn"><thead><tr><td><?php echo $input_proyek->nama_proyek->FldCaption() ?></td></tr></thead></table></span></td>
	<?php } else { ?>
		<td><div onmousedown="ew_Sort(event,'<?php echo $input_proyek->SortUrl($input_proyek->nama_proyek) ?>',1);"><span id="elh_input_proyek_nama_proyek" class="input_proyek_nama_proyek">
			<table class="ewTableHeaderBtn"><thead><tr><td class="ewTableHeaderCaption"><?php echo $input_proyek->nama_proyek->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></td><td class="ewTableHeaderSort"><?php if ($input_proyek->nama_proyek->getSort() == "ASC") { ?><img src="phpimages/sortup.gif" width="10" height="9" alt="" style="border: 0;"><?php } elseif ($input_proyek->nama_proyek->getSort() == "DESC") { ?><img src="phpimages/sortdown.gif" width="10" height="9" alt="" style="border: 0;"><?php } ?></td></tr></thead></table>
		</span></div></td>		
	<?php } ?>
<?php } ?>		
<?php if ($input_proyek->tanggal->Visible) { // tanggal ?>
	<?php if ($input_proyek->SortUrl($input_proyek->tanggal) == "") { ?>
		<td><span id="elh_input_proyek_tanggal" class="input_proyek_tanggal"><table class="ewTableHeaderBtn"><thead><tr><td><?php echo $input_proyek->tanggal->FldCaption() ?></td></tr></thead></table></span></td>
	<?php } else { ?>
		<td><div onmousedown="ew_Sort(event,'<?php echo $input_proyek->SortUrl($input_proyek->tanggal) ?>',1);"><span id="elh_input_proyek_tanggal" class="input_proyek_tanggal">
			<table class="ewTableHeaderBtn"><thead><tr><td class="ewTableHeaderCaption"><?php echo $input_proyek->tanggal->FldCaption() ?></td><td class="ewTableHeaderSort"><?php if ($input_proyek->tanggal->getSort() == "ASC") { ?><img src="phpimages/sortup.gif" width="10" height="9" alt="" style="border: 0;"><?php } elseif ($input_proyek->tanggal->getSort() == "DESC") { ?><img src="phpimages/sortdown.gif" width="10" height="9" alt="" style="border: 0;"><?php } ?></td></tr></thead></table>
		</span></div></td>		
	<?php } ?>
<?php } ?>		
<?php if ($input_proyek->kardus->Visible) { // kardus ?>
	<?php if ($input_proyek->SortUrl($input_proyek->kardus) == "") { ?>
		<td><span id="elh_input_proyek_kardus" class="input_proyek_kardus"><table class="ewTableHeaderBtn"><thead><tr><td><?php echo $input_proyek->kardus->FldCaption() ?></td></tr></thead></table></span></td>
	<?php } else { ?>
		<td><div onmousedown="ew_Sort(event,'<?php echo $input_proyek->SortUrl($input_proyek->kardus) ?>',1);"><span id="elh_input_proyek_kardus" class="input_proyek_kardus">
			<table class="ewTableHeaderBtn"><thead><tr><td class="ewTableHeaderCaption"><?php echo $input_proyek->kardus->FldCaption() ?></td><td class="ewTableHeaderSort"><?php if ($input_proyek->kardus->getSort() == "ASC") { ?><img src="phpimages/sortup.gif" width="10" height="9" alt="" style="border: 0;"><?php } elseif ($input_proyek->kardus->getSort() == "DESC") { ?><img src="phpimages/sortdown.gif" width="10" height="9" alt="" style="border: 0;"><?php } ?></td></tr></thead></table>
		</span></div></td>		
	<?php } ?>
<?php } ?>		
<?php if ($input_proyek->kayu->Visible) { // kayu ?>
	<?php if ($input_proyek->SortUrl($input_proyek->kayu) == "") { ?>
		<td><span id="elh_input_proyek_kayu" class="input_proyek_kayu"><table class="ewTableHeaderBtn"><thead><tr><td><?php echo $input_proyek->kayu->FldCaption() ?></td></tr></thead></table></span></td>
	<?php } else { ?>
		<td><div onmousedown="ew_Sort(event,'<?php echo $input_proyek->SortUrl($input_proyek->kayu) ?>',1);"><span id="elh_input_proyek_kayu" class="input_proyek_kayu">
			<table class="ewTableHeaderBtn"><thead><tr><td class="ewTableHeaderCaption"><?php echo $input_proyek->kayu->FldCaption() ?></td><td class="ewTableHeaderSort"><?php if ($input_proyek->kayu->getSort() == "ASC") { ?><img src="phpimages/sortup.gif" width="10" height="9" alt="" style="border: 0;"><?php } elseif ($input_proyek->kayu->getSort() == "DESC") { ?><img src="phpimages/sortdown.gif" width="10" height="9" alt="" style="border: 0;"><?php } ?></td></tr></thead></table>
		</span></div></td>		
	<?php } ?>
<?php } ?>		
<?php if ($input_proyek->besi->Visible) { // besi ?>
	<?php if ($input_proyek->SortUrl($input_proyek->besi) == "") { ?>
		<td><span id="elh_input_proyek_besi" class="input_proyek_besi"><table class="ewTableHeaderBtn"><thead><tr><td><?php echo $input_proyek->besi->FldCaption() ?></td></tr></thead></table></span></td>
	<?php } else { ?>
		<td><div onmousedown="ew_Sort(event,'<?php echo $input_proyek->SortUrl($input_proyek->besi) ?>',1);"><span id="elh_input_proyek_besi" class="input_proyek_besi">
			<table class="ewTableHeaderBtn"><thead><tr><td class="ewTableHeaderCaption"><?php echo $input_proyek->besi->FldCaption() ?></td><td class="ewTableHeaderSort"><?php if ($input_proyek->besi->getSort() == "ASC") { ?><img src="phpimages/sortup.gif" width="10" height="9" alt="" style="border: 0;"><?php } elseif ($input_proyek->besi->getSort() == "DESC") { ?><img src="phpimages/sortdown.gif" width="10" height="9" alt="" style="border: 0;"><?php } ?></td></tr></thead></table>
		</span></div></td>		
	<?php } ?>
<?php } ?>		
<?php if ($input_proyek->harga->Visible) { // harga ?>
	<?php if ($input_proyek->SortUrl($input_proyek->harga) == "") { ?>
		<td><span id="elh_input_proyek_harga" class="input_proyek_harga"><table class="ewTableHeaderBtn"><thead><tr><td><?php echo $input_proyek->harga->FldCaption() ?></td></tr></thead></table></span></td>
	<?php } else { ?>
		<td><div onmousedown="ew_Sort(event,'<?php echo $input_proyek->SortUrl($input_proyek->harga) ?>',1);"><span id="elh_input_proyek_harga" class="input_proyek_harga">
			<table class="ewTableHeaderBtn"><thead><tr><td class="ewTableHeaderCaption"><?php echo $input_proyek->harga->FldCaption() ?></td><td class="ewTableHeaderSort"><?php if ($input_proyek->harga->getSort() == "ASC") { ?><img src="phpimages/sortup.gif" width="10" height="9" alt="" style="border: 0;"><?php } elseif ($input_proyek->harga->getSort() == "DESC") { ?><img src="phpimages/sortdown.gif" width="10" height="9" alt="" style="border: 0;"><?php } ?></td></tr></thead></table>
		</span></div></td>		
	<?php } ?>
<?php } ?>		
<?php

// Render list options (header, right)
$input_proyek_list->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
if ($input_proyek->ExportAll && $input_proyek->Export <> "") {
	$input_proyek_list->StopRec = $input_proyek_list->TotalRecs;
} else {

	// Set the last record to display
	if ($input_proyek_list->TotalRecs > $input_proyek_list->StartRec + $input_proyek_list->DisplayRecs - 1)
		$input_proyek_list->StopRec = $input_proyek_list->StartRec + $input_proyek_list->DisplayRecs - 1;
	else
		$input_proyek_list->StopRec = $input_proyek_list->TotalRecs;
}
$input_proyek_list->RecCnt = $input_proyek_list->StartRec - 1;
if ($input_proyek_list->Recordset && !$input_proyek_list->Recordset->EOF) {
	$input_proyek_list->Recordset->MoveFirst();
	if (!$bSelectLimit && $input_proyek_list->StartRec > 1)
		$input_proyek_list->Recordset->Move($input_proyek_list->StartRec - 1);
} elseif (!$input_proyek->AllowAddDeleteRow && $input_proyek_list->StopRec == 0) {
	$input_proyek_list->StopRec = $input_proyek->GridAddRowCount;
}

// Initialize aggregate
$input_proyek->RowType = EW_ROWTYPE_AGGREGATEINIT;
$input_proyek->ResetAttrs();
$input_proyek_list->RenderRow();
while ($input_proyek_list->RecCnt < $input_proyek_list->StopRec) {
	$input_proyek_list->RecCnt++;
	if (intval($input_proyek_list->RecCnt) >= intval($input_proyek_list->StartRec)) {
		$input_proyek_list->RowCnt++;

		// Set up key count
		$input_proyek_list->KeyCount = $input_proyek_list->RowIndex;

		// Init row class and style
		$input_proyek->ResetAttrs();
		$input_proyek->CssClass = "";
		if ($input_proyek->CurrentAction == "gridadd") {
		} else {
			$input_proyek_list->LoadRowValues($input_proyek_list->Recordset); // Load row values
		}
		$input_proyek->RowType = EW_ROWTYPE_VIEW; // Render view

		// Set up row id / data-rowindex
		$input_proyek->RowAttrs = array_merge($input_proyek->RowAttrs, array('data-rowindex'=>$input_proyek_list->RowCnt, 'id'=>'r' . $input_proyek_list->RowCnt . '_input_proyek', 'data-rowtype'=>$input_proyek->RowType));

		// Render row
		$input_proyek_list->RenderRow();

		// Render list options
		$input_proyek_list->RenderListOptions();
?>
	<tr<?php echo $input_proyek->RowAttributes() ?>>
<?php

// Render list options (body, left)
$input_proyek_list->ListOptions->Render("body", "left", $input_proyek_list->RowCnt);
?>
	<?php if ($input_proyek->id_proyek->Visible) { // id_proyek ?>
		<td<?php echo $input_proyek->id_proyek->CellAttributes() ?>><span id="el<?php echo $input_proyek_list->RowCnt ?>_input_proyek_id_proyek" class="input_proyek_id_proyek">
<span<?php echo $input_proyek->id_proyek->ViewAttributes() ?>>
<?php echo $input_proyek->id_proyek->ListViewValue() ?></span>
</span><a id="<?php echo $input_proyek_list->PageObjName . "_row_" . $input_proyek_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($input_proyek->nama_proyek->Visible) { // nama_proyek ?>
		<td<?php echo $input_proyek->nama_proyek->CellAttributes() ?>><span id="el<?php echo $input_proyek_list->RowCnt ?>_input_proyek_nama_proyek" class="input_proyek_nama_proyek">
<span<?php echo $input_proyek->nama_proyek->ViewAttributes() ?>>
<?php echo $input_proyek->nama_proyek->ListViewValue() ?></span>
</span><a id="<?php echo $input_proyek_list->PageObjName . "_row_" . $input_proyek_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($input_proyek->tanggal->Visible) { // tanggal ?>
		<td<?php echo $input_proyek->tanggal->CellAttributes() ?>><span id="el<?php echo $input_proyek_list->RowCnt ?>_input_proyek_tanggal" class="input_proyek_tanggal">
<span<?php echo $input_proyek->tanggal->ViewAttributes() ?>>
<?php echo $input_proyek->tanggal->ListViewValue() ?></span>
</span><a id="<?php echo $input_proyek_list->PageObjName . "_row_" . $input_proyek_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($input_proyek->kardus->Visible) { // kardus ?>
		<td<?php echo $input_proyek->kardus->CellAttributes() ?>><span id="el<?php echo $input_proyek_list->RowCnt ?>_input_proyek_kardus" class="input_proyek_kardus">
<span<?php echo $input_proyek->kardus->ViewAttributes() ?>>
<?php echo $input_proyek->kardus->ListViewValue() ?></span>
</span><a id="<?php echo $input_proyek_list->PageObjName . "_row_" . $input_proyek_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($input_proyek->kayu->Visible) { // kayu ?>
		<td<?php echo $input_proyek->kayu->CellAttributes() ?>><span id="el<?php echo $input_proyek_list->RowCnt ?>_input_proyek_kayu" class="input_proyek_kayu">
<span<?php echo $input_proyek->kayu->ViewAttributes() ?>>
<?php echo $input_proyek->kayu->ListViewValue() ?></span>
</span><a id="<?php echo $input_proyek_list->PageObjName . "_row_" . $input_proyek_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($input_proyek->besi->Visible) { // besi ?>
		<td<?php echo $input_proyek->besi->CellAttributes() ?>><span id="el<?php echo $input_proyek_list->RowCnt ?>_input_proyek_besi" class="input_proyek_besi">
<span<?php echo $input_proyek->besi->ViewAttributes() ?>>
<?php echo $input_proyek->besi->ListViewValue() ?></span>
</span><a id="<?php echo $input_proyek_list->PageObjName . "_row_" . $input_proyek_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($input_proyek->harga->Visible) { // harga ?>
		<td<?php echo $input_proyek->harga->CellAttributes() ?>><span id="el<?php echo $input_proyek_list->RowCnt ?>_input_proyek_harga" class="input_proyek_harga">
<span<?php echo $input_proyek->harga->ViewAttributes() ?>>
<?php echo $input_proyek->harga->ListViewValue() ?></span>
</span><a id="<?php echo $input_proyek_list->PageObjName . "_row_" . $input_proyek_list->RowCnt ?>"></a></td>
	<?php } ?>
<?php

// Render list options (body, right)
$input_proyek_list->ListOptions->Render("body", "right", $input_proyek_list->RowCnt);
?>
	</tr>
<?php
	}
	if ($input_proyek->CurrentAction <> "gridadd")
		$input_proyek_list->Recordset->MoveNext();
}
?>
</tbody>
<?php

// Render aggregate row
$input_proyek->RowType = EW_ROWTYPE_AGGREGATE;
$input_proyek->ResetAttrs();
$input_proyek_list->RenderRow();
?>
<?php if ($input_proyek_list->TotalRecs > 0 && ($input_proyek->CurrentAction <> "gridadd" && $input_proyek->CurrentAction <> "gridedit")) { ?>
<tfoot><!-- Table footer -->
	<tr class="ewTableFooter">
<?php

// Render list options
$input_proyek_list->RenderListOptions();

// Render list options (footer, left)
$input_proyek_list->ListOptions->Render("footer", "left");
?>
	<?php if ($input_proyek->id_proyek->Visible) { // id_proyek ?>
		<td><span id="elf_input_proyek_id_proyek" class="input_proyek_id_proyek">
		&nbsp;
		</span></td>
	<?php } ?>
	<?php if ($input_proyek->nama_proyek->Visible) { // nama_proyek ?>
		<td><span id="elf_input_proyek_nama_proyek" class="input_proyek_nama_proyek">
		&nbsp;
		</span></td>
	<?php } ?>
	<?php if ($input_proyek->tanggal->Visible) { // tanggal ?>
		<td><span id="elf_input_proyek_tanggal" class="input_proyek_tanggal">
		&nbsp;
		</span></td>
	<?php } ?>
	<?php if ($input_proyek->kardus->Visible) { // kardus ?>
		<td><span id="elf_input_proyek_kardus" class="input_proyek_kardus">
<?php echo $Language->Phrase("TOTAL") ?>: 
<?php echo $input_proyek->kardus->ViewValue ?>
		</span></td>
	<?php } ?>
	<?php if ($input_proyek->kayu->Visible) { // kayu ?>
		<td><span id="elf_input_proyek_kayu" class="input_proyek_kayu">
<?php echo $Language->Phrase("TOTAL") ?>: 
<?php echo $input_proyek->kayu->ViewValue ?>
		</span></td>
	<?php } ?>
	<?php if ($input_proyek->besi->Visible) { // besi ?>
		<td><span id="elf_input_proyek_besi" class="input_proyek_besi">
<?php echo $Language->Phrase("TOTAL") ?>: 
<?php echo $input_proyek->besi->ViewValue ?>
		</span></td>
	<?php } ?>
	<?php if ($input_proyek->harga->Visible) { // harga ?>
		<td><span id="elf_input_proyek_harga" class="input_proyek_harga">
<?php echo $Language->Phrase("TOTAL") ?>: 
<?php echo $input_proyek->harga->ViewValue ?>
		</span></td>
	<?php } ?>
<?php

// Render list options (footer, right)
$input_proyek_list->ListOptions->Render("footer", "right");
?>
	</tr>
</tfoot>	
<?php } ?>
</table>
<?php } ?>
<?php if ($input_proyek->CurrentAction == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
</div>
</form>
<?php

// Close recordset
if ($input_proyek_list->Recordset)
	$input_proyek_list->Recordset->Close();
?>
<?php if ($input_proyek->Export == "") { ?>
<div class="ewGridLowerPanel">
<?php if ($input_proyek->CurrentAction <> "gridadd" && $input_proyek->CurrentAction <> "gridedit") { ?>
<form name="ewpagerform" id="ewpagerform" class="ewForm" action="<?php echo ew_CurrentPage() ?>">
<table class="ewPager"><tr><td>
<?php if (!isset($input_proyek_list->Pager)) $input_proyek_list->Pager = new cPrevNextPager($input_proyek_list->StartRec, $input_proyek_list->DisplayRecs, $input_proyek_list->TotalRecs) ?>
<?php if ($input_proyek_list->Pager->RecordCount > 0) { ?>
	<table cellspacing="0" class="ewStdTable"><tbody><tr><td><span class="phpmaker"><?php echo $Language->Phrase("Page") ?>&nbsp;</span></td>
<!--first page button-->
	<?php if ($input_proyek_list->Pager->FirstButton->Enabled) { ?>
	<td><a href="<?php echo $input_proyek_list->PageUrl() ?>start=<?php echo $input_proyek_list->Pager->FirstButton->Start ?>"><img src="phpimages/first.gif" alt="<?php echo $Language->Phrase("PagerFirst") ?>" width="16" height="16" style="border: 0;"></a></td>
	<?php } else { ?>
	<td><img src="phpimages/firstdisab.gif" alt="<?php echo $Language->Phrase("PagerFirst") ?>" width="16" height="16" style="border: 0;"></td>
	<?php } ?>
<!--previous page button-->
	<?php if ($input_proyek_list->Pager->PrevButton->Enabled) { ?>
	<td><a href="<?php echo $input_proyek_list->PageUrl() ?>start=<?php echo $input_proyek_list->Pager->PrevButton->Start ?>"><img src="phpimages/prev.gif" alt="<?php echo $Language->Phrase("PagerPrevious") ?>" width="16" height="16" style="border: 0;"></a></td>
	<?php } else { ?>
	<td><img src="phpimages/prevdisab.gif" alt="<?php echo $Language->Phrase("PagerPrevious") ?>" width="16" height="16" style="border: 0;"></td>
	<?php } ?>
<!--current page number-->
	<td><input type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" id="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $input_proyek_list->Pager->CurrentPage ?>" size="4"></td>
<!--next page button-->
	<?php if ($input_proyek_list->Pager->NextButton->Enabled) { ?>
	<td><a href="<?php echo $input_proyek_list->PageUrl() ?>start=<?php echo $input_proyek_list->Pager->NextButton->Start ?>"><img src="phpimages/next.gif" alt="<?php echo $Language->Phrase("PagerNext") ?>" width="16" height="16" style="border: 0;"></a></td>	
	<?php } else { ?>
	<td><img src="phpimages/nextdisab.gif" alt="<?php echo $Language->Phrase("PagerNext") ?>" width="16" height="16" style="border: 0;"></td>
	<?php } ?>
<!--last page button-->
	<?php if ($input_proyek_list->Pager->LastButton->Enabled) { ?>
	<td><a href="<?php echo $input_proyek_list->PageUrl() ?>start=<?php echo $input_proyek_list->Pager->LastButton->Start ?>"><img src="phpimages/last.gif" alt="<?php echo $Language->Phrase("PagerLast") ?>" width="16" height="16" style="border: 0;"></a></td>	
	<?php } else { ?>
	<td><img src="phpimages/lastdisab.gif" alt="<?php echo $Language->Phrase("PagerLast") ?>" width="16" height="16" style="border: 0;"></td>
	<?php } ?>
	<td><span class="phpmaker">&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $input_proyek_list->Pager->PageCount ?></span></td>
	</tr></tbody></table>
	</td>	
	<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
	<td>
	<span class="phpmaker"><?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $input_proyek_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $input_proyek_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $input_proyek_list->Pager->RecordCount ?></span>
<?php } else { ?>
	<?php if ($input_proyek_list->SearchWhere == "0=101") { ?>
	<span class="phpmaker"><?php echo $Language->Phrase("EnterSearchCriteria") ?></span>
	<?php } else { ?>
	<span class="phpmaker"><?php echo $Language->Phrase("NoRecord") ?></span>
	<?php } ?>
<?php } ?>
	</td>
</tr></table>
</form>
<?php } ?>
<span class="phpmaker">
<?php if ($input_proyek_list->AddUrl <> "") { ?>
<a class="ewGridLink" href="<?php echo $input_proyek_list->AddUrl ?>"><?php echo $Language->Phrase("AddLink") ?></a>&nbsp;&nbsp;
<?php } ?>
</span>
</div>
<?php } ?>
</td></tr></table>
<?php if ($input_proyek->Export == "") { ?>
<script type="text/javascript">
finput_proyeklistsrch.Init();
finput_proyeklist.Init();
</script>
<?php } ?>
<?php
$input_proyek_list->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<?php if ($input_proyek->Export == "") { ?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php } ?>
<?php include_once "footer.php" ?>
<?php
$input_proyek_list->Page_Terminate();
?>
