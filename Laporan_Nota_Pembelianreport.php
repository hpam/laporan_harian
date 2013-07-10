<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg9.php" ?>
<?php include_once "ewmysql9.php" ?>
<?php include_once "phpfn9.php" ?>
<?php

// Global variable for table object
$Laporan_Nota_Pembelian = NULL;

//
// Table class for Laporan Nota Pembelian
//
class cLaporan_Nota_Pembelian extends cTableBase {
	var $id_nota;
	var $id_dana;
	var $nomor_nota;
	var $nama_toko;
	var $tanggal_nota;
	var $jumlah_pembelian;
	var $pesan;

	//
	// Table class constructor
	//
	function __construct() {
		global $Language;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();
		$this->TableVar = 'Laporan_Nota_Pembelian';
		$this->TableName = 'Laporan Nota Pembelian';
		$this->TableType = 'REPORT';
		$this->ExportAll = TRUE;
		$this->ExportPageBreakCount = 0; // Page break per every n record (PDF only)
		$this->ExportPageOrientation = "portrait"; // Page orientation (PDF only)
		$this->ExportPageSize = "a4"; // Page size (PDF only)
		$this->UserIDAllowSecurity = 0; // User ID Allow

		// id_nota
		$this->id_nota = new cField('Laporan_Nota_Pembelian', 'Laporan Nota Pembelian', 'x_id_nota', 'id_nota', '`id_nota`', '`id_nota`', 3, -1, FALSE, '`id_nota`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->id_nota->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['id_nota'] = &$this->id_nota;

		// id_dana
		$this->id_dana = new cField('Laporan_Nota_Pembelian', 'Laporan Nota Pembelian', 'x_id_dana', 'id_dana', '`id_dana`', '`id_dana`', 3, 7, FALSE, '`id_dana`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->id_dana->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['id_dana'] = &$this->id_dana;

		// nomor_nota
		$this->nomor_nota = new cField('Laporan_Nota_Pembelian', 'Laporan Nota Pembelian', 'x_nomor_nota', 'nomor_nota', '`nomor_nota`', '`nomor_nota`', 200, -1, FALSE, '`nomor_nota`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['nomor_nota'] = &$this->nomor_nota;

		// nama_toko
		$this->nama_toko = new cField('Laporan_Nota_Pembelian', 'Laporan Nota Pembelian', 'x_nama_toko', 'nama_toko', '`nama_toko`', '`nama_toko`', 200, -1, FALSE, '`nama_toko`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['nama_toko'] = &$this->nama_toko;

		// tanggal_nota
		$this->tanggal_nota = new cField('Laporan_Nota_Pembelian', 'Laporan Nota Pembelian', 'x_tanggal_nota', 'tanggal_nota', '`tanggal_nota`', 'DATE_FORMAT(`tanggal_nota`, \'%d/%m/%Y %H:%i:%s\')', 133, 7, FALSE, '`tanggal_nota`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->tanggal_nota->FldDefaultErrMsg = str_replace("%s", "/", $Language->Phrase("IncorrectDateDMY"));
		$this->fields['tanggal_nota'] = &$this->tanggal_nota;

		// jumlah_pembelian
		$this->jumlah_pembelian = new cField('Laporan_Nota_Pembelian', 'Laporan Nota Pembelian', 'x_jumlah_pembelian', 'jumlah_pembelian', '`jumlah_pembelian`', '`jumlah_pembelian`', 5, -1, FALSE, '`jumlah_pembelian`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->jumlah_pembelian->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['jumlah_pembelian'] = &$this->jumlah_pembelian;

		// pesan
		$this->pesan = new cField('Laporan_Nota_Pembelian', 'Laporan Nota Pembelian', 'x_pesan', 'pesan', '`pesan`', '`pesan`', 201, -1, FALSE, '`pesan`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['pesan'] = &$this->pesan;
	}

	// Report detail level SQL
	function SqlDetailSelect() { // Select
		return "SELECT * FROM `lh_nota`";
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
			return "Laporan_Nota_Pembelianreport.php";
		}
	}

	function setReturnUrl($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL] = $v;
	}

	// List URL
	function GetListUrl() {
		return "Laporan_Nota_Pembelianreport.php";
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
		if (!is_null($this->id_nota->CurrentValue)) {
			$sUrl .= "id_nota=" . urlencode($this->id_nota->CurrentValue);
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
			$arKeys[] = @$_GET["id_nota"]; // id_nota

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
			$this->id_nota->CurrentValue = $key;
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

$Laporan_Nota_Pembelian_report = NULL; // Initialize page object first

class cLaporan_Nota_Pembelian_report extends cLaporan_Nota_Pembelian {

	// Page ID
	var $PageID = 'report';

	// Project ID
	var $ProjectID = "{67264FB2-6364-478B-87DD-B3E0D7A29425}";

	// Table name
	var $TableName = 'Laporan Nota Pembelian';

	// Page object name
	var $PageObjName = 'Laporan_Nota_Pembelian_report';

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

		// Table object (Laporan_Nota_Pembelian)
		if (!isset($GLOBALS["Laporan_Nota_Pembelian"])) {
			$GLOBALS["Laporan_Nota_Pembelian"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["Laporan_Nota_Pembelian"];
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
			define("EW_TABLE_NAME", 'Laporan Nota Pembelian', TRUE);

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

		if ($this->jumlah_pembelian->FormValue == $this->jumlah_pembelian->CurrentValue && is_numeric(ew_StrToFloat($this->jumlah_pembelian->CurrentValue)))
			$this->jumlah_pembelian->CurrentValue = ew_StrToFloat($this->jumlah_pembelian->CurrentValue);

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

			// tanggal_nota
			$this->tanggal_nota->ViewValue = $this->tanggal_nota->CurrentValue;
			$this->tanggal_nota->ViewValue = ew_FormatDateTime($this->tanggal_nota->ViewValue, 7);
			$this->tanggal_nota->ViewCustomAttributes = "";

			// jumlah_pembelian
			$this->jumlah_pembelian->ViewValue = $this->jumlah_pembelian->CurrentValue;
			$this->jumlah_pembelian->ViewValue = ew_FormatNumber($this->jumlah_pembelian->ViewValue, 2, -1, -2, -1);
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
if (!isset($Laporan_Nota_Pembelian_report)) $Laporan_Nota_Pembelian_report = new cLaporan_Nota_Pembelian_report();

// Page init
$Laporan_Nota_Pembelian_report->Page_Init();

// Page main
$Laporan_Nota_Pembelian_report->Page_Main();
?>
<?php include_once "header.php" ?>
<?php if ($Laporan_Nota_Pembelian->Export == "") { ?>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php } ?>
<?php if ($Laporan_Nota_Pembelian->Export == "") { ?>
<?php } ?>
<p><span id="ewPageCaption" class="ewTitle ewReportTitle"><?php echo $Language->Phrase("TblTypeReport") ?><?php echo $Laporan_Nota_Pembelian->TableCaption() ?>
&nbsp;&nbsp;</span><?php $Laporan_Nota_Pembelian_report->ExportOptions->Render("body"); ?>
</p>
<?php $Laporan_Nota_Pembelian_report->ShowPageHeader(); ?>
<form method="post">
<table class="ewReportTable">
<?php
$Laporan_Nota_Pembelian_report->RecCnt = 1; // No grouping
if ($Laporan_Nota_Pembelian_report->DbDetailFilter <> "") {
	if ($Laporan_Nota_Pembelian_report->ReportFilter <> "") $Laporan_Nota_Pembelian_report->ReportFilter .= " AND ";
	$Laporan_Nota_Pembelian_report->ReportFilter .= "(" . $Laporan_Nota_Pembelian_report->DbDetailFilter . ")";
}

	// Get detail records
	$Laporan_Nota_Pembelian_report->ReportFilter = $Laporan_Nota_Pembelian_report->DefaultFilter;
	if ($Laporan_Nota_Pembelian_report->DbDetailFilter <> "") {
		if ($Laporan_Nota_Pembelian_report->ReportFilter <> "")
			$Laporan_Nota_Pembelian_report->ReportFilter .= " AND ";
		$Laporan_Nota_Pembelian_report->ReportFilter .= "(" . $Laporan_Nota_Pembelian_report->DbDetailFilter . ")";
	}
	if (!$Security->CanReport()) {
		if ($sFilter <> "") $sFilter .= " AND ";
		$sFilter .= "(0=1)";
	}

	// Set up detail SQL
	$Laporan_Nota_Pembelian->CurrentFilter = $Laporan_Nota_Pembelian_report->ReportFilter;
	$Laporan_Nota_Pembelian_report->ReportSql = $Laporan_Nota_Pembelian->DetailSQL();

	// Load detail records
	$Laporan_Nota_Pembelian_report->DetailRecordset = $conn->Execute($Laporan_Nota_Pembelian_report->ReportSql);
	$Laporan_Nota_Pembelian_report->DtlRecordCount = $Laporan_Nota_Pembelian_report->DetailRecordset->RecordCount();

	// Initialize aggregates
	if (!$Laporan_Nota_Pembelian_report->DetailRecordset->EOF) {
		$Laporan_Nota_Pembelian_report->RecCnt++;
	}
	if ($Laporan_Nota_Pembelian_report->RecCnt == 1) {
		$Laporan_Nota_Pembelian_report->ReportCounts[0] = 0;
	}
	$Laporan_Nota_Pembelian_report->ReportCounts[0] += $Laporan_Nota_Pembelian_report->DtlRecordCount;
?>
	<tr>
		<td class="ewGroupHeader"><span class="phpmaker"><?php echo $Laporan_Nota_Pembelian->id_dana->FldCaption() ?></span></td>
		<td class="ewGroupHeader"><span class="phpmaker"><?php echo $Laporan_Nota_Pembelian->nomor_nota->FldCaption() ?></span></td>
		<td class="ewGroupHeader"><span class="phpmaker"><?php echo $Laporan_Nota_Pembelian->nama_toko->FldCaption() ?></span></td>
		<td class="ewGroupHeader"><span class="phpmaker"><?php echo $Laporan_Nota_Pembelian->tanggal_nota->FldCaption() ?></span></td>
		<td class="ewGroupHeader"><span class="phpmaker"><?php echo $Laporan_Nota_Pembelian->jumlah_pembelian->FldCaption() ?></span></td>
		<td class="ewGroupHeader"><span class="phpmaker"><?php echo $Laporan_Nota_Pembelian->pesan->FldCaption() ?></span></td>
	</tr>
<?php
	while (!$Laporan_Nota_Pembelian_report->DetailRecordset->EOF) {
		$Laporan_Nota_Pembelian->id_dana->setDbValue($Laporan_Nota_Pembelian_report->DetailRecordset->fields('id_dana'));
		$Laporan_Nota_Pembelian->nomor_nota->setDbValue($Laporan_Nota_Pembelian_report->DetailRecordset->fields('nomor_nota'));
		$Laporan_Nota_Pembelian->nama_toko->setDbValue($Laporan_Nota_Pembelian_report->DetailRecordset->fields('nama_toko'));
		$Laporan_Nota_Pembelian->tanggal_nota->setDbValue($Laporan_Nota_Pembelian_report->DetailRecordset->fields('tanggal_nota'));
		$Laporan_Nota_Pembelian->jumlah_pembelian->setDbValue($Laporan_Nota_Pembelian_report->DetailRecordset->fields('jumlah_pembelian'));
		$Laporan_Nota_Pembelian->pesan->setDbValue($Laporan_Nota_Pembelian_report->DetailRecordset->fields('pesan'));

		// Render for view
		$Laporan_Nota_Pembelian->RowType = EW_ROWTYPE_VIEW;
		$Laporan_Nota_Pembelian->ResetAttrs();
		$Laporan_Nota_Pembelian_report->RenderRow();
?>
	<tr>
		<td<?php echo $Laporan_Nota_Pembelian->id_dana->CellAttributes() ?>><span class="phpmaker">
<span<?php echo $Laporan_Nota_Pembelian->id_dana->ViewAttributes() ?>>
<?php echo $Laporan_Nota_Pembelian->id_dana->ViewValue ?></span>
</span></td>
		<td<?php echo $Laporan_Nota_Pembelian->nomor_nota->CellAttributes() ?>><span class="phpmaker">
<span<?php echo $Laporan_Nota_Pembelian->nomor_nota->ViewAttributes() ?>>
<?php echo $Laporan_Nota_Pembelian->nomor_nota->ViewValue ?></span>
</span></td>
		<td<?php echo $Laporan_Nota_Pembelian->nama_toko->CellAttributes() ?>><span class="phpmaker">
<span<?php echo $Laporan_Nota_Pembelian->nama_toko->ViewAttributes() ?>>
<?php echo $Laporan_Nota_Pembelian->nama_toko->ViewValue ?></span>
</span></td>
		<td<?php echo $Laporan_Nota_Pembelian->tanggal_nota->CellAttributes() ?>><span class="phpmaker">
<span<?php echo $Laporan_Nota_Pembelian->tanggal_nota->ViewAttributes() ?>>
<?php echo $Laporan_Nota_Pembelian->tanggal_nota->ViewValue ?></span>
</span></td>
		<td<?php echo $Laporan_Nota_Pembelian->jumlah_pembelian->CellAttributes() ?>><span class="phpmaker">
<span<?php echo $Laporan_Nota_Pembelian->jumlah_pembelian->ViewAttributes() ?>>
<?php echo $Laporan_Nota_Pembelian->jumlah_pembelian->ViewValue ?></span>
</span></td>
		<td<?php echo $Laporan_Nota_Pembelian->pesan->CellAttributes() ?>><span class="phpmaker">
<span<?php echo $Laporan_Nota_Pembelian->pesan->ViewAttributes() ?>>
<?php echo $Laporan_Nota_Pembelian->pesan->ViewValue ?></span>
</span></td>
	</tr>
<?php
		$Laporan_Nota_Pembelian_report->DetailRecordset->MoveNext();
	}
	$Laporan_Nota_Pembelian_report->DetailRecordset->Close();
?>
	<tr><td colspan=6><span class="phpmaker">&nbsp;<br></span></td></tr>
	<tr><td colspan=6 class="ewGrandSummary"><span class="phpmaker"><?php echo $Language->Phrase("RptGrandTotal") ?>&nbsp;(<?php echo ew_FormatNumber($Laporan_Nota_Pembelian_report->ReportCounts[0], 0) ?>&nbsp;<?php echo $Language->Phrase("RptDtlRec") ?>)</span></td></tr>
	<tr><td colspan=6><span class="phpmaker">&nbsp;<br></span></td></tr>
</table>
</form>
<?php
$Laporan_Nota_Pembelian_report->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<?php if ($Laporan_Nota_Pembelian->Export == "") { ?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php } ?>
<?php include_once "footer.php" ?>
<?php
$Laporan_Nota_Pembelian_report->Page_Terminate();
?>
