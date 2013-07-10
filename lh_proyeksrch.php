<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg9.php" ?>
<?php include_once "ewmysql9.php" ?>
<?php include_once "phpfn9.php" ?>
<?php include_once "lh_proyekinfo.php" ?>
<?php include_once "lh_userinfo.php" ?>
<?php include_once "userfn9.php" ?>
<?php

//
// Page class
//

$lh_proyek_search = NULL; // Initialize page object first

class clh_proyek_search extends clh_proyek {

	// Page ID
	var $PageID = 'search';

	// Project ID
	var $ProjectID = "{67264FB2-6364-478B-87DD-B3E0D7A29425}";

	// Table name
	var $TableName = 'lh_proyek';

	// Page object name
	var $PageObjName = 'lh_proyek_search';

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

		// Table object (lh_proyek)
		if (!isset($GLOBALS["lh_proyek"])) {
			$GLOBALS["lh_proyek"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["lh_proyek"];
		}

		// Table object (lh_user)
		if (!isset($GLOBALS['lh_user'])) $GLOBALS['lh_user'] = new clh_user();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'search', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'lh_proyek', TRUE);

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
		if (!$Security->CanSearch()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage($Language->Phrase("NoPermission")); // Set no permission
			$this->Page_Terminate("lh_proyeklist.php");
		}

		// Create form object
		$objForm = new cFormObj();
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

	//
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsSearchError;
		if ($this->IsPageRequest()) { // Validate request

			// Get action
			$this->CurrentAction = $objForm->GetValue("a_search");
			switch ($this->CurrentAction) {
				case "S": // Get search criteria

					// Build search string for advanced search, remove blank field
					$this->LoadSearchValues(); // Get search values
					if ($this->ValidateSearch()) {
						$sSrchStr = $this->BuildAdvancedSearch();
					} else {
						$sSrchStr = "";
						$this->setFailureMessage($gsSearchError);
					}
					if ($sSrchStr <> "") {
						$sSrchStr = $this->UrlParm($sSrchStr);
						$this->Page_Terminate("lh_proyeklist.php" . "?" . $sSrchStr); // Go to list page
					}
			}
		}

		// Restore search settings from Session
		if ($gsSearchError == "")
			$this->LoadAdvancedSearch();

