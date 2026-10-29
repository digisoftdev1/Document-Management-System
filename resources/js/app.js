import "./bootstrap";

import Alpine from "alpinejs";
import collapse from "@alpinejs/collapse";

import $ from "jquery";

import DataTable from "datatables.net-dt";
import "datatables.net-buttons-dt";

import "datatables.net-buttons/js/buttons.html5.mjs";
import "datatables.net-buttons/js/buttons.colVis.mjs";
import "datatables.net-dt/css/dataTables.dataTables.css";
import "datatables.net-buttons-dt/css/buttons.dataTables.css";
import JSZip from "jszip";

import Chart from "chart.js/auto";
import Swal from "sweetalert2";

window.Alpine = Alpine;

window.$ = window.jQuery = $;

window.Chart = Chart;
window.Swal = Swal;

window.DataTable = DataTable;

// Register export libraries
DataTable.Buttons.jszip(JSZip);

Alpine.plugin(collapse);
Alpine.start();
