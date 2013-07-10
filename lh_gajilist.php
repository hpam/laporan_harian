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

$lh_gaji_list = NULL; // Initialize page object first

class clh_gaji_list extends clh_gaji {

	// Page ID
	var $PageID = 'list';

	// Project ID
	var $ProjectID = "{67264FB2-6364-478B-87DD-B3E0D7A29425}";

	// Table name
	var $TableName = 'lh_gaji';

	// Page object name
	var $PageObjName = 'lh_gaji_list';

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

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html";
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml";
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";
		$this->AddUrl = "lh_gajiadd.php";
		$this->InlineAddUrl = $this->PageUrl() . "a=add";
		$this->GridAddUrl = $this->PageUrl() . "a=gridadd";
		$this->GridEditUrl = $this->PageUrl() . "a=gridedit";
		$this->MultiDeleteUrl = "lh_gajidelete.php";
		$this->MultiUpdateUrl = "lh_gajiupdate.php";

		// Table object (lh_user)
		if (!isset($GLOBALS['lh_user'])) $GLOBALS['lh_user'] = new clh_user();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'list', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'lh_gaji', TRUE);

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

			// Get and validate search values for advanced search
			$this->LoadSearchValues(); // Get search values
			if (!$this->ValidateSearch())
				$this->setFailureMessage($gsSearchError);

			// Restore search parms from Session if not searching / reset
			if ($this->Command <> "search" && $this->Command <> "reset" && $this->Command <> "resetall")
				$this->RestoreSearchParms();

			// Call Recordset SearchValidated event
			$this->Recordset_SearchValidated();

			// Set up sorting order
			$this->SetUpSortOrder();

			// Get search criteria for advanced search
			if ($gsSearchError == "")
				$sSrchAdvanced = $this->AdvancedSearchWhere();
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

			// Load advanced search from default
			if ($this->LoadAdvancedSearchDefault()) {
				$sSrchAdvanced = $this->AdvancedSearchWhere();
			}
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
			$this->id_gaji->setFormValue($arrKeyFlds[0]);
			if (!is_numeric($this->id_gaji->FormValue))
				return FALSE;
		}
		return TRUE;
	}

	// Advanced search WHERE clause based on QueryString
	function AdvancedSearchWhere() {
		global $Security;
		$sWhere = "";
		if (!$Security->CanSearch()) return "";
		$this->BuildSearchSql($sWhere, $this->id_gaji, FALSE); // id_gaji
		$this->BuildSearchSql($sWhere, $this->id_user, FALSE); // id_user
		$this->BuildSearchSql($sWhere, $this->status, FALSE); // status
		$this->BuildSearchSql($sWhere, $this->tanggal, FALSE); // tanggal
		$this->BuildSearchSql($sWhere, $this->gaji_pokok, FALSE); // gaji_pokok
		$this->BuildSearchSql($sWhere, $this->lembur, FALSE); // lembur
		$this->BuildSearchSql($sWhere, $this->tunjangan_proyek, FALSE); // tunjangan_proyek

		// Set up search parm
		if ($sWhere <> "") {
			$this->Command = "search";
		}
		if ($this->Command == "search") {
			$this->id_gaji->AdvancedSearch->Save(); // id_gaji
			$this->id_user->AdvancedSearch->Save(); // id_user
			$this->status->AdvancedSearch->Save(); // status
			$this->tanggal->AdvancedSearch->Save(); // tanggal
			$this->gaji_pokok->AdvancedSearch->Save(); // gaji_pokok
			$this->lembur->AdvancedSearch->Save(); // lembur
			$this->tunjangan_proyek->AdvancedSearch->Save(); // tunjangan_proyek
		}
		return $sWhere;
	}

	// Build search SQL
	function BuildSearchSql(&$Where, &$Fld, $MultiValue) {
		$FldParm = substr($Fld->FldVar, 2);
		$FldVal = $Fld->AdvancedSearch->SearchValue; // @$_GET["x_$FldParm"]
		$FldOpr = $Fld->AdvancedSearch->SearchOperator; // @$_GET["z_$FldParm"]
		$FldCond = $Fld->AdvancedSearch->SearchCondition; // @$_GET["v_$FldParm"]
		$FldVal2 = $Fld->AdvancedSearch->SearchValue2; // @$_GET["y_$FldParm"]
		$FldOpr2 = $Fld->AdvancedSearch->SearchOperator2; // @$_GET["w_$FldParm"]
		$sWrk = "";

		//$FldVal = ew_StripSlashes($FldVal);
		if (is_array($FldVal)) $FldVal = implode(",", $FldVal);

		//$FldVal2 = ew_StripSlashes($FldVal2);
		if (is_array($FldVal2)) $FldVal2 = implode(",", $FldVal2);
		$FldOpr = strtoupper(trim($FldOpr));
		if ($FldOpr == "") $FldOpr = "=";
		$FldOpr2 = strtoupper(trim($FldOpr2));
		if ($FldOpr2 == "") $FldOpr2 = "=";
		if (EW_SEARCH_MULTI_VALUE_OPTION == 1 || $FldOpr <> "LIKE" ||
			($FldOpr2 <> "LIKE" && $FldVal2 <> ""))
			$MultiValue = FALSE;
		if ($MultiValue) {
			$sWrk1 = ($FldVal <> "") ? ew_GetMultiSearchSql($Fld, $FldOpr, $FldVal) : ""; // Field value 1
			$sWrk2 = ($FldVal2 <> "") ? ew_GetMultiSearchSql($Fld, $FldOpr2, $FldVal2) : ""; // Field value 2
			$sWrk = $sWrk1; // Build final SQL
			if ($sWrk2 <> "")
				$sWrk = ($sWrk <> "") ? "($sWrk) $FldCond ($sWrk2)" : $sWrk2;
		} else {
			$FldVal = $this->ConvertSearchValue($Fld, $FldVal);
			$FldVal2 = $this->ConvertSearchValue($Fld, $FldVal2);
			$sWrk = ew_GetSearchSql($Fld, $FldVal, $FldOpr, $FldCond, $FldVal2, $FldOpr2);
		}
		ew_AddFilter($Where, $sWrk);
	}

	// Convert search value
	function ConvertSearchValue(&$Fld, $FldVal) {
		if ($FldVal == EW_NULL_VALUE || $FldVal == EW_NOT_NULL_VALUE)
			return $FldVal;
		$Value = $FldVal;
		if ($Fld->FldDataType == EW_DATATYPE_BOOLEAN) {
			if ($FldVal <> "") $Value = ($FldVal == "1" || strtolower(strval($FldVal)) == "y" || strtolower(strval($FldVal)) == "t") ? $Fld->TrueValue : $Fld->FalseValue;
		} elseif ($Fld->FldDataType == EW_DATATYPE_DATE) {
			if ($FldVal <> "") $Value = ew_UnFormatDateTime($FldVal, $Fld->FldDateTimeFormat);
		}
		return $Value;
	}

	// Check if search parm exists
	function CheckSearchParms() {
		if ($this->id_gaji->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->id_user->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->status->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->tanggal->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->gaji_pokok->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->lembur->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->tunjangan_proyek->AdvancedSearch->IssetSession())
			return TRUE;
		return FALSE;
	}

	// Clear all search parameters
	function ResetSearchParms() {

		// Clear search WHERE clause
		$this->SearchWhere = "";
		$this->setSearchWhere($this->SearchWhere);

		// Clear advanced search parameters
		$this->ResetAdvancedSearchParms();
	}

	// Load advanced search default values
	function LoadAdvancedSearchDefault() {
		return FALSE;
	}

	// Clear all advanced search parameters
	function ResetAdvancedSearchParms() {
		$this->id_gaji->AdvancedSearch->UnsetSession();
		$this->id_user->AdvancedSearch->UnsetSession();
		$this->status->AdvancedSearch->UnsetSession();
		$this->tanggal->AdvancedSearch->UnsetSession();
		$this->gaji_pokok->AdvancedSearch->UnsetSession();
		$this->lembur->AdvancedSearch->UnsetSession();
		$this->tunjangan_proyek->AdvancedSearch->UnsetSession();
	}

	// Restore all search parameters
	function RestoreSearchParms() {

		// Restore advanced search values
		$this->id_gaji->AdvancedSearch->Load();
		$this->id_user->AdvancedSearch->Load();
		$this->status->AdvancedSearch->Load();
		$this->tanggal->AdvancedSearch->Load();
		$this->gaji_pokok->AdvancedSearch->Load();
		$this->lembur->AdvancedSearch->Load();
		$this->tunjangan_proyek->AdvancedSearch->Load();
	}

	// Set up sort parameters
	function SetUpSortOrder() {

		// Check for "order" parameter
		if (@$_GET["order"] <> "") {
			$this->CurrentOrder = ew_StripSlashes(@$_GET["order"]);
			$this->CurrentOrderType = @$_GET["ordertype"];
			$this->UpdateSort($this->id_gaji); // id_gaji
			$this->UpdateSort($this->id_user); // id_user
			$this->UpdateSort($this->status); // status
			$this->UpdateSort($this->tanggal); // tanggal
			$this->UpdateSort($this->gaji_pokok); // gaji_pokok
			$this->UpdateSort($this->lembur); // lembur
			$this->UpdateSort($this->tunjangan_proyek); // tunjangan_proyek
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
				$this->tanggal->setSort("DESC");
				$this->id_gaji->setSort("DESC");
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
				$this->id_gaji->setSort("");
				$this->id_user->setSort("");
				$this->status->setSort("");
				$this->tanggal->setSort("");
				$this->gaji_pokok->setSort("");
				$this->lembur->setSort("");
				$this->tunjangan_proyek->setSort("");
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

	//  Load search values for validation
	function LoadSearchValues() {
		global $objForm;

		// Load search values
		// id_gaji

		$this->id_gaji->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_id_gaji"]);
		if ($this->id_gaji->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->id_gaji->AdvancedSearch->SearchOperator = @$_GET["z_id_gaji"];

		// id_user
		$this->id_user->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_id_user"]);
		if ($this->id_user->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->id_user->AdvancedSearch->SearchOperator = @$_GET["z_id_user"];

		// status
		$this->status->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_status"]);
		if ($this->status->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->status->AdvancedSearch->SearchOperator = @$_GET["z_status"];

		// tanggal
		$this->tanggal->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_tanggal"]);
		if ($this->tanggal->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->tanggal->AdvancedSearch->SearchOperator = @$_GET["z_tanggal"];

		// gaji_pokok
		$this->gaji_pokok->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_gaji_pokok"]);
		if ($this->gaji_pokok->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->gaji_pokok->AdvancedSearch->SearchOperator = @$_GET["z_gaji_pokok"];

		// lembur
		$this->lembur->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_lembur"]);
		if ($this->lembur->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->lembur->AdvancedSearch->SearchOperator = @$_GET["z_lembur"];

		// tunjangan_proyek
		$this->tunjangan_proyek->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_tunjangan_proyek"]);
		if ($this->tunjangan_proyek->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->tunjangan_proyek->AdvancedSearch->SearchOperator = @$_GET["z_tunjangan_proyek"];
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

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("id_gaji")) <> "")
			$this->id_gaji->CurrentValue = $this->getKey("id_gaji"); // id_gaji
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
		} elseif ($this->RowType == EW_ROWTYPE_SEARCH) { // Search row

			// id_gaji
			$this->id_gaji->EditCustomAttributes = "";
			$this->id_gaji->EditValue = ew_HtmlEncode($this->id_gaji->AdvancedSearch->SearchValue);

			// id_user
			$this->id_user->EditCustomAttributes = "";

			// status
			$this->status->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->status->FldTagValue(1), $this->status->FldTagCaption(1) <> "" ? $this->status->FldTagCaption(1) : $this->status->FldTagValue(1));
			$arwrk[] = array($this->status->FldTagValue(2), $this->status->FldTagCaption(2) <> "" ? $this->status->FldTagCaption(2) : $this->status->FldTagValue(2));
			$this->status->EditValue = $arwrk;

			// tanggal
			$this->tanggal->EditCustomAttributes = "";
			$this->tanggal->EditValue = ew_HtmlEncode(ew_FormatDateTime(ew_UnFormatDateTime($this->tanggal->AdvancedSearch->SearchValue, 7), 7));

			// gaji_pokok
			$this->gaji_pokok->EditCustomAttributes = "";
			$this->gaji_pokok->EditValue = ew_HtmlEncode($this->gaji_pokok->AdvancedSearch->SearchValue);

			// lembur
			$this->lembur->EditCustomAttributes = "";
			$this->lembur->EditValue = ew_HtmlEncode($this->lembur->AdvancedSearch->SearchValue);

			// tunjangan_proyek
			$this->tunjangan_proyek->EditCustomAttributes = "";
			$this->tunjangan_proyek->EditValue = ew_HtmlEncode($this->tunjangan_proyek->AdvancedSearch->SearchValue);
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

	// Validate search
	function ValidateSearch() {
		global $gsSearchError;

		// Initialize
		$gsSearchError = "";

		// Check if validation required
		if (!EW_SERVER_VALIDATE)
			return TRUE;

		// Return validate result
		$ValidateSearch = ($gsSearchError == "");

		// Call Form_CustomValidate event
		$sFormCustomError = "";
		$ValidateSearch = $ValidateSearch && $this->Form_CustomValidate($sFormCustomError);
		if ($sFormCustomError <> "") {
			ew_AddMessage($gsSearchError, $sFormCustomError);
		}
		return $ValidateSearch;
	}

	// Load advanced search
	function LoadAdvancedSearch() {
		$this->id_gaji->AdvancedSearch->Load();
		$this->id_user->AdvancedSearch->Load();
		$this->status->AdvancedSearch->Load();
		$this->tanggal->AdvancedSearch->Load();
		$this->gaji_pokok->AdvancedSearch->Load();
		$this->lembur->AdvancedSearch->Load();
		$this->tunjangan_proyek->AdvancedSearch->Load();
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
		$item->Body = "<a id=\"emf_lh_gaji\" href=\"javascript:void(0);\" onclick=\"ew_EmailDialogShow({lnk:'emf_lh_gaji',hdr:ewLanguage.Phrase('ExportToEmail'),f:document.flh_gajilist,sel:false});\">" . $Language->Phrase("ExportToEmail") . "</a>";
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
if (!isset($lh_gaji_list)) $lh_gaji_list = new clh_gaji_list();

// Page init
$lh_gaji_list->Page_Init();

// Page main
$lh_gaji_list->Page_Main();
?>
<?php include_once "header.php" ?>
<?php if ($lh_gaji->Export == "") { ?>
<script type="text/javascript">

// Page object
var lh_gaji_list = new ew_Page("lh_gaji_list");
lh_gaji_list.PageID = "list"; // Page ID
var EW_PAGE_ID = lh_gaji_list.PageID; // For backward compatibility

// Form object
var flh_gajilist = new ew_Form("flh_gajilist");

// Form_CustomValidate event
flh_gajilist.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
flh_gajilist.ValidateRequired = true;
<?php } else { ?>
flh_gajilist.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
flh_gajilist.Lists["x_id_user"] = {"LinkField":"x_id_user","Ajax":null,"AutoFill":false,"DisplayFields":["x_nama","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
var flh_gajilistsrch = new ew_Form("flh_gajilistsrch");

// Validate function for search
flh_gajilistsrch.Validate = function(fobj) {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	fobj = fobj || this.Form;
	this.PostAutoSuggest();
	var infix = "";

	// Set up row object
	ew_ElementsToRow(fobj, infix);

	// Fire Form_CustomValidate event
	if (!this.Form_CustomValidate(fobj))
		return false;
	return true;
}

// Form_CustomValidate event
flh_gajilistsrch.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
flh_gajilistsrch.ValidateRequired = true; // uses JavaScript validation
<?php } else { ?>
flh_gajilistsrch.ValidateRequired = false; // no JavaScript validation
<?php } ?>

// Dynamic selection lists
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php } ?>
<?php
	$bSelectLimit = EW_SELECT_LIMIT;
	if ($bSelectLimit) {
		$lh_gaji_list->TotalRecs = $lh_gaji->SelectRecordCount();
	} else {
		if ($lh_gaji_list->Recordset = $lh_gaji_list->LoadRecordset())
			$lh_gaji_list->TotalRecs = $lh_gaji_list->Recordset->RecordCount();
	}
	$lh_gaji_list->StartRec = 1;
	if ($lh_gaji_list->DisplayRecs <= 0 || ($lh_gaji->Export <> "" && $lh_gaji->ExportAll)) // Display all records
		$lh_gaji_list->DisplayRecs = $lh_gaji_list->TotalRecs;
	if (!($lh_gaji->Export <> "" && $lh_gaji->ExportAll))
		$lh_gaji_list->SetUpStartRec(); // Set up start record position
	if ($bSelectLimit)
		$lh_gaji_list->Recordset = $lh_gaji_list->LoadRecordset($lh_gaji_list->StartRec-1, $lh_gaji_list->DisplayRecs);
?>
<p style="white-space: nowrap;"><span id="ewPageCaption" class="ewTitle ewTableTitle"><?php echo $Language->Phrase("TblTypeTABLE") ?><?php echo $lh_gaji->TableCaption() ?>&nbsp;&nbsp;</span>
<?php $lh_gaji_list->ExportOptions->Render("body"); ?>
</p>
<?php if ($Security->CanSearch()) { ?>
<?php if ($lh_gaji->Export == "" && $lh_gaji->CurrentAction == "") { ?>
<form name="flh_gajilistsrch" id="flh_gajilistsrch" class="ewForm" action="<?php echo ew_CurrentPage() ?>" onsubmit="return ewForms[this.id].Submit();">
<a href="javascript:flh_gajilistsrch.ToggleSearchPanel();" style="text-decoration: none;"><img id="flh_gajilistsrch_SearchImage" src="phpimages/collapse.gif" alt="" width="9" height="9" style="border: 0;"></a><span class="phpmaker">&nbsp;<?php echo $Language->Phrase("Search") ?></span><br>
<div id="flh_gajilistsrch_SearchPanel">
<input type="hidden" name="cmd" value="search">
<input type="hidden" name="t" value="lh_gaji">
<div class="ewBasicSearch">
<?php
if ($gsSearchError == "")
	$lh_gaji_list->LoadAdvancedSearch(); // Load advanced search

// Render for search
$lh_gaji->RowType = EW_ROWTYPE_SEARCH;

// Render row
$lh_gaji->ResetAttrs();
$lh_gaji_list->RenderRow();
?>
<div id="xsr_1" class="ewRow">
<?php if ($lh_gaji->status->Visible) { // status ?>
	<span id="xsc_status" class="ewCell">
		<span class="ewSearchCaption"><?php echo $lh_gaji->status->FldCaption() ?></span>
		<span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_status" id="z_status" value="="></span>
		<span class="ewSearchField">
<div id="tp_x_status" class="<?php echo EW_ITEM_TEMPLATE_CLASSNAME ?>"><input type="radio" name="x_status" id="x_status" value="{value}"<?php echo $lh_gaji->status->EditAttributes() ?>></div>
<div id="dsl_x_status" data-repeatcolumn="5" class="ewItemList">
<?php
$arwrk = $lh_gaji->status->EditValue;
if (is_array($arwrk)) {
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($lh_gaji->status->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " checked=\"checked\"" : "";
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
</span>
	</span>
<?php } ?>
</div>
<div id="xsr_2" class="ewRow">
	<input type="submit" name="btnsubmit" id="btnsubmit" value="<?php echo ew_BtnCaption($Language->Phrase("QuickSearchBtn")) ?>">&nbsp;
	<a href="<?php echo $lh_gaji_list->PageUrl() ?>cmd=reset" id="a_ShowAll" class="ewLink"><?php echo $Language->Phrase("ShowAll") ?></a>&nbsp;
</div>
</div>
</div>
</form>
<?php } ?>
<?php } ?>
<?php $lh_gaji_list->ShowPageHeader(); ?>
<?php
$lh_gaji_list->ShowMessage();
?>
<br>
<table cellspacing="0" class="ewGrid"><tr><td class="ewGridContent">
<form name="flh_gajilist" id="flh_gajilist" class="ewForm" action="" method="post">
<input type="hidden" name="t" value="lh_gaji">
<div id="gmp_lh_gaji" class="ewGridMiddlePanel">
<?php if ($lh_gaji_list->TotalRecs > 0) { ?>
<table id="tbl_lh_gajilist" class="ewTable ewTableSeparate">
<?php echo $lh_gaji->TableCustomInnerHtml ?>
<thead><!-- Table header -->
	<tr class="ewTableHeader">
<?php

// Render list options
$lh_gaji_list->RenderListOptions();

// Render list options (header, left)
$lh_gaji_list->ListOptions->Render("header", "left");
?>
<?php if ($lh_gaji->id_gaji->Visible) { // id_gaji ?>
	<?php if ($lh_gaji->SortUrl($lh_gaji->id_gaji) == "") { ?>
		<td><span id="elh_lh_gaji_id_gaji" class="lh_gaji_id_gaji"><table class="ewTableHeaderBtn"><thead><tr><td><?php echo $lh_gaji->id_gaji->FldCaption() ?></td></tr></thead></table></span></td>
	<?php } else { ?>
		<td><div onmousedown="ew_Sort(event,'<?php echo $lh_gaji->SortUrl($lh_gaji->id_gaji) ?>',1);"><span id="elh_lh_gaji_id_gaji" class="lh_gaji_id_gaji">
			<table class="ewTableHeaderBtn"><thead><tr><td class="ewTableHeaderCaption"><?php echo $lh_gaji->id_gaji->FldCaption() ?></td><td class="ewTableHeaderSort"><?php if ($lh_gaji->id_gaji->getSort() == "ASC") { ?><img src="phpimages/sortup.gif" width="10" height="9" alt="" style="border: 0;"><?php } elseif ($lh_gaji->id_gaji->getSort() == "DESC") { ?><img src="phpimages/sortdown.gif" width="10" height="9" alt="" style="border: 0;"><?php } ?></td></tr></thead></table>
		</span></div></td>		
	<?php } ?>
<?php } ?>		
<?php if ($lh_gaji->id_user->Visible) { // id_user ?>
	<?php if ($lh_gaji->SortUrl($lh_gaji->id_user) == "") { ?>
		<td><span id="elh_lh_gaji_id_user" class="lh_gaji_id_user"><table class="ewTableHeaderBtn"><thead><tr><td><?php echo $lh_gaji->id_user->FldCaption() ?></td></tr></thead></table></span></td>
	<?php } else { ?>
		<td><div onmousedown="ew_Sort(event,'<?php echo $lh_gaji->SortUrl($lh_gaji->id_user) ?>',1);"><span id="elh_lh_gaji_id_user" class="lh_gaji_id_user">
			<table class="ewTableHeaderBtn"><thead><tr><td class="ewTableHeaderCaption"><?php echo $lh_gaji->id_user->FldCaption() ?></td><td class="ewTableHeaderSort"><?php if ($lh_gaji->id_user->getSort() == "ASC") { ?><img src="phpimages/sortup.gif" width="10" height="9" alt="" style="border: 0;"><?php } elseif ($lh_gaji->id_user->getSort() == "DESC") { ?><img src="phpimages/sortdown.gif" width="10" height="9" alt="" style="border: 0;"><?php } ?></td></tr></thead></table>
		</span></div></td>		
	<?php } ?>
<?php } ?>		
<?php if ($lh_gaji->status->Visible) { // status ?>
	<?php if ($lh_gaji->SortUrl($lh_gaji->status) == "") { ?>
		<td><span id="elh_lh_gaji_status" class="lh_gaji_status"><table class="ewTableHeaderBtn"><thead><tr><td><?php echo $lh_gaji->status->FldCaption() ?></td></tr></thead></table></span></td>
	<?php } else { ?>
		<td><div onmousedown="ew_Sort(event,'<?php echo $lh_gaji->SortUrl($lh_gaji->status) ?>',1);"><span id="elh_lh_gaji_status" class="lh_gaji_status">
			<table class="ewTableHeaderBtn"><thead><tr><td class="ewTableHeaderCaption"><?php echo $lh_gaji->status->FldCaption() ?></td><td class="ewTableHeaderSort"><?php if ($lh_gaji->status->getSort() == "ASC") { ?><img src="phpimages/sortup.gif" width="10" height="9" alt="" style="border: 0;"><?php } elseif ($lh_gaji->status->getSort() == "DESC") { ?><img src="phpimages/sortdown.gif" width="10" height="9" alt="" style="border: 0;"><?php } ?></td></tr></thead></table>
		</span></div></td>		
	<?php } ?>
<?php } ?>		
<?php if ($lh_gaji->tanggal->Visible) { // tanggal ?>
	<?php if ($lh_gaji->SortUrl($lh_gaji->tanggal) == "") { ?>
		<td><span id="elh_lh_gaji_tanggal" class="lh_gaji_tanggal"><table class="ewTableHeaderBtn"><thead><tr><td><?php echo $lh_gaji->tanggal->FldCaption() ?></td></tr></thead></table></span></td>
	<?php } else { ?>
		<td><div onmousedown="ew_Sort(event,'<?php echo $lh_gaji->SortUrl($lh_gaji->tanggal) ?>',1);"><span id="elh_lh_gaji_tanggal" class="lh_gaji_tanggal">
			<table class="ewTableHeaderBtn"><thead><tr><td class="ewTableHeaderCaption"><?php echo $lh_gaji->tanggal->FldCaption() ?></td><td class="ewTableHeaderSort"><?php if ($lh_gaji->tanggal->getSort() == "ASC") { ?><img src="phpimages/sortup.gif" width="10" height="9" alt="" style="border: 0;"><?php } elseif ($lh_gaji->tanggal->getSort() == "DESC") { ?><img src="phpimages/sortdown.gif" width="10" height="9" alt="" style="border: 0;"><?php } ?></td></tr></thead></table>
		</span></div></td>		
	<?php } ?>
<?php } ?>		
<?php if ($lh_gaji->gaji_pokok->Visible) { // gaji_pokok ?>
	<?php if ($lh_gaji->SortUrl($lh_gaji->gaji_pokok) == "") { ?>
		<td><span id="elh_lh_gaji_gaji_pokok" class="lh_gaji_gaji_pokok"><table class="ewTableHeaderBtn"><thead><tr><td><?php echo $lh_gaji->gaji_pokok->FldCaption() ?></td></tr></thead></table></span></td>
	<?php } else { ?>
		<td><div onmousedown="ew_Sort(event,'<?php echo $lh_gaji->SortUrl($lh_gaji->gaji_pokok) ?>',1);"><span id="elh_lh_gaji_gaji_pokok" class="lh_gaji_gaji_pokok">
			<table class="ewTableHeaderBtn"><thead><tr><td class="ewTableHeaderCaption"><?php echo $lh_gaji->gaji_pokok->FldCaption() ?></td><td class="ewTableHeaderSort"><?php if ($lh_gaji->gaji_pokok->getSort() == "ASC") { ?><img src="phpimages/sortup.gif" width="10" height="9" alt="" style="border: 0;"><?php } elseif ($lh_gaji->gaji_pokok->getSort() == "DESC") { ?><img src="phpimages/sortdown.gif" width="10" height="9" alt="" style="border: 0;"><?php } ?></td></tr></thead></table>
		</span></div></td>		
	<?php } ?>
<?php } ?>		
<?php if ($lh_gaji->lembur->Visible) { // lembur ?>
	<?php if ($lh_gaji->SortUrl($lh_gaji->lembur) == "") { ?>
		<td><span id="elh_lh_gaji_lembur" class="lh_gaji_lembur"><table class="ewTableHeaderBtn"><thead><tr><td><?php echo $lh_gaji->lembur->FldCaption() ?></td></tr></thead></table></span></td>
	<?php } else { ?>
		<td><div onmousedown="ew_Sort(event,'<?php echo $lh_gaji->SortUrl($lh_gaji->lembur) ?>',1);"><span id="elh_lh_gaji_lembur" class="lh_gaji_lembur">
			<table class="ewTableHeaderBtn"><thead><tr><td class="ewTableHeaderCaption"><?php echo $lh_gaji->lembur->FldCaption() ?></td><td class="ewTableHeaderSort"><?php if ($lh_gaji->lembur->getSort() == "ASC") { ?><img src="phpimages/sortup.gif" width="10" height="9" alt="" style="border: 0;"><?php } elseif ($lh_gaji->lembur->getSort() == "DESC") { ?><img src="phpimages/sortdown.gif" width="10" height="9" alt="" style="border: 0;"><?php } ?></td></tr></thead></table>
		</span></div></td>		
	<?php } ?>
<?php } ?>		
<?php if ($lh_gaji->tunjangan_proyek->Visible) { // tunjangan_proyek ?>
	<?php if ($lh_gaji->SortUrl($lh_gaji->tunjangan_proyek) == "") { ?>
		<td><span id="elh_lh_gaji_tunjangan_proyek" class="lh_gaji_tunjangan_proyek"><table class="ewTableHeaderBtn"><thead><tr><td><?php echo $lh_gaji->tunjangan_proyek->FldCaption() ?></td></tr></thead></table></span></td>
	<?php } else { ?>
		<td><div onmousedown="ew_Sort(event,'<?php echo $lh_gaji->SortUrl($lh_gaji->tunjangan_proyek) ?>',1);"><span id="elh_lh_gaji_tunjangan_proyek" class="lh_gaji_tunjangan_proyek">
			<table class="ewTableHeaderBtn"><thead><tr><td class="ewTableHeaderCaption"><?php echo $lh_gaji->tunjangan_proyek->FldCaption() ?></td><td class="ewTableHeaderSort"><?php if ($lh_gaji->tunjangan_proyek->getSort() == "ASC") { ?><img src="phpimages/sortup.gif" width="10" height="9" alt="" style="border: 0;"><?php } elseif ($lh_gaji->tunjangan_proyek->getSort() == "DESC") { ?><img src="phpimages/sortdown.gif" width="10" height="9" alt="" style="border: 0;"><?php } ?></td></tr></thead></table>
		</span></div></td>		
	<?php } ?>
<?php } ?>		
<?php

// Render list options (header, right)
$lh_gaji_list->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
if ($lh_gaji->ExportAll && $lh_gaji->Export <> "") {
	$lh_gaji_list->StopRec = $lh_gaji_list->TotalRecs;
} else {

	// Set the last record to display
	if ($lh_gaji_list->TotalRecs > $lh_gaji_list->StartRec + $lh_gaji_list->DisplayRecs - 1)
		$lh_gaji_list->StopRec = $lh_gaji_list->StartRec + $lh_gaji_list->DisplayRecs - 1;
	else
		$lh_gaji_list->StopRec = $lh_gaji_list->TotalRecs;
}
$lh_gaji_list->RecCnt = $lh_gaji_list->StartRec - 1;
if ($lh_gaji_list->Recordset && !$lh_gaji_list->Recordset->EOF) {
	$lh_gaji_list->Recordset->MoveFirst();
	if (!$bSelectLimit && $lh_gaji_list->StartRec > 1)
		$lh_gaji_list->Recordset->Move($lh_gaji_list->StartRec - 1);
} elseif (!$lh_gaji->AllowAddDeleteRow && $lh_gaji_list->StopRec == 0) {
	$lh_gaji_list->StopRec = $lh_gaji->GridAddRowCount;
}

// Initialize aggregate
$lh_gaji->RowType = EW_ROWTYPE_AGGREGATEINIT;
$lh_gaji->ResetAttrs();
$lh_gaji_list->RenderRow();
while ($lh_gaji_list->RecCnt < $lh_gaji_list->StopRec) {
	$lh_gaji_list->RecCnt++;
	if (intval($lh_gaji_list->RecCnt) >= intval($lh_gaji_list->StartRec)) {
		$lh_gaji_list->RowCnt++;

		// Set up key count
		$lh_gaji_list->KeyCount = $lh_gaji_list->RowIndex;

		// Init row class and style
		$lh_gaji->ResetAttrs();
		$lh_gaji->CssClass = "";
		if ($lh_gaji->CurrentAction == "gridadd") {
		} else {
			$lh_gaji_list->LoadRowValues($lh_gaji_list->Recordset); // Load row values
		}
		$lh_gaji->RowType = EW_ROWTYPE_VIEW; // Render view

		// Set up row id / data-rowindex
		$lh_gaji->RowAttrs = array_merge($lh_gaji->RowAttrs, array('data-rowindex'=>$lh_gaji_list->RowCnt, 'id'=>'r' . $lh_gaji_list->RowCnt . '_lh_gaji', 'data-rowtype'=>$lh_gaji->RowType));

		// Render row
		$lh_gaji_list->RenderRow();

		// Render list options
		$lh_gaji_list->RenderListOptions();
?>
	<tr<?php echo $lh_gaji->RowAttributes() ?>>
<?php

// Render list options (body, left)
$lh_gaji_list->ListOptions->Render("body", "left", $lh_gaji_list->RowCnt);
?>
	<?php if ($lh_gaji->id_gaji->Visible) { // id_gaji ?>
		<td<?php echo $lh_gaji->id_gaji->CellAttributes() ?>><span id="el<?php echo $lh_gaji_list->RowCnt ?>_lh_gaji_id_gaji" class="lh_gaji_id_gaji">
<span<?php echo $lh_gaji->id_gaji->ViewAttributes() ?>>
<?php echo $lh_gaji->id_gaji->ListViewValue() ?></span>
</span><a id="<?php echo $lh_gaji_list->PageObjName . "_row_" . $lh_gaji_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($lh_gaji->id_user->Visible) { // id_user ?>
		<td<?php echo $lh_gaji->id_user->CellAttributes() ?>><span id="el<?php echo $lh_gaji_list->RowCnt ?>_lh_gaji_id_user" class="lh_gaji_id_user">
<span<?php echo $lh_gaji->id_user->ViewAttributes() ?>>
<?php echo $lh_gaji->id_user->ListViewValue() ?></span>
</span><a id="<?php echo $lh_gaji_list->PageObjName . "_row_" . $lh_gaji_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($lh_gaji->status->Visible) { // status ?>
		<td<?php echo $lh_gaji->status->CellAttributes() ?>><span id="el<?php echo $lh_gaji_list->RowCnt ?>_lh_gaji_status" class="lh_gaji_status">
<span<?php echo $lh_gaji->status->ViewAttributes() ?>>
<?php echo $lh_gaji->status->ListViewValue() ?></span>
</span><a id="<?php echo $lh_gaji_list->PageObjName . "_row_" . $lh_gaji_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($lh_gaji->tanggal->Visible) { // tanggal ?>
		<td<?php echo $lh_gaji->tanggal->CellAttributes() ?>><span id="el<?php echo $lh_gaji_list->RowCnt ?>_lh_gaji_tanggal" class="lh_gaji_tanggal">
<span<?php echo $lh_gaji->tanggal->ViewAttributes() ?>>
<?php echo $lh_gaji->tanggal->ListViewValue() ?></span>
</span><a id="<?php echo $lh_gaji_list->PageObjName . "_row_" . $lh_gaji_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($lh_gaji->gaji_pokok->Visible) { // gaji_pokok ?>
		<td<?php echo $lh_gaji->gaji_pokok->CellAttributes() ?>><span id="el<?php echo $lh_gaji_list->RowCnt ?>_lh_gaji_gaji_pokok" class="lh_gaji_gaji_pokok">
<span<?php echo $lh_gaji->gaji_pokok->ViewAttributes() ?>>
<?php echo $lh_gaji->gaji_pokok->ListViewValue() ?></span>
</span><a id="<?php echo $lh_gaji_list->PageObjName . "_row_" . $lh_gaji_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($lh_gaji->lembur->Visible) { // lembur ?>
		<td<?php echo $lh_gaji->lembur->CellAttributes() ?>><span id="el<?php echo $lh_gaji_list->RowCnt ?>_lh_gaji_lembur" class="lh_gaji_lembur">
<span<?php echo $lh_gaji->lembur->ViewAttributes() ?>>
<?php echo $lh_gaji->lembur->ListViewValue() ?></span>
</span><a id="<?php echo $lh_gaji_list->PageObjName . "_row_" . $lh_gaji_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($lh_gaji->tunjangan_proyek->Visible) { // tunjangan_proyek ?>
		<td<?php echo $lh_gaji->tunjangan_proyek->CellAttributes() ?>><span id="el<?php echo $lh_gaji_list->RowCnt ?>_lh_gaji_tunjangan_proyek" class="lh_gaji_tunjangan_proyek">
<span<?php echo $lh_gaji->tunjangan_proyek->ViewAttributes() ?>>
<?php echo $lh_gaji->tunjangan_proyek->ListViewValue() ?></span>
</span><a id="<?php echo $lh_gaji_list->PageObjName . "_row_" . $lh_gaji_list->RowCnt ?>"></a></td>
	<?php } ?>
<?php

// Render list options (body, right)
$lh_gaji_list->ListOptions->Render("body", "right", $lh_gaji_list->RowCnt);
?>
	</tr>
<?php
	}
	if ($lh_gaji->CurrentAction <> "gridadd")
		$lh_gaji_list->Recordset->MoveNext();
}
?>
</tbody>
</table>
<?php } ?>
<?php if ($lh_gaji->CurrentAction == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
</div>
</form>
<?php

// Close recordset
if ($lh_gaji_list->Recordset)
	$lh_gaji_list->Recordset->Close();
?>
<?php if ($lh_gaji->Export == "") { ?>
<div class="ewGridLowerPanel">
<?php if ($lh_gaji->CurrentAction <> "gridadd" && $lh_gaji->CurrentAction <> "gridedit") { ?>
<form name="ewpagerform" id="ewpagerform" class="ewForm" action="<?php echo ew_CurrentPage() ?>">
<table class="ewPager"><tr><td>
<?php if (!isset($lh_gaji_list->Pager)) $lh_gaji_list->Pager = new cPrevNextPager($lh_gaji_list->StartRec, $lh_gaji_list->DisplayRecs, $lh_gaji_list->TotalRecs) ?>
<?php if ($lh_gaji_list->Pager->RecordCount > 0) { ?>
	<table cellspacing="0" class="ewStdTable"><tbody><tr><td><span class="phpmaker"><?php echo $Language->Phrase("Page") ?>&nbsp;</span></td>
<!--first page button-->
	<?php if ($lh_gaji_list->Pager->FirstButton->Enabled) { ?>
	<td><a href="<?php echo $lh_gaji_list->PageUrl() ?>start=<?php echo $lh_gaji_list->Pager->FirstButton->Start ?>"><img src="phpimages/first.gif" alt="<?php echo $Language->Phrase("PagerFirst") ?>" width="16" height="16" style="border: 0;"></a></td>
	<?php } else { ?>
	<td><img src="phpimages/firstdisab.gif" alt="<?php echo $Language->Phrase("PagerFirst") ?>" width="16" height="16" style="border: 0;"></td>
	<?php } ?>
<!--previous page button-->
	<?php if ($lh_gaji_list->Pager->PrevButton->Enabled) { ?>
	<td><a href="<?php echo $lh_gaji_list->PageUrl() ?>start=<?php echo $lh_gaji_list->Pager->PrevButton->Start ?>"><img src="phpimages/prev.gif" alt="<?php echo $Language->Phrase("PagerPrevious") ?>" width="16" height="16" style="border: 0;"></a></td>
	<?php } else { ?>
	<td><img src="phpimages/prevdisab.gif" alt="<?php echo $Language->Phrase("PagerPrevious") ?>" width="16" height="16" style="border: 0;"></td>
	<?php } ?>
<!--current page number-->
	<td><input type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" id="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $lh_gaji_list->Pager->CurrentPage ?>" size="4"></td>
<!--next page button-->
	<?php if ($lh_gaji_list->Pager->NextButton->Enabled) { ?>
	<td><a href="<?php echo $lh_gaji_list->PageUrl() ?>start=<?php echo $lh_gaji_list->Pager->NextButton->Start ?>"><img src="phpimages/next.gif" alt="<?php echo $Language->Phrase("PagerNext") ?>" width="16" height="16" style="border: 0;"></a></td>	
	<?php } else { ?>
	<td><img src="phpimages/nextdisab.gif" alt="<?php echo $Language->Phrase("PagerNext") ?>" width="16" height="16" style="border: 0;"></td>
	<?php } ?>
<!--last page button-->
	<?php if ($lh_gaji_list->Pager->LastButton->Enabled) { ?>
	<td><a href="<?php echo $lh_gaji_list->PageUrl() ?>start=<?php echo $lh_gaji_list->Pager->LastButton->Start ?>"><img src="phpimages/last.gif" alt="<?php echo $Language->Phrase("PagerLast") ?>" width="16" height="16" style="border: 0;"></a></td>	
	<?php } else { ?>
	<td><img src="phpimages/lastdisab.gif" alt="<?php echo $Language->Phrase("PagerLast") ?>" width="16" height="16" style="border: 0;"></td>
	<?php } ?>
	<td><span class="phpmaker">&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $lh_gaji_list->Pager->PageCount ?></span></td>
	</tr></tbody></table>
	</td>	
	<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
	<td>
	<span class="phpmaker"><?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $lh_gaji_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $lh_gaji_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $lh_gaji_list->Pager->RecordCount ?></span>
<?php } else { ?>
	<?php if ($Security->CanList()) { ?>
	<?php if ($lh_gaji_list->SearchWhere == "0=101") { ?>
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
<?php if ($lh_gaji_list->AddUrl <> "") { ?>
<a class="ewGridLink" href="<?php echo $lh_gaji_list->AddUrl ?>"><?php echo $Language->Phrase("AddLink") ?></a>&nbsp;&nbsp;
<?php } ?>
<?php } ?>
</span>
</div>
<?php } ?>
</td></tr></table>
<?php if ($lh_gaji->Export == "") { ?>
<script type="text/javascript">
flh_gajilistsrch.Init();
flh_gajilist.Init();
</script>
<?php } ?>
<?php
$lh_gaji_list->ShowPageFooter();
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
$lh_gaji_list->Page_Terminate();
?>