		// Render row for search
		$this->RowType = EW_ROWTYPE_SEARCH;
		$this->ResetAttrs();
		$this->RenderRow();
	}

	// Build advanced search
	function BuildAdvancedSearch() {
		$sSrchUrl = "";
		$this->BuildSearchUrl($sSrchUrl, $this->id_proyek); // id_proyek
		$this->BuildSearchUrl($sSrchUrl, $this->nama_proyek); // nama_proyek
		$this->BuildSearchUrl($sSrchUrl, $this->tanggal); // tanggal
		$this->BuildSearchUrl($sSrchUrl, $this->kardus); // kardus
		$this->BuildSearchUrl($sSrchUrl, $this->kayu); // kayu
		$this->BuildSearchUrl($sSrchUrl, $this->besi); // besi
		$this->BuildSearchUrl($sSrchUrl, $this->harga); // harga
		if ($sSrchUrl <> "") $sSrchUrl .= "&";
		$sSrchUrl .= "cmd=search";
		return $sSrchUrl;
	}

	// Build search URL
	function BuildSearchUrl(&$Url, &$Fld, $OprOnly=FALSE) {
		global $objForm;
		$sWrk = "";
		$FldParm = substr($Fld->FldVar, 2);
		$FldVal = $objForm->GetValue("x_$FldParm");
		$FldOpr = $objForm->GetValue("z_$FldParm");
		$FldCond = $objForm->GetValue("v_$FldParm");
		$FldVal2 = $objForm->GetValue("y_$FldParm");
		$FldOpr2 = $objForm->GetValue("w_$FldParm");
		$FldVal = ew_StripSlashes($FldVal);
		if (is_array($FldVal)) $FldVal = implode(",", $FldVal);
		$FldVal2 = ew_StripSlashes($FldVal2);
		if (is_array($FldVal2)) $FldVal2 = implode(",", $FldVal2);
		$FldOpr = strtoupper(trim($FldOpr));
		$lFldDataType = ($Fld->FldIsVirtual) ? EW_DATATYPE_STRING : $Fld->FldDataType;
		if ($FldOpr == "BETWEEN") {
			$IsValidValue = ($lFldDataType <> EW_DATATYPE_NUMBER) ||
				($lFldDataType == EW_DATATYPE_NUMBER && $this->SearchValueIsNumeric($Fld, $FldVal) && $this->SearchValueIsNumeric($Fld, $FldVal2));
			if ($FldVal <> "" && $FldVal2 <> "" && $IsValidValue) {
				$sWrk = "x_" . $FldParm . "=" . urlencode($FldVal) .
					"&y_" . $FldParm . "=" . urlencode($FldVal2) .
					"&z_" . $FldParm . "=" . urlencode($FldOpr);
			}
		} else {
			$IsValidValue = ($lFldDataType <> EW_DATATYPE_NUMBER) ||
				($lFldDataType == EW_DATATYPE_NUMBER && $this->SearchValueIsNumeric($Fld, $FldVal));
			if ($FldVal <> "" && $IsValidValue && ew_IsValidOpr($FldOpr, $lFldDataType)) {
				$sWrk = "x_" . $FldParm . "=" . urlencode($FldVal) .
					"&z_" . $FldParm . "=" . urlencode($FldOpr);
			} elseif ($FldOpr == "IS NULL" || $FldOpr == "IS NOT NULL" || ($FldOpr <> "" && $OprOnly && ew_IsValidOpr($FldOpr, $lFldDataType))) {
				$sWrk = "z_" . $FldParm . "=" . urlencode($FldOpr);
			}
			$IsValidValue = ($lFldDataType <> EW_DATATYPE_NUMBER) ||
				($lFldDataType == EW_DATATYPE_NUMBER && $this->SearchValueIsNumeric($Fld, $FldVal2));
			if ($FldVal2 <> "" && $IsValidValue && ew_IsValidOpr($FldOpr2, $lFldDataType)) {
				if ($sWrk <> "") $sWrk .= "&v_" . $FldParm . "=" . urlencode($FldCond) . "&";
				$sWrk .= "y_" . $FldParm . "=" . urlencode($FldVal2) .
					"&w_" . $FldParm . "=" . urlencode($FldOpr2);
			} elseif ($FldOpr2 == "IS NULL" || $FldOpr2 == "IS NOT NULL" || ($FldOpr2 <> "" && $OprOnly && ew_IsValidOpr($FldOpr2, $lFldDataType))) {
				if ($sWrk <> "") $sWrk .= "&v_" . $FldParm . "=" . urlencode($FldCond) . "&";
				$sWrk .= "w_" . $FldParm . "=" . urlencode($FldOpr2);
			}
		}
		if ($sWrk <> "") {
			if ($Url <> "") $Url .= "&";
			$Url .= $sWrk;
		}
	}

	function SearchValueIsNumeric($Fld, $Value) {
		if (ew_IsFloatFormat($Fld->FldType)) $Value = ew_StrToFloat($Value);
		return is_numeric($Value);
	}

	//  Load search values for validation
	function LoadSearchValues() {
		global $objForm;

		// Load search values
		// id_proyek

		$this->id_proyek->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_id_proyek"));
		$this->id_proyek->AdvancedSearch->SearchOperator = $objForm->GetValue("z_id_proyek");

		// nama_proyek
		$this->nama_proyek->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_nama_proyek"));
		$this->nama_proyek->AdvancedSearch->SearchOperator = $objForm->GetValue("z_nama_proyek");

		// tanggal
		$this->tanggal->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_tanggal"));
		$this->tanggal->AdvancedSearch->SearchOperator = $objForm->GetValue("z_tanggal");
		$this->tanggal->AdvancedSearch->SearchCondition = $objForm->GetValue("v_tanggal");
		$this->tanggal->AdvancedSearch->SearchValue2 = ew_StripSlashes($objForm->GetValue("y_tanggal"));
		$this->tanggal->AdvancedSearch->SearchOperator2 = $objForm->GetValue("w_tanggal");

		// kardus
		$this->kardus->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_kardus"));
		$this->kardus->AdvancedSearch->SearchOperator = $objForm->GetValue("z_kardus");

		// kayu
		$this->kayu->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_kayu"));
		$this->kayu->AdvancedSearch->SearchOperator = $objForm->GetValue("z_kayu");

		// besi
		$this->besi->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_besi"));
		$this->besi->AdvancedSearch->SearchOperator = $objForm->GetValue("z_besi");

		// harga
		$this->harga->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_harga"));
		$this->harga->AdvancedSearch->SearchOperator = $objForm->GetValue("z_harga");
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
		} elseif ($this->RowType == EW_ROWTYPE_SEARCH) { // Search row

			// id_proyek
			$this->id_proyek->EditCustomAttributes = "";
			$this->id_proyek->EditValue = ew_HtmlEncode($this->id_proyek->AdvancedSearch->SearchValue);

			// nama_proyek
			$this->nama_proyek->EditCustomAttributes = "";
			$this->nama_proyek->EditValue = ew_HtmlEncode($this->nama_proyek->AdvancedSearch->SearchValue);

			// tanggal
			$this->tanggal->EditCustomAttributes = "";
			$this->tanggal->EditValue = ew_HtmlEncode(ew_FormatDateTime(ew_UnFormatDateTime($this->tanggal->AdvancedSearch->SearchValue, 7), 7));
			$this->tanggal->EditCustomAttributes = "";
			$this->tanggal->EditValue2 = ew_HtmlEncode(ew_FormatDateTime(ew_UnFormatDateTime($this->tanggal->AdvancedSearch->SearchValue2, 7), 7));

			// kardus
			$this->kardus->EditCustomAttributes = "";
			$this->kardus->EditValue = ew_HtmlEncode($this->kardus->AdvancedSearch->SearchValue);

			// kayu
			$this->kayu->EditCustomAttributes = "";
			$this->kayu->EditValue = ew_HtmlEncode($this->kayu->AdvancedSearch->SearchValue);

			// besi
			$this->besi->EditCustomAttributes = "";
			$this->besi->EditValue = ew_HtmlEncode($this->besi->AdvancedSearch->SearchValue);

			// harga
			$this->harga->EditCustomAttributes = "";
			$this->harga->EditValue = ew_HtmlEncode($this->harga->AdvancedSearch->SearchValue);
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
		if (!ew_CheckInteger($this->id_proyek->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->id_proyek->FldErrMsg());
		}
		if (!ew_CheckEuroDate($this->tanggal->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->tanggal->FldErrMsg());
		}
		if (!ew_CheckEuroDate($this->tanggal->AdvancedSearch->SearchValue2)) {
			ew_AddMessage($gsSearchError, $this->tanggal->FldErrMsg());
		}
		if (!ew_CheckInteger($this->kardus->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->kardus->FldErrMsg());
		}
		if (!ew_CheckInteger($this->kayu->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->kayu->FldErrMsg());
		}
		if (!ew_CheckInteger($this->besi->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->besi->FldErrMsg());
		}
		if (!ew_CheckNumber($this->harga->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->harga->FldErrMsg());
		}

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
		$this->id_proyek->AdvancedSearch->Load();
		$this->nama_proyek->AdvancedSearch->Load();
		$this->tanggal->AdvancedSearch->Load();
		$this->kardus->AdvancedSearch->Load();
		$this->kayu->AdvancedSearch->Load();
		$this->besi->AdvancedSearch->Load();
		$this->harga->AdvancedSearch->Load();
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
if (!isset($lh_proyek_search)) $lh_proyek_search = new clh_proyek_search();

// Page init
$lh_proyek_search->Page_Init();

// Page main
$lh_proyek_search->Page_Main();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var lh_proyek_search = new ew_Page("lh_proyek_search");
lh_proyek_search.PageID = "search"; // Page ID
var EW_PAGE_ID = lh_proyek_search.PageID; // For backward compatibility

// Form object
var flh_proyeksearch = new ew_Form("flh_proyeksearch");

// Form_CustomValidate event
flh_proyeksearch.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
flh_proyeksearch.ValidateRequired = true;
<?php } else { ?>
flh_proyeksearch.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
// Form object for search
// Validate function for search

flh_proyeksearch.Validate = function(fobj) {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	fobj = fobj || this.Form;
	this.PostAutoSuggest();
	var infix = "";
	elm = fobj.elements["x" + infix + "_id_proyek"];
	if (elm && !ew_CheckInteger(elm.value))
		return ew_OnError(this, elm, "<?php echo ew_JsEncode2($lh_proyek->id_proyek->FldErrMsg()) ?>");
	elm = fobj.elements["x" + infix + "_tanggal"];
	if (elm && !ew_CheckEuroDate(elm.value))
		return ew_OnError(this, elm, "<?php echo ew_JsEncode2($lh_proyek->tanggal->FldErrMsg()) ?>");
	elm = fobj.elements["x" + infix + "_kardus"];
	if (elm && !ew_CheckInteger(elm.value))
		return ew_OnError(this, elm, "<?php echo ew_JsEncode2($lh_proyek->kardus->FldErrMsg()) ?>");
	elm = fobj.elements["x" + infix + "_kayu"];
	if (elm && !ew_CheckInteger(elm.value))
		return ew_OnError(this, elm, "<?php echo ew_JsEncode2($lh_proyek->kayu->FldErrMsg()) ?>");
	elm = fobj.elements["x" + infix + "_besi"];
	if (elm && !ew_CheckInteger(elm.value))
		return ew_OnError(this, elm, "<?php echo ew_JsEncode2($lh_proyek->besi->FldErrMsg()) ?>");
	elm = fobj.elements["x" + infix + "_harga"];
	if (elm && !ew_CheckNumber(elm.value))
		return ew_OnError(this, elm, "<?php echo ew_JsEncode2($lh_proyek->harga->FldErrMsg()) ?>");

	// Set up row object
	ew_ElementsToRow(fobj, infix);

	// Fire Form_CustomValidate event
	if (!this.Form_CustomValidate(fobj))
		return false;
	return true;
}

// Form_CustomValidate event
flh_proyeksearch.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
flh_proyeksearch.ValidateRequired = true; // uses JavaScript validation
<?php } else { ?>
flh_proyeksearch.ValidateRequired = false; // no JavaScript validation
<?php } ?>

