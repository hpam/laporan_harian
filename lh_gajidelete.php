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

$lh_gaji_delete = NULL; // Initialize page object first

class clh_gaji_delete extends clh_gaji {

	// Page ID
	var $PageID = 'delete';

	// Project ID
	var $ProjectID = "{67264FB2-6364-478B-87DD-B3E0D7A29425}";

	// Table name
	var $TableName = 'lh_gaji';

	// Page object name
	var $PageObjName = 'lh_gaji_delete';

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

		// Table object (lh_gaji)
		if (!isset($GLOBALS["lh_gaji"])) {
			$GLOBALS["lh_gaji"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["lh_gaji"];
		}

		// Table object (lh_user)
		if (!isset($GLOBALS['lh_user'])) $GLOBALS['lh_user'] = new clh_user();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'delete', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'lh_gaji', TRUE);

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
		if (!$Security->CanDelete()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage($Language->Phrase("NoPermission")); // Set no permission
			$this->Page_Terminate("lh_gajilist.php");
		}
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
	var $TotalRecs = 0;
	var $RecCnt;
	var $RecKeys = array();
	var $Recordset;
	var $StartRowCnt = 1;
	var $RowCnt = 0;

	//
	// Page main
	//
	function Page_Main() {
		global $Language;

		// Load key parameters
		$this->RecKeys = $this->GetRecordKeys(); // Load record keys
		$sFilter = $this->GetKeyFilter();
		if ($sFilter == "")
			$this->Page_Terminate("lh_gajilist.php"); // Prevent SQL injection, return to list

		// Set up filter (SQL WHHERE clause) and get return SQL
		// SQL constructor in lh_gaji class, lh_gajiinfo.php

		$this->CurrentFilter = $sFilter;

		// Get action
		if (@$_POST["a_delete"] <> "") {
			$this->CurrentAction = $_POST["a_delete"];
		} else {
			$this->CurrentAction = "I"; // Display record
		}
		switch ($this->CurrentAction) {
			case "D": // Delete
				$this->SendEmail = TRUE; // Send email on delete success
				if ($this->DeleteRows()) { // delete rows
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("DeleteSuccess")); // Set up success message
					$this->Page_Terminate($this->getReturnUrl()); // Return to caller
				}
		}
	}

// No functions
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

	//
	// Delete records based on current filter
	//
	function DeleteRows() {
		global $conn, $Language, $Security;
		$DeleteRows = TRUE;
		$sSql = $this->SQL();
		$conn->raiseErrorFn = 'ew_ErrorFn';
		$rs = $conn->Execute($sSql);
		$conn->raiseErrorFn = '';
		if ($rs === FALSE) {
			return FALSE;
		} elseif ($rs->EOF) {
			$this->setFailureMessage($Language->Phrase("NoRecord")); // No record found
			$rs->Close();
			return FALSE;
		} else {
			$this->LoadRowValues($rs); // Load row values
		}
		if (!$Security->CanDelete()) {
			$this->setFailureMessage($Language->Phrase("NoDeletePermission")); // No delete permission
			return FALSE;
		}
		$conn->BeginTrans();

		// Clone old rows
		$rsold = ($rs) ? $rs->GetRows() : array();
		if ($rs)
			$rs->Close();

		// Call row deleting event
		if ($DeleteRows) {
			foreach ($rsold as $row) {
				$DeleteRows = $this->Row_Deleting($row);
				if (!$DeleteRows) break;
			}
		}
		if ($DeleteRows) {
			$sKey = "";
			foreach ($rsold as $row) {
				$sThisKey = "";
				if ($sThisKey <> "") $sThisKey .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
				$sThisKey .= $row['id_gaji'];
				$conn->raiseErrorFn = 'ew_ErrorFn';
				$DeleteRows = $this->Delete($row); // Delete
				$conn->raiseErrorFn = '';
				if ($DeleteRows === FALSE)
					break;
				if ($sKey <> "") $sKey .= ", ";
				$sKey .= $sThisKey;
			}
		} else {

			// Set up error message
			if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

				// Use the message, do nothing
			} elseif ($this->CancelMessage <> "") {
				$this->setFailureMessage($this->CancelMessage);
				$this->CancelMessage = "";
			} else {
				$this->setFailureMessage($Language->Phrase("DeleteCancelled"));
			}
		}
		if ($DeleteRows) {
			$conn->CommitTrans(); // Commit the changes
		} else {
			$conn->RollbackTrans(); // Rollback changes
		}

		// Call Row Deleted event
		if ($DeleteRows) {
			foreach ($rsold as $row) {
				$this->Row_Deleted($row);
			}
		}
		return $DeleteRows;
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
if (!isset($lh_gaji_delete)) $lh_gaji_delete = new clh_gaji_delete();

