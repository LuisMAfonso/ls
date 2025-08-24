<?php 
include( 'include.php' );

if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = ''; };


if ( $t == 'change' ) {
    print_r('[
    { "id": "change", "icon": "mdi mdi-lock-open-check-outline", "value": "Change", "twoState": "true" }
    ]');
}
if ( $t == 'exp_daed' ) {
    print_r('[
    { "type": "datePicker", "id": "dtFrom", "editable": true, "weekStart": "monday", "weekNumbers": false, "mode": "calendar", "width": "90px", "tooltip": "start date", "placeholder": "from", "value": "", "dateFormat": "%Y-%m-%d" },
    { "type": "datePicker", "id": "dtTo", "editable": true, "weekStart": "monday", "weekNumbers": false, "mode": "calendar", "width": "90px", "tooltip": "start date", "placeholder": "to", "value": "", "dateFormat": "%Y-%m-%d"  },
    { "type": "separator" },
    { "id": "add", "icon": "mdi mdi-credit-card-plus-outline", "value": "Add" },
    { "id": "edit", "icon": "mdi mdi-credit-card-edit-outline", "value": "Edit" },
    { "type": "separator" },
    {  "id": "delete", "icon": "mdi mdi-credit-card-remove-outline", "value": "Delete" }
    ]');
}
if ( $t == 'rt_sp' ) {
    print_r('[
    { "type": "datePicker", "id": "dtFrom", "editable": true, "weekStart": "monday", "weekNumbers": false, "mode": "calendar", "width": "90px", "tooltip": "start date", "placeholder": "from", "value": "", "dateFormat": "%Y-%m-%d" },
    { "type": "datePicker", "id": "dtTo", "editable": true, "weekStart": "monday", "weekNumbers": false, "mode": "calendar", "width": "90px", "tooltip": "start date", "placeholder": "to", "value": "", "dateFormat": "%Y-%m-%d"  },
    { "type": "separator" },
    { "id": "proj", "icon": "mdi mdi-land-plots-marker", "value": "By Project", "group": "byWhat", "active": "true" },
    { "id": "staff", "icon": "mdi mdi-human-queue", "value": "By Staff", "group": "byWhat" }
    ]');
}
if ( $t == 'a_c_r' ) {
    print_r('[
    { "id": "add", "icon": "mdi mdi-account-plus-outline", "value": "Add" },
    { "id": "edit", "icon": "mdi mdi-account-edit-outline", "value": "Edit" },
    { "type": "separator" },
    {  "id": "delete", "icon": "mdi mdi-account-remove-outline", "value": "Delete" },
    {  "type": "separator" },
    {  "id": "search", "type": "input", "placeholder": "Search", "icon": "mdi mdi-magnify" }
    ]');
}
if ( $t == 'r_aed_s' ) {
    print_r('[
    { "id": "add", "icon": "mdi mdi-invoice-text-plus-outline", "value": "Add" },
    { "id": "edit", "icon": "mdi mdi-invoice-text-edit-outline", "value": "Edit" },
    { "type": "separator" },
    {  "id": "delete", "icon": "mdi mdi-invoice-text-remove-outline", "value": "Delete" },
    {  "type": "separator" },
    {  "id": "search", "type": "input", "placeholder": "Search", "icon": "mdi mdi-magnify" },
    {  "type": "separator" },
    {  "id": "reqDet", "icon": "mdi mdi-file-table-box-multiple-outline", "value": "Request details" }
    ]');
}if ( $t == 'j_search' ) {
	print_r('[
    {  "id": "search", "type": "input", "placeholder": "Search", "icon": "mdi mdi-magnify" }
    ]');
}
if ( $t == 'rd_aed' ) {
    print_r('[
    { "id": "add", "icon": "mdi mdi-store-plus-outline", "value": "Add" },
    { "id": "edit", "icon": "mdi mdi-store-edit-outline", "value": "Edit" },
    { "type": "separator" },
    {  "id": "delete", "icon": "mdi mdi-store-remove-outline", "value": "Delete" }
    ]');
}
if ( $t == 'a_aed' ) {
    print_r('[
    { "id": "add", "icon": "mdi mdi-account-plus-outline", "value": "Add" },
    { "id": "edit", "icon": "mdi mdi-account-edit-outline", "value": "Edit" },
    { "type": "separator" },
    {  "id": "delete", "icon": "mdi mdi-account-remove-outline", "value": "Delete" }
    ]');
}
if ( $t == 'm_aed' ) {
    print_r('[
    { "id": "add", "icon": "mdi mdi-playlist-plus", "value": "Add" },
    { "id": "edit", "icon": "mdi mdi-playlist-edit", "value": "Edit" },
    { "type": "separator" },
    {  "id": "delete", "icon": "mdi mdi-playlist-remove", "value": "Delete" }
    ]');
}
if ( $t == 'm_aeds' ) {
    print_r('[
    { "id": "add", "icon": "mdi mdi-playlist-plus", "value": "Add" },
    { "id": "edit", "icon": "mdi mdi-playlist-edit", "value": "Edit" },
    { "type": "separator" },
    {  "id": "delete", "icon": "mdi mdi-playlist-remove", "value": "Delete" },
    {  "type": "separator" },
    {  "id": "search", "type": "input", "placeholder": "Search", "icon": "mdi mdi-text-search" }
    ]');
}
if ( $t == 'm_ed' ) {
    print_r('[
    { "id": "edit", "icon": "mdi mdi-playlist-edit", "value": "Edit" },
    { "type": "separator" },
    {  "id": "delete", "icon": "mdi mdi-playlist-remove", "value": "Delete" },
    { "type": "separator" },
    {  "id": "group", "icon": "mdi mdi-format-list-group", "value": "Group", "twoState": "true" },
    { "type": "separator" },
    {  "id": "note", "icon": "mdi mdi-message-bulleted", "value": "Note" },
    { "type": "separator" },
    {  "id": "import", "icon": "mdi mdi-file-import-outline", "value": "Import" },
    { "type": "separator" },
    {  "id": "print", "icon": "mdi mdi-printer-outline", "value": "Print" },
    { "type": "separator" },
    {  "id": "lock", "icon": "mdi mdi-sort-variant-lock", "value": "Lock" },
    { "type": "spacer" },
    {  "id": "imargin", "icon": "mdi mdi-margin", "value": "" },
    { "type": "title", "id": "margin", "value": "" }
    ]');
}
if ( $t == 'g_ad' ) {
    print_r('[
    { "id": "add", "icon": "mdi mdi-account-multiple-plus-outline", "value": "Add" },
    { "type": "separator" },
    {  "id": "delete", "icon": "mdi mdi-account-multiple-remove-outline", "value": "Delete" }
    ]');
}
if ( $t == 'g_aed' ) {
    print_r('[
    { "id": "add", "icon": "mdi mdi-account-multiple-plus-outline", "value": "Add" },
    { "id": "edit", "icon": "mdi mdi-account-multiple-check-outline", "value": "Edit" },
    { "type": "separator" },
    {  "id": "delete", "icon": "mdi mdi-account-multiple-remove-outline", "value": "Delete" }
    ]');
}
if ( $t == 'prj_e' ) {
    print_r('[
    { "id": "edProj", "icon": "mdi mdi-folder-edit-outline", "value": "Project" },
    { "id": "edDate", "icon": "mdi mdi-folder-edit-outline", "value": "Dates" },
    { "id": "edFina", "icon": "mdi mdi-folder-edit-outline", "value": "Finance" }
    ]');
}
if ( $t == 'f_aed' ) {
    print_r('[
    { "id": "add", "icon": "mdi mdi-folder-plus-outline", "value": "Add" },
    { "id": "edit", "icon": "mdi mdi-folder-edit-outline", "value": "Edit" },
    { "type": "separator" },
    {  "id": "view", "icon": "mdi mdi-folder-eye-outline", "value": "View" },
    { "type": "separator" },
    {  "id": "delete", "icon": "mdi mdi-file-remove-outline", "value": "Delete" },
    {  "type": "separator" },
    {  "id": "search", "type": "input", "placeholder": "Search", "icon": "mdi mdi-folder-search-outline" }
    ]');
}
if ( $t == 'e_aed' ) {
    print_r('[
    { "id": "add", "icon": "mdi mdi-file-document-plus-outline", "value": "Add" },
    { "id": "edit", "icon": "mdi mdi-file-document-edit-outline", "value": "Edit" },
    { "type": "separator" },
    {  "id": "view", "icon": "mdi mdi-file-eye-outline", "value": "View" },
    { "type": "separator" },
    {  "id": "delete", "icon": "mdi mdi-file-document-remove-outline", "value": "Delete" },
    {  "type": "separator" },
    {  "id": "search", "type": "input", "placeholder": "Search", "icon": "mdi mdi-file-find-outline" }
    ]');
}
if ( $t == 'n_aed' ) {
    print_r('[
    { "id": "add", "icon": "mdi mdi-notebook-plus-outline", "value": "Add" },
    { "id": "edit", "icon": "mdi mdi-notebook-edit-outline", "value": "Edit" },
    { "type": "separator" },
    {  "id": "delete", "icon": "mdi mdi-notebook-remove-outline", "value": "Delete" },
    {  "type": "separator" },
    {  "id": "search", "type": "input", "placeholder": "Search", "icon": "mdi mdi-text-search" }
    ]');
}
if ( $t == 't_aed' ) {
    print_r('[
    { "id": "add", "icon": "mdi mdi-tag-plus-outline", "value": "Add" },
    { "id": "edit", "icon": "mdi mdi-tag-edit-outline", "value": "Edit" },
    { "type": "separator" },
    {  "id": "delete", "icon": "mdi mdi-tag-remove-outline", "value": "Delete" }
    ]');
}
if ( $t == 's_aed' ) {
    print_r('[
    { "id": "add", "icon": "mdi mdi-vector-square-plus", "value": "Add" },
    { "id": "edit", "icon": "mdi mdi-vector-square-edit", "value": "Edit" },
    { "type": "separator" },
    {  "id": "delete", "icon": "mdi mdi-vector-square-remove", "value": "Delete" },
    { "type": "separator" },
    { "id": "addPrice", "icon": "mdi mdi-tag-plus-outline", "value": "Add price" }
    ]');
}
if ( $t == 'p_aeds' ) {
    print_r('[
    { "id": "add", "icon": "mdi mdi-package-variant-closed-plus", "value": "Add" },
    { "id": "edit", "icon": "mdi mdi-package-variant", "value": "Edit" },
    { "type": "separator" },
    {  "id": "delete", "icon": "mdi mdi-package-variant-closed-remove", "value": "Delete" },
    {  "type": "separator" },
    {  "id": "search", "type": "input", "placeholder": "Search", "icon": "mdi mdi-text-search" }
    ]');
}
if ( $t == 'ip_aeds' ) {
    print_r('[
    { "id": "iType", "type": "selectButton", "css": "width: 150px;", 
        "items": [ ');
    $sql = "SELECT impTypeCode
            FROM dataImpTypes
            ORDER BY impTypeDesc";
    $rows_emps = $db->Execute($sql);

    $first1 = 1;
    $fOption = '';
    while(!$rows_emps->EOF){
        if ( $first1 == 0 ) print_r(', ');
        if ( $first1 == 1 ) {
            $first1 = 0;
            $fOption = trim($rows_emps->fields[0]);
        }
        print_r('{ "value": "'.trim($rows_emps->fields[0]).'" }');
        $rows_emps->MoveNext();
    }
    $rows_emps->Close();
    print_r('  ], "value": "'.$fOption.'" 
    },
    { "type": "separator" },
    { "id": "add", "icon": "mdi mdi-package-variant-closed-plus", "value": "Add" },
    { "id": "edit", "icon": "mdi mdi-package-variant", "value": "Edit" },
    { "type": "separator" },
    {  "id": "delete", "icon": "mdi mdi-package-variant-closed-remove", "value": "Delete" },
    {  "type": "separator" },
    {  "id": "search", "type": "input", "placeholder": "Search", "icon": "mdi mdi-text-search" }
    ]');
}
if ( $t == 'k_aed' ) {
    print_r('[
    { "id": "add_l", "icon": "mdi mdi-account-cowboy-hat-outline", "value": "Add", "tooltip": "Labour" },
    { "id": "add_p", "icon": "mdi mdi-fence", "value": "Add", "tooltip": "Product" },
    { "id": "add_e", "icon": "mdi mdi-tractor-variant", "value": "Add", "tooltip": "Equipment" },
    { "id": "add_s", "icon": "mdi mdi-account-wrench-outline", "value": "Add", "tooltip": "Service" },
    { "id": "add_c", "icon": "mdi mdi-compost", "value": "Add", "tooltip": "Consumable" },
    { "type": "separator" },
    { "id": "edit", "icon": "mdi mdi-clipboard-edit-outline", "value": "Edit" },
    { "type": "separator" },
    {  "id": "delete", "icon": "mdi mdi-package-variant-remove", "value": "Delete" }
    ]');
}
if ( $t == 'b_aed' ) {
    print_r('[
    { "id": "add", "icon": "mdi mdi-briefcase-plus-outline", "value": "Add" },
    { "id": "edit", "icon": "mdi mdi-briefcase-edit-outline", "value": "Edit" },
    { "type": "separator" },
    {  "id": "delete", "icon": "mdi mdi-briefcase-remove-outline", "value": "Delete" }
    ]');
}
if ( $t == 't_aeds' ) {
    print_r('[
    { "id": "add", "icon": "mdi mdi-truck-plus-outline", "value": "Add" },
    { "id": "edit", "icon": "mdi mdi-truck-delivery-outline", "value": "Edit" },
    { "type": "separator" },
    {  "id": "delete", "icon": "mdi mdi-truck-remove-outline", "value": "Delete" },
    {  "type": "separator" },
    {  "id": "search", "type": "input", "placeholder": "Search", "icon": "mdi mdi-text-search" }
    ]');
}
if ( $t == 'tm_aed' ) {
    print_r('[
    { "id": "add", "icon": "mdi mdi-timer-plus-outline", "value": "Add" },
    { "type": "separator" },
    { "id": "edit", "icon": "mdi mdi-timer-edit-outline", "value": "Edit" },
    { "type": "separator" },
    {  "id": "delete", "icon": "mdi mdi-timer-remove-outline", "value": "Delete" }
    ]');
}
if ( $t == 'tp_aed' ) {
    print_r('[
    { "id": "add", "icon": "mdi mdi-timer-plus-outline", "value": "Add" },
    { "id": "addG", "icon": "mdi mdi-timetable", "value": "Add Team" },
    { "type": "separator" },
    { "id": "edit", "icon": "mdi mdi-timer-edit-outline", "value": "Edit" },
    { "type": "separator" },
    {  "id": "delete", "icon": "mdi mdi-timer-remove-outline", "value": "Delete" }
    ]');
}
if ( $t == 'tmt_aed' ) {
    print_r('[
    { "type": "datePicker", "id": "tmFrom", "editable": "true", "weekNumbers": false, "mode": "calendar", "timePicker": true, "timeFormat": 24, "dateFormat": "%Y-%m-%d %H:%i" },
    { "type": "datePicker", "id": "tmTo", "editable": "true", "weekNumbers": false, "mode": "calendar", "timePicker": true, "timeFormat": 24, "dateFormat": "%Y-%m-%d %H:%i" }, 
    { "id": "add", "icon": "mdi mdi-timer-plus-outline", "value": "Add" }
   ]');
}
if ( $t == 'r_ae' ) {
    print_r('[
    { "id": "add", "icon": "mdi mdi-file-document-plus-outline", "value": "Add" },
    { "id": "edit", "icon": "mdi mdi-file-document-edit-outline", "value": "Edit" }
    ]');
}

?>