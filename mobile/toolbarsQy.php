<?php 
include( '../include.php' );

if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = ''; };


if ( $t == 'tm_search' ) {
	print_r('[
    {  "id": "search", "type": "input", "placeholder": "Search", "icon": "mdi mdi-magnify", "width": "100px" },
    { "id": "add", "icon": "mdi mdi-timer-plus-outline", "value": "Add" },
    { "id": "edit", "icon": "mdi mdi-timer-edit-outline", "value": "Edit" },
    { "type": "separator" },
    {  "id": "delete", "icon": "mdi mdi-timer-remove-outline", "value": "Delete" }

    ]');
}
if ( $t == 'tp_search' ) {
    print_r('[
    {  "id": "search", "type": "input", "placeholder": "Search", "icon": "mdi mdi-magnify", "width": "100px" },
    { "id": "add", "icon": "mdi mdi-timetable", "value": "Add" },
    { "id": "edit", "icon": "mdi mdi-timer-edit-outline", "value": "Edit" },
    { "type": "separator" },
    {  "id": "delete", "icon": "mdi mdi-timer-remove-outline", "value": "Delete" }

    ]');
}
if ( $t == 'nt_search' ) {
    print_r('[
    {  "id": "search", "type": "input", "placeholder": "Search", "icon": "mdi mdi-magnify" },
    { "id": "add", "icon": "mdi mdi-notebook-plus-outline", "value": "Add" },
    { "type": "separator" },
    {  "id": "delete", "icon": "mdi mdi-notebook-remove-outline", "value": "Delete" }

    ]');
}
if ( $t == 'wd_search' ) {
    print_r('[
    {  "id": "search", "type": "input", "placeholder": "Search", "icon": "mdi mdi-text-search" },
    { "type": "separator" },
    {  "id": "select", "icon": "mdi mdi-touch-text-outline", "value": "Select" }

    ]');
}
if ( $t == 'ex_search' ) {
    print_r('[
    {  "id": "search", "type": "input", "placeholder": "Search", "icon": "mdi mdi-credit-card-search-outline" },
    { "id": "add", "icon": "mdi mdi-credit-card-plus-outline", "value": "Add" },
    { "id": "edit", "icon": "mdi mdi-credit-card-edit-outline", "value": "Edit" },
    { "type": "separator" },
    {  "id": "delete", "icon": "mdi mdi-credit-card-remove-outline", "value": "Delete" }

    ]');
}

?>