// Dynamic selection lists
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<p><span id="ewPageCaption" class="ewTitle ewTableTitle"><?php echo $Language->Phrase("Search") ?>&nbsp;<?php echo $Language->Phrase("TblTypeTABLE") ?><?php echo $lh_proyek->TableCaption() ?></span></p>
<p class="phpmaker"><a href="<?php echo $lh_proyek->getReturnUrl() ?>" id="a_BackToList" class="ewLink"><?php echo $Language->Phrase("BackToList") ?></a></p>
<?php $lh_proyek_search->ShowPageHeader(); ?>
<?php
$lh_proyek_search->ShowMessage();
?>
<form name="flh_proyeksearch" id="flh_proyeksearch" class="ewForm" action="<?php echo ew_CurrentPage() ?>" method="post" onsubmit="return ewForms[this.id].Submit();">
<br>
<input type="hidden" name="t" value="lh_proyek">
<input type="hidden" name="a_search" id="a_search" value="S">
<table cellspacing="0" class="ewGrid"><tr><td class="ewGridContent">
<div class="ewGridMiddlePanel">
<table id="tbl_lh_proyeksearch" class="ewTable">
<?php if ($lh_proyek->id_proyek->Visible) { // id_proyek ?>
	<tr id="r_id_proyek"<?php echo $lh_proyek->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_lh_proyek_id_proyek"><table class="ewTableHeaderBtn"><tr><td><?php echo $lh_proyek->id_proyek->FldCaption() ?></td></tr></table></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_id_proyek" id="z_id_proyek" value="="></span></td>
		<td<?php echo $lh_proyek->id_proyek->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_lh_proyek_id_proyek" class="phpmaker">
<input type="text" name="x_id_proyek" id="x_id_proyek" value="<?php echo $lh_proyek->id_proyek->EditValue ?>"<?php echo $lh_proyek->id_proyek->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php if ($lh_proyek->nama_proyek->Visible) { // nama_proyek ?>
	<tr id="r_nama_proyek"<?php echo $lh_proyek->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_lh_proyek_nama_proyek"><table class="ewTableHeaderBtn"><tr><td><?php echo $lh_proyek->nama_proyek->FldCaption() ?></td></tr></table></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_nama_proyek" id="z_nama_proyek" value="LIKE"></span></td>
		<td<?php echo $lh_proyek->nama_proyek->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_lh_proyek_nama_proyek" class="phpmaker">
<input type="text" name="x_nama_proyek" id="x_nama_proyek" size="30" maxlength="255" value="<?php echo $lh_proyek->nama_proyek->EditValue ?>"<?php echo $lh_proyek->nama_proyek->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php if ($lh_proyek->tanggal->Visible) { // tanggal ?>
	<tr id="r_tanggal"<?php echo $lh_proyek->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_lh_proyek_tanggal"><table class="ewTableHeaderBtn"><tr><td><?php echo $lh_proyek->tanggal->FldCaption() ?></td></tr></table></span></td>
		<td><span class="ewSearchOperator"><select name="z_tanggal" id="z_tanggal" onchange="ewForms['flh_proyeksearch'].SrchOprChanged(this);"><option value="="<?php echo ($lh_proyek->tanggal->AdvancedSearch->SearchOperator=="=") ? " selected=\"selected\"" : "" ?> ><?php echo $Language->Phrase("=") ?></option><option value="<>"<?php echo ($lh_proyek->tanggal->AdvancedSearch->SearchOperator=="<>") ? " selected=\"selected\"" : "" ?> ><?php echo $Language->Phrase("<>") ?></option><option value="<"<?php echo ($lh_proyek->tanggal->AdvancedSearch->SearchOperator=="<") ? " selected=\"selected\"" : "" ?> ><?php echo $Language->Phrase("<") ?></option><option value="<="<?php echo ($lh_proyek->tanggal->AdvancedSearch->SearchOperator=="<=") ? " selected=\"selected\"" : "" ?> ><?php echo $Language->Phrase("<=") ?></option><option value=">"<?php echo ($lh_proyek->tanggal->AdvancedSearch->SearchOperator==">") ? " selected=\"selected\"" : "" ?> ><?php echo $Language->Phrase(">") ?></option><option value=">="<?php echo ($lh_proyek->tanggal->AdvancedSearch->SearchOperator==">=") ? " selected=\"selected\"" : "" ?> ><?php echo $Language->Phrase(">=") ?></option><option value="BETWEEN"<?php echo ($lh_proyek->tanggal->AdvancedSearch->SearchOperator=="BETWEEN") ? " selected=\"selected\"" : "" ?> ><?php echo $Language->Phrase("BETWEEN") ?></option></select></span></td>
		<td<?php echo $lh_proyek->tanggal->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_lh_proyek_tanggal" class="phpmaker">
<input type="text" name="x_tanggal" id="x_tanggal" value="<?php echo $lh_proyek->tanggal->EditValue ?>"<?php echo $lh_proyek->tanggal->EditAttributes() ?>>
<?php if (!$lh_proyek->tanggal->ReadOnly && !$lh_proyek->tanggal->Disabled && @$lh_proyek->tanggal->EditAttrs["readonly"] == "" && @$lh_proyek->tanggal->EditAttrs["disabled"] == "") { ?>
&nbsp;<img src="phpimages/calendar.png" id="flh_proyeksearch$x_tanggal$" name="flh_proyeksearch$x_tanggal$" alt="<?php echo $Language->Phrase("PickDate") ?>" title="<?php echo $Language->Phrase("PickDate") ?>" class="ewCalendar" style="border: 0;">
<script type="text/javascript">
ew_CreateCalendar("flh_proyeksearch", "x_tanggal", "%d/%m/%Y");
</script>
<?php } ?>
</span>
				<span class="ewSearchCond btw0_tanggal"><label><input type="radio" name="v_tanggal" id="v_tanggal" value="AND"<?php if ($lh_proyek->tanggal->AdvancedSearch->SearchCondition <> "OR") echo " checked=\"checked\"" ?>><?php echo $Language->Phrase("AND") ?></label>&nbsp;<label><input type="radio" name="v_tanggal" id="v_tanggal" value="OR"<?php if ($lh_proyek->tanggal->AdvancedSearch->SearchCondition == "OR") echo " checked=\"checked\"" ?>><?php echo $Language->Phrase("OR") ?></label>&nbsp;</span>
				<span class="ewSearchCond btw1_tanggal">&nbsp;<?php echo $Language->Phrase("AND") ?>&nbsp;</span>
				<span class="ewSearchOperator btw0_tanggal"><select name="w_tanggal" id="w_tanggal" onchange="ewForms['flh_proyeksearch'].SrchOprChanged(this);"><option value="="<?php echo ($lh_proyek->tanggal->AdvancedSearch->SearchOperator2=="=") ? " selected=\"selected\"" : "" ?> ><?php echo $Language->Phrase("=") ?></option><option value="<>"<?php echo ($lh_proyek->tanggal->AdvancedSearch->SearchOperator2=="<>") ? " selected=\"selected\"" : "" ?> ><?php echo $Language->Phrase("<>") ?></option><option value="<"<?php echo ($lh_proyek->tanggal->AdvancedSearch->SearchOperator2=="<") ? " selected=\"selected\"" : "" ?> ><?php echo $Language->Phrase("<") ?></option><option value="<="<?php echo ($lh_proyek->tanggal->AdvancedSearch->SearchOperator2=="<=") ? " selected=\"selected\"" : "" ?> ><?php echo $Language->Phrase("<=") ?></option><option value=">"<?php echo ($lh_proyek->tanggal->AdvancedSearch->SearchOperator2==">") ? " selected=\"selected\"" : "" ?> ><?php echo $Language->Phrase(">") ?></option><option value=">="<?php echo ($lh_proyek->tanggal->AdvancedSearch->SearchOperator2==">=") ? " selected=\"selected\"" : "" ?> ><?php echo $Language->Phrase(">=") ?></option></select></span>
				<span id="e2_lh_proyek_tanggal" class="phpmaker">
<input type="text" name="y_tanggal" id="y_tanggal" value="<?php echo $lh_proyek->tanggal->EditValue2 ?>"<?php echo $lh_proyek->tanggal->EditAttributes() ?>>
<?php if (!$lh_proyek->tanggal->ReadOnly && !$lh_proyek->tanggal->Disabled && @$lh_proyek->tanggal->EditAttrs["readonly"] == "" && @$lh_proyek->tanggal->EditAttrs["disabled"] == "") { ?>
&nbsp;<img src="phpimages/calendar.png" id="flh_proyeksearch$y_tanggal$" name="flh_proyeksearch$y_tanggal$" alt="<?php echo $Language->Phrase("PickDate") ?>" title="<?php echo $Language->Phrase("PickDate") ?>" class="ewCalendar" style="border: 0;">
<script type="text/javascript">
ew_CreateCalendar("flh_proyeksearch", "y_tanggal", "%d/%m/%Y");
</script>
<?php } ?>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php if ($lh_proyek->kardus->Visible) { // kardus ?>
	<tr id="r_kardus"<?php echo $lh_proyek->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_lh_proyek_kardus"><table class="ewTableHeaderBtn"><tr><td><?php echo $lh_proyek->kardus->FldCaption() ?></td></tr></table></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_kardus" id="z_kardus" value="="></span></td>
		<td<?php echo $lh_proyek->kardus->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_lh_proyek_kardus" class="phpmaker">
<input type="text" name="x_kardus" id="x_kardus" size="30" value="<?php echo $lh_proyek->kardus->EditValue ?>"<?php echo $lh_proyek->kardus->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php if ($lh_proyek->kayu->Visible) { // kayu ?>
	<tr id="r_kayu"<?php echo $lh_proyek->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_lh_proyek_kayu"><table class="ewTableHeaderBtn"><tr><td><?php echo $lh_proyek->kayu->FldCaption() ?></td></tr></table></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_kayu" id="z_kayu" value="="></span></td>
		<td<?php echo $lh_proyek->kayu->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_lh_proyek_kayu" class="phpmaker">
<input type="text" name="x_kayu" id="x_kayu" size="30" value="<?php echo $lh_proyek->kayu->EditValue ?>"<?php echo $lh_proyek->kayu->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php if ($lh_proyek->besi->Visible) { // besi ?>
	<tr id="r_besi"<?php echo $lh_proyek->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_lh_proyek_besi"><table class="ewTableHeaderBtn"><tr><td><?php echo $lh_proyek->besi->FldCaption() ?></td></tr></table></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_besi" id="z_besi" value="="></span></td>
		<td<?php echo $lh_proyek->besi->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_lh_proyek_besi" class="phpmaker">
<input type="text" name="x_besi" id="x_besi" size="30" value="<?php echo $lh_proyek->besi->EditValue ?>"<?php echo $lh_proyek->besi->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php if ($lh_proyek->harga->Visible) { // harga ?>
	<tr id="r_harga"<?php echo $lh_proyek->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_lh_proyek_harga"><table class="ewTableHeaderBtn"><tr><td><?php echo $lh_proyek->harga->FldCaption() ?></td></tr></table></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_harga" id="z_harga" value="="></span></td>
		<td<?php echo $lh_proyek->harga->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_lh_proyek_harga" class="phpmaker">
<input type="text" name="x_harga" id="x_harga" size="30" value="<?php echo $lh_proyek->harga->EditValue ?>"<?php echo $lh_proyek->harga->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
</table>
</div>
</td></tr></table>
<br>
<input type="submit" name="Action" value="<?php echo ew_BtnCaption($Language->Phrase("Search")) ?>">
<input type="button" name="Reset" value="<?php echo ew_BtnCaption($Language->Phrase("Reset")) ?>" onclick="ew_ClearForm(this.form);">
</form>
<script type="text/javascript">
flh_proyeksearch.Init();
</script>
<?php
$lh_proyek_search->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$lh_proyek_search->Page_Terminate();
?>
