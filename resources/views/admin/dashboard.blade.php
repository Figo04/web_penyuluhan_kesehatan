@extends('layouts.admin')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.css" rel="stylesheet">
@endpush

@section('title', 'Dashboard')

@section('content')
<div class="page-header">
     <div>
         <div class="page-title">Dashboard</div>
         <div class="page-sub">Monitoring program penyuluhan kesehatan</div>
     </div>
     <div style="display:flex;gap:10px;">
         <a href="{{ route('admin.export.csv') }}" class="btn btn-outline">
             <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
             Export CSV
         </a>
         <a href="{{ route('admin.export.excel') }}" class="btn btn-primary">
             <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
             Export Excel
         </a>
     </div>
 </div>

 <div class="stats-grid">
     <div class="stat-card">
         <div class="stat-icon stat-icon-teal">
             <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
         </div>
         <div class="stat-number">{{ $totalRespondents }}</div>
         <div class="stat-label">Total Responden</div>
     </div>

     <div class="stat-card">
         <div class="stat-icon stat-icon-blue">
             <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 002-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
         </div>
         <div class="stat-number">{{ $preTestDone }}</div>
         <div class="stat-label">Pre-Test Selesai</div>
     </div>

     <div class="stat-card">
         <div class="stat-icon stat-icon-purple">
             <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
         </div>
         <div class="stat-number">{{ $postTestDone }}</div>
         <div class="stat-label">Post-Test Selesai</div>
     </div>

     <div class="stat-card">
         <div class="stat-icon stat-icon-amber">
             <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
         </div>
         <div class="stat-number">{{ $completed }}</div>
         <div class="stat-label">Komplit (Pre+Post)</div>
     </div>
 </div>

 <!-- Data Graphics Section -->
 <div style="margin: 32px 0;">
     <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
         <!-- Test Completion Chart -->
         <div class="card">
             <div style="font-size:17px;font-weight:800;margin-bottom:16px;">Status Penyelesaian Test</div>
             <div style="position:relative; height:280px;">
                 <canvas id="testCompletionChart"></canvas>
             </div>
         </div>

         <!-- Material Completion Chart -->
         <div class="card">
             <div style="font-size:17px;font-weight:800;margin-bottom:16px;">Penyelesaian Materi</div>
             <div style="position:relative; height:280px;">
                 <canvas id="materialChart"></canvas>
             </div>
         </div>

         <!-- Gender Distribution Chart -->
         <div class="card">
             <div style="font-size:17px;font-weight:800;margin-bottom:16px;">Distribusi Gender</div>
             <div style="position:relative; height:280px;">
                 <canvas id="genderChart"></canvas>
             </div>
         </div>

         <!-- Age Distribution Chart -->
         <div class="card">
             <div style="font-size:17px;font-weight:800;margin-bottom:16px;">Distribusi Usia</div>
             <div style="position:relative; height:280px;">
                 <canvas id="ageChart"></canvas>
             </div>
         </div>
     </div>
 </div>

 <div style="font-size:17px;font-weight:800;margin-bottom:16px;">Aktivitas Terbaru</div>

 <div class="card">
     <div class="table-wrap">
         <table>
             <thead>
                 <tr>
                     <th>Nama</th>
                     <th>Lokasi</th>
                     <th>Pre-Test</th>
                     <th>Post-Test</th>
                 </tr>
             </thead>
             <tbody>
                 @foreach($recentRespondents as $r)
                 <tr>
                     <td style="font-weight:600;">{{ $r->name }}</td>
                     <td style="color:var(--text-muted);">{{ $r->location ? $r->location->name : '—' }}</td>
                     <td>
                         @if($r->pre_test_done)
                             <span class="badge badge-success">✓ Selesai</span>
                         @else
                             <span class="badge badge-muted">○ Belum</span>
                         @endif
                     </td>
                     <td>
                         @if($r->post_test_done)
                             <span class="badge badge-success">✓ Selesai</span>
                         @else
                             <span class="badge badge-muted">○ Belum</span>
                         @endif
                     </td>
                 </tr>
                 @endforeach
             </tbody>
         </table>
     </div>
 </div>
 @endsection

 @push('scripts')
 <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
 <script>
     document.addEventListener('DOMContentLoaded', function() {
         // Test Completion Chart (Doughnut)
         const testCompletionCtx = document.getElementById('testCompletionChart').getContext('2d');
         new Chart(testCompletionCtx, {
             type: 'doughnut',
             data: {
                 labels: ['Belum Test', 'Hanya Pre-Test', 'Hanya Post-Test', 'Keduanya'],
                 datasets: [{
                     data: [{{ $neitherTest }}, {{ $preOnly }}, {{ $postOnly }}, {{ $bothTest }}],
                     backgroundColor: [
                         '#ef4444', // red
                         '#fbbf24', // amber
                         '#3b82f6', // blue
                         '#10b981'  // green
                     ],
                     borderWidth: 0
                 }]
             },
             options: {
                 responsive: true,
                 maintainAspectRatio: false,
                 plugins: {
                     legend: {
                         position: 'bottom',
                         labels: {
                             usePointStyle: true,
                             padding: 20
                         }
                     },
                     tooltip: {
                         callbacks: {
                             label: function(context) {
                                 const label = context.label || '';
                                 const value = context.parsed || 0;
                                 const sum = context.dataset.data.reduce((a, b) => a + b, 0);
                                 const percentage = (value / sum * 100).toFixed(1) + '%';
                                 return `${label}: ${value} (${percentage})`;
                             }
                         }
                     }
                 }
             }
         });

         // Material Completion Chart (Doughnut)
         const materialCtx = document.getElementById('materialChart').getContext('2d');
         new Chart(materialCtx, {
             type: 'doughnut',
             data: {
                 labels: ['Belum Selesai', 'Selesai'],
                 datasets: [{
                     data: [{{ $materialNotDone }}, {{ $materialDone }}],
                     backgroundColor: [
                         '#ef4444', // red
                         '#10b981'  // green
                     ],
                     borderWidth: 0
                 }]
             },
             options: {
                 responsive: true,
                 maintainAspectRatio: false,
                 plugins: {
                     legend: {
                         position: 'bottom',
                         labels: {
                             usePointStyle: true,
                             padding: 20
                         }
                     },
                     tooltip: {
                         callbacks: {
                             label: function(context) {
                                 const label = context.label || '';
                                 const value = context.parsed || 0;
                                 const sum = context.dataset.data.reduce((a, b) => a + b, 0);
                                 const percentage = (value / sum * 100).toFixed(1) + '%';
                                 return `${label}: ${value} (${percentage})`;
                             }
                         }
                     }
                 }
             }
         });

         // Gender Distribution Chart (Bar)
         const genderCtx = document.getElementById('genderChart').getContext('2d');
         new Chart(genderCtx, {
             type: 'bar',
             data: {
                 labels: ['Laki-laki', 'Perempuan'],
                 datasets: [{
                     label: 'Jumlah Responden',
                     data: [{{ $genderMale }}, {{ $genderFemale }}],
                     backgroundColor: [
                         '#3b82f6', // blue
                         '#ec4899'  // pink
                     ],
                     borderWidth: 0
                 }]
             },
             options: {
                 responsive: true,
                 maintainAspectRatio: false,
                 plugins: {
                     legend: {
                         display: false
                     },
                     tooltip: {
                         callbacks: {
                             label: function(context) {
                                 return context.parsed.y + ' orang';
                             }
                         }
                     }
                 },
                 scales: {
                     y: {
                         beginAtZero: true,
                         ticks: {
                             precision: 0
                         }
                     }
                 }
             }
         });

         // Age Distribution Chart (Bar)
         const ageCtx = document.getElementById('ageChart').getContext('2d');
         new Chart(ageCtx, {
             type: 'bar',
             data: {
                 labels: [@json(array_keys($ageGroups))],
                 datasets: [{
                     label: 'Jumlah Responden',
                     data: [@json(array_values($ageGroups))],
                     backgroundColor: [
                         '#8b5cf6', // violet
                         '#06b6d4', // cyan
                         '#10b981', // emerald
                         '#f59e0b', // amber
                         '#ef4444'  // red
                     ],
                     borderWidth: 0
                 }]
             },
             options: {
                 responsive: true,
                 maintainAspectRatio: false,
                 plugins: {
                     legend: {
                         display: false
                     },
                     tooltip: {
                         callbacks: {
                             label: function(context) {
                                 return context.parsed.y + ' orang';
                             }
                         }
                     }
                 },
                 scales: {
                     y: {
                         beginAtZero: true,
                         ticks: {
                             precision: 0
                         }
                     }
                 }
             }
         });
     });
 </script>
 @endpush