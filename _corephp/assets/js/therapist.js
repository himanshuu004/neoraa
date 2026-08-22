$(document).ready(function() {
    // Live update for timetable grid (poll every 5 seconds)
    var lastUpdateTime = null;
    var updateInterval = null;
    
    function loadTimetableGrid() {
        $.ajax({
            url: 'api/get_timetable_grid.php',
            method: 'GET',
            success: function(response) {
                var res = typeof response === 'string' ? JSON.parse(response) : response;
                if (res.success) {
                    // Only update if data has changed
                    if (lastUpdateTime === null || res.last_updated > lastUpdateTime) {
                        renderTimetableGrid(res);
                        lastUpdateTime = res.last_updated;
                    }
                }
            },
            error: function() {
                console.error('Error loading timetable grid');
            }
        });
    }
    
    function renderTimetableGrid(data) {
        var days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        var timeSlots = data.time_slots || [];
        var timetable = data.timetable || {};
        
        // Store data globally for export
        window.timetableGridData = {
            days: days,
            timeSlots: timeSlots,
            timetable: timetable
        };
        
        var html = '<div class="table-responsive"><table class="table table-bordered timetable-grid-view" id="timetableGridDisplay">';
        // Header row: Time slots on X-axis (top)
        html += '<thead class="table-light"><tr><th class="time-column">Day / Time</th>';
        
        timeSlots.forEach(function(slot) {
            html += '<th class="day-column">' + slot.display_name + '</th>';
        });
        html += '</tr></thead><tbody>';
        
        // Rows: Days on Y-axis (left)
        days.forEach(function(day) {
            html += '<tr><td class="time-column fw-bold">' + day + '</td>';
            
            timeSlots.forEach(function(slot) {
                var timeStart = slot.time_start;
                var cellData = timetable[day] && timetable[day][timeStart] ? timetable[day][timeStart] : null;
                
                html += '<td class="day-column">';
                if (cellData) {
                    html += '<div class="cell-content-readonly" title="' + (cellData.kid_name || 'N/A') + '">';
                    html += '<strong>' + (cellData.kid_name || 'N/A') + '</strong>';
                    if (cellData.session_type) {
                        html += '<br><small class="text-muted"><i class="fas fa-tag"></i> ' + cellData.session_type + '</small>';
                    }
                    html += '</div>';
                } else {
                    html += '<span class="text-muted">-</span>';
                }
                html += '</td>';
            });
            
            html += '</tr>';
        });
        
        html += '</tbody></table></div>';
        
        // Update grid container
        var gridContainer = $('.timetable-grid-container');
        if (gridContainer.length) {
            gridContainer.html(html);
        }
    }
    
    // Load grid when timetable tab is shown
    $('#timetable-tab').on('shown.bs.tab', function() {
        loadTimetableGrid();
        // Start polling for updates
        if (updateInterval) {
            clearInterval(updateInterval);
        }
        updateInterval = setInterval(loadTimetableGrid, 5000); // Update every 5 seconds
    });
    
    // Stop polling when tab is hidden
    $('#timetable-tab').on('hidden.bs.tab', function() {
        if (updateInterval) {
            clearInterval(updateInterval);
            updateInterval = null;
        }
    });
    
    // Load grid immediately if timetable tab is active
    if ($('#timetable-tab').hasClass('active')) {
        loadTimetableGrid();
        updateInterval = setInterval(loadTimetableGrid, 5000);
    }
    
    // Initialize DataTables (for other tables if any)
    if ($('#timetableTable').length && $.fn.DataTable) {
        $('#timetableTable').DataTable({
            responsive: true,
            pageLength: 10,
            order: [[0, 'asc'], [1, 'asc']]
        });
    }
});

// Export functions - Grid structure
function exportTimetable(format) {
    var gridData = window.timetableGridData;
    if (!gridData) {
        alert('No timetable data available. Please refresh the page.');
        return;
    }
    
    if (format === 'csv') {
        exportToCSV(gridData);
    } else if (format === 'pdf') {
        exportToPDF(gridData);
    } else if (format === 'excel') {
        exportToExcel(gridData);
    }
}

