<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg9.php" ?>
<?php include_once "ewmysql9.php" ?>
<?php include_once "phpfn9.php" ?>
<?php include_once "catat_notainfo.php" ?>
<?php include_once "userfn9.php" ?>
<?php

//
// Page class
//

$catat_nota_list = NULL; // Initialize page object first

class ccatat_nota_list extends ccatat_nota {

	// Page ID
	var $PageID = 'list';

	// Project ID
	var $ProjectID = "{67264FB2-6364-478B-87DD-B3E0D7A29425}";

	// Table name
	var $TableName = 'catat_nota';

	// Page object name
	var $PageObjName = 'catat_nota_list';

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

		// Table object (catat_nota)
		if (!isset($GLOBALS["catat_nota"])) {
			$GLOBALS["catat_nota"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["catat_nota"];
		}

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html";
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml";
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";
		$this->AddUrl = "catat_notaadd.php";
		$this->InlineAddUrl = $this->PageUrl() . "a=add";
		$this->GridAddUrl = $this->PageUrl() . "a=gridadd";
		$this->GridEditUrl = $this->PageUrl() . "a=gridedit";
		$this->MultiDeleteUrl = "catat_notadelete.php";
		$this->MultiUpdateUrl = "catat_notaupdate.php";

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'list', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'catat_nota', TRUE);

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

		// Get grid add count
		$gridaddcnt = @$_GET[EW_TABLE_GRID_ADD_ROW_COUNT];
		if (is_numeric($gridaddcnt) && $gridaddcnt > 0)
			$this->GridAddRowCount = $gridaddcnt;

