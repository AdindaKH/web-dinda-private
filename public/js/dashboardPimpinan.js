// document.addEventListener('DOMContentLoaded', function () {
//     const ctx = document.getElementById('financeChart').getContext('2d');

//    const chart = new Chart(ctx, {
//         type: 'line',
//         data: {
//             labels: @json($labels),
//             datasets: [
//                 {
//                     label: 'Pendapatan',
//                     data: @json($grafikPendapatan), // Data proporsional
//                     borderColor: 'blue',
//                     backgroundColor: 'rgba(0, 0, 255, 0.2)',
//                     borderWidth: 2
//                 },
//                 {
//                     label: 'Pengeluaran',
//                     data: @json($grafikPengeluaran), // Data proporsional
//                     borderColor: 'red',
//                     backgroundColor: 'rgba(255, 0, 0, 0.2)',
//                     borderWidth: 2
//                 }
//             ]
//         },
//         options: {
//             responsive: true,
//             plugins: {
//                 tooltip: {
//                     callbacks: {
//                         label: function(context) {
//                             const index = context.dataIndex;
//                             const label = context.dataset.label;
//                             const dataPendapatan = @json($pendapatanBulanan);
//                             const dataPengeluaran = @json($pengeluaranBulanan);
//                             let value = 0;

//                             if (label === 'Pendapatan') {
//                                 value = dataPendapatan[index];
//                             } else {
//                                 value = dataPengeluaran[index];
//                             }

//                             return label + ': Rp ' + value.toLocaleString('id-ID');
//                         }
//                     }
//                 }
//             },
//             scales: {
//                 y: {
//                     title: {
//                         display: true,
//                         text: 'Skala Proporsional (%)'
//                     },
//                     ticks: {
//                         callback: function(value) {
//                             return value + '%';
//                         }
//                     }
//                 }
//             }
//         }
//     });
// });
