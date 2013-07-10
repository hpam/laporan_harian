<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg9.php" ?>
<?php include_once "ewmysql9.php" ?>
<?php include_once "phpfn9.php" ?>
<?php

// Global variable for table object
$Laporan_Pendapatan_Proyek = NULL;

//
// Table class for Laporan Pendapatan Proyek
//
class cLaporan_Pendapatan_Proyek extends cTableBase {
	var $id_proyek;
	var $nama_proyek;
	var $tanggal;
	var $kardus;
	var $kayu;
	var $besi;
	var $harga;

	//
	// Table class constructor
	//
	function __construct() {
		global $Language;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();
		$this->TableVar = 'Laporan_Pendapatan_Proyek';
		$this->TableName = 'Laporan Pendapatan Proyek';
		$this->TableType = 'REPORT';
		$this->ExportAll = TRUE;
		$this->ExportPageBreakCount = 0; // Page break per every n record (PDF only)
		$this->ExportPageOrientation = "portrait"; // Page orientation (PDF only)
		$this->ExportPageSize = "a4"; // Page size (PDF only)
		$this->UserIDAllowSecurity = 0; // User ID Allow

		// id_proyek
		$this->id_proyek = new cField('Laporan_Pendapatan_Proyek', 'Laporan Pendapatan Proyek', 'x_id_proyek', 'id_proyek', '`id_proyek`', '`id_proyek`', 3, -1, FALSE, '`id_proyek`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->id_proyek->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['id_proyek'] = &$this->id_proyek;

		// nama_proyek
		$this->nama_proyek = new cField('Laporan_Pendapatan_Proyek', 'Laporan Pendapatan Proyek', 'x_nama_proyek', 'nama_proyek', '`nama_proyek`', '`nama_proyek`', 200, -1, FALSE, '`nama_proyek`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['nama_proyek'] = &$this->nama_proyek;

		// tanggal
		$this->tanggal = new cField('Laporan_Pendapatan_Proyek', 'Laporan Pendapatan Proyek', 'x_tanggal', 'tanggal', '`tanggal`', 'DATE_FORMAT(`tanggal`, \'%d/%m/%Y %H:%i:%s\')', 135, 7, FALSE, '`tanggal`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->tanggal->FldDefaultErrMsg = str_replace("%s", "/", $Language->Phrase("IncorrectDateDMY"));
		$this->fields['tanggal'] = &$this->tanggal;

		// kardus
		$this->kardus = new cField('Laporan_Pendapatan_Proyek', 'Laporan Pendapatan Proyek', 'x_kardus', 'kardus', '`kardus`', '`kardus`', 3, -1, FALSE, '`kardus`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->kardus->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['kardus'] = &$this->kardus;

		// kayu
		$this->kayu = new cField('Laporan_Pendapatan_Proyek', 'Laporan Pendapatan Proyek', 'x_kayu', 'kayu', '`kayu`', '`kayu`', 3, -1, FALSE, '`kayu`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->kayu->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['kayu'] = &$this->kayu;

		// besi
		$this->besi = new cField('Laporan_Pendapatan_Proyek', 'Laporan Pendapatan Proyek', 'x_besi', 'besi', '`besi`', '`besi`', 3, -1, FALSE, '`besi`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->besi->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['besi'] = &$this->besi;

		// harga
		$this->harga = new cField('Laporan_Pendapatan_Proyek', 'Laporan Pendapatan Proyek', 'x_harga', 'harga', '`harga`', '`harga`', 5, -1, FALSE, '`harga`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->harga->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['harga'] = &$this->harga;
	}

	// Report detail level SQL
	function SqlDetailSelect() { // Select
		return "SELECT * FROM `lh_proyek`";
	}

	function SqlDetailWhere() { // Where
		return "";
	}

	function SqlDetailGroupBy() { // Group By
		return "";
	}

	function SqlDetailHaving() { // Having
		return "";
	}

	function SqlDetailOrderBy() { // Order By
		return "";
	}

	// Check if Anonymous User is allowed
	function AllowAnonymousUser() {
		switch (@$this->PageID) {
			case "add":
			case "register":
			case "addopt":
				return FALSE;
			case "edit":
			case "update":
			case "changepwd":
			case "forgotpwd":
				return FALSE;
			case "delete":
				return FALSE;
			case "view":
				return FALSE;
			case "search":
				return FALSE;
			default:
				return FALSE;
		}
	}

	// Apply User ID filters
	function ApplyUserIDFilters($sFilter) {
		return $sFilter;
	}

	// Check if User ID security allows view all
	function UserIDAllow($id = "") {
		return TRUE;
	}

	// Report detail SQL
	function DetailSQL() {
		$sFilter = $this->CurrentFilter;
		$sFilter = $this->ApplyUserIDFilters($sFilter);
		$sSort = "";
		return ew_BuildSelectSql($this->SqlDetailSelect(), $this->SqlDetailWhere(),
			$this->SqlDetailGroupBy(), $this->SqlDetailHaving(),
			$this->SqlDetailOrderBy(), $sFilter, $sSort);
	}

	// Return page URL
	function getReturnUrl() {
		$name = EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL;

		// Get referer URL automatically
		if (ew_ServerVar("HTTP_REFERER") <> "" && ew_ReferPage() <> ew_CurrentPage() && ew_ReferPage() <> "login.php") // Referer not same page or login page
			$_SESSION[$name] = ew_ServerVar("HTTP_REFERER"); // Save to Session
		if (@$_SESSION[$name] <> "") {
			return $_SESSION[$name];
		} else {
			return "Laporan_Pendapatan_Proyekreport.php";
		}
	}

	function setReturnUrl($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL] = $v;
	}

	// List URL
	function GetListUrl() {
		return "Laporan_Pendapatan_Proyekreport.php";
	}

	// View URL
	function GetViewUrl() {
		return $this->KeyUrl("", $this->UrlParm());
	}

	// Add URL
	function GetAddUrl() {
		return "";
	}

	// Edit URL
	function GetEditUrl($parm = "") {
		return $this->KeyUrl("", $this->UrlParm($parm));
	}

	// Inline edit URL
	function GetInlineEditUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=edit"));
	}

	// Copy URL
	function GetCopyUrl($parm = "") {
		return $this->KeyUrl("", $this->UrlParm($parm));
	}

	// Inline copy URL
	function GetInlineCopyUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=copy"));
	}

	// Delete URL
	function GetDeleteUrl() {
		return $this->KeyUrl("", $this->UrlParm());
	}

	// Add key value to URL
	function KeyUrl($url, $parm = "") {
		$sUrl = $url . "?";
		if ($parm <> "") $sUrl .= $parm . "&";
		if (!is_null($this->id_proyek->CurrentValue)) {
			$sUrl .= "id_proyek=" . urlencode($this->id_proyek->CurrentValue);
		} else {
			return "javascript:alert(ewLanguage.Phrase('InvalidRecord'));";
		}
		return $sUrl;
	}

	// Sort URL
	function SortUrl(&$fld) {
		if ($this->CurrentAction <> "" || $this->Export <> "" ||
			in_array($fld->FldType, array(128, 204, 205))) { // Unsortable data type
				return "";
		} elseif ($fld->Sortable) {
			$sUrlParm = $this->UrlParm("order=" . urlencode($fld->FldName) . "&ordertype=" . $fld->ReverseSort());
			return ew_CurrentPage() . "?" . $sUrlParm;
		} else {
			return "";
		}
	}

	// Get record keys from $_POST/$_GET/$_SESSION
	function GetRecordKeys() {
		global $EW_COMPOSITE_KEY_SEPARATOR;
		$arKeys = array();
		$arKey = array();
		if (isset($_POST["key_m"])) {
			$arKeys = ew_StripSlashes($_POST["key_m"]);
			$cnt = count($arKeys);
		} elseif (isset($_GET["key_m"])) {
			$arKeys = ew_StripSlashes($_GET["key_m"]);
			$cnt = count($arKeys);
		} elseif (isset($_GET)) {
			$arKeys[] = @$_GET["id_proyek"]; // id_proyek

			//return $arKeys; // do not return yet, so the values will also be checked by the following code
		}

		// check keys
		$ar = array();
		foreach ($arKeys as $key) {
			if (!is_numeric($key))
				continue;
			$ar[] = $key;
		}
		return $ar;
	}

	// Get key filter
	function GetKeyFilter() {
		$arKeys = $this->GetRecordKeys();
		$sKeyFilter = "";
		foreach ($arKeys as $key) {
			if ($sKeyFilter <> "") $sKeyFilter .= " OR ";
			$this->id_proyek->CurrentValue = $key;
			$sKeyFilter .= "(" . $this->KeyFilter() . ")";
		}
		return $sKeyFilter;
	}

	// Load rows based on filter
	function &LoadRs($sFilter) {
		global $conn;

		// Set up filter (SQL WHERE clause) and get return SQL
		//$this->CurrentFilter = $sFilter;
		//$sSql = $this->SQL();

		$sSql = $this->GetSQL($sFilter, "");
		$rs = $conn->Execute($sSql);
		return $rs;
	}

	// Row Rendering event
	function Row_Rendering() {

		// Enter your code here	
	}

	// Row Rendered event
	function Row_Rendered() {

		// To view properties of field class, use:
		//var_dump($this-><FieldName>); 

	}

	// User ID Filtering event
	function UserID_Filtering(&$filter) {

		// Enter your code here
	}
}
?>
<?php include_once "lh_userinfo.php" ?>
<?php include_once "userfn9.php" ?>
<?php

//
// Page class
//

$Laporan_Pendapatan_Proyek_report = NULL; // Initialize page object first

class cLaporan_Pendapatan_Proyek_report extends cLaporan_Pendapatan_Proyek {

	// Page ID
	var $PageID = 'report';

	// Project ID
	var $ProjectID = "{67264FB2-6364-478B-87DD-B3E0D7A29425}";

	// Table name
	var $TableName = 'Laporan Pendapatan Proyek';

	// Page object name
	var $PageObjName = 'Laporan_Pendapatan_Proyek_report';

	// Page name
	function PageName() {
		return ew_CurrentPage();
	}

	// Page URL
	function PageUrl() {
		$PageUrl = ew_CurrentPage() . "?";
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
		return TRUE;
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

		// Table object (Laporan_Pendapatan_Proyek)
		if (!isset($GLOBALS["Laporan_Pendapatan_Proyek"])) {
			$GLOBALS["Laporan_Pendapatan_Proyek"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["Laporan_Pendapatan_Proyek"];
		}

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";

		// Table object (lh_user)
		if (!isset($GLOBALS['lh_user'])) $GLOBALS['lh_user'] = new clh_user();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'report', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'Laporan Pendapatan Proyek', TRUE);

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
		if (!$Security->CanReport()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage($Language->Phrase("NoPermission")); // Set no permission
			$this->Page_Terminate("login.php");
		}

		// Get export parameters
		if (@$_GET["export"] <> "") {
			$this->Export = $_GET["export"];
		}
		$gsExport = $this->Export; // Get export parameter, used in header
		$gsExportFile = $this->TableVar; // Get export file, used in header

		// Setup export options
		$this->SetupExportOptions();

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
		global $EW_EXPORT_REPORT;

		// Page Unload event
		$this->Page_Unload();

		// Export
		if ($this->Export <> "" && array_key_exists($this->Export, $EW_EXPORT_REPORT)) {
			$sContent = ob_get_contents();
			$fn = $EW_EXPORT_REPORT[$this->Export];
			$this->$fn($sContent);
			if ($this->Export == "email") { // Email
				ob_end_clean();
				$conn->Close(); // Close connection
				header("Location: " . ew_CurrentPage());
				exit();
			}
		}

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
	var $RecCnt = 0;
	var $ReportSql = "";
	var $ReportFilter = "";
	var $DefaultFilter = "";
	var $DbMasterFilter = "";
	var $DbDetailFilter = "";
	var $MasterRecordExists;
	var $Command;
	var $DtlRecordCount;
	var $ReportGroups;
	var $ReportCounts;
	var $LevelBreak;
	var $ReportTotals;
	var $ReportMaxs;
	var $ReportMins;
	var $Recordset;
	var $DetailRecordset;

	//
	// Page main
	//
	function Page_Main() {
		global $Language;
		$this->ReportGroups = &ew_InitArray(1, NULL);
		$this->ReportCounts = &ew_InitArray(1, 0);
		$this->LevelBreak = &ew_InitArray(1, FALSE);
		$this->ReportTotals = &ew_Init2DArray(1, 7, 0);
		$this->ReportMaxs = &ew_Init2DArray(1, 7, 0);
		$this->ReportMins = &ew_Init2DArray(1, 7, 0);
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

		// Hide options for export/action
		if ($this->Export <> "")
			$this->ExportOptions->HideAllOptions();
	}

	// Export report to EXCEL
	function ExportReportExcel($html) {
		global $gsExportFile;
		header('Content-Type: application/vnd.ms-excel' . (EW_CHARSET <> '' ? ';charset=' . EW_CHARSET : ''));
		header('Content-Disposition: attachment; filename=' . $gsExportFile . '.xls');
		echo $html;
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
if (!isset($Laporan_Pendapatan_Proyek_report)) $Laporan_Pendapatan_Proyek_report = new cLaporan_Pendapatan_Proyek_report();

// Page init
$Laporan_Pendapatan_Proyek_report->Page_Init();

// Page main
$Laporan_Pendapatan_Proyek_report->Page_Main();
?>
<?php include_once "header.php" ?>
<?php if ($Laporan_Pendapatan_Proyek->Export == "") { ?>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php } ?>
<?php if ($Laporan_Pendapatan_Proyek->Export == "") { ?>
<?php } ?>
<p><span id="ewPageCaption" class="ewTitle ewReportTitle"><?php echo $Language->Phrase("TblTypeReport") ?><?php echo $Laporan_Pendapatan_Proyek->TableCaption() ?>
&nbsp;&nbsp;</span><?php $Laporan_Pendapatan_Proyek_report->ExportOptions->Render("body"); ?>
</p>
<?php $Laporan_Pendapatan_Proyek_report->ShowPageHeader(); ?>
<form method="post">
<table class="ewReportTable">
<?php
$Laporan_Pendapatan_Proyek_report->RecCnt = 1; // No grouping
if ($Laporan_Pendapatan_Proyek_report->DbDetailFilter <> "") {
	if ($Laporan_Pendapatan_Proyek_report->ReportFilter <> "") $Laporan_Pendapatan_Proyek_report->ReportFilter .= " AND ";
	$Laporan_Pendapatan_Proyek_report->ReportFilter .= "(" . $Laporan_Pendapatan_Proyek_report->DbDetailFilter . ")";
}

	// Get detail records
	$Laporan_Pendapatan_Proyek_report->ReportFilter = $Laporan_Pendapatan_Proyek_report->DefaultFilter;
	if ($Laporan_Pendapatan_Proyek_report->DbDetailFilter <> "") {
		if ($Laporan_Pendapatan_Proyek_report->ReportFilter <> "")
			$Laporan_Pendapatan_Proyek_report->ReportFilter .= " AND ";
		$Laporan_Pendapatan_Proyek_report->ReportFilter .= "(" . $Laporan_Pendapatan_Proyek_report->DbDetailFilter . ")";
	}
	if (!$Security->CanReport()) {
		if ($sFilter <> "") $sFilter .= " AND ";
		$sFilter .= "(0=1)";
	}

	// Set up detail SQL
	$Laporan_Pendapatan_Proyek->CurrentFilter = $Laporan_Pendapatan_Proyek_report->ReportFilter;
	$Laporan_Pendapatan_Proyek_report->ReportSql = $Laporan_Pendapatan_Proyek->DetailSQL();

	// Load detail records
	$Laporan_Pendapatan_Proyek_report->DetailRecordset = $conn->Execute($Laporan_Pendapatan_Proyek_report->ReportSql);
	$Laporan_Pendapatan_Proyek_report->DtlRecordCount = $Laporan_Pendapatan_Proyek_report->DetailRecordset->RecordCount();

	// Initialize aggregates
	if (!$Laporan_Pendapatan_Proyek_report->DetailRecordset->EOF) {
		$Laporan_Pendapatan_Proyek_report->RecCnt++;
	}
	if ($Laporan_Pendapatan_Proyek_report->RecCnt == 1) {
		$Laporan_Pendapatan_Proyek_report->ReportCounts[0] = 0;
	}
	$Laporan_Pendapatan_Proyek_report->ReportCounts[0] += $Laporan_Pendapatan_Proyek_report->DtlRecordCount;
?>
	<tr>
		<td class="ewGroupHeader"><span class="phpmaker"><?php echo $Laporan_Pendapatan_Proyek->nama_proyek->FldCaption() ?></span></td>
		<td class="ewGroupHeader"><span class="phpmaker"><?php echo $Laporan_Pendapatan_Proyek->tanggal->FldCaption() ?></span></td>
		<td class="ewGroupHeader"><span class="phpmaker"><?php echo $Laporan_Pendapatan_Proyek->kardus->FldCaption() ?></span></td>
		<td class="ewGroupHeader"><span class="phpmaker"><?php echo $Laporan_Pendapatan_Proyek->kayu->FldCaption() ?></span></td>
		<td class="ewGroupHeader"><span class="phpmaker"><?php echo $Laporan_Pendapatan_Proyek->besi->FldCaption() ?></span></td>
		<td class="ewGroupHeader"><span class="phpmaker"><?php echo $Laporan_Pendapatan_Proyek->harga->FldCaption() ?></span></td>
	</tr>
<?php
	while (!$Laporan_Pendapatan_Proyek_report->DetailRecordset->EOF) {
		$Laporan_Pendapatan_Proyek->nama_proyek->setDbValue($Laporan_Pendapatan_Proyek_report->DetailRecordset->fields('nama_proyek'));
		$Laporan_Pendapatan_Proyek->tanggal->setDbValue($Laporan_Pendapatan_Proyek_report->DetailRecordset->fields('tanggal'));
		$Laporan_Pendapatan_Proyek->kardus->setDbValue($Laporan_Pendapatan_Proyek_report->DetailRecordset->fields('kardus'));
		$Laporan_Pendapatan_Proyek->kayu->setDbValue($Laporan_Pendapatan_Proyek_report->DetailRecordset->fields('kayu'));
		$Laporan_Pendapatan_Proyek->besi->setDbValue($Laporan_Pendapatan_Proyek_report->DetailRecordset->fields('besi'));
		$Laporan_Pendapatan_Proyek->harga->setDbValue($Laporan_Pendapatan_Proyek_report->DetailRecordset->fields('harga'));

		// Render for view
		$Laporan_Pendapatan_Proyek->RowType = EW_ROWTYPE_VIEW;
		$Laporan_Pendapatan_Proyek->ResetAttrs();
		$Laporan_Pendapatan_Proyek_report->RenderRow();
?>
	<tr>
		<td<?php echo $Laporan_Pendapatan_Proyek->nama_proyek->CellAttributes() ?>><span class="phpmaker">
<span<?php echo $Laporan_Pendapatan_Proyek->nama_proyek->ViewAttributes() ?>>
<?php echo $Laporan_Pendapatan_Proyek->nama_proyek->ViewValue ?></span>
</span></td>
		<td<?php echo $Laporan_Pendapatan_Proyek->tanggal->CellAttributes() ?>><span class="phpmaker">
<span<?php echo $Laporan_Pendapatan_Proyek->tanggal->ViewAttributes() ?>>
<?php echo $Laporan_Pendapatan_Proyek->tanggal->ViewValue ?></span>
</span></td>
		<td<?php echo $Laporan_Pendapatan_Proyek->kardus->CellAttributes() ?>><span class="phpmaker">
<span<?php echo $Laporan_Pendapatan_Proyek->kardus->ViewAttributes() ?>>
<?php echo $Laporan_Pendapatan_Proyek->kardus->ViewValue ?></span>
</span></td>
		<td<?php echo $Laporan_Pendapatan_Proyek->kayu->CellAttributes() ?>><span class="phpmaker">
<span<?php echo $Laporan_Pendapatan_Proyek->kayu->ViewAttributes() ?>>
<?php echo $Laporan_Pendapatan_Proyek->kayu->ViewValue ?></span>
</span></td>
		<td<?php echo $Laporan_Pendapatan_Proyek->besi->CellAttributes() ?>><span class="phpmaker">
<span<?php echo $Laporan_Pendapatan_Proyek->besi->ViewAttributes() ?>>
<?php echo $Laporan_Pendapatan_Proyek->besi->ViewValue ?></span>
</span></td>
		<td<?php echo $Laporan_Pendapatan_Proyek->harga->CellAttributes() ?>><span class="phpmaker">
<span<?php echo $Laporan_Pendapatan_Proyek->harga->ViewAttributes() ?>>
<?php echo $Laporan_Pendapatan_Proyek->harga->ViewValue ?></span>
</span></td>
	</tr>
<?php
		$Laporan_Pendapatan_Proyek_report->DetailRecordset->MoveNext();
	}
	$Laporan_Pendapatan_Proyek_report->DetailRecordset->Close();
?>
	<tr><td colspan=6><span class="phpmaker">&nbsp;<br></span></td></tr>
	<tr><td colspan=6 class="ewGrandSummary"><span class="phpmaker"><?php echo $Language->Phrase("RptGrandTotal") ?>&nbsp;(<?php echo ew_FormatNumber($Laporan_Pendapatan_Proyek_report->ReportCounts[0], 0) ?>&nbsp;<?php echo $Language->Phrase("RptDtlRec") ?>)</span></td></tr>
	<tr><td colspan=6><span class="phpmaker">&nbsp;<br></span></td></tr>
</table>
</form>
<?php
$Laporan_Pendapatan_Proyek_report->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<?php if ($Laporan_Pendapatan_Proyek->Export == "") { ?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php } ?>
<?php include_once "footer.php" ?>
<?php
$Laporan_Pendapatan_Proyek_report->Page_Terminate();
?>
