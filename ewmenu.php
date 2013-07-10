<!-- Begin Main Menu -->
<div class="phpmaker">
<?php $RootMenu = new cMenu("RootMenu"); ?>
<?php

// Generate all menu items
$RootMenu->IsRoot = TRUE;
$RootMenu->AddMenuItem(22, $Language->MenuPhrase("22", "MenuText"), "", -1, "", IsLoggedIn(), FALSE);
$RootMenu->AddMenuItem(1, $Language->MenuPhrase("1", "MenuText"), "lh_danalist.php", 22, "", AllowListMenu('{67264FB2-6364-478B-87DD-B3E0D7A29425}lh_dana'), FALSE);
$RootMenu->AddMenuItem(2, $Language->MenuPhrase("2", "MenuText"), "lh_notalist.php", 22, "", AllowListMenu('{67264FB2-6364-478B-87DD-B3E0D7A29425}lh_nota'), FALSE);
$RootMenu->AddMenuItem(3, $Language->MenuPhrase("3", "MenuText"), "lh_proyeklist.php", 22, "", AllowListMenu('{67264FB2-6364-478B-87DD-B3E0D7A29425}lh_proyek'), FALSE);
$RootMenu->AddMenuItem(25, $Language->MenuPhrase("25", "MenuText"), "lh_gajilist.php", 22, "", AllowListMenu('{67264FB2-6364-478B-87DD-B3E0D7A29425}lh_gaji'), FALSE);
$RootMenu->AddMenuItem(26, $Language->MenuPhrase("26", "MenuText"), "lh_total_gajilist.php", 22, "", AllowListMenu('{67264FB2-6364-478B-87DD-B3E0D7A29425}lh_total_gaji'), FALSE);
$RootMenu->AddMenuItem(23, $Language->MenuPhrase("23", "MenuText"), "", -1, "", IsLoggedIn(), FALSE);
$RootMenu->AddMenuItem(6, $Language->MenuPhrase("6", "MenuText"), "lh_input_notalist.php", 23, "", AllowListMenu('{67264FB2-6364-478B-87DD-B3E0D7A29425}lh_input_nota'), FALSE);
$RootMenu->AddMenuItem(7, $Language->MenuPhrase("7", "MenuText"), "lh_input_proyeklist.php", 23, "", AllowListMenu('{67264FB2-6364-478B-87DD-B3E0D7A29425}lh_input_proyek'), FALSE);
$RootMenu->AddMenuItem(24, $Language->MenuPhrase("24", "MenuText"), "", -1, "", IsLoggedIn(), FALSE);
$RootMenu->AddMenuItem(4, $Language->MenuPhrase("4", "MenuText"), "Laporan_Nota_Pembelianreport.php", 24, "", AllowListMenu('{67264FB2-6364-478B-87DD-B3E0D7A29425}Laporan Nota Pembelian'), FALSE);
$RootMenu->AddMenuItem(5, $Language->MenuPhrase("5", "MenuText"), "Laporan_Pendapatan_Proyekreport.php", 24, "", AllowListMenu('{67264FB2-6364-478B-87DD-B3E0D7A29425}Laporan Pendapatan Proyek'), FALSE);
$RootMenu->AddMenuItem(18, $Language->MenuPhrase("18", "MenuText"), "", -1, "", IsLoggedIn(), FALSE);
$RootMenu->AddMenuItem(8, $Language->MenuPhrase("8", "MenuText"), "lh_userlist.php", 18, "", AllowListMenu('{67264FB2-6364-478B-87DD-B3E0D7A29425}lh_user'), FALSE);
$RootMenu->AddMenuItem(9, $Language->MenuPhrase("9", "MenuText"), "userlevelpermissionslist.php", 18, "", (@$_SESSION[EW_SESSION_USER_LEVEL] & EW_ALLOW_ADMIN) == EW_ALLOW_ADMIN, FALSE);
$RootMenu->AddMenuItem(10, $Language->MenuPhrase("10", "MenuText"), "userlevelslist.php", 18, "", (@$_SESSION[EW_SESSION_USER_LEVEL] & EW_ALLOW_ADMIN) == EW_ALLOW_ADMIN, FALSE);
$RootMenu->AddMenuItem(-2, $Language->Phrase("ChangePwd"), "changepwd.php", -1, "", IsLoggedIn() && !IsSysAdmin());
$RootMenu->AddMenuItem(-1, $Language->Phrase("Logout"), "logout.php", -1, "", IsLoggedIn());
$RootMenu->AddMenuItem(-1, $Language->Phrase("Login"), "login.php", -1, "", !IsLoggedIn() && substr(@$_SERVER["URL"], -1 * strlen("login.php")) <> "login.php");
$RootMenu->Render();
?>
</div>
<!-- End Main Menu -->
