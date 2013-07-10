<?php

// Global variable for table object
$input_nota = NULL;

//
// Table class for input nota
//
class cinput_nota extends cTable {
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
		$this->TableVar = 'input_nota';
		$this->TableName = 'input nota';
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

		// id_nota
		$this->id_nota = new cField('input_nota', 'input nota', 'x_id_nota', 'id_nota', '`id_nota`', '`id_nota`', 3, -1, FALSE, '`id_nota`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->id_nota->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['id_nota'] = &$this->id_nota;

		// id_dana
		$this->id_dana = new cField('input_nota', 'input nota', 'x_id_dana', 'id_dana', '`id_dana`', '`id_dana`', 3, -1, FALSE, '`id_dana`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->id_dana->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['id_dana'] = &$this->id_dana;

		// nomor_nota
		$this->nomor_nota = new cField('input_nota', 'input nota', 'x_nomor_nota', 'nomor_nota', '`nomor_nota`', '`nomor_nota`', 200, -1, FALSE, '`nomor_nota`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['nomor_nota'] = &$this->nomor_nota;

		// nama_toko
		$this->nama_toko = new cField('input_nota', 'input nota', 'x_nama_toko', 'nama_toko', '`nama_toko`', '`nama_toko`', 200, -1, FALSE, '`nama_toko`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['nama_toko'] = &$this->nama_toko;

		// tanggal_nota
		$this->tanggal_nota = new cField('input_nota', 'input nota', 'x_tanggal_nota', 'tanggal_nota', '`tanggal_nota`', 'DATE_FORMAT(`tanggal_nota`, \'%d/%m/%Y %H:%i:%s\')', 133, 7, FALSE, '`tanggal_nota`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->tanggal_nota->FldDefaultErrMsg = str_replace("%s", "/", $Language->Phrase("IncorrectDateDMY"));
		$this->fields['tanggal_nota'] = &$this->tanggal_nota;

		// jumlah_pembelian
		$this->jumlah_pembelian = new cField('input_nota', 'input nota', 'x_jumlah_pembelian', 'jumlah_pembelian', '`jumlah_pembelian`', '`jumlah_pembelian`', 3, -1, FALSE, '`jumlah_pembelian`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->jumlah_pembelian->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['jumlah_pembelian'] = &$this->jumlah_pembelian;

		// pesan
		$this->pesan = new cField('input_nota', 'input nota', 'x_pesan', 'pesan', '`pesan`', '`pesan`', 201, -1, FALSE, '`pesan`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['pesan'] = &$this->pesan;
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
		return "`input nota`";
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
	var $UpdateTable = "`input nota`";

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
			$sql .= ew_QuotedName('id_nota') . '=' . ew_QuotedValue($rs['id_nota'], $this->id_nota->FldDataType) . ' AND ';
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
		return "`id_nota` = @id_nota@";
	}

	// Key filter
	function KeyFilter() {
		$sKeyFilter = $this->SqlKeyFilter();
		if (!is_numeric($this->id_nota->CurrentValue))
			$sKeyFilter = "0=1"; // Invalid key
		$sKeyFilter = str_replace("@id_nota@", ew_AdjustSql($this->id_nota->CurrentValue), $sKeyFilter); // Replace key value
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
			return "input_notalist.php";
		}
	}

	function setReturnUrl($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL] = $v;
	}

	// List URL
	function GetListUrl() {
		return "input_notalist.php";
	}

	// View URL
	function GetViewUrl() {
		return $this->KeyUrl("input_notaview.php", $this->UrlParm());
	}

	// Add URL
	function GetAddUrl() {
		return "input_notaadd.php";
	}

	// Edit URL
	function GetEditUrl($parm = "") {
		return $this->KeyUrl("input_notaedit.php", $this->UrlParm($parm));
	}

	// Inline edit URL
	function GetInlineEditUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=edit"));
	}

	// Copy URL
	function GetCopyUrl($parm = "") {
		return $this->KeyUrl("input_notaadd.php", $this->UrlParm($parm));
	}

	// Inline copy URL
	function GetInlineCopyUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=copy"));
	}

	// Delete URL
	function GetDeleteUrl() {
		return $this->KeyUrl("input_notadelete.php", $this->UrlParm());
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

	// Load row values from recordset
	function LoadListRowValues(&$rs) {
		$this->id_nota->setDbValue($rs->fields('id_nota'));
		$this->id_dana->setDbValue($rs->fields('id_dana'));
		$this->nomor_nota->setDbValue($rs->fields('nomor_nota'));
		$this->nama_toko->setDbValue($rs->fields('nama_toko'));
		$this->tanggal_nota->setDbValue($rs->fields('tanggal_nota'));
		$this->jumlah_pembelian->setDbValue($rs->fields('jumlah_pembelian'));
		$this->pesan->setDbValue($rs->fields('pesan'));
	}

	// Render list row values
	function RenderListRow() {
		global $conn, $Security;

		// Call Row Rendering event
		$this->Row_Rendering();

   // Common render codes
		// id_nota
		// id_dana
		// nomor_nota
		// nama_toko
		// tanggal_nota
		// jumlah_pembelian
		// pesan
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
		$this->jumlah_pembelian->ViewValue = ew_FormatNumber($this->jumlah_pembelian->ViewValue, 2, -1, -1, -1);
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

		// Call Row Rendered event
		$this->Row_Rendered();
	}

	// Aggregate list row values
	function AggregateListRowValues() {
			if (is_numeric($this->jumlah_pembelian->CurrentValue))
				$this->jumlah_pembelian->Total += $this->jumlah_pembelian->CurrentValue; // Accumulate total
	}

	// Aggregate list row (for rendering)
	function AggregateListRow() {
			$this->jumlah_pembelian->CurrentValue = $this->jumlah_pembelian->Total;
			$this->jumlah_pembelian->ViewValue = $this->jumlah_pembelian->CurrentValue;
			$this->jumlah_pembelian->ViewValue = ew_FormatNumber($this->jumlah_pembelian->ViewValue, 2, -1, -1, -1);
			$this->jumlah_pembelian->CellCssStyle .= "text-align: right;";
			$this->jumlah_pembelian->ViewCustomAttributes = "";
			$this->jumlah_pembelian->HrefValue = ""; // Clear href value
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
				if ($this->id_nota->Exportable) $Doc->ExportCaption($this->id_nota);
				if ($this->id_dana->Exportable) $Doc->ExportCaption($this->id_dana);
				if ($this->nomor_nota->Exportable) $Doc->ExportCaption($this->nomor_nota);
				if ($this->nama_toko->Exportable) $Doc->ExportCaption($this->nama_toko);
				if ($this->tanggal_nota->Exportable) $Doc->ExportCaption($this->tanggal_nota);
				if ($this->jumlah_pembelian->Exportable) $Doc->ExportCaption($this->jumlah_pembelian);
				if ($this->pesan->Exportable) $Doc->ExportCaption($this->pesan);
			} else {
				if ($this->id_nota->Exportable) $Doc->ExportCaption($this->id_nota);
				if ($this->id_dana->Exportable) $Doc->ExportCaption($this->id_dana);
				if ($this->nomor_nota->Exportable) $Doc->ExportCaption($this->nomor_nota);
				if ($this->nama_toko->Exportable) $Doc->ExportCaption($this->nama_toko);
				if ($this->tanggal_nota->Exportable) $Doc->ExportCaption($this->tanggal_nota);
				if ($this->jumlah_pembelian->Exportable) $Doc->ExportCaption($this->jumlah_pembelian);
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
					if ($this->id_nota->Exportable) $Doc->ExportField($this->id_nota);
					if ($this->id_dana->Exportable) $Doc->ExportField($this->id_dana);
					if ($this->nomor_nota->Exportable) $Doc->ExportField($this->nomor_nota);
					if ($this->nama_toko->Exportable) $Doc->ExportField($this->nama_toko);
					if ($this->tanggal_nota->Exportable) $Doc->ExportField($this->tanggal_nota);
					if ($this->jumlah_pembelian->Exportable) $Doc->ExportField($this->jumlah_pembelian);
					if ($this->pesan->Exportable) $Doc->ExportField($this->pesan);
				} else {
					if ($this->id_nota->Exportable) $Doc->ExportField($this->id_nota);
					if ($this->id_dana->Exportable) $Doc->ExportField($this->id_dana);
					if ($this->nomor_nota->Exportable) $Doc->ExportField($this->nomor_nota);
					if ($this->nama_toko->Exportable) $Doc->ExportField($this->nama_toko);
					if ($this->tanggal_nota->Exportable) $Doc->ExportField($this->tanggal_nota);
					if ($this->jumlah_pembelian->Exportable) $Doc->ExportField($this->jumlah_pembelian);
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
			$Doc->ExportAggregate($this->id_nota, '');
			$Doc->ExportAggregate($this->id_dana, '');
			$Doc->ExportAggregate($this->nomor_nota, '');
			$Doc->ExportAggregate($this->nama_toko, '');
			$Doc->ExportAggregate($this->tanggal_nota, '');
			$Doc->ExportAggregate($this->jumlah_pembelian, 'TOTAL');
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
