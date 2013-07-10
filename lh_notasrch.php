<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg9.php" ?>
<?php include_once "ewmysql9.php" ?>
<?php include_once "phpfn9.php" ?>
<?php include_once "lh_notainfo.php" ?>
<?php include_once "lh_userinfo.php" ?>
<?php include_once "userfn9.php" ?>
<?php

//
// Page class
//

$lh_nota_search = NULL; // Initialize page object first

class clh_nota_search extends clh_nota {

	// Page ID
	var $PageID = 'search';

	// Project ID
	var $ProjectID = "{67264FB2-6364-478B-87DD-B3E0D7A29425}";

	// Table name
	var $TableName = 'lh_nota';

	// Page object name
	var $PageObjName = 'lh_nota_search';

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

		// Table object (lh_nota)
		if (!isset($GLOBALS["lh_nota"])) {
			$GLOBALS["lh_nota"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["lh_nota"];
		}

		// Table object (lh_user)
		if (!isset($GLOBALS['lh_user'])) $GLOBALS['lh_user'] = new clh_user();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'search', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'lh_nota', TRUE);

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
			$this->Page_Terminate("lh_notalist.php");
		}

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
						$this->Page_Terminate("lh_notalist.php" . "?" . $sSrchStr); // Go to list page
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
		} elseif ($this->RowType == EW_ROWTYPE_SEARCH) { // Search row

			// id_nota
			$this->id_nota->EditCustomAttributes = "";
			$this->id_nota->EditValue = ew_HtmlEncode($this->id_nota->AdvancedSearch->SearchValue);

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
		if (!ew_CheckNumber($this->jumlah_pembelian->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->jumlah_pembelian->FldErrMsg());
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
if (!isset($lh_nota_search)) $lh_nota_search = new clh_nota_search();

// Page init
$lh_nota_search->Page_Init();

// Page main
$lh_nota_search->Page_Main();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var lh_nota_search = new ew_Page("lh_nota_search");
lh_nota_search.PageID = "search"; // Page ID
var EW_PAGE_ID = lh_nota_search.PageID; // For backward compatibility

// Form object
var flh_notasearch = new ew_Form("flh_notasearch");

// Form_CustomValidate event
flh_notasearch.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
flh_notasearch.ValidateRequired = true;
<?php } else { ?>
flh_notasearch.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
flh_notasearch.Lists["x_id_dana"] = {"LinkField":"x_id_dana","Ajax":null,"AutoFill":false,"DisplayFields":["x_periode_pembiayaan","x_jumlah_dana","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
// Validate function for search

flh_notasearch.Validate = function(fobj) {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	fobj = fobj || this.Form;
	this.PostAutoSuggest();
	var infix = "";
	elm = fobj.elements["x" + infix + "_id_nota"];
	if (elm && !ew_CheckInteger(elm.value))
		return ew_OnError(this, elm, "<?php echo ew_JsEncode2($lh_nota->id_nota->FldErrMsg()) ?>");
	elm = fobj.elements["x" + infix + "_tanggal_nota"];
	if (elm && !ew_CheckEuroDate(elm.value))
		return ew_OnError(this, elm, "<?php echo ew_JsEncode2($lh_nota->tanggal_nota->FldErrMsg()) ?>");
	elm = fobj.elements["x" + infix + "_jumlah_pembelian"];
	if (elm && !ew_CheckNumber(elm.value))
		return ew_OnError(this, elm, "<?php echo ew_JsEncode2($lh_nota->jumlah_pembelian->FldErrMsg()) ?>");

	// Set up row object
	ew_ElementsToRow(fobj, infix);

	// Fire Form_CustomValidate event
	if (!this.Form_CustomValidate(fobj))
		return false;
	return true;
}

// Form_CustomValidate event
flh_notasearch.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
flh_notasearch.ValidateRequired = true; // uses JavaScript validation
<?php } else { ?>
flh_notasearch.ValidateRequired = false; // no JavaScript validation
<?php } ?>

// Dynamic selection lists
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<p><span id="ewPageCaption" class="ewTitle ewTableTitle"><?php echo $Language->Phrase("Search") ?>&nbsp;<?php echo $Language->Phrase("TblTypeTABLE") ?><?php echo $lh_nota->TableCaption() ?></span></p>
<p class="phpmaker"><a href="<?php echo $lh_nota->getReturnUrl() ?>" id="a_BackToList" class="ewLink"><?php echo $Language->Phrase("BackToList") ?></a></p>
<?php $lh_nota_search->ShowPageHeader(); ?>
<?php
$lh_nota_search->ShowMessage();
?>
<form name="flh_notasearch" id="flh_notasearch" class="ewForm" action="<?php echo ew_CurrentPage() ?>" method="post" onsubmit="return ewForms[this.id].Submit();">
<br>
<input type="hidden" name="t" value="lh_nota">
<input type="hidden" name="a_search" id="a_search" value="S">
<table cellspacing="0" class="ewGrid"><tr><td class="ewGridContent">
<div class="ewGridMiddlePanel">
<table id="tbl_lh_notasearch" class="ewTable">
<?php if ($lh_nota->id_nota->Visible) { // id_nota ?>
	<tr id="r_id_nota"<?php echo $lh_nota->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_lh_nota_id_nota"><table class="ewTableHeaderBtn"><tr><td><?php echo $lh_nota->id_nota->FldCaption() ?></td></tr></table></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_id_nota" id="z_id_nota" value="="></span></td>
		<td<?php echo $lh_nota->id_nota->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_lh_nota_id_nota" class="phpmaker">
<input type="text" name="x_id_nota" id="x_id_nota" value="<?php echo $lh_nota->id_nota->EditValue ?>"<?php echo $lh_nota->id_nota->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php if ($lh_nota->id_dana->Visible) { // id_dana ?>
	<tr id="r_id_dana"<?php echo $lh_nota->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_lh_nota_id_dana"><table class="ewTableHeaderBtn"><tr><td><?php echo $lh_nota->id_dana->FldCaption() ?></td></tr></table></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_id_dana" id="z_id_dana" value="="></span></td>
		<td<?php echo $lh_nota->id_dana->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_lh_nota_id_dana" class="phpmaker">
<select id="x_id_dana" name="x_id_dana"<?php echo $lh_nota->id_dana->EditAttributes() ?>>
<?php
if (is_array($lh_nota->id_dana->EditValue)) {
	$arwrk = $lh_nota->id_dana->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($lh_nota->id_dana->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
<?php if ($arwrk[$rowcntwrk][2] <> "") { ?>
<?php echo ew_ValueSeparator(1,$lh_nota->id_dana) ?><?php echo $arwrk[$rowcntwrk][2] ?>
<?php } ?>
</option>
<?php
	}
}
?>
</select>
<script type="text/javascript">
flh_notasearch.Lists["x_id_dana"].Options = <?php echo (is_array($lh_nota->id_dana->EditValue)) ? ew_ArrayToJson($lh_nota->id_dana->EditValue, 1) : "[]" ?>;
</script>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php if ($lh_nota->nomor_nota->Visible) { // nomor_nota ?>
	<tr id="r_nomor_nota"<?php echo $lh_nota->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_lh_nota_nomor_nota"><table class="ewTableHeaderBtn"><tr><td><?php echo $lh_nota->nomor_nota->FldCaption() ?></td></tr></table></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_nomor_nota" id="z_nomor_nota" value="LIKE"></span></td>
		<td<?php echo $lh_nota->nomor_nota->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_lh_nota_nomor_nota" class="phpmaker">
<input type="text" name="x_nomor_nota" id="x_nomor_nota" size="30" maxlength="255" value="<?php echo $lh_nota->nomor_nota->EditValue ?>"<?php echo $lh_nota->nomor_nota->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php if ($lh_nota->nama_toko->Visible) { // nama_toko ?>
	<tr id="r_nama_toko"<?php echo $lh_nota->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_lh_nota_nama_toko"><table class="ewTableHeaderBtn"><tr><td><?php echo $lh_nota->nama_toko->FldCaption() ?></td></tr></table></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_nama_toko" id="z_nama_toko" value="LIKE"></span></td>
		<td<?php echo $lh_nota->nama_toko->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_lh_nota_nama_toko" class="phpmaker">
<input type="text" name="x_nama_toko" id="x_nama_toko" size="30" maxlength="255" value="<?php echo $lh_nota->nama_toko->EditValue ?>"<?php echo $lh_nota->nama_toko->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php if ($lh_nota->tanggal_nota->Visible) { // tanggal_nota ?>
	<tr id="r_tanggal_nota"<?php echo $lh_nota->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_lh_nota_tanggal_nota"><table class="ewTableHeaderBtn"><tr><td><?php echo $lh_nota->tanggal_nota->FldCaption() ?></td></tr></table></span></td>
		<td><span class="ewSearchOperator"><select name="z_tanggal_nota" id="z_tanggal_nota" onchange="ewForms['flh_notasearch'].SrchOprChanged(this);"><option value="="<?php echo ($lh_nota->tanggal_nota->AdvancedSearch->SearchOperator=="=") ? " selected=\"selected\"" : "" ?> ><?php echo $Language->Phrase("=") ?></option><option value="<>"<?php echo ($lh_nota->tanggal_nota->AdvancedSearch->SearchOperator=="<>") ? " selected=\"selected\"" : "" ?> ><?php echo $Language->Phrase("<>") ?></option><option value="<"<?php echo ($lh_nota->tanggal_nota->AdvancedSearch->SearchOperator=="<") ? " selected=\"selected\"" : "" ?> ><?php echo $Language->Phrase("<") ?></option><option value="<="<?php echo ($lh_nota->tanggal_nota->AdvancedSearch->SearchOperator=="<=") ? " selected=\"selected\"" : "" ?> ><?php echo $Language->Phrase("<=") ?></option><option value=">"<?php echo ($lh_nota->tanggal_nota->AdvancedSearch->SearchOperator==">") ? " selected=\"selected\"" : "" ?> ><?php echo $Language->Phrase(">") ?></option><option value=">="<?php echo ($lh_nota->tanggal_nota->AdvancedSearch->SearchOperator==">=") ? " selected=\"selected\"" : "" ?> ><?php echo $Language->Phrase(">=") ?></option><option value="BETWEEN"<?php echo ($lh_nota->tanggal_nota->AdvancedSearch->SearchOperator=="BETWEEN") ? " selected=\"selected\"" : "" ?> ><?php echo $Language->Phrase("BETWEEN") ?></option></select></span></td>
		<td<?php echo $lh_nota->tanggal_nota->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_lh_nota_tanggal_nota" class="phpmaker">
<input type="text" name="x_tanggal_nota" id="x_tanggal_nota" value="<?php echo $lh_nota->tanggal_nota->EditValue ?>"<?php echo $lh_nota->tanggal_nota->EditAttributes() ?>>
<?php if (!$lh_nota->tanggal_nota->ReadOnly && !$lh_nota->tanggal_nota->Disabled && @$lh_nota->tanggal_nota->EditAttrs["readonly"] == "" && @$lh_nota->tanggal_nota->EditAttrs["disabled"] == "") { ?>
&nbsp;<img src="phpimages/calendar.png" id="flh_notasearch$x_tanggal_nota$" name="flh_notasearch$x_tanggal_nota$" alt="<?php echo $Language->Phrase("PickDate") ?>" title="<?php echo $Language->Phrase("PickDate") ?>" class="ewCalendar" style="border: 0;">
<script type="text/javascript">
ew_CreateCalendar("flh_notasearch", "x_tanggal_nota", "%d/%m/%Y");
</script>
<?php } ?>
</span>
				<span class="ewSearchCond btw0_tanggal_nota"><label><input type="radio" name="v_tanggal_nota" id="v_tanggal_nota" value="AND"<?php if ($lh_nota->tanggal_nota->AdvancedSearch->SearchCondition <> "OR") echo " checked=\"checked\"" ?>><?php echo $Language->Phrase("AND") ?></label>&nbsp;<label><input type="radio" name="v_tanggal_nota" id="v_tanggal_nota" value="OR"<?php if ($lh_nota->tanggal_nota->AdvancedSearch->SearchCondition == "OR") echo " checked=\"checked\"" ?>><?php echo $Language->Phrase("OR") ?></label>&nbsp;</span>
				<span class="ewSearchCond btw1_tanggal_nota">&nbsp;<?php echo $Language->Phrase("AND") ?>&nbsp;</span>
				<span class="ewSearchOperator btw0_tanggal_nota"><select name="w_tanggal_nota" id="w_tanggal_nota" onchange="ewForms['flh_notasearch'].SrchOprChanged(this);"><option value="="<?php echo ($lh_nota->tanggal_nota->AdvancedSearch->SearchOperator2=="=") ? " selected=\"selected\"" : "" ?> ><?php echo $Language->Phrase("=") ?></option><option value="<>"<?php echo ($lh_nota->tanggal_nota->AdvancedSearch->SearchOperator2=="<>") ? " selected=\"selected\"" : "" ?> ><?php echo $Language->Phrase("<>") ?></option><option value="<"<?php echo ($lh_nota->tanggal_nota->AdvancedSearch->SearchOperator2=="<") ? " selected=\"selected\"" : "" ?> ><?php echo $Language->Phrase("<") ?></option><option value="<="<?php echo ($lh_nota->tanggal_nota->AdvancedSearch->SearchOperator2=="<=") ? " selected=\"selected\"" : "" ?> ><?php echo $Language->Phrase("<=") ?></option><option value=">"<?php echo ($lh_nota->tanggal_nota->AdvancedSearch->SearchOperator2==">") ? " selected=\"selected\"" : "" ?> ><?php echo $Language->Phrase(">") ?></option><option value=">="<?php echo ($lh_nota->tanggal_nota->AdvancedSearch->SearchOperator2==">=") ? " selected=\"selected\"" : "" ?> ><?php echo $Language->Phrase(">=") ?></option></select></span>
				<span id="e2_lh_nota_tanggal_nota" class="phpmaker">
<input type="text" name="y_tanggal_nota" id="y_tanggal_nota" value="<?php echo $lh_nota->tanggal_nota->EditValue2 ?>"<?php echo $lh_nota->tanggal_nota->EditAttributes() ?>>
<?php if (!$lh_nota->tanggal_nota->ReadOnly && !$lh_nota->tanggal_nota->Disabled && @$lh_nota->tanggal_nota->EditAttrs["readonly"] == "" && @$lh_nota->tanggal_nota->EditAttrs["disabled"] == "") { ?>
&nbsp;<img src="phpimages/calendar.png" id="flh_notasearch$y_tanggal_nota$" name="flh_notasearch$y_tanggal_nota$" alt="<?php echo $Language->Phrase("PickDate") ?>" title="<?php echo $Language->Phrase("PickDate") ?>" class="ewCalendar" style="border: 0;">
<script type="text/javascript">
ew_CreateCalendar("flh_notasearch", "y_tanggal_nota", "%d/%m/%Y");
</script>
<?php } ?>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php if ($lh_nota->jumlah_pembelian->Visible) { // jumlah_pembelian ?>
	<tr id="r_jumlah_pembelian"<?php echo $lh_nota->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_lh_nota_jumlah_pembelian"><table class="ewTableHeaderBtn"><tr><td><?php echo $lh_nota->jumlah_pembelian->FldCaption() ?></td></tr></table></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_jumlah_pembelian" id="z_jumlah_pembelian" value="="></span></td>
		<td<?php echo $lh_nota->jumlah_pembelian->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_lh_nota_jumlah_pembelian" class="phpmaker">
<input type="text" name="x_jumlah_pembelian" id="x_jumlah_pembelian" size="30" value="<?php echo $lh_nota->jumlah_pembelian->EditValue ?>"<?php echo $lh_nota->jumlah_pembelian->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php if ($lh_nota->pesan->Visible) { // pesan ?>
	<tr id="r_pesan"<?php echo $lh_nota->RowAttributes() ?>>
		<td class="ewTableHeader"><span id="elh_lh_nota_pesan"><table class="ewTableHeaderBtn"><tr><td><?php echo $lh_nota->pesan->FldCaption() ?></td></tr></table></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_pesan" id="z_pesan" value="LIKE"></span></td>
		<td<?php echo $lh_nota->pesan->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_lh_nota_pesan" class="phpmaker">
<textarea name="x_pesan" id="x_pesan" cols="25" rows="4"<?php echo $lh_nota->pesan->EditAttributes() ?>><?php echo $lh_nota->pesan->EditValue ?></textarea>
<script type="text/javascript">
ew_CreateEditor("flh_notasearch", "x_pesan", 25, 4, <?php echo ($lh_nota->pesan->ReadOnly || FALSE) ? "true" : "false" ?>);
</script>
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
flh_notasearch.Init();
</script>
<?php
$lh_nota_search->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$lh_nota_search->Page_Terminate();
?>