// Page init
$lh_gaji_delete->Page_Init();

// Page main
$lh_gaji_delete->Page_Main();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var lh_gaji_delete = new ew_Page("lh_gaji_delete");
lh_gaji_delete.PageID = "delete"; // Page ID
var EW_PAGE_ID = lh_gaji_delete.PageID; // For backward compatibility

// Form object
var flh_gajidelete = new ew_Form("flh_gajidelete");

// Form_CustomValidate event
flh_gajidelete.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
flh_gajidelete.ValidateRequired = true;
<?php } else { ?>
flh_gajidelete.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
flh_gajidelete.Lists["x_id_user"] = {"LinkField":"x_id_user","Ajax":null,"AutoFill":false,"DisplayFields":["x_nama","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php

// Load records for display
if ($lh_gaji_delete->Recordset = $lh_gaji_delete->LoadRecordset())
	$lh_gaji_deleteTotalRecs = $lh_gaji_delete->Recordset->RecordCount(); // Get record count
if ($lh_gaji_deleteTotalRecs <= 0) { // No record found, exit
	if ($lh_gaji_delete->Recordset)
		$lh_gaji_delete->Recordset->Close();
	$lh_gaji_delete->Page_Terminate("lh_gajilist.php"); // Return to list
}
?>
<p><span id="ewPageCaption" class="ewTitle ewTableTitle"><?php echo $Language->Phrase("Delete") ?>&nbsp;<?php echo $Language->Phrase("TblTypeTABLE") ?><?php echo $lh_gaji->TableCaption() ?></span></p>
<p class="phpmaker"><a href="<?php echo $lh_gaji->getReturnUrl() ?>" id="a_GoBack" class="ewLink"><?php echo $Language->Phrase("GoBack") ?></a></p>
<?php $lh_gaji_delete->ShowPageHeader(); ?>
<?php
$lh_gaji_delete->ShowMessage();
?>
<form name="flh_gajidelete" id="flh_gajidelete" class="ewForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<br>
<input type="hidden" name="t" value="lh_gaji">
<input type="hidden" name="a_delete" id="a_delete" value="D">
<?php foreach ($lh_gaji_delete->RecKeys as $key) { ?>
<?php $keyvalue = is_array($key) ? implode($EW_COMPOSITE_KEY_SEPARATOR, $key) : $key; ?>
<input type="hidden" name="key_m[]" value="<?php echo ew_HtmlEncode($keyvalue) ?>">
<?php } ?>
<table cellspacing="0" class="ewGrid"><tr><td class="ewGridContent">
<div class="ewGridMiddlePanel">
<table id="tbl_lh_gajidelete" class="ewTable ewTableSeparate">
<?php echo $lh_gaji->TableCustomInnerHtml ?>
	<thead>
	<tr class="ewTableHeader">
		<td><span id="elh_lh_gaji_id_gaji" class="lh_gaji_id_gaji"><table class="ewTableHeaderBtn"><tr><td><?php echo $lh_gaji->id_gaji->FldCaption() ?></td></tr></table></span></td>
		<td><span id="elh_lh_gaji_id_user" class="lh_gaji_id_user"><table class="ewTableHeaderBtn"><tr><td><?php echo $lh_gaji->id_user->FldCaption() ?></td></tr></table></span></td>
		<td><span id="elh_lh_gaji_status" class="lh_gaji_status"><table class="ewTableHeaderBtn"><tr><td><?php echo $lh_gaji->status->FldCaption() ?></td></tr></table></span></td>
		<td><span id="elh_lh_gaji_tanggal" class="lh_gaji_tanggal"><table class="ewTableHeaderBtn"><tr><td><?php echo $lh_gaji->tanggal->FldCaption() ?></td></tr></table></span></td>
		<td><span id="elh_lh_gaji_gaji_pokok" class="lh_gaji_gaji_pokok"><table class="ewTableHeaderBtn"><tr><td><?php echo $lh_gaji->gaji_pokok->FldCaption() ?></td></tr></table></span></td>
		<td><span id="elh_lh_gaji_lembur" class="lh_gaji_lembur"><table class="ewTableHeaderBtn"><tr><td><?php echo $lh_gaji->lembur->FldCaption() ?></td></tr></table></span></td>
		<td><span id="elh_lh_gaji_tunjangan_proyek" class="lh_gaji_tunjangan_proyek"><table class="ewTableHeaderBtn"><tr><td><?php echo $lh_gaji->tunjangan_proyek->FldCaption() ?></td></tr></table></span></td>
	</tr>
	</thead>
	<tbody>
<?php
$lh_gaji_delete->RecCnt = 0;
$i = 0;
while (!$lh_gaji_delete->Recordset->EOF) {
	$lh_gaji_delete->RecCnt++;
	$lh_gaji_delete->RowCnt++;

	// Set row properties
	$lh_gaji->ResetAttrs();
	$lh_gaji->RowType = EW_ROWTYPE_VIEW; // View

	// Get the field contents
	$lh_gaji_delete->LoadRowValues($lh_gaji_delete->Recordset);

	// Render row
	$lh_gaji_delete->RenderRow();
?>
	<tr<?php echo $lh_gaji->RowAttributes() ?>>
		<td<?php echo $lh_gaji->id_gaji->CellAttributes() ?>><span id="el<?php echo $lh_gaji_delete->RowCnt ?>_lh_gaji_id_gaji" class="lh_gaji_id_gaji">
<span<?php echo $lh_gaji->id_gaji->ViewAttributes() ?>>
<?php echo $lh_gaji->id_gaji->ListViewValue() ?></span>
</span></td>
		<td<?php echo $lh_gaji->id_user->CellAttributes() ?>><span id="el<?php echo $lh_gaji_delete->RowCnt ?>_lh_gaji_id_user" class="lh_gaji_id_user">
<span<?php echo $lh_gaji->id_user->ViewAttributes() ?>>
<?php echo $lh_gaji->id_user->ListViewValue() ?></span>
</span></td>
		<td<?php echo $lh_gaji->status->CellAttributes() ?>><span id="el<?php echo $lh_gaji_delete->RowCnt ?>_lh_gaji_status" class="lh_gaji_status">
<span<?php echo $lh_gaji->status->ViewAttributes() ?>>
<?php echo $lh_gaji->status->ListViewValue() ?></span>
</span></td>
		<td<?php echo $lh_gaji->tanggal->CellAttributes() ?>><span id="el<?php echo $lh_gaji_delete->RowCnt ?>_lh_gaji_tanggal" class="lh_gaji_tanggal">
<span<?php echo $lh_gaji->tanggal->ViewAttributes() ?>>
<?php echo $lh_gaji->tanggal->ListViewValue() ?></span>
</span></td>
		<td<?php echo $lh_gaji->gaji_pokok->CellAttributes() ?>><span id="el<?php echo $lh_gaji_delete->RowCnt ?>_lh_gaji_gaji_pokok" class="lh_gaji_gaji_pokok">
<span<?php echo $lh_gaji->gaji_pokok->ViewAttributes() ?>>
<?php echo $lh_gaji->gaji_pokok->ListViewValue() ?></span>
</span></td>
		<td<?php echo $lh_gaji->lembur->CellAttributes() ?>><span id="el<?php echo $lh_gaji_delete->RowCnt ?>_lh_gaji_lembur" class="lh_gaji_lembur">
<span<?php echo $lh_gaji->lembur->ViewAttributes() ?>>
<?php echo $lh_gaji->lembur->ListViewValue() ?></span>
</span></td>
		<td<?php echo $lh_gaji->tunjangan_proyek->CellAttributes() ?>><span id="el<?php echo $lh_gaji_delete->RowCnt ?>_lh_gaji_tunjangan_proyek" class="lh_gaji_tunjangan_proyek">
<span<?php echo $lh_gaji->tunjangan_proyek->ViewAttributes() ?>>
<?php echo $lh_gaji->tunjangan_proyek->ListViewValue() ?></span>
</span></td>
	</tr>
<?php
	$lh_gaji_delete->Recordset->MoveNext();
}
$lh_gaji_delete->Recordset->Close();
?>
</tbody>
</table>
</div>
</td></tr></table>
<br>
<input type="submit" name="Action" value="<?php echo ew_BtnCaption($Language->Phrase("DeleteBtn")) ?>">
</form>
<script type="text/javascript">
flh_gajidelete.Init();
</script>
<?php
$lh_gaji_delete->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$lh_gaji_delete->Page_Terminate();
?>
