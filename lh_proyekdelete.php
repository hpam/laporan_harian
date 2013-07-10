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

$lh_proyek_delete = NULL; // Initialize page object first

class clh_proyek_delete extends clh_proyek {

	// Page ID
	var $PageID = 'delete';

	// Project ID
	var $ProjectID = "{67264FB2-6364-478B-87DD-B3E0D7A29425}";

	// Table name
	var $TableName = 'lh_proyek';

	// Page object name
	var $PageObjName = 'lh_proyek_delete';

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
			define("EW_PAGE_ID", 'delete', TRUE);

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
		if (!$Security->CanDelete()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage($Language->Phrase("NoPermission")); // Set no permission
			$this->Page_Terminate("lh_proyeklist.php");
		}
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
			$this->Page_Terminate("lh_proyeklist.php"); // Prevent SQL injection, return to list

		// Set up filter (SQL WHHERE clause) and get return SQL
		// SQL constructor in lh_proyek class, lh_proyekinfo.php

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
		$this->id_proyek->setDbValue($rs->fields('id_proyek'));
		$this->nama_proyek->setDbValue($rs->fields('nama_proyek'));
		$this->tanggal->setDbValue($rs->fields('tanggal'));
		$this->kardus->setDbValue($rs->fields('kardus'));
		$this->kayu->setDbValue($rs->fields('kayu'));
		$this->besi->setDbValue($rs->fields('besi'));
		$this->harga->setDbValue($rs->fields('harga'));
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
				$sThisKey .= $row['id_proyek'];
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
if (!isset($lh_proyek_delete)) $lh_proyek_delete = new clh_proyek_delete();

// Page init
$lh_proyek_delete->Page_Init();

// Page main
$lh_proyek_delete->Page_Main();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var lh_proyek_delete = new ew_Page("lh_proyek_delete");
lh_proyek_delete.PageID = "delete"; // Page ID
var EW_PAGE_ID = lh_proyek_delete.PageID; // For backward compatibility

// Form object
var flh_proyekdelete = new ew_Form("flh_proyekdelete");

// Form_CustomValidate event
flh_proyekdelete.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
flh_proyekdelete.ValidateRequired = true;
<?php } else { ?>
flh_proyekdelete.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
// Form object for search

</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php

// Load records for display
if ($lh_proyek_delete->Recordset = $lh_proyek_delete->LoadRecordset())
	$lh_proyek_deleteTotalRecs = $lh_proyek_delete->Recordset->RecordCount(); // Get record count
if ($lh_proyek_deleteTotalRecs <= 0) { // No record found, exit
	if ($lh_proyek_delete->Recordset)
		$lh_proyek_delete->Recordset->Close();
	$lh_proyek_delete->Page_Terminate("lh_proyeklist.php"); // Return to list
}
?>
<p><span id="ewPageCaption" class="ewTitle ewTableTitle"><?php echo $Language->Phrase("Delete") ?>&nbsp;<?php echo $Language->Phrase("TblTypeTABLE") ?><?php echo $lh_proyek->TableCaption() ?></span></p>
<p class="phpmaker"><a href="<?php echo $lh_proyek->getReturnUrl() ?>" id="a_GoBack" class="ewLink"><?php echo $Language->Phrase("GoBack") ?></a></p>
<?php $lh_proyek_delete->ShowPageHeader(); ?>
<?php
$lh_proyek_delete->ShowMessage();
?>
<form name="flh_proyekdelete" id="flh_proyekdelete" class="ewForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<br>
<input type="hidden" name="t" value="lh_proyek">
<input type="hidden" name="a_delete" id="a_delete" value="D">
<?php foreach ($lh_proyek_delete->RecKeys as $key) { ?>
<?php $keyvalue = is_array($key) ? implode($EW_COMPOSITE_KEY_SEPARATOR, $key) : $key; ?>
<input type="hidden" name="key_m[]" value="<?php echo ew_HtmlEncode($keyvalue) ?>">
<?php } ?>
<table cellspacing="0" class="ewGrid"><tr><td class="ewGridContent">
<div class="ewGridMiddlePanel">
<table id="tbl_lh_proyekdelete" class="ewTable ewTableSeparate">
<?php echo $lh_proyek->TableCustomInnerHtml ?>
	<thead>
	<tr class="ewTableHeader">
		<td><span id="elh_lh_proyek_id_proyek" class="lh_proyek_id_proyek"><table class="ewTableHeaderBtn"><tr><td><?php echo $lh_proyek->id_proyek->FldCaption() ?></td></tr></table></span></td>
		<td><span id="elh_lh_proyek_nama_proyek" class="lh_proyek_nama_proyek"><table class="ewTableHeaderBtn"><tr><td><?php echo $lh_proyek->nama_proyek->FldCaption() ?></td></tr></table></span></td>
		<td><span id="elh_lh_proyek_tanggal" class="lh_proyek_tanggal"><table class="ewTableHeaderBtn"><tr><td><?php echo $lh_proyek->tanggal->FldCaption() ?></td></tr></table></span></td>
		<td><span id="elh_lh_proyek_kardus" class="lh_proyek_kardus"><table class="ewTableHeaderBtn"><tr><td><?php echo $lh_proyek->kardus->FldCaption() ?></td></tr></table></span></td>
		<td><span id="elh_lh_proyek_kayu" class="lh_proyek_kayu"><table class="ewTableHeaderBtn"><tr><td><?php echo $lh_proyek->kayu->FldCaption() ?></td></tr></table></span></td>
		<td><span id="elh_lh_proyek_besi" class="lh_proyek_besi"><table class="ewTableHeaderBtn"><tr><td><?php echo $lh_proyek->besi->FldCaption() ?></td></tr></table></span></td>
		<td><span id="elh_lh_proyek_harga" class="lh_proyek_harga"><table class="ewTableHeaderBtn"><tr><td><?php echo $lh_proyek->harga->FldCaption() ?></td></tr></table></span></td>
	</tr>
	</thead>
	<tbody>
<?php
$lh_proyek_delete->RecCnt = 0;
$i = 0;
while (!$lh_proyek_delete->Recordset->EOF) {
	$lh_proyek_delete->RecCnt++;
	$lh_proyek_delete->RowCnt++;

	// Set row properties
	$lh_proyek->ResetAttrs();
	$lh_proyek->RowType = EW_ROWTYPE_VIEW; // View

	// Get the field contents
	$lh_proyek_delete->LoadRowValues($lh_proyek_delete->Recordset);

	// Render row
	$lh_proyek_delete->RenderRow();
?>
	<tr<?php echo $lh_proyek->RowAttributes() ?>>
		<td<?php echo $lh_proyek->id_proyek->CellAttributes() ?>><span id="el<?php echo $lh_proyek_delete->RowCnt ?>_lh_proyek_id_proyek" class="lh_proyek_id_proyek">
<span<?php echo $lh_proyek->id_proyek->ViewAttributes() ?>>
<?php echo $lh_proyek->id_proyek->ListViewValue() ?></span>
</span></td>
		<td<?php echo $lh_proyek->nama_proyek->CellAttributes() ?>><span id="el<?php echo $lh_proyek_delete->RowCnt ?>_lh_proyek_nama_proyek" class="lh_proyek_nama_proyek">
<span<?php echo $lh_proyek->nama_proyek->ViewAttributes() ?>>
<?php echo $lh_proyek->nama_proyek->ListViewValue() ?></span>
</span></td>
		<td<?php echo $lh_proyek->tanggal->CellAttributes() ?>><span id="el<?php echo $lh_proyek_delete->RowCnt ?>_lh_proyek_tanggal" class="lh_proyek_tanggal">
<span<?php echo $lh_proyek->tanggal->ViewAttributes() ?>>
<?php echo $lh_proyek->tanggal->ListViewValue() ?></span>
</span></td>
		<td<?php echo $lh_proyek->kardus->CellAttributes() ?>><span id="el<?php echo $lh_proyek_delete->RowCnt ?>_lh_proyek_kardus" class="lh_proyek_kardus">
<span<?php echo $lh_proyek->kardus->ViewAttributes() ?>>
<?php echo $lh_proyek->kardus->ListViewValue() ?></span>
</span></td>
		<td<?php echo $lh_proyek->kayu->CellAttributes() ?>><span id="el<?php echo $lh_proyek_delete->RowCnt ?>_lh_proyek_kayu" class="lh_proyek_kayu">
<span<?php echo $lh_proyek->kayu->ViewAttributes() ?>>
<?php echo $lh_proyek->kayu->ListViewValue() ?></span>
</span></td>
		<td<?php echo $lh_proyek->besi->CellAttributes() ?>><span id="el<?php echo $lh_proyek_delete->RowCnt ?>_lh_proyek_besi" class="lh_proyek_besi">
<span<?php echo $lh_proyek->besi->ViewAttributes() ?>>
<?php echo $lh_proyek->besi->ListViewValue() ?></span>
</span></td>
		<td<?php echo $lh_proyek->harga->CellAttributes() ?>><span id="el<?php echo $lh_proyek_delete->RowCnt ?>_lh_proyek_harga" class="lh_proyek_harga">
<span<?php echo $lh_proyek->harga->ViewAttributes() ?>>
<?php echo $lh_proyek->harga->ListViewValue() ?></span>
</span></td>
	</tr>
<?php
	$lh_proyek_delete->Recordset->MoveNext();
}
$lh_proyek_delete->Recordset->Close();
?>
</tbody>
</table>
</div>
</td></tr></table>
<br>
<input type="submit" name="Action" value="<?php echo ew_BtnCaption($Language->Phrase("DeleteBtn")) ?>">
</form>
<script type="text/javascript">
flh_proyekdelete.Init();
</script>
<?php
$lh_proyek_delete->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$lh_proyek_delete->Page_Terminate();
?>
