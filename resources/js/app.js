import "./bootstrap";
// import './smooth-navigation';

import Alpine from "alpinejs";
import collapse from "@alpinejs/collapse";
import $ from "jquery";
import jQuery from "jquery";
// import Sortable from "sortablejs";
import DataTable from "datatables.net-dt";
import Chart from "chart.js/auto";
import Swal from "sweetalert2";
window.Alpine = Alpine;
window.$ = window.jQuery = $;
window.Chart = Chart;
window.Swal = Swal;

// window.Sortable = Sortable;
window.DataTable = DataTable;

Alpine.plugin(collapse);
Alpine.start();
