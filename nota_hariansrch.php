<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg9.php" ?>
<?php include_once "ewmysql9.php" ?>
<?php include_once "phpfn9.php" ?>
<?php include_once "nota_harianinfo.php" ?>
<?php include_once "userfn9.php" ?>
<?php

//
// Page class
//

$nota_harian_search = NULL; // Initialize page object first

class cnota_harian_search extends cnota_harian {

	// Page ID
	var $PageID = 'search';

	// Project ID
	var $ProjectID = "{67264FB2-6364-478B-87DD-B3E0D7A29425}";

	// Table name
	var $TableName = 'nota harian';

	// Page object name
	var $PageObjName = 'nota_harian_search';

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

		// Table object (nota_harian)
		if (!isset($GLOBALS["nota_harian"])) {
			$GLOBALS["nota_harian"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["nota_harian"];
		}

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'search', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'nota harian', TRUE);

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
		$this->id_nota->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

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
						$this->Page_Terminate("nota_harianlist.php" . "?" . $sSrchStr); // Go to list page
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
		$this->BuildSearchUrl($sSrchUrl, $this->id_nota); // id_nota
		$this->BuildSearchUrl($sSrchUrl, $this->id_dana); // id_dana
		$this->BuildSearchUrl($sSrchUrl, $this->nomor_nota); // nomor_nota
		$this->BuildSearchUrl($sSrchUrl, $this->nama_toko); // nama_toko
		$this->BuildSearchUrl($sSrchUrl, $this->tanggal_nota); // tanggal_nota
		$this->BuildSearchUrl($sSrchUrl, $this->jumlah_pembelian); // jumlah_pembelian
		$this->BuildSearchUrl($sSrchUrl, $this->pesan); // pesan
		$this->BuildSearchUrl($sSrchUrl, $this->jumlah_dana); // jumlah_dana
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
		// id_nota

		$this->id_nota->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_id_nota"));
		$this->id_nota->AdvancedSearch->SearchOperator = $objForm->GetValue("z_id_nota");

		// id_dana
		$this->id_dana->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_id_dana"));
		$this->id_dana->AdvancedSearch->SearchOperator = $objForm->GetValue("z_id_dana");

		// nomor_nota
		$this->nomor_nota->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_nomor_nota"));
		$this->nomor_nota->AdvancedSearch->SearchOperator = $objForm->GetValue("z_nomor_nota");

		// nama_toko
		$this->nama_toko->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_nama_toko"));
		$this->nama_toko->AdvancedSearch->SearchOperator = $objForm->GetValue("z_nama_toko");

		// tanggal_nota
		$this->tanggal_nota->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_tanggal_nota"));
		$this->tanggal_nota->AdvancedSearch->SearchOperator = $objForm->GetValue("z_tanggal_nota");
		$this->tanggal_nota->AdvancedSearch->SearchCondition = $objForm->GetValue("v_tanggal_nota");
		$this->tanggal_nota->AdvancedSearch->SearchValue2 = ew_StripSlashes($objForm->GetValue("y_tanggal_nota"));
		$this->tanggal_nota->AdvancedSearch->SearchOperator2 = $objForm->GetValue("w_tanggal_nota");

		// jumlah_pembelian
		$this->jumlah_pembelian->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_jumlah_pembelian"));
		$this->jumlah_pembelian->AdvancedSearch->SearchOperator = $objForm->GetValue("z_jumlah_pembelian");

		// pesan
		$this->pesan->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_pesan"));
		$this->pesan->AdvancedSearch->SearchOperator = $objForm->GetValue("z_pesan");

		// jumlah_dana
		$this->jumlah_dana->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_jumlah_dana"));
		$this->jumlah_dana->AdvancedSearch->SearchOperator = $objForm->GetValue("z_jumlah_dana");
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
		// jumlah_dana

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
			$this->jumlah_pembelian->ViewCustomAttributes = "";

			// pesan
			$this->pesan->ViewValue = $this->pesan->CurrentValue;
			$this->pesan->ViewCustomAttributes = "";