		// Set up list options
		$this->SetupListOptions();
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
		if (count($arrKeyFlds) >= 0) {
		}
		return TRUE;
	}

	// Return basic search SQL
	function BasicSearchSQL($Keyword) {
		$sKeyword = ew_AdjustSql($Keyword);
		$sWhere = "";
		$this->BuildBasicSearchSQL($sWhere, $this->nomor_nota, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->nama_toko, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->pesan, $Keyword);
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
			$this->UpdateSort($this->id_dana); // id_dana
			$this->UpdateSort($this->nomor_nota); // nomor_nota
			$this->UpdateSort($this->nama_toko); // nama_toko
			$this->UpdateSort($this->jumlah_pembelian); // jumlah_pembelian
			$this->UpdateSort($this->tanggal_nota); // tanggal_nota
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
				$this->id_dana->setSort("");
				$this->nomor_nota->setSort("");
				$this->nama_toko->setSort("");
				$this->jumlah_pembelian->setSort("");
				$this->tanggal_nota->setSort("");
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
		$this->id_dana->setDbValue($rs->fields('id_dana'));
		$this->nomor_nota->setDbValue($rs->fields('nomor_nota'));
		$this->nama_toko->setDbValue($rs->fields('nama_toko'));
		$this->jumlah_pembelian->setDbValue($rs->fields('jumlah_pembelian'));
		$this->pesan->setDbValue($rs->fields('pesan'));
		$this->tanggal_nota->setDbValue($rs->fields('tanggal_nota'));
	}

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;

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
		// id_dana
		// nomor_nota
		// nama_toko
		// jumlah_pembelian
		// pesan
		// tanggal_nota
		// Accumulate aggregate value

		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT && $this->RowType <> EW_ROWTYPE_AGGREGATE) {
			if (is_numeric($this->jumlah_pembelian->CurrentValue))
				$this->jumlah_pembelian->Total += $this->jumlah_pembelian->CurrentValue; // Accumulate total
		}
		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// id_dana
			if (strval($this->id_dana->CurrentValue) <> "") {
				$sFilterWrk = "`id_dana`" . ew_SearchString("=", $this->id_dana->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `id_dana`, `periode_pembiayaan` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lh_dana`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->id_dana->ViewValue = ew_FormatDateTime($rswrk->fields('DispFld'), 7);
					$rswrk->Close();
				} else {
					$this->id_dana->ViewValue = $this->id_dana->CurrentValue;
				}
			} else {
				$this->id_dana->ViewValue = NULL;
			}
			$this->id_dana->ViewValue = ew_FormatDateTime($this->id_dana->ViewValue, 7);
			$this->id_dana->ViewCustomAttributes = "";

			// nomor_nota
			$this->nomor_nota->ViewValue = $this->nomor_nota->CurrentValue;
			$this->nomor_nota->ViewCustomAttributes = "";

			// nama_toko
			$this->nama_toko->ViewValue = $this->nama_toko->CurrentValue;
			$this->nama_toko->ViewCustomAttributes = "";

			// jumlah_pembelian
			$this->jumlah_pembelian->ViewValue = $this->jumlah_pembelian->CurrentValue;
			$this->jumlah_pembelian->ViewValue = ew_FormatNumber($this->jumlah_pembelian->ViewValue, 2, -1, -2, -1);
			$this->jumlah_pembelian->ViewCustomAttributes = "";

			// tanggal_nota
			$this->tanggal_nota->ViewValue = $this->tanggal_nota->CurrentValue;
			$this->tanggal_nota->ViewValue = ew_FormatDateTime($this->tanggal_nota->ViewValue, 7);
			$this->tanggal_nota->ViewCustomAttributes = "";

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

			// jumlah_pembelian
			$this->jumlah_pembelian->LinkCustomAttributes = "";
			$this->jumlah_pembelian->HrefValue = "";
			$this->jumlah_pembelian->TooltipValue = "";

			// tanggal_nota
			$this->tanggal_nota->LinkCustomAttributes = "";
			$this->tanggal_nota->HrefValue = "";
			$this->tanggal_nota->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_AGGREGATEINIT) { // Initialize aggregate row
			$this->jumlah_pembelian->Total = 0; // Initialize total
		} elseif ($this->RowType == EW_ROWTYPE_AGGREGATE) { // Aggregate row
			$this->jumlah_pembelian->CurrentValue = $this->jumlah_pembelian->Total;
			$this->jumlah_pembelian->ViewValue = $this->jumlah_pembelian->CurrentValue;
			$this->jumlah_pembelian->ViewValue = ew_FormatNumber($this->jumlah_pembelian->ViewValue, 2, -1, -2, -1);
			$this->jumlah_pembelian->ViewCustomAttributes = "";
			$this->jumlah_pembelian->HrefValue = ""; // Clear href value
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
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
if (!isset($catat_nota_list)) $catat_nota_list = new ccatat_nota_list();

// Page init
$catat_nota_list->Page_Init();

// Page main
$catat_nota_list->Page_Main();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var catat_nota_list = new ew_Page("catat_nota_list");
catat_nota_list.PageID = "list"; // Page ID
var EW_PAGE_ID = catat_nota_list.PageID; // For backward compatibility

// Form object
var fcatat_notalist = new ew_Form("fcatat_notalist");

// Form_CustomValidate event
fcatat_notalist.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fcatat_notalist.ValidateRequired = true;
<?php } else { ?>
fcatat_notalist.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fcatat_notalist.Lists["x_id_dana"] = {"LinkField":"x_id_dana","Ajax":null,"AutoFill":false,"DisplayFields":["x_periode_pembiayaan","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
var fcatat_notalistsrch = new ew_Form("fcatat_notalistsrch");
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php
	$bSelectLimit = EW_SELECT_LIMIT;
	if ($bSelectLimit) {
		$catat_nota_list->TotalRecs = $catat_nota->SelectRecordCount();
	} else {
		if ($catat_nota_list->Recordset = $catat_nota_list->LoadRecordset())
			$catat_nota_list->TotalRecs = $catat_nota_list->Recordset->RecordCount();
	}
	$catat_nota_list->StartRec = 1;
	if ($catat_nota_list->DisplayRecs <= 0 || ($catat_nota->Export <> "" && $catat_nota->ExportAll)) // Display all records
		$catat_nota_list->DisplayRecs = $catat_nota_list->TotalRecs;
	if (!($catat_nota->Export <> "" && $catat_nota->ExportAll))
		$catat_nota_list->SetUpStartRec(); // Set up start record position
	if ($bSelectLimit)
		$catat_nota_list->Recordset = $catat_nota_list->LoadRecordset($catat_nota_list->StartRec-1, $catat_nota_list->DisplayRecs);
?>
<p style="white-space: nowrap;"><span id="ewPageCaption" class="ewTitle ewTableTitle"><?php echo $Language->Phrase("TblTypeVIEW") ?><?php echo $catat_nota->TableCaption() ?>&nbsp;&nbsp;</span>
<?php $catat_nota_list->ExportOptions->Render("body"); ?>
</p>
<?php if ($catat_nota->Export == "" && $catat_nota->CurrentAction == "") { ?>
<form name="fcatat_notalistsrch" id="fcatat_notalistsrch" class="ewForm" action="<?php echo ew_CurrentPage() ?>">
<a href="javascript:fcatat_notalistsrch.ToggleSearchPanel();" style="text-decoration: none;"><img id="fcatat_notalistsrch_SearchImage" src="phpimages/collapse.gif" alt="" width="9" height="9" style="border: 0;"></a><span class="phpmaker">&nbsp;<?php echo $Language->Phrase("Search") ?></span><br>
<div id="fcatat_notalistsrch_SearchPanel">
<input type="hidden" name="cmd" value="search">
<input type="hidden" name="t" value="catat_nota">
<div class="ewBasicSearch">
<div id="xsr_1" class="ewRow">
	<input type="text" name="<?php echo EW_TABLE_BASIC_SEARCH ?>" id="<?php echo EW_TABLE_BASIC_SEARCH ?>" size="20" value="<?php echo ew_HtmlEncode($catat_nota_list->BasicSearch->getKeyword()) ?>">
	<input type="submit" name="btnsubmit" id="btnsubmit" value="<?php echo ew_BtnCaption($Language->Phrase("QuickSearchBtn")) ?>">&nbsp;
	<a href="<?php echo $catat_nota_list->PageUrl() ?>cmd=reset" id="a_ShowAll" class="ewLink"><?php echo $Language->Phrase("ShowAll") ?></a>&nbsp;
</div>
<div id="xsr_2" class="ewRow">
	<label><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="="<?php if ($catat_nota_list->BasicSearch->getType() == "=") { ?> checked="checked"<?php } ?>><?php echo $Language->Phrase("ExactPhrase") ?></label>&nbsp;&nbsp;<label><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="AND"<?php if ($catat_nota_list->BasicSearch->getType() == "AND") { ?> checked="checked"<?php } ?>><?php echo $Language->Phrase("AllWord") ?></label>&nbsp;&nbsp;<label><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="OR"<?php if ($catat_nota_list->BasicSearch->getType() == "OR") { ?> checked="checked"<?php } ?>><?php echo $Language->Phrase("AnyWord") ?></label>
</div>
</div>
</div>
</form>
<?php } ?>
<?php $catat_nota_list->ShowPageHeader(); ?>
<?php
$catat_nota_list->ShowMessage();
?>
<br>
<table cellspacing="0" class="ewGrid"><tr><td class="ewGridContent">
<form name="fcatat_notalist" id="fcatat_notalist" class="ewForm" action="" method="post">
<input type="hidden" name="t" value="catat_nota">
<div id="gmp_catat_nota" class="ewGridMiddlePanel">
<?php if ($catat_nota_list->TotalRecs > 0) { ?>
<table id="tbl_catat_notalist" class="ewTable ewTableSeparate">
<?php echo $catat_nota->TableCustomInnerHtml ?>
<thead><!-- Table header -->
	<tr class="ewTableHeader">
<?php

// Render list options
$catat_nota_list->RenderListOptions();

// Render list options (header, left)
$catat_nota_list->ListOptions->Render("header", "left");
?>
<?php if ($catat_nota->id_dana->Visible) { // id_dana ?>
	<?php if ($catat_nota->SortUrl($catat_nota->id_dana) == "") { ?>
		<td><span id="elh_catat_nota_id_dana" class="catat_nota_id_dana"><table class="ewTableHeaderBtn"><thead><tr><td><?php echo $catat_nota->id_dana->FldCaption() ?></td></tr></thead></table></span></td>
	<?php } else { ?>
		<td><div onmousedown="ew_Sort(event,'<?php echo $catat_nota->SortUrl($catat_nota->id_dana) ?>',1);"><span id="elh_catat_nota_id_dana" class="catat_nota_id_dana">
			<table class="ewTableHeaderBtn"><thead><tr><td class="ewTableHeaderCaption"><?php echo $catat_nota->id_dana->FldCaption() ?></td><td class="ewTableHeaderSort"><?php if ($catat_nota->id_dana->getSort() == "ASC") { ?><img src="phpimages/sortup.gif" width="10" height="9" alt="" style="border: 0;"><?php } elseif ($catat_nota->id_dana->getSort() == "DESC") { ?><img src="phpimages/sortdown.gif" width="10" height="9" alt="" style="border: 0;"><?php } ?></td></tr></thead></table>
		</span></div></td>		
	<?php } ?>
<?php } ?>		
<?php if ($catat_nota->nomor_nota->Visible) { // nomor_nota ?>
	<?php if ($catat_nota->SortUrl($catat_nota->nomor_nota) == "") { ?>
		<td><span id="elh_catat_nota_nomor_nota" class="catat_nota_nomor_nota"><table class="ewTableHeaderBtn"><thead><tr><td><?php echo $catat_nota->nomor_nota->FldCaption() ?></td></tr></thead></table></span></td>
	<?php } else { ?>
		<td><div onmousedown="ew_Sort(event,'<?php echo $catat_nota->SortUrl($catat_nota->nomor_nota) ?>',1);"><span id="elh_catat_nota_nomor_nota" class="catat_nota_nomor_nota">
			<table class="ewTableHeaderBtn"><thead><tr><td class="ewTableHeaderCaption"><?php echo $catat_nota->nomor_nota->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></td><td class="ewTableHeaderSort"><?php if ($catat_nota->nomor_nota->getSort() == "ASC") { ?><img src="phpimages/sortup.gif" width="10" height="9" alt="" style="border: 0;"><?php } elseif ($catat_nota->nomor_nota->getSort() == "DESC") { ?><img src="phpimages/sortdown.gif" width="10" height="9" alt="" style="border: 0;"><?php } ?></td></tr></thead></table>
		</span></div></td>		
	<?php } ?>
<?php } ?>		
<?php if ($catat_nota->nama_toko->Visible) { // nama_toko ?>
	<?php if ($catat_nota->SortUrl($catat_nota->nama_toko) == "") { ?>
		<td><span id="elh_catat_nota_nama_toko" class="catat_nota_nama_toko"><table class="ewTableHeaderBtn"><thead><tr><td><?php echo $catat_nota->nama_toko->FldCaption() ?></td></tr></thead></table></span></td>
	<?php } else { ?>
		<td><div onmousedown="ew_Sort(event,'<?php echo $catat_nota->SortUrl($catat_nota->nama_toko) ?>',1);"><span id="elh_catat_nota_nama_toko" class="catat_nota_nama_toko">
			<table class="ewTableHeaderBtn"><thead><tr><td class="ewTableHeaderCaption"><?php echo $catat_nota->nama_toko->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></td><td class="ewTableHeaderSort"><?php if ($catat_nota->nama_toko->getSort() == "ASC") { ?><img src="phpimages/sortup.gif" width="10" height="9" alt="" style="border: 0;"><?php } elseif ($catat_nota->nama_toko->getSort() == "DESC") { ?><img src="phpimages/sortdown.gif" width="10" height="9" alt="" style="border: 0;"><?php } ?></td></tr></thead></table>
		</span></div></td>		
	<?php } ?>
<?php } ?>		
<?php if ($catat_nota->jumlah_pembelian->Visible) { // jumlah_pembelian ?>
	<?php if ($catat_nota->SortUrl($catat_nota->jumlah_pembelian) == "") { ?>
		<td><span id="elh_catat_nota_jumlah_pembelian" class="catat_nota_jumlah_pembelian"><table class="ewTableHeaderBtn"><thead><tr><td><?php echo $catat_nota->jumlah_pembelian->FldCaption() ?></td></tr></thead></table></span></td>
	<?php } else { ?>
		<td><div onmousedown="ew_Sort(event,'<?php echo $catat_nota->SortUrl($catat_nota->jumlah_pembelian) ?>',1);"><span id="elh_catat_nota_jumlah_pembelian" class="catat_nota_jumlah_pembelian">
			<table class="ewTableHeaderBtn"><thead><tr><td class="ewTableHeaderCaption"><?php echo $catat_nota->jumlah_pembelian->FldCaption() ?></td><td class="ewTableHeaderSort"><?php if ($catat_nota->jumlah_pembelian->getSort() == "ASC") { ?><img src="phpimages/sortup.gif" width="10" height="9" alt="" style="border: 0;"><?php } elseif ($catat_nota->jumlah_pembelian->getSort() == "DESC") { ?><img src="phpimages/sortdown.gif" width="10" height="9" alt="" style="border: 0;"><?php } ?></td></tr></thead></table>
		</span></div></td>		
	<?php } ?>
<?php } ?>		
<?php if ($catat_nota->tanggal_nota->Visible) { // tanggal_nota ?>
	<?php if ($catat_nota->SortUrl($catat_nota->tanggal_nota) == "") { ?>
		<td><span id="elh_catat_nota_tanggal_nota" class="catat_nota_tanggal_nota"><table class="ewTableHeaderBtn"><thead><tr><td><?php echo $catat_nota->tanggal_nota->FldCaption() ?></td></tr></thead></table></span></td>
	<?php } else { ?>
		<td><div onmousedown="ew_Sort(event,'<?php echo $catat_nota->SortUrl($catat_nota->tanggal_nota) ?>',1);"><span id="elh_catat_nota_tanggal_nota" class="catat_nota_tanggal_nota">
			<table class="ewTableHeaderBtn"><thead><tr><td class="ewTableHeaderCaption"><?php echo $catat_nota->tanggal_nota->FldCaption() ?></td><td class="ewTableHeaderSort"><?php if ($catat_nota->tanggal_nota->getSort() == "ASC") { ?><img src="phpimages/sortup.gif" width="10" height="9" alt="" style="border: 0;"><?php } elseif ($catat_nota->tanggal_nota->getSort() == "DESC") { ?><img src="phpimages/sortdown.gif" width="10" height="9" alt="" style="border: 0;"><?php } ?></td></tr></thead></table>
		</span></div></td>		
	<?php } ?>
<?php } ?>		
<?php

// Render list options (header, right)
$catat_nota_list->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
if ($catat_nota->ExportAll && $catat_nota->Export <> "") {
	$catat_nota_list->StopRec = $catat_nota_list->TotalRecs;
} else {

	// Set the last record to display
	if ($catat_nota_list->TotalRecs > $catat_nota_list->StartRec + $catat_nota_list->DisplayRecs - 1)
		$catat_nota_list->StopRec = $catat_nota_list->StartRec + $catat_nota_list->DisplayRecs - 1;
	else
		$catat_nota_list->StopRec = $catat_nota_list->TotalRecs;
}
$catat_nota_list->RecCnt = $catat_nota_list->StartRec - 1;
if ($catat_nota_list->Recordset && !$catat_nota_list->Recordset->EOF) {
	$catat_nota_list->Recordset->MoveFirst();
	if (!$bSelectLimit && $catat_nota_list->StartRec > 1)
		$catat_nota_list->Recordset->Move($catat_nota_list->StartRec - 1);
} elseif (!$catat_nota->AllowAddDeleteRow && $catat_nota_list->StopRec == 0) {
	$catat_nota_list->StopRec = $catat_nota->GridAddRowCount;
}

// Initialize aggregate
$catat_nota->RowType = EW_ROWTYPE_AGGREGATEINIT;
$catat_nota->ResetAttrs();
$catat_nota_list->RenderRow();
while ($catat_nota_list->RecCnt < $catat_nota_list->StopRec) {
	$catat_nota_list->RecCnt++;
	if (intval($catat_nota_list->RecCnt) >= intval($catat_nota_list->StartRec)) {
		$catat_nota_list->RowCnt++;

		// Set up key count
		$catat_nota_list->KeyCount = $catat_nota_list->RowIndex;

		// Init row class and style
		$catat_nota->ResetAttrs();
		$catat_nota->CssClass = "";
		if ($catat_nota->CurrentAction == "gridadd") {
		} else {
			$catat_nota_list->LoadRowValues($catat_nota_list->Recordset); // Load row values
		}
		$catat_nota->RowType = EW_ROWTYPE_VIEW; // Render view

		// Set up row id / data-rowindex
		$catat_nota->RowAttrs = array_merge($catat_nota->RowAttrs, array('data-rowindex'=>$catat_nota_list->RowCnt, 'id'=>'r' . $catat_nota_list->RowCnt . '_catat_nota', 'data-rowtype'=>$catat_nota->RowType));

		// Render row
		$catat_nota_list->RenderRow();

		// Render list options
		$catat_nota_list->RenderListOptions();
?>
	<tr<?php echo $catat_nota->RowAttributes() ?>>
<?php

// Render list options (body, left)
$catat_nota_list->ListOptions->Render("body", "left", $catat_nota_list->RowCnt);
?>
	<?php if ($catat_nota->id_dana->Visible) { // id_dana ?>
		<td<?php echo $catat_nota->id_dana->CellAttributes() ?>><span id="el<?php echo $catat_nota_list->RowCnt ?>_catat_nota_id_dana" class="catat_nota_id_dana">
<span<?php echo $catat_nota->id_dana->ViewAttributes() ?>>
<?php echo $catat_nota->id_dana->ListViewValue() ?></span>
</span><a id="<?php echo $catat_nota_list->PageObjName . "_row_" . $catat_nota_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($catat_nota->nomor_nota->Visible) { // nomor_nota ?>
		<td<?php echo $catat_nota->nomor_nota->CellAttributes() ?>><span id="el<?php echo $catat_nota_list->RowCnt ?>_catat_nota_nomor_nota" class="catat_nota_nomor_nota">
<span<?php echo $catat_nota->nomor_nota->ViewAttributes() ?>>
<?php echo $catat_nota->nomor_nota->ListViewValue() ?></span>
</span><a id="<?php echo $catat_nota_list->PageObjName . "_row_" . $catat_nota_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($catat_nota->nama_toko->Visible) { // nama_toko ?>
		<td<?php echo $catat_nota->nama_toko->CellAttributes() ?>><span id="el<?php echo $catat_nota_list->RowCnt ?>_catat_nota_nama_toko" class="catat_nota_nama_toko">
<span<?php echo $catat_nota->nama_toko->ViewAttributes() ?>>
<?php echo $catat_nota->nama_toko->ListViewValue() ?></span>
</span><a id="<?php echo $catat_nota_list->PageObjName . "_row_" . $catat_nota_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($catat_nota->jumlah_pembelian->Visible) { // jumlah_pembelian ?>
		<td<?php echo $catat_nota->jumlah_pembelian->CellAttributes() ?>><span id="el<?php echo $catat_nota_list->RowCnt ?>_catat_nota_jumlah_pembelian" class="catat_nota_jumlah_pembelian">
<span<?php echo $catat_nota->jumlah_pembelian->ViewAttributes() ?>>
<?php echo $catat_nota->jumlah_pembelian->ListViewValue() ?></span>
</span><a id="<?php echo $catat_nota_list->PageObjName . "_row_" . $catat_nota_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($catat_nota->tanggal_nota->Visible) { // tanggal_nota ?>
		<td<?php echo $catat_nota->tanggal_nota->CellAttributes() ?>><span id="el<?php echo $catat_nota_list->RowCnt ?>_catat_nota_tanggal_nota" class="catat_nota_tanggal_nota">
<span<?php echo $catat_nota->tanggal_nota->ViewAttributes() ?>>
<?php echo $catat_nota->tanggal_nota->ListViewValue() ?></span>
</span><a id="<?php echo $catat_nota_list->PageObjName . "_row_" . $catat_nota_list->RowCnt ?>"></a></td>
	<?php } ?>
<?php

// Render list options (body, right)
$catat_nota_list->ListOptions->Render("body", "right", $catat_nota_list->RowCnt);
?>
	</tr>
<?php
	}
	if ($catat_nota->CurrentAction <> "gridadd")
		$catat_nota_list->Recordset->MoveNext();
}
?>
</tbody>
<?php

// Render aggregate row
$catat_nota->RowType = EW_ROWTYPE_AGGREGATE;
$catat_nota->ResetAttrs();
$catat_nota_list->RenderRow();
?>
<?php if ($catat_nota_list->TotalRecs > 0 && ($catat_nota->CurrentAction <> "gridadd" && $catat_nota->CurrentAction <> "gridedit")) { ?>
<tfoot><!-- Table footer -->
	<tr class="ewTableFooter">
<?php

// Render list options
$catat_nota_list->RenderListOptions();

// Render list options (footer, left)
$catat_nota_list->ListOptions->Render("footer", "left");
?>
	<?php if ($catat_nota->id_dana->Visible) { // id_dana ?>
		<td><span id="elf_catat_nota_id_dana" class="catat_nota_id_dana">
		&nbsp;
		</span></td>
	<?php } ?>
	<?php if ($catat_nota->nomor_nota->Visible) { // nomor_nota ?>
		<td><span id="elf_catat_nota_nomor_nota" class="catat_nota_nomor_nota">
		&nbsp;
		</span></td>
	<?php } ?>
	<?php if ($catat_nota->nama_toko->Visible) { // nama_toko ?>
		<td><span id="elf_catat_nota_nama_toko" class="catat_nota_nama_toko">
		&nbsp;
		</span></td>
	<?php } ?>
	<?php if ($catat_nota->jumlah_pembelian->Visible) { // jumlah_pembelian ?>
		<td><span id="elf_catat_nota_jumlah_pembelian" class="catat_nota_jumlah_pembelian">
<?php echo $Language->Phrase("TOTAL") ?>: 
<?php echo $catat_nota->jumlah_pembelian->ViewValue ?>
		</span></td>
	<?php } ?>
	<?php if ($catat_nota->tanggal_nota->Visible) { // tanggal_nota ?>
		<td><span id="elf_catat_nota_tanggal_nota" class="catat_nota_tanggal_nota">
		&nbsp;
		</span></td>
	<?php } ?>
<?php

// Render list options (footer, right)
$catat_nota_list->ListOptions->Render("footer", "right");
?>
	</tr>
</tfoot>	
<?php } ?>
</table>
<?php } ?>
<?php if ($catat_nota->CurrentAction == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
</div>
</form>
<?php

// Close recordset
if ($catat_nota_list->Recordset)
	$catat_nota_list->Recordset->Close();
?>
<div class="ewGridLowerPanel">
<?php if ($catat_nota->CurrentAction <> "gridadd" && $catat_nota->CurrentAction <> "gridedit") { ?>
<form name="ewpagerform" id="ewpagerform" class="ewForm" action="<?php echo ew_CurrentPage() ?>">
<table class="ewPager"><tr><td>
<?php if (!isset($catat_nota_list->Pager)) $catat_nota_list->Pager = new cPrevNextPager($catat_nota_list->StartRec, $catat_nota_list->DisplayRecs, $catat_nota_list->TotalRecs) ?>
<?php if ($catat_nota_list->Pager->RecordCount > 0) { ?>
	<table cellspacing="0" class="ewStdTable"><tbody><tr><td><span class="phpmaker"><?php echo $Language->Phrase("Page") ?>&nbsp;</span></td>
<!--first page button-->
	<?php if ($catat_nota_list->Pager->FirstButton->Enabled) { ?>
	<td><a href="<?php echo $catat_nota_list->PageUrl() ?>start=<?php echo $catat_nota_list->Pager->FirstButton->Start ?>"><img src="phpimages/first.gif" alt="<?php echo $Language->Phrase("PagerFirst") ?>" width="16" height="16" style="border: 0;"></a></td>
	<?php } else { ?>
	<td><img src="phpimages/firstdisab.gif" alt="<?php echo $Language->Phrase("PagerFirst") ?>" width="16" height="16" style="border: 0;"></td>
	<?php } ?>
<!--previous page button-->
	<?php if ($catat_nota_list->Pager->PrevButton->Enabled) { ?>
	<td><a href="<?php echo $catat_nota_list->PageUrl() ?>start=<?php echo $catat_nota_list->Pager->PrevButton->Start ?>"><img src="phpimages/prev.gif" alt="<?php echo $Language->Phrase("PagerPrevious") ?>" width="16" height="16" style="border: 0;"></a></td>
	<?php } else { ?>
	<td><img src="phpimages/prevdisab.gif" alt="<?php echo $Language->Phrase("PagerPrevious") ?>" width="16" height="16" style="border: 0;"></td>
	<?php } ?>
<!--current page number-->
	<td><input type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" id="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $catat_nota_list->Pager->CurrentPage ?>" size="4"></td>
<!--next page button-->
	<?php if ($catat_nota_list->Pager->NextButton->Enabled) { ?>
	<td><a href="<?php echo $catat_nota_list->PageUrl() ?>start=<?php echo $catat_nota_list->Pager->NextButton->Start ?>"><img src="phpimages/next.gif" alt="<?php echo $Language->Phrase("PagerNext") ?>" width="16" height="16" style="border: 0;"></a></td>	
	<?php } else { ?>
	<td><img src="phpimages/nextdisab.gif" alt="<?php echo $Language->Phrase("PagerNext") ?>" width="16" height="16" style="border: 0;"></td>
	<?php } ?>
<!--last page button-->
	<?php if ($catat_nota_list->Pager->LastButton->Enabled) { ?>
	<td><a href="<?php echo $catat_nota_list->PageUrl() ?>start=<?php echo $catat_nota_list->Pager->LastButton->Start ?>"><img src="phpimages/last.gif" alt="<?php echo $Language->Phrase("PagerLast") ?>" width="16" height="16" style="border: 0;"></a></td>	
	<?php } else { ?>
	<td><img src="phpimages/lastdisab.gif" alt="<?php echo $Language->Phrase("PagerLast") ?>" width="16" height="16" style="border: 0;"></td>
	<?php } ?>
	<td><span class="phpmaker">&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $catat_nota_list->Pager->PageCount ?></span></td>
	</tr></tbody></table>
	</td>	
	<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
	<td>
	<span class="phpmaker"><?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $catat_nota_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $catat_nota_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $catat_nota_list->Pager->RecordCount ?></span>
<?php } else { ?>
	<?php if ($catat_nota_list->SearchWhere == "0=101") { ?>
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
</span>
</div>
</td></tr></table>
<script type="text/javascript">
fcatat_notalistsrch.Init();
fcatat_notalist.Init();
</script>
<?php
$catat_nota_list->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$catat_nota_list->Page_Terminate();
?>
