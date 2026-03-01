import './bootstrap';
import './autosave';

import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse'

Alpine.plugin(collapse)
import ApexCharts from 'apexcharts'

window.Alpine = Alpine;
window.ApexCharts = ApexCharts;

Alpine.start();