			// jumlah_dana
			$this->jumlah_dana->ViewValue = $this->jumlah_dana->CurrentValue;
			$this->jumlah_dana->ViewCustomAttributes = "";

			// id_nota
			$this->id_nota->LinkCustomAttributes = "";
			$this->id_nota->HrefValue = "";
			$this->id_nota->TooltipValue = "";

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

			// jumlah_dana
			$this->jumlah_dana->LinkCustomAttributes = "";
			$this->jumlah_dana->HrefValue = "";
			$this->jumlah_dana->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_SEARCH) { // Search row

			// id_nota
			$this->id_nota->EditCustomAttributes = "";
			$this->id_nota->EditValue = ew_HtmlEncode($this->id_nota->AdvancedSearch->SearchValue);

			// id_dana
			$this->id_dana->EditCustomAttributes = "";
			$sFilterWrk = "";
			$sSqlWrk = "SELECT `id_dana`, `periode_pembiayaan` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `lh_dana`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$rowswrk = count($arwrk);
			for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
				$arwrk[$rowcntwrk][1] = ew_FormatDateTime($arwrk[$rowcntwrk][1], 7);
			}
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->id_dana->EditValue = $arwrk;

			// nomor_nota
			$this->nomor_nota->EditCustomAttributes = "";
			$this->nomor_nota->EditValue = ew_HtmlEncode($this->nomor_nota->AdvancedSearch->SearchValue);

			// nama_toko
			$this->nama_toko->EditCustomAttributes = "";
			$this->nama_toko->EditValue = ew_HtmlEncode($this->nama_toko->AdvancedSearch->SearchValue);

			// tanggal_nota
			$this->tanggal_nota->EditCustomAttributes = "";
			$this->tanggal_nota->EditValue = ew_HtmlEncode(ew_FormatDateTime(ew_UnFormatDateTime($this->tanggal_nota->AdvancedSearch->SearchValue, 7), 7));
			$this->tanggal_nota->EditCustomAttributes = "";
			$this->tanggal_nota->EditValue2 = ew_HtmlEncode(ew_FormatDateTime(ew_UnFormatDateTime($this->tanggal_nota->AdvancedSearch->SearchValue2, 7), 7));

			// jumlah_pembelian
			$this->jumlah_pembelian->EditCustomAttributes = "";
			$this->jumlah_pembelian->EditValue = ew_HtmlEncode($this->jumlah_pembelian->AdvancedSearch->SearchValue);

			// pesan
			$this->pesan->EditCustomAttributes = "";
			$this->pesan->EditValue = ew_HtmlEncode($this->pesan->AdvancedSearch->SearchValue);

			// jumlah_dana
			$this->jumlah_dana->EditCustomAttributes = "";
			$this->jumlah_dana->EditValue = ew_HtmlEncode($this->jumlah_dana->AdvancedSearch->SearchValue);
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
		if (!ew_CheckInteger($this->id_nota->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->id_nota->FldErrMsg());
		}
		if (!ew_CheckEuroDate($this->tanggal_nota->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->tanggal_nota->FldErrMsg());
		}
		if (!ew_CheckEuroDate($this->tanggal_nota->AdvancedSearch->SearchValue2)) {
			ew_AddMessage($gsSearchError, $this->tanggal_nota->FldErrMsg());
		}
		if (!ew_CheckInteger($this->jumlah_pembelian->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->jumlah_pembelian->FldErrMsg());
		}
		if (!ew_CheckInteger($this->jumlah_dana->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->jumlah_dana->FldErrMsg());
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
		$this->id_nota->AdvancedSearch->Load();
		$this->id_dana->AdvancedSearch->Load();
		$this->nomor_nota->AdvancedSearch->Load();
		$this->nama_toko->AdvancedSearch->Load();
		$this->tanggal_nota->AdvancedSearch->Load();
		$this->jumlah_pembelian->AdvancedSearch->Load();
		$this->pesan->AdvancedSearch->Load();
		$this->jumlah_dana->AdvancedSearch->Load();
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
if (!isset($nota_harian_search)) $nota_harian_search = new cnota_harian_search();

// Page init
$nota_harian_search->Page_Init();

// Page main
$nota_harian_search->Page_Main();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var nota_harian_search = new ew_Page("nota_harian_search");
nota_harian_search.PageID = "search"; // Page ID
var EW_PAGE_ID = nota_harian_search.PageID; // For backward compatibility

// Form object
var fnota_hariansearch = new ew_Form("fnota_hariansearch");

// Form_CustomValidate event
fnota_hariansearch.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fnota_hariansearch.ValidateRequired = true;
<?php } else { ?>
fnota_hariansearch.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fnota_hariansearch.Lists["x_id_dana"] = {"LinkField":"x_id_dana","Ajax":null,"AutoFill":false,"DisplayFields":["x_periode_pembiayaan","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
// Validate function for search

fnota_hariansearch.Validate = function(fobj) {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	fobj = fobj || this.Form;
	this.PostAutoSuggest();
	var infix = "";
	elm = fobj.elements["x" + infix + "_id_nota"];
	if (elm && !ew_CheckInteger(elm.value))
		return ew_OnError(this, elm, "<?php echo ew_JsEncode2($nota_harian->id_nota->FldErrMsg()) ?>");
	elm = fobj.elements["x" + infix + "_tanggal_nota"];
	if (elm && !ew_CheckEuroDate(elm.value))
		return ew_OnError(this, elm, "<?php echo ew_JsEncode2($nota_harian->tanggal_nota->FldErrMsg()) ?>");
	elm = fobj.elements["x" + infix + "_jumlah_pembelian"];
	if (elm && !ew_CheckInteger(elm.value))
		return ew_OnError(this, elm, "<?php echo ew_JsEncode2($nota_harian->jumlah_pembelian->FldErrMsg()) ?>");
	elm = fobj.elements["x" + infix + "_jumlah_dana"];
	if (elm && !ew_CheckInteger(elm.value))
		return ew_OnError(this, elm, "<?php echo ew_JsEncode2($nota_harian->jumlah_dana->FldErrMsg()) ?>");

	// Set up row object
	ew_ElementsToRow(fobj, infix);

	// Fire Form_CustomValidate event
	if (!this.Form_CustomValidate(fobj))
		return false;
	return true;
}

// Form_CustomValidate event
fnota_hariansearch.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fnota_hariansearch.ValidateRequired = true; // uses JavaScript validation
<?php } else { ?>
fnota_hariansearch.ValidateRequired = false; // no JavaScript validation
<?php } ?>

// Dynamic selection lists
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<p><span id="ewPageCaption" class="ewTitle ewTableTitle"><?php echo $Language->Phrase("Search") ?>&nbsp;<?php echo $Language->Phrase("TblTypeVIEW") ?><?php echo $nota_harian->TableCaption() ?></span></p>
<p class="phpmaker"><a href="<?php echo $nota_harian->getReturnUrl() ?>" id="a_BackToList" class="ewLink"><?php echo $Language->Phrase("BackToList") ?></a></p>
<?php $nota_harian_search->ShowPageHeader(); ?>
<?php
$nota_harian_search->ShowMessage();
?>
<form name="fnota_hariansearch" id="fnota_hariansearch" class="ewForm" action="<?php echo ew_CurrentPage() ?>" method="post" onsubmit="return ewForms[this.id].Submit();">
<br>
<input type="hidden" name="t" value="nota_harian">
<input type="hidden" name="a_search" id="a_search" value="S">
<table cellspacing="0" class="ewGrid"><tr><td class="ewGridContent">
<div class="ewGridMiddlePanel">
<table id="tbl_nota_hariansearch" class="ewTable">
<?php if ($nota_harian->id_nota->Visible) { // id_nota ?>
	<tr id="r_id_nota"<?php echo $nota_harian->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_nota_harian_id_nota"><table class="ewTableHeaderBtn"><tr><td><?php echo $nota_harian->id_nota->FldCaption() ?></td></tr></table></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_id_nota" id="z_id_nota" value="="></span></td>
		<td<?php echo $nota_harian->id_nota->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_nota_harian_id_nota" class="phpmaker">
<input type="text" name="x_id_nota" id="x_id_nota" value="<?php echo $nota_harian->id_nota->EditValue ?>"<?php echo $nota_harian->id_nota->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php if ($nota_harian->id_dana->Visible) { // id_dana ?>
	<tr id="r_id_dana"<?php echo $nota_harian->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_nota_harian_id_dana"><table class="ewTableHeaderBtn"><tr><td><?php echo $nota_harian->id_dana->FldCaption() ?></td></tr></table></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_id_dana" id="z_id_dana" value="="></span></td>
		<td<?php echo $nota_harian->id_dana->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_nota_harian_id_dana" class="phpmaker">
<select id="x_id_dana" name="x_id_dana"<?php echo $nota_harian->id_dana->EditAttributes() ?>>
<?php
if (is_array($nota_harian->id_dana->EditValue)) {
	$arwrk = $nota_harian->id_dana->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($nota_harian->id_dana->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
fnota_hariansearch.Lists["x_id_dana"].Options = <?php echo (is_array($nota_harian->id_dana->EditValue)) ? ew_ArrayToJson($nota_harian->id_dana->EditValue, 1) : "[]" ?>;
</script>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php if ($nota_harian->nomor_nota->Visible) { // nomor_nota ?>
	<tr id="r_nomor_nota"<?php echo $nota_harian->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_nota_harian_nomor_nota"><table class="ewTableHeaderBtn"><tr><td><?php echo $nota_harian->nomor_nota->FldCaption() ?></td></tr></table></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_nomor_nota" id="z_nomor_nota" value="LIKE"></span></td>
		<td<?php echo $nota_harian->nomor_nota->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_nota_harian_nomor_nota" class="phpmaker">
<input type="text" name="x_nomor_nota" id="x_nomor_nota" size="30" maxlength="255" value="<?php echo $nota_harian->nomor_nota->EditValue ?>"<?php echo $nota_harian->nomor_nota->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php if ($nota_harian->nama_toko->Visible) { // nama_toko ?>
	<tr id="r_nama_toko"<?php echo $nota_harian->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_nota_harian_nama_toko"><table class="ewTableHeaderBtn"><tr><td><?php echo $nota_harian->nama_toko->FldCaption() ?></td></tr></table></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_nama_toko" id="z_nama_toko" value="LIKE"></span></td>
		<td<?php echo $nota_harian->nama_toko->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_nota_harian_nama_toko" class="phpmaker">
<input type="text" name="x_nama_toko" id="x_nama_toko" size="30" maxlength="255" value="<?php echo $nota_harian->nama_toko->EditValue ?>"<?php echo $nota_harian->nama_toko->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php if ($nota_harian->tanggal_nota->Visible) { // tanggal_nota ?>
	<tr id="r_tanggal_nota"<?php echo $nota_harian->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_nota_harian_tanggal_nota"><table class="ewTableHeaderBtn"><tr><td><?php echo $nota_harian->tanggal_nota->FldCaption() ?></td></tr></table></span></td>
		<td><span class="ewSearchOperator"><select name="z_tanggal_nota" id="z_tanggal_nota" onchange="ewForms['fnota_hariansearch'].SrchOprChanged(this);"><option value="="<?php echo ($nota_harian->tanggal_nota->AdvancedSearch->SearchOperator=="=") ? " selected=\"selected\"" : "" ?> ><?php echo $Language->Phrase("=") ?></option><option value="<>"<?php echo ($nota_harian->tanggal_nota->AdvancedSearch->SearchOperator=="<>") ? " selected=\"selected\"" : "" ?> ><?php echo $Language->Phrase("<>") ?></option><option value="<"<?php echo ($nota_harian->tanggal_nota->AdvancedSearch->SearchOperator=="<") ? " selected=\"selected\"" : "" ?> ><?php echo $Language->Phrase("<") ?></option><option value="<="<?php echo ($nota_harian->tanggal_nota->AdvancedSearch->SearchOperator=="<=") ? " selected=\"selected\"" : "" ?> ><?php echo $Language->Phrase("<=") ?></option><option value=">"<?php echo ($nota_harian->tanggal_nota->AdvancedSearch->SearchOperator==">") ? " selected=\"selected\"" : "" ?> ><?php echo $Language->Phrase(">") ?></option><option value=">="<?php echo ($nota_harian->tanggal_nota->AdvancedSearch->SearchOperator==">=") ? " selected=\"selected\"" : "" ?> ><?php echo $Language->Phrase(">=") ?></option><option value="BETWEEN"<?php echo ($nota_harian->tanggal_nota->AdvancedSearch->SearchOperator=="BETWEEN") ? " selected=\"selected\"" : "" ?> ><?php echo $Language->Phrase("BETWEEN") ?></option></select></span></td>
		<td<?php echo $nota_harian->tanggal_nota->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_nota_harian_tanggal_nota" class="phpmaker">
<input type="text" name="x_tanggal_nota" id="x_tanggal_nota" value="<?php echo $nota_harian->tanggal_nota->EditValue ?>"<?php echo $nota_harian->tanggal_nota->EditAttributes() ?>>
<?php if (!$nota_harian->tanggal_nota->ReadOnly && !$nota_harian->tanggal_nota->Disabled && @$nota_harian->tanggal_nota->EditAttrs["readonly"] == "" && @$nota_harian->tanggal_nota->EditAttrs["disabled"] == "") { ?>
&nbsp;<img src="phpimages/calendar.png" id="fnota_hariansearch$x_tanggal_nota$" name="fnota_hariansearch$x_tanggal_nota$" alt="<?php echo $Language->Phrase("PickDate") ?>" title="<?php echo $Language->Phrase("PickDate") ?>" class="ewCalendar" style="border: 0;">
<script type="text/javascript">
ew_CreateCalendar("fnota_hariansearch", "x_tanggal_nota", "%d/%m/%Y");
</script>
<?php } ?>
</span>
				<span class="ewSearchCond btw0_tanggal_nota"><label><input type="radio" name="v_tanggal_nota" id="v_tanggal_nota" value="AND"<?php if ($nota_harian->tanggal_nota->AdvancedSearch->SearchCondition <> "OR") echo " checked=\"checked\"" ?>><?php echo $Language->Phrase("AND") ?></label>&nbsp;<label><input type="radio" name="v_tanggal_nota" id="v_tanggal_nota" value="OR"<?php if ($nota_harian->tanggal_nota->AdvancedSearch->SearchCondition == "OR") echo " checked=\"checked\"" ?>><?php echo $Language->Phrase("OR") ?></label>&nbsp;</span>
				<span class="ewSearchCond btw1_tanggal_nota">&nbsp;<?php echo $Language->Phrase("AND") ?>&nbsp;</span>
				<span class="ewSearchOperator btw0_tanggal_nota"><select name="w_tanggal_nota" id="w_tanggal_nota" onchange="ewForms['fnota_hariansearch'].SrchOprChanged(this);"><option value="="<?php echo ($nota_harian->tanggal_nota->AdvancedSearch->SearchOperator2=="=") ? " selected=\"selected\"" : "" ?> ><?php echo $Language->Phrase("=") ?></option><option value="<>"<?php echo ($nota_harian->tanggal_nota->AdvancedSearch->SearchOperator2=="<>") ? " selected=\"selected\"" : "" ?> ><?php echo $Language->Phrase("<>") ?></option><option value="<"<?php echo ($nota_harian->tanggal_nota->AdvancedSearch->SearchOperator2=="<") ? " selected=\"selected\"" : "" ?> ><?php echo $Language->Phrase("<") ?></option><option value="<="<?php echo ($nota_harian->tanggal_nota->AdvancedSearch->SearchOperator2=="<=") ? " selected=\"selected\"" : "" ?> ><?php echo $Language->Phrase("<=") ?></option><option value=">"<?php echo ($nota_harian->tanggal_nota->AdvancedSearch->SearchOperator2==">") ? " selected=\"selected\"" : "" ?> ><?php echo $Language->Phrase(">") ?></option><option value=">="<?php echo ($nota_harian->tanggal_nota->AdvancedSearch->SearchOperator2==">=") ? " selected=\"selected\"" : "" ?> ><?php echo $Language->Phrase(">=") ?></option></select></span>
				<span id="e2_nota_harian_tanggal_nota" class="phpmaker">
<input type="text" name="y_tanggal_nota" id="y_tanggal_nota" value="<?php echo $nota_harian->tanggal_nota->EditValue2 ?>"<?php echo $nota_harian->tanggal_nota->EditAttributes() ?>>
<?php if (!$nota_harian->tanggal_nota->ReadOnly && !$nota_harian->tanggal_nota->Disabled && @$nota_harian->tanggal_nota->EditAttrs["readonly"] == "" && @$nota_harian->tanggal_nota->EditAttrs["disabled"] == "") { ?>
&nbsp;<img src="phpimages/calendar.png" id="fnota_hariansearch$y_tanggal_nota$" name="fnota_hariansearch$y_tanggal_nota$" alt="<?php echo $Language->Phrase("PickDate") ?>" title="<?php echo $Language->Phrase("PickDate") ?>" class="ewCalendar" style="border: 0;">
<script type="text/javascript">
ew_CreateCalendar("fnota_hariansearch", "y_tanggal_nota", "%d/%m/%Y");
</script>
<?php } ?>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php if ($nota_harian->jumlah_pembelian->Visible) { // jumlah_pembelian ?>
	<tr id="r_jumlah_pembelian"<?php echo $nota_harian->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_nota_harian_jumlah_pembelian"><table class="ewTableHeaderBtn"><tr><td><?php echo $nota_harian->jumlah_pembelian->FldCaption() ?></td></tr></table></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_jumlah_pembelian" id="z_jumlah_pembelian" value="="></span></td>
		<td<?php echo $nota_harian->jumlah_pembelian->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_nota_harian_jumlah_pembelian" class="phpmaker">
<input type="text" name="x_jumlah_pembelian" id="x_jumlah_pembelian" size="30" value="<?php echo $nota_harian->jumlah_pembelian->EditValue ?>"<?php echo $nota_harian->jumlah_pembelian->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php if ($nota_harian->pesan->Visible) { // pesan ?>
	<tr id="r_pesan"<?php echo $nota_harian->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_nota_harian_pesan"><table class="ewTableHeaderBtn"><tr><td><?php echo $nota_harian->pesan->FldCaption() ?></td></tr></table></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_pesan" id="z_pesan" value="LIKE"></span></td>
		<td<?php echo $nota_harian->pesan->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_nota_harian_pesan" class="phpmaker">
<textarea name="x_pesan" id="x_pesan" cols="35" rows="4"<?php echo $nota_harian->pesan->EditAttributes() ?>><?php echo $nota_harian->pesan->EditValue ?></textarea>
<script type="text/javascript">
ew_CreateEditor("fnota_hariansearch", "x_pesan", 35, 4, <?php echo ($nota_harian->pesan->ReadOnly || FALSE) ? "true" : "false" ?>);
</script>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php if ($nota_harian->jumlah_dana->Visible) { // jumlah_dana ?>
	<tr id="r_jumlah_dana"<?php echo $nota_harian->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_nota_harian_jumlah_dana"><table class="ewTableHeaderBtn"><tr><td><?php echo $nota_harian->jumlah_dana->FldCaption() ?></td></tr></table></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_jumlah_dana" id="z_jumlah_dana" value="="></span></td>
		<td<?php echo $nota_harian->jumlah_dana->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_nota_harian_jumlah_dana" class="phpmaker">
<input type="text" name="x_jumlah_dana" id="x_jumlah_dana" size="30" value="<?php echo $nota_harian->jumlah_dana->EditValue ?>"<?php echo $nota_harian->jumlah_dana->EditAttributes() ?>>
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
fnota_hariansearch.Init();
</script>
<?php
$nota_harian_search->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$nota_harian_search->Page_Terminate();
?>
