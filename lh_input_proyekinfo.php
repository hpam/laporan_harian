<?php

// Global variable for table object
$lh_input_proyek = NULL;

//
// Table class for lh_input_proyek
//
class clh_input_proyek extends cTable {
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
		$this->TableVar = 'lh_input_proyek';
		$this->TableName = 'lh_input_proyek';
		$this->TableType = 'VIEW';
		$this->ExportAll = TRUE;
		$this->ExportPageBreakCount = 0; // Page break per every n record (PDF only)
		$this->ExportPageOrientation = "portrait"; // Page orientation (PDF only)
		$this->ExportPageSize = "a4"; // Page size (PDF only)
		$this->DetailAdd = FALSE; // Allow detail add
		$this->DetailEdit = FALSE; // Allow detail edit
		$this->GridAddRowCount = 5;
		$this->AllowAddDeleteRow = ew_AllowAddDeleteRow(); // Allow add/delete row
		$this->UserIDAllowSecurity = 0; // User ID Allow
		$this->BasicSearch = new cBasicSearch($this->TableVar);

		// id_proyek
		$this->id_proyek = new cField('lh_input_proyek', 'lh_input_proyek', 'x_id_proyek', 'id_proyek', '`id_proyek`', '`id_proyek`', 3, -1, FALSE, '`id_proyek`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->id_proyek->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['id_proyek'] = &$this->id_proyek;

		// nama_proyek
		$this->nama_proyek = new cField('lh_input_proyek', 'lh_input_proyek', 'x_nama_proyek', 'nama_proyek', '`nama_proyek`', '`nama_proyek`', 200, -1, FALSE, '`nama_proyek`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['nama_proyek'] = &$this->nama_proyek;

		// tanggal
		$this->tanggal = new cField('lh_input_proyek', 'lh_input_proyek', 'x_tanggal', 'tanggal', '`tanggal`', 'DATE_FORMAT(`tanggal`, \'%d/%m/%Y %H:%i:%s\')', 135, 7, FALSE, '`tanggal`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->tanggal->FldDefaultErrMsg = str_replace("%s", "/", $Language->Phrase("IncorrectDateDMY"));
		$this->fields['tanggal'] = &$this->tanggal;

		// kardus
		$this->kardus = new cField('lh_input_proyek', 'lh_input_proyek', 'x_kardus', 'kardus', '`kardus`', '`kardus`', 3, -1, FALSE, '`kardus`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->kardus->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['kardus'] = &$this->kardus;

		// kayu
		$this->kayu = new cField('lh_input_proyek', 'lh_input_proyek', 'x_kayu', 'kayu', '`kayu`', '`kayu`', 3, -1, FALSE, '`kayu`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->kayu->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['kayu'] = &$this->kayu;

		// besi
		$this->besi = new cField('lh_input_proyek', 'lh_input_proyek', 'x_besi', 'besi', '`besi`', '`besi`', 3, -1, FALSE, '`besi`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->besi->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['besi'] = &$this->besi;

		// harga
		$this->harga = new cField('lh_input_proyek', 'lh_input_proyek', 'x_harga', 'harga', '`harga`', '`harga`', 5, -1, FALSE, '`harga`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->harga->FldDefaultErrMsg = $Language->Phrase("IncorrectFloat");
		$this->fields['harga'] = &$this->harga;
	}

	// Single column sort
	function UpdateSort(&$ofld) {
		if ($this->CurrentOrder == $ofld->FldName) {
			$sSortField = $ofld->FldExpression;
			$sLastSort = $ofld->getSort();
			if ($this->CurrentOrderType == "ASC" || $this->CurrentOrderType == "DESC") {
				$sThisSort = $this->CurrentOrderType;
			} else {
				$sThisSort = ($sLastSort == "ASC") ? "DESC" : "ASC";
			}
			$ofld->setSort($sThisSort);
			$this->setSessionOrderBy($sSortField . " " . $sThisSort); // Save to Session
		} else {
			$ofld->setSort("");
		}
	}

	// Table level SQL
	function SqlFrom() { // From
		return "`lh_input_proyek`";
	}

	function SqlSelect() { // Select
		return "SELECT * FROM " . $this->SqlFrom();
	}

	function SqlWhere() { // Where
		$sWhere = "";
		$this->TableFilter = "";
		ew_AddFilter($sWhere, $this->TableFilter);
		return $sWhere;
	}

	function SqlGroupBy() { // Group By
		return "";
	}

	function SqlHaving() { // Having
		return "";
	}

	function SqlOrderBy() { // Order By
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

	// Get SQL
	function GetSQL($where, $orderby) {
		return ew_BuildSelectSql($this->SqlSelect(), $this->SqlWhere(),
			$this->SqlGroupBy(), $this->SqlHaving(), $this->SqlOrderBy(),
			$where, $orderby);
	}

	// Table SQL
	function SQL() {
		$sFilter = $this->CurrentFilter;
		$sFilter = $this->ApplyUserIDFilters($sFilter);
		$sSort = $this->getSessionOrderBy();
		return ew_BuildSelectSql($this->SqlSelect(), $this->SqlWhere(),
			$this->SqlGroupBy(), $this->SqlHaving(), $this->SqlOrderBy(),
			$sFilter, $sSort);
	}

	// Table SQL with List page filter
	function SelectSQL() {
		$sFilter = $this->getSessionWhere();
		ew_AddFilter($sFilter, $this->CurrentFilter);
		$sFilter = $this->ApplyUserIDFilters($sFilter);
		$sSort = $this->getSessionOrderBy();
		return ew_BuildSelectSql($this->SqlSelect(), $this->SqlWhere(), $this->SqlGroupBy(),
			$this->SqlHaving(), $this->SqlOrderBy(), $sFilter, $sSort);
	}

	// Get ORDER BY clause
	function GetOrderBy() {
		$sSort = $this->getSessionOrderBy();
		return ew_BuildSelectSql("", "", "", "", $this->SqlOrderBy(), "", $sSort);
	}

	// Try to get record count
	function TryGetRecordCount($sSql) {
		global $conn;
		$cnt = -1;
		if ($this->TableType == 'TABLE' || $this->TableType == 'VIEW') {
			$sSql = "SELECT COUNT(*) FROM" . substr($sSql, 13);
			$sOrderBy = $this->GetOrderBy();
			if (substr($sSql, strlen($sOrderBy) * -1) == $sOrderBy)
				$sSql = substr($sSql, 0, strlen($sSql) - strlen($sOrderBy)); // Remove ORDER BY clause
		} else {
			$sSql = "SELECT COUNT(*) FROM (" . $sSql . ") EW_COUNT_TABLE";
		}
		if ($rs = $conn->Execute($sSql)) {
			if (!$rs->EOF && $rs->FieldCount() > 0) {
				$cnt = $rs->fields[0];
				$rs->Close();
			}
		}
		return intval($cnt);
	}

	// Get record count based on filter (for detail record count in master table pages)
	function LoadRecordCount($sFilter) {
		$origFilter = $this->CurrentFilter;
		$this->CurrentFilter = $sFilter;
		$this->Recordset_Selecting($this->CurrentFilter);

		//$sSql = $this->SQL();
		$sSql = $this->GetSQL($this->CurrentFilter, "");
		$cnt = $this->TryGetRecordCount($sSql);
		if ($cnt == -1) {
			if ($rs = $this->LoadRs($this->CurrentFilter)) {
				$cnt = $rs->RecordCount();
				$rs->Close();
			}
		}
		$this->CurrentFilter = $origFilter;
		return intval($cnt);
	}

	// Get record count (for current List page)
	function SelectRecordCount() {
		global $conn;
		$origFilter = $this->CurrentFilter;
		$this->Recordset_Selecting($this->CurrentFilter);
		$sSql = $this->SelectSQL();
		$cnt = $this->TryGetRecordCount($sSql);
		if ($cnt == -1) {
			if ($rs = $conn->Execute($sSql)) {
				$cnt = $rs->RecordCount();
				$rs->Close();
			}
		}
		$this->CurrentFilter = $origFilter;
		return intval($cnt);
	}

	// Update Table
	var $UpdateTable = "`lh_input_proyek`";

	// INSERT statement
	function InsertSQL(&$rs) {
		global $conn;
		$names = "";
		$values = "";
		foreach ($rs as $name => $value) {
			if (!isset($this->fields[$name]))
				continue;
			$names .= $this->fields[$name]->FldExpression . ",";
			$values .= ew_QuotedValue($value, $this->fields[$name]->FldDataType) . ",";
		}
		while (substr($names, -1) == ",")
			$names = substr($names, 0, -1);
		while (substr($values, -1) == ",")
			$values = substr($values, 0, -1);
		return "INSERT INTO " . $this->UpdateTable . " ($names) VALUES ($values)";
	}

	// Insert
	function Insert(&$rs) {
		global $conn;
		return $conn->Execute($this->InsertSQL($rs));
	}

	// UPDATE statement
	function UpdateSQL(&$rs, $where = "") {
		$sql = "UPDATE " . $this->UpdateTable . " SET ";
		foreach ($rs as $name => $value) {
			if (!isset($this->fields[$name]))
				continue;
			$sql .= $this->fields[$name]->FldExpression . "=";
			$sql .= ew_QuotedValue($value, $this->fields[$name]->FldDataType) . ",";
		}
		while (substr($sql, -1) == ",")
			$sql = substr($sql, 0, -1);
		$filter = $this->CurrentFilter;
		ew_AddFilter($filter, $where);
		if ($filter <> "")	$sql .= " WHERE " . $filter;
		return $sql;
	}

	// Update
	function Update(&$rs, $where = "") {
		global $conn;
		return $conn->Execute($this->UpdateSQL($rs, $where));
	}

	// DELETE statement
	function DeleteSQL(&$rs, $where = "") {
		$sql = "DELETE FROM " . $this->UpdateTable . " WHERE ";
		if ($rs) {
			$sql .= ew_QuotedName('id_proyek') . '=' . ew_QuotedValue($rs['id_proyek'], $this->id_proyek->FldDataType) . ' AND ';
		}
		if (substr($sql, -5) == " AND ") $sql = substr($sql, 0, -5);
		$filter = $this->CurrentFilter;
		ew_AddFilter($filter, $where);
		if ($filter <> "")	$sql .= " AND " . $filter;
		return $sql;
	}

	// Delete
	function Delete(&$rs, $where = "") {
		global $conn;
		return $conn->Execute($this->DeleteSQL($rs, $where));
	}

	// Key filter WHERE clause
	function SqlKeyFilter() {
		return "`id_proyek` = @id_proyek@";
	}

	// Key filter
	function KeyFilter() {
		$sKeyFilter = $this->SqlKeyFilter();
		if (!is_numeric($this->id_proyek->CurrentValue))
			$sKeyFilter = "0=1"; // Invalid key
		$sKeyFilter = str_replace("@id_proyek@", ew_AdjustSql($this->id_proyek->CurrentValue), $sKeyFilter); // Replace key value
		return $sKeyFilter;
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
			return "lh_input_proyeklist.php";
		}
	}

	function setReturnUrl($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL] = $v;
	}

	// List URL
	function GetListUrl() {
		return "lh_input_proyeklist.php";
	}

	// View URL
	function GetViewUrl() {
		return $this->KeyUrl("lh_input_proyekview.php", $this->UrlParm());
	}

	// Add URL
	function GetAddUrl() {
		return "lh_input_proyekadd.php";
	}

	// Edit URL
	function GetEditUrl($parm = "") {
		return $this->KeyUrl("lh_input_proyekedit.php", $this->UrlParm($parm));
	}

	// Inline edit URL
	function GetInlineEditUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=edit"));
	}

	// Copy URL
	function GetCopyUrl($parm = "") {
		return $this->KeyUrl("lh_input_proyekadd.php", $this->UrlParm($parm));
	}

	// Inline copy URL
	function GetInlineCopyUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=copy"));
	}

	// Delete URL
	function GetDeleteUrl() {
		return $this->KeyUrl("lh_input_proyekdelete.php", $this->UrlParm());
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

	// Load row values from recordset
	function LoadListRowValues(&$rs) {
		$this->id_proyek->setDbValue($rs->fields('id_proyek'));
		$this->nama_proyek->setDbValue($rs->fields('nama_proyek'));
		$this->tanggal->setDbValue($rs->fields('tanggal'));
		$this->kardus->setDbValue($rs->fields('kardus'));
		$this->kayu->setDbValue($rs->fields('kayu'));
		$this->besi->setDbValue($rs->fields('besi'));
		$this->harga->setDbValue($rs->fields('harga'));
	}

	// Render list row values
	function RenderListRow() {
		global $conn, $Security;

		// Call Row Rendering event
		$this->Row_Rendering();

   // Common render codes
		// id_proyek
		// nama_proyek
		// tanggal
		// kardus
		// kayu
		// besi
		// harga
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

		// Call Row Rendered event
		$this->Row_Rendered();
	}

	// Aggregate list row values
	function AggregateListRowValues() {
			if (is_numeric($this->kardus->CurrentValue))
				$this->kardus->Total += $this->kardus->CurrentValue; // Accumulate total
			if (is_numeric($this->kayu->CurrentValue))
				$this->kayu->Total += $this->kayu->CurrentValue; // Accumulate total
			if (is_numeric($this->besi->CurrentValue))
				$this->besi->Total += $this->besi->CurrentValue; // Accumulate total
			if (is_numeric($this->harga->CurrentValue))
				$this->harga->Total += $this->harga->CurrentValue; // Accumulate total
	}

	// Aggregate list row (for rendering)
	function AggregateListRow() {
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
			$this->harga->ViewValue = ew_FormatNumber($this->harga->ViewValue, 2, -1, -2, -1);
			$this->harga->CellCssStyle .= "text-align: right;";
			$this->harga->ViewCustomAttributes = "";
			$this->harga->HrefValue = ""; // Clear href value
	}

	// Export data in HTML/CSV/Word/Excel/Email/PDF format
	function ExportDocument(&$Doc, &$Recordset, $StartRec, $StopRec, $ExportPageType = "") {
		if (!$Recordset || !$Doc)
			return;

		// Write header
		$Doc->ExportTableHeader();
		if ($Doc->Horizontal) { // Horizontal format, write header
			$Doc->BeginExportRow();
			if ($ExportPageType == "view") {
				if ($this->id_proyek->Exportable) $Doc->ExportCaption($this->id_proyek);
				if ($this->nama_proyek->Exportable) $Doc->ExportCaption($this->nama_proyek);
				if ($this->tanggal->Exportable) $Doc->ExportCaption($this->tanggal);
				if ($this->kardus->Exportable) $Doc->ExportCaption($this->kardus);
				if ($this->kayu->Exportable) $Doc->ExportCaption($this->kayu);
				if ($this->besi->Exportable) $Doc->ExportCaption($this->besi);
				if ($this->harga->Exportable) $Doc->ExportCaption($this->harga);
			} else {
				if ($this->id_proyek->Exportable) $Doc->ExportCaption($this->id_proyek);
				if ($this->nama_proyek->Exportable) $Doc->ExportCaption($this->nama_proyek);
				if ($this->tanggal->Exportable) $Doc->ExportCaption($this->tanggal);
				if ($this->kardus->Exportable) $Doc->ExportCaption($this->kardus);
				if ($this->kayu->Exportable) $Doc->ExportCaption($this->kayu);
				if ($this->besi->Exportable) $Doc->ExportCaption($this->besi);
				if ($this->harga->Exportable) $Doc->ExportCaption($this->harga);
			}
			$Doc->EndExportRow();
		}

		// Move to first record
		$RecCnt = $StartRec - 1;
		if (!$Recordset->EOF) {
			$Recordset->MoveFirst();
			if ($StartRec > 1)
				$Recordset->Move($StartRec - 1);
		}
		while (!$Recordset->EOF && $RecCnt < $StopRec) {
			$RecCnt++;
			if (intval($RecCnt) >= intval($StartRec)) {
				$RowCnt = intval($RecCnt) - intval($StartRec) + 1;

				// Page break
				if ($this->ExportPageBreakCount > 0) {
					if ($RowCnt > 1 && ($RowCnt - 1) % $this->ExportPageBreakCount == 0)
						$Doc->ExportPageBreak();
				}
				$this->LoadListRowValues($Recordset);
				$this->AggregateListRowValues(); // Aggregate row values

				// Render row
				$this->RowType = EW_ROWTYPE_VIEW; // Render view
				$this->ResetAttrs();
				$this->RenderListRow();
				$Doc->BeginExportRow($RowCnt); // Allow CSS styles if enabled
				if ($ExportPageType == "view") {
					if ($this->id_proyek->Exportable) $Doc->ExportField($this->id_proyek);
					if ($this->nama_proyek->Exportable) $Doc->ExportField($this->nama_proyek);
					if ($this->tanggal->Exportable) $Doc->ExportField($this->tanggal);
					if ($this->kardus->Exportable) $Doc->ExportField($this->kardus);
					if ($this->kayu->Exportable) $Doc->ExportField($this->kayu);
					if ($this->besi->Exportable) $Doc->ExportField($this->besi);
					if ($this->harga->Exportable) $Doc->ExportField($this->harga);
				} else {
					if ($this->id_proyek->Exportable) $Doc->ExportField($this->id_proyek);
					if ($this->nama_proyek->Exportable) $Doc->ExportField($this->nama_proyek);
					if ($this->tanggal->Exportable) $Doc->ExportField($this->tanggal);
					if ($this->kardus->Exportable) $Doc->ExportField($this->kardus);
					if ($this->kayu->Exportable) $Doc->ExportField($this->kayu);
					if ($this->besi->Exportable) $Doc->ExportField($this->besi);
					if ($this->harga->Exportable) $Doc->ExportField($this->harga);
				}
				$Doc->EndExportRow();
			}
			$Recordset->MoveNext();
		}

		// Export aggregates (horizontal format only)
		if ($Doc->Horizontal) {
			$this->RowType = EW_ROWTYPE_AGGREGATE;
			$this->ResetAttrs();
			$this->AggregateListRow();
			$Doc->BeginExportRow(-1);
			$Doc->ExportAggregate($this->id_proyek, '');
			$Doc->ExportAggregate($this->nama_proyek, '');
			$Doc->ExportAggregate($this->tanggal, '');
			$Doc->ExportAggregate($this->kardus, 'TOTAL');
			$Doc->ExportAggregate($this->kayu, 'TOTAL');
			$Doc->ExportAggregate($this->besi, 'TOTAL');
			$Doc->ExportAggregate($this->harga, 'TOTAL');
			$Doc->EndExportRow();
		}
		$Doc->ExportTableFooter();
	}

	// Table level events
	// Recordset Selecting event
	function Recordset_Selecting(&$filter) {

		// Enter your code here	
	}

	// Recordset Selected event
	function Recordset_Selected(&$rs) {

		//echo "Recordset Selected";
	}

	// Recordset Search Validated event
	function Recordset_SearchValidated() {

		// Example:
		//$this->MyField1->AdvancedSearch->SearchValue = "your search criteria"; // Search value

	}

	// Recordset Searching event
	function Recordset_Searching(&$filter) {

		// Enter your code here	
	}

	// Row_Selecting event
	function Row_Selecting(&$filter) {

		// Enter your code here	
	}

	// Row Selected event
	function Row_Selected(&$rs) {

		//echo "Row Selected";
	}

	// Row Inserting event
	function Row_Inserting($rsold, &$rsnew) {

		// Enter your code here
		// To cancel, set return value to FALSE

		return TRUE;
	}

	// Row Inserted event
	function Row_Inserted($rsold, &$rsnew) {

		//echo "Row Inserted"
	}

	// Row Updating event
	function Row_Updating($rsold, &$rsnew) {

		// Enter your code here
		// To cancel, set return value to FALSE

		return TRUE;
	}

	// Row Updated event
	function Row_Updated($rsold, &$rsnew) {

		//echo "Row Updated";
	}

	// Row Update Conflict event
	function Row_UpdateConflict($rsold, &$rsnew) {

		// Enter your code here
		// To ignore conflict, set return value to FALSE

		return TRUE;
	}

	// Row Deleting event
	function Row_Deleting(&$rs) {

		// Enter your code here
		// To cancel, set return value to False

		return TRUE;
	}

	// Row Deleted event
	function Row_Deleted(&$rs) {

		//echo "Row Deleted";
	}

	// Email Sending event
	function Email_Sending(&$Email, &$Args) {

		//var_dump($Email); var_dump($Args); exit();
		return TRUE;
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