function exportToCSV(gridData) {
    var csv = ',';
    
    // Header row: Time slots
    gridData.timeSlots.forEach(function(slot) {
        csv += '"' + slot.display_name + '",';
    });
    csv = csv.slice(0, -1) + '\n'; // Remove last comma, add newline
    
    // Data rows: Days
    gridData.days.forEach(function(day) {
        csv += '"' + day + '",';
        gridData.timeSlots.forEach(function(slot) {
            var timeStart = slot.time_start;
            var cellData = gridData.timetable[day] && gridData.timetable[day][timeStart] ? gridData.timetable[day][timeStart] : null;
            var kidName = cellData ? (cellData.kid_name || '-') : '-';
            csv += '"' + kidName + '",';
        });
        csv = csv.slice(0, -1) + '\n'; // Remove last comma, add newline
    });
    
    var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    var url = window.URL.createObjectURL(blob);
    var a = document.createElement('a');
    a.href = url;
    a.download = 'timetable_grid_' + new Date().toISOString().split('T')[0] + '.csv';
    a.click();
    window.URL.revokeObjectURL(url);
}

function exportToPDF(gridData) {
    const { jsPDF } = window.jspdf;
    var doc = new jsPDF('landscape', 'mm', 'a4');
    
    doc.setFontSize(16);
    doc.text('Weekly Timetable Grid', 14, 15);
    doc.setFontSize(9);
    
    var startX = 14;
    var startY = 25;
    var pageWidth = doc.internal.pageSize.getWidth();
    var pageHeight = doc.internal.pageSize.getHeight();
    var totalCols = gridData.timeSlots.length + 1;
    var cellWidth = (pageWidth - startX * 2) / totalCols;
    var cellHeight = 7;
    var x = startX;
    var y = startY;
    
    // Header row: Time slots (X-axis - top)
    doc.setFont(undefined, 'bold');
    doc.rect(x, y - 5, cellWidth, cellHeight);
    doc.text('Day / Time', x + 2, y);
    x += cellWidth;
    
    gridData.timeSlots.forEach(function(slot) {
        doc.rect(x, y - 5, cellWidth, cellHeight);
        doc.text(slot.display_name, x + cellWidth/2, y, { align: 'center', maxWidth: cellWidth - 4 });
        x += cellWidth;
    });
    
    y += cellHeight;
    doc.setFont(undefined, 'normal');
    
    // Data rows: Days (Y-axis - left)
    gridData.days.forEach(function(day) {
        if (y + cellHeight > pageHeight - 20) {
            doc.addPage();
            y = 15;
        }
        
        x = startX;
        doc.setFont(undefined, 'bold');
        doc.rect(x, y - 5, cellWidth, cellHeight);
        doc.text(day, x + 2, y);
        x += cellWidth;
        doc.setFont(undefined, 'normal');
        
        gridData.timeSlots.forEach(function(slot) {
            var timeStart = slot.time_start;
            var cellData = gridData.timetable[day] && gridData.timetable[day][timeStart] ? gridData.timetable[day][timeStart] : null;
            var kidName = cellData ? (cellData.kid_name || '-') : '-';
            doc.rect(x, y - 5, cellWidth, cellHeight);
            doc.text(kidName, x + cellWidth/2, y, { align: 'center', maxWidth: cellWidth - 4 });
            x += cellWidth;
        });
        
        y += cellHeight;
    });
    
    doc.save('timetable_grid_' + new Date().toISOString().split('T')[0] + '.pdf');
}

function exportToExcel(gridData) {
    var wb = XLSX.utils.book_new();
    
    // Build grid data: First row is time slots, then days as rows
    var wsData = [];
    
    // Header row: Empty first cell, then time slots
    var headerRow = [''];
    gridData.timeSlots.forEach(function(slot) {
        headerRow.push(slot.display_name);
    });
    wsData.push(headerRow);
    
    // Data rows: Day name, then kid names
    gridData.days.forEach(function(day) {
        var row = [day];
        gridData.timeSlots.forEach(function(slot) {
            var timeStart = slot.time_start;
            var cellData = gridData.timetable[day] && gridData.timetable[day][timeStart] ? gridData.timetable[day][timeStart] : null;
            var kidName = cellData ? (cellData.kid_name || '') : '';
            row.push(kidName);
        });
        wsData.push(row);
    });
    
    var ws = XLSX.utils.aoa_to_sheet(wsData);
    
    // Set column widths
    var colWidths = [{ wch: 15 }]; // First column (Day) width
    gridData.timeSlots.forEach(function() {
        colWidths.push({ wch: 15 }); // Time slot columns width
    });
    ws['!cols'] = colWidths;
    
    XLSX.utils.book_append_sheet(wb, ws, 'Timetable Grid');
    
    XLSX.writeFile(wb, 'timetable_grid_' + new Date().toISOString().split('T')[0] + '.xlsx');
}
