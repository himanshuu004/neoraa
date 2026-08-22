$(document).ready(function() {
    // Helper function to check if DataTable is already initialized
    function isDataTableInitialized(tableId) {
        return $.fn.DataTable.isDataTable('#' + tableId);
    }
    
    // Helper function to initialize DataTable safely
    function initDataTable(tableId, options) {
        var $table = $('#' + tableId);
        if ($table.length === 0) {
            console.log('Table ' + tableId + ' not found');
            return false;
        }
        
        // Check if table has proper structure (at least a thead and tbody)
        if ($table.find('thead').length === 0) {
            console.log('Table ' + tableId + ' does not have a thead element');
            return false;
        }
        
        // Check if table has rows (at least header row)
        var headerRows = $table.find('thead tr').length;
        if (headerRows === 0) {
            console.log('Table ' + tableId + ' does not have header rows');
            return false;
        }
        
        // Check if table has proper column structure
        var headerCols = $table.find('thead tr:first th').length;
        if (headerCols === 0) {
            console.log('Table ' + tableId + ' does not have header columns');
            return false;
        }
        
        // Destroy existing instance if any
        if (isDataTableInitialized(tableId)) {
            console.log('Destroying existing DataTable for ' + tableId);
            try {
                var dt = $table.DataTable();
                dt.destroy(true); // true = remove table elements
            } catch (e) {
                console.warn('Error destroying DataTable:', e);
                // Force cleanup
                $table.removeClass('dataTable');
                $table.closest('.dataTables_wrapper').remove();
            }
        }
        
        // Initialize new DataTable
        try {
            // Check if table has any data rows
            var dataRows = $table.find('tbody tr').length;
            
            // Ensure table is visible before initializing
            if (!$table.is(':visible')) {
                // Temporarily show table for initialization
                $table.css('display', 'table');
            }
            
            // Check if table is empty - DataTables can have issues with empty tables
            if (dataRows === 0 && !options.bDestroy) {
                // Add empty state row if needed
                var emptyRow = '<tr><td colspan="' + headerCols + '" class="text-center text-muted">No data available</td></tr>';
                if ($table.find('tbody tr').length === 0) {
                    $table.find('tbody').html(emptyRow);
                }
            }
            
            // Initialize DataTable with destroy option to prevent reinit errors
            var dtOptions = $.extend({}, options, {
                destroy: true  // Allow reinitialization
            });
            
            $table.DataTable(dtOptions);
            console.log('DataTable initialized for ' + tableId);
            return true;
        } catch (e) {
            console.error('Error initializing DataTable for ' + tableId + ':', e);
            console.error('Table structure:', {
                rows: $table.find('tbody tr').length,
                cols: headerCols,
                visible: $table.is(':visible'),
                thead: $table.find('thead tr').length,
                tbody: $table.find('tbody').length
            });
            return false;
        }
    }
    
    // Initialize DataTables when tabs are shown
    var therapistsTab = document.querySelector('#therapists-tab');
    var timetableTab = document.querySelector('#timetable-tab');
    
    if (therapistsTab) {
        therapistsTab.addEventListener('shown.bs.tab', function() {
            initDataTable('therapistsTable', {
                responsive: true,
                pageLength: 10,
                order: [[0, 'desc']]
            });
        });
    }
    
    if (timetableTab) {
        timetableTab.addEventListener('shown.bs.tab', function() {
            initDataTable('timetableTable', {
                responsive: true,
                pageLength: 10,
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'csv',
                        text: '<i class="fas fa-file-csv"></i> CSV',
                        className: 'btn btn-sm btn-success'
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fas fa-file-pdf"></i> PDF',
                        className: 'btn btn-sm btn-danger',
                        orientation: 'landscape',
                        pageSize: 'A4'
                    },
                    {
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        className: 'btn btn-sm btn-success'
                    }
                ],
                order: [[2, 'asc'], [3, 'asc']]
            });
        });
    }
    
    if ($('#timetable').hasClass('active') || $('#timetable').hasClass('show')) {
        initDataTable('timetableTable', {
            responsive: true,
            pageLength: 10,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'csv',
                    text: '<i class="fas fa-file-csv"></i> CSV',
                    className: 'btn btn-sm btn-success'
                },
                {
                    extend: 'pdf',
                    text: '<i class="fas fa-file-pdf"></i> PDF',
                    className: 'btn btn-sm btn-danger',
                    orientation: 'landscape',
                    pageSize: 'A4'
                },
                {
                    extend: 'excel',
                    text: '<i class="fas fa-file-excel"></i> Excel',
                    className: 'btn btn-sm btn-success'
                }
            ],
            order: [[2, 'asc'], [3, 'asc']]
        });
    }
    
    // Therapist filter (will work after table is initialized)
    $('#therapistFilter').on('change', function() {
        if (isDataTableInitialized('timetableTable')) {
            try {
                var table = $('#timetableTable').DataTable();
                if ($(this).val()) {
                    table.column(1).search($(this).val()).draw();
                } else {
                    table.column(1).search('').draw();
                }
            } catch (e) {
                console.error('Error filtering timetable:', e);
            }
        }
    });
    
    // Create Therapist Form
    $('#createTherapistForm').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        
        $.ajax({
            url: 'api/create_therapist.php',
            method: 'POST',
            data: formData,
            success: function(response) {
                var res = typeof response === 'string' ? JSON.parse(response) : response;
                if (res.success) {
                    alert('Therapist created successfully');
                    location.reload();
                } else {
                    alert('Error: ' + res.message);
                }
            },
            error: function() {
                alert('An error occurred');
            }
        });
    });
    
    // Edit Therapist
    $('.edit-therapist').on('click', function() {
        var id = $(this).data('id');
        
        $.ajax({
            url: 'api/get_therapist.php?id=' + id,
            method: 'GET',
            success: function(response) {
                var res = typeof response === 'string' ? JSON.parse(response) : response;
                if (res.success) {
                    var data = res.data;
                    $('#editTherapistId').val(data.id);
                    $('#editTherapistUsername').val(data.username);
                    $('#editTherapistName').val(data.name || '');
                    $('#editTherapistContact').val(data.contact || '');
                    $('#editTherapistEmail').val(data.email || '');
                    $('#editTherapistModal').modal('show');
                }
            }
        });
    });
    
    // Update Therapist Form
    $('#editTherapistForm').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        
        $.ajax({
            url: 'api/update_therapist.php',
            method: 'POST',
            data: formData,
            success: function(response) {
                var res = typeof response === 'string' ? JSON.parse(response) : response;
                if (res.success) {
                    alert('Therapist updated successfully');
                    location.reload();
                } else {
                    alert('Error: ' + res.message);
                }
            }
        });
    });
    
    // Delete Therapist
    $('.delete-therapist').on('click', function() {
        if (!confirm('Are you sure you want to delete this therapist?')) {
            return;
        }
        
        var id = $(this).data('id');
        
        $.ajax({
            url: 'api/delete_therapist.php',
            method: 'POST',
            data: { id: id },
            success: function(response) {
                var res = typeof response === 'string' ? JSON.parse(response) : response;
                if (res.success) {
                    alert('Therapist deleted successfully');
                    location.reload();
                } else {
                    alert('Error: ' + res.message);
                }
            }
        });
    });
    
    // Grid-based Session Creation System with Custom Timings
    var kidsList = [];
    var customTimingsList = [];
    var currentTherapistId = null;
    var gridData = {};
    
    // Load kids when modal opens
    $('#createSessionModal').on('show.bs.modal', function() {
        loadKids();
        $('#selectTherapist').val('');
        $('#step2').hide();
        $('#step3').hide();
        $('#saveTimetableBtn').hide();
        customTimingsList = [];
        gridData = {};
        updateTimingsDisplay();
    });
    
    // Load kids list
    function loadKids() {
        return $.ajax({
            url: 'api/get_kids.php',
            method: 'GET',
            success: function(response) {
                var res = typeof response === 'string' ? JSON.parse(response) : response;
                if (res.success && res.data) {
                    kidsList = res.data || [];
                    console.log('Kids loaded:', kidsList.length);
                } else {
                    console.warn('Failed to load kids:', res.message || 'Unknown error');
                    kidsList = [];
                    // Show user-friendly message
                    if (res.message && res.message.includes('does not exist')) {
                        console.error('Please run database_update.sql to create the kids table');
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading kids:', error);
                kidsList = [];
            }
        });
    }
    
    // Load custom timings for selected therapist
    function loadCustomTimings(therapistId) {
        return $.ajax({
            url: 'api/get_custom_timings.php?therapist_id=' + therapistId,
            method: 'GET',
            success: function(response) {
                var res = typeof response === 'string' ? JSON.parse(response) : response;
                if (res.success && res.data) {
                    customTimingsList = res.data || [];
                    console.log('Custom timings loaded:', customTimingsList.length);
                    updateTimingsDisplay();
                    
                    if (customTimingsList.length > 0) {
                        $('#step3').show();
                        $('#saveTimetableBtn').show();
                    }
                } else {
                    console.warn('Failed to load custom timings:', res.message || 'Unknown error');
                    customTimingsList = [];
                    updateTimingsDisplay();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading custom timings:', error);
                customTimingsList = [];
                updateTimingsDisplay();
            }
        });
    }
    
    // Update timings display
    function updateTimingsDisplay() {
        var displayHtml = '';
        
        if (customTimingsList.length === 0) {
            displayHtml = '<p class="mb-0 text-muted"><i class="fas fa-clock"></i> No timings added yet. Add at least one timing to continue.</p>';
            $('#step3').hide();
            $('#saveTimetableBtn').hide();
        } else {
            displayHtml = '<div class="d-flex flex-wrap gap-2">';
            customTimingsList.forEach(function(timing, index) {
                displayHtml += '<div class="badge bg-primary p-2 d-flex align-items-center gap-2">' +
                    '<span>' + timing.display_name + '</span>' +
                    '<button type="button" class="btn btn-sm btn-link text-white p-0 edit-timing" data-index="' + index + '" title="Edit">' +
                    '<i class="fas fa-edit"></i>' +
                    '</button>' +
                    '<button type="button" class="btn btn-sm btn-link text-white p-0 delete-timing" data-index="' + index + '" title="Delete">' +
                    '<i class="fas fa-times"></i>' +
                    '</button>' +
                    '</div>';
            });
            displayHtml += '</div>';
            $('#step3').show();
            $('#saveTimetableBtn').show();
        }
        
        $('#timingsDisplay').html(displayHtml);
    }
    
    // When therapist is selected, show custom timing interface
    $('#selectTherapist').on('change', function() {
        var therapistId = $(this).val();
        if (therapistId) {
            currentTherapistId = therapistId;
            
            // Load kids and existing custom timings
            $.when(loadKids(), loadCustomTimings(therapistId)).done(function() {
                if (kidsList.length === 0) {
                    alert('No kids found!\n\nPlease add kids first before creating a timetable.');
                    $('#step2').hide();
                    $('#step3').hide();
                    $('#saveTimetableBtn').hide();
                    return;
                }
                
                $('#step2').show();
                
                // If custom timings exist, build the grid
                if (customTimingsList.length > 0) {
                    buildTimetableGrid(function() {
                        loadExistingTimetable(therapistId);
                    });
                }
            });
        } else {
            $('#step2').hide();
            $('#step3').hide();
            $('#saveTimetableBtn').hide();
            currentTherapistId = null;
            customTimingsList = [];
        }
    });
    
    // Build the timetable grid with custom timings
    function buildTimetableGrid(callback) {
        var days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        var thead = $('#timetableGrid thead tr');
        var tbody = $('#timetableGrid tbody');
        
        // Clear existing
        thead.find('th:not(:first)').remove();
        tbody.empty();
        
        // Check if custom timings are loaded
        if (!customTimingsList || customTimingsList.length === 0) {
            console.error('No custom timings available');
            tbody.append('<tr><td colspan="1" class="text-center text-warning">No custom timings added. Please add at least one timing above.</td></tr>');
            if (callback && typeof callback === 'function') {
                callback();
            }
            return;
        }
        
        // Add custom timing headers (X-axis - top row)
        customTimingsList.forEach(function(timing) {
            var th = $('<th style="min-width: 130px; text-align: center; padding: 0.75rem;"></th>');
            th.text(timing.display_name);
            thead.append(th);
        });
        
        // Initialize grid data structure
        days.forEach(function(day) {
            gridData[day] = {};
            customTimingsList.forEach(function(timing) {
                gridData[day][timing.time_start] = null;
            });
        });
        
        // Build rows for each day (Y-axis - left column)
        days.forEach(function(day) {
            var row = $('<tr></tr>');
            row.append('<td class="fw-bold bg-light" style="min-width: 140px; padding: 0.75rem;">' + day + '</td>');
            
            customTimingsList.forEach(function(timing) {
                var cell = $('<td style="padding: 5px; text-align: center;"></td>');
                var select = $('<select class="form-select form-select-sm kid-select" data-day="' + day + '" data-time="' + timing.time_start + '" style="min-width: 120px;"></select>');
                select.append('<option value="">-- Select --</option>');
                
                if (kidsList && kidsList.length > 0) {
                    kidsList.forEach(function(kid) {
                        select.append('<option value="' + kid.kid_id + '">' + kid.kid_name + '</option>');
                    });
                } else {
                    select.append('<option value="">No kids</option>');
                }
                
                cell.append(select);
                row.append(cell);
            });
            
            tbody.append(row);
        });
        
        console.log('Grid built with', customTimingsList.length, 'custom timings and', days.length, 'days');
        
        // Callback when grid is built
        if (callback && typeof callback === 'function') {
            callback();
        }
    }
    
    // Load existing timetable with custom timings
    function loadExistingTimetable(therapistId) {
        $.ajax({
            url: 'api/get_timetable_grid.php?therapist_id=' + therapistId,
            method: 'GET',
            success: function(response) {
                var res = typeof response === 'string' ? JSON.parse(response) : response;
                if (res.success) {
                    // Load custom timings if they exist
                    if (res.custom_timings && res.custom_timings.length > 0) {
                        customTimingsList = res.custom_timings;
                        updateTimingsDisplay();
                        
                        // Rebuild grid with loaded timings
                        buildTimetableGrid(function() {
                            // Now populate the grid data
                            if (res.data && Object.keys(res.data).length > 0) {
                                var loadedCount = 0;
                                Object.keys(res.data).forEach(function(day) {
                                    if (gridData[day]) {
                                        Object.keys(res.data[day]).forEach(function(time) {
                                            var kidId = res.data[day][time];
                                            if (kidId) {
                                                // Match time format (handle both HH:MM:SS and HH:MM)
                                                var timeFormatted = time;
                                                if (time.length === 5) {
                                                    timeFormatted = time + ':00';
                                                }
                                                
                                                var $select = $('select[data-day="' + day + '"][data-time="' + timeFormatted + '"]');
                                                if ($select.length === 0) {
                                                    // Try without seconds
                                                    $select = $('select[data-day="' + day + '"][data-time="' + time + '"]');
                                                }
                                                
                                                if ($select.length) {
                                                    $select.val(kidId);
                                                    gridData[day][timeFormatted] = kidId;
                                                    loadedCount++;
                                                }
                                            }
                                        });
                                    }
                                });
                                console.log('Existing timetable loaded:', loadedCount, 'sessions');
                            }
                        });
                    } else {
                        console.log('No existing timetable or custom timings found for this therapist');
                    }
                } else {
                    console.log('Error loading timetable:', res.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading existing timetable:', error);
            }
        });
    }
    
    // Track changes in grid (for modal grid)
    $(document).on('change', '.kid-select', function() {
        var day = $(this).data('day');
        var time = $(this).data('time');
        var kidId = $(this).val() || null;
        
        // Initialize day object if it doesn't exist
        if (!gridData[day]) {
            gridData[day] = {};
        }
        gridData[day][time] = kidId;
    });
    
    // DISABLED: Old custom timing save handler - now handled by timetable.js
    // $('#saveTimetableBtn').on('click', function() { ... });
    // The simplified version without custom timing is in assets/js/timetable.js
    
    // Add custom timing
    $('#addCustomTimeBtn').on('click', function() {
        var startTime = $('#customTimeStart').val();
        var endTime = $('#customTimeEnd').val();
        var displayName = $('#customTimeDisplay').val();
        
        if (!startTime || !endTime || !displayName) {
            alert('Please fill all fields for custom timing');
            return;
        }
        
        // Validate time range
        if (startTime >= endTime) {
            alert('End time must be after start time');
            return;
        }
        
        // Check for duplicates
        var isDuplicate = customTimingsList.some(function(timing) {
            return timing.time_start === startTime + ':00' || timing.display_name === displayName;
        });
        
        if (isDuplicate) {
            alert('A timing with this start time or display name already exists');
            return;
        }
        
        // Add to custom timings list
        var newTiming = {
            time_start: startTime + ':00',
            time_end: endTime + ':00',
            display_name: displayName,
            sort_order: customTimingsList.length
        };
        
        customTimingsList.push(newTiming);
        
        // Sort by start time
        customTimingsList.sort(function(a, b) {
            return a.time_start.localeCompare(b.time_start);
        });
        
        // Update sort order
        customTimingsList.forEach(function(timing, index) {
            timing.sort_order = index;
        });
        
        // Update display
        updateTimingsDisplay();
        
        // Rebuild grid
        buildTimetableGrid(function() {
            if (currentTherapistId) {
                loadExistingTimetable(currentTherapistId);
            }
        });
        
        // Reset form
        $('#customTimeStart').val('');
        $('#customTimeEnd').val('');
        $('#customTimeDisplay').val('');
    });
    
    // Edit timing
    $(document).on('click', '.edit-timing', function() {
        var index = $(this).data('index');
        var timing = customTimingsList[index];
        
        if (!timing) return;
        
        var startTime = prompt('Enter new start time (HH:MM):', timing.time_start.substring(0, 5));
        if (!startTime) return;
        
        var endTime = prompt('Enter new end time (HH:MM):', timing.time_end.substring(0, 5));
        if (!endTime) return;
        
        var displayName = prompt('Enter new display name:', timing.display_name);
        if (!displayName) return;
        
        // Validate
        if (startTime >= endTime) {
            alert('End time must be after start time');
            return;
        }
        
        // Update timing
        customTimingsList[index] = {
            time_start: startTime + ':00',
            time_end: endTime + ':00',
            display_name: displayName,
            sort_order: index
        };
        
        // Sort by start time
        customTimingsList.sort(function(a, b) {
            return a.time_start.localeCompare(b.time_start);
        });
        
        // Update sort order
        customTimingsList.forEach(function(timing, idx) {
            timing.sort_order = idx;
        });
        
        // Update display
        updateTimingsDisplay();
        
        // Rebuild grid
        buildTimetableGrid(function() {
            if (currentTherapistId) {
                loadExistingTimetable(currentTherapistId);
            }
        });
    });
    
    // Delete timing
    $(document).on('click', '.delete-timing', function() {
        var index = $(this).data('index');
        
        if (!confirm('Delete this timing? This will remove the corresponding column from the timetable.')) {
            return;
        }
        
        // Remove from list
        customTimingsList.splice(index, 1);
        
        // Update sort order
        customTimingsList.forEach(function(timing, idx) {
            timing.sort_order = idx;
        });
        
        // Update display
        updateTimingsDisplay();
        
        // Rebuild grid if timings still exist
        if (customTimingsList.length > 0) {
            buildTimetableGrid(function() {
                if (currentTherapistId) {
                    loadExistingTimetable(currentTherapistId);
                }
            });
        } else {
            $('#step3').hide();
            $('#saveTimetableBtn').hide();
            $('#timetableGrid tbody').empty();
        }
    });
    
    // Edit Session
    $('.edit-session').on('click', function() {
        var id = $(this).data('id');
        
        $.ajax({
            url: 'api/get_session.php?id=' + id,
            method: 'GET',
            success: function(response) {
                var res = typeof response === 'string' ? JSON.parse(response) : response;
                if (res.success) {
                    var data = res.data;
                    $('#editSessionId').val(data.id);
                    $('#editSessionTherapist').val(data.therapist_id);
                    $('#editSessionDay').val(data.day_of_week);
                    $('#editSessionTime').val(data.time_slot);
                    $('#editSessionClient').val(data.client_name);
                    $('#editSessionType').val(data.session_type || '');
                    $('#editSessionNotes').val(data.special_notes || '');
                    $('#editSessionModal').modal('show');
                }
            }
        });
    });
    
    // Update Session Form
    $('#editSessionForm').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        
        $.ajax({
            url: 'api/update_session.php',
            method: 'POST',
            data: formData,
            success: function(response) {
                var res = typeof response === 'string' ? JSON.parse(response) : response;
                if (res.success) {
                    alert('Session updated successfully');
                    location.reload();
                } else {
                    alert('Error: ' + res.message);
                }
            }
        });
    });
    
    // Delete Session
    $('.delete-session').on('click', function() {
        if (!confirm('Are you sure you want to delete this session?')) {
            return;
        }
        
        var id = $(this).data('id');
        
        $.ajax({
            url: 'api/delete_session.php',
            method: 'POST',
            data: { id: id },
            success: function(response) {
                var res = typeof response === 'string' ? JSON.parse(response) : response;
                if (res.success) {
                    alert('Session deleted successfully');
                    location.reload();
                } else {
                    alert('Error: ' + res.message);
                }
            }
        });
    });
    
    // Grid View: Load timetable grid when therapist is selected
    $('#therapistFilterGrid').on('change', function() {
        var therapistId = $(this).val();
        if (therapistId) {
            loadTimetableGrid(therapistId);
        } else {
            $('#gridContainer').html('<div class="text-center text-muted py-5"><i class="fas fa-calendar-alt fa-3x mb-3"></i><p>Please select a therapist to view their timetable grid</p></div>');
        }
    });
    
    // Function to load dynamic timetable grid (with inline editing)
    function loadTimetableGrid(therapistId) {
        $('#gridContainer').html('<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x"></i><p>Loading grid...</p></div>');
        
        $.ajax({
            url: 'api/load_dynamic_grid.php?therapist_id=' + therapistId,
            method: 'GET',
            success: function(html) {
                $('#gridContainer').html(html);
            },
            error: function(xhr, status, error) {
                console.error('Grid load error:', error);
                $('#gridContainer').html('<div class="alert alert-danger">Error loading timetable grid. Please try again.</div>');
            }
        });
    }
    
    // Function to render timetable grid
    // X-axis (top): Time slots
    // Y-axis (left): Days
    function renderTimetableGrid(data, therapistId) {
        var days = data.days || ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        var timeSlots = data.time_slots || [];
        var grid = data.grid || {};
        
        var html = '<div class="mb-3"><h6><i class="fas fa-user"></i> Therapist: ' + (data.therapist_name || 'Unknown') + '</h6></div>';
        html += '<div class="table-responsive"><table class="table table-bordered table-hover timetable-grid-view" id="timetableGridDisplay">';
        
        // Header row: Time slots on X-axis (top)
        html += '<thead class="table-light"><tr><th class="sticky-col">Day / Time</th>';
        
        timeSlots.forEach(function(slot) {
            html += '<th class="text-center" style="min-width: 120px;">' + (slot.display_name || slot.display_time) + '</th>';
        });
        html += '</tr></thead><tbody>';
        
        // Rows: Days on Y-axis (left)
        days.forEach(function(day) {
            html += '<tr><td class="sticky-col fw-bold">' + day + '</td>';
            
            timeSlots.forEach(function(slot) {
                var timeSlotName = slot.display_name || slot.display_time;
                var cellData = grid[day] && grid[day][timeSlotName] ? grid[day][timeSlotName] : null;
                var cellId = 'cell_' + therapistId + '_' + day + '_' + slot.id;
                
                var kids = data.kids || [];
                var kidId = cellData ? cellData.kid_id : null;
                var kidName = cellData && cellData.client_name ? cellData.client_name : 'Empty';
                
                html += '<td class="text-center" data-therapist-id="' + therapistId + '" data-day="' + day + '" data-time-slot="' + timeSlotName + '" data-kid-id="' + (kidId || '') + '" id="' + cellId + '">';
                html += '<div class="cell-content">';
                
                // Determine display
                var selectedKidId = kidId;
                
                // Display kid name or "Empty"
                if (kidName && kidName !== 'Empty') {
                    html += '<span class="badge bg-info">' + kidName + '</span>';
                } else {
                    html += '<span class="text-muted">-</span>';
                }
                
                html += '</div></td>';
            });
            
            html += '</tr>';
        });
        
        html += '</tbody></table></div>';
        $('#gridContainer').html(html);
    }
    
    // Auto-load grid if only one therapist exists
    $(document).ready(function() {
        // Wait for tab to be ready
        setTimeout(function() {
            if (typeof autoLoadTherapistId !== 'undefined' && autoLoadTherapistId) {
                $('#therapistFilterGrid').val(autoLoadTherapistId);
                // Trigger change to load grid
                $('#therapistFilterGrid').trigger('change');
            }
        }, 200);
    });
    
    // Grid cell: Show edit popup
    $(document).on('click', '.cell-edit-btn', function(e) {
        e.stopPropagation();
        var cellId = $(this).data('cell-id');
        var cell = $('#' + cellId);
        
        // Close any other open popups first
        $('.cell-popup-edit').hide();
        $('.cell-normal-view').show();
        
        // Hide normal view, show popup
        cell.find('.cell-normal-view').hide();
        cell.find('.cell-popup-edit').show();
        
        // Set dropdown to current value
        var currentKidId = cell.data('kid-id') || '';
        cell.find('.cell-kid-select').val(currentKidId);
    });
    
    // Close popup when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.cell-popup-edit').length && !$(e.target).closest('.cell-edit-btn').length) {
            $('.cell-popup-edit').hide();
            $('.cell-normal-view').show();
        }
    });
    
    // Prevent popup from closing when clicking inside it
    $(document).on('click', '.cell-popup-edit', function(e) {
        e.stopPropagation();
    });
    
    // Grid cell: Cancel edit
    $(document).on('click', '.cell-cancel-btn', function(e) {
        e.stopPropagation();
        var cellId = $(this).data('cell-id');
        var cell = $('#' + cellId);
        
        // Hide popup, show normal view
        cell.find('.cell-popup-edit').hide();
        cell.find('.cell-normal-view').show();
    });
    
    // Grid cell: Save kid selection
    $(document).on('click', '.cell-save-btn', function(e) {
        e.stopPropagation();
        var cellId = $(this).data('cell-id');
        var cell = $('#' + cellId);
        var therapistId = cell.data('therapist-id');
        var day = cell.data('day');
        var timeSlotId = cell.data('time-slot-id');
        var kidId = cell.find('.cell-kid-select').val();
        var sessionId = cell.data('session-id');
        var selectedKidName = cell.find('.cell-kid-select option:selected').text();
        
        if (!therapistId || !day || !timeSlotId) {
            console.error('Missing required data for grid cell update');
            return;
        }
        
        var originalKidId = cell.data('kid-id');
        var $saveBtn = $(this);
        var originalText = $saveBtn.html();
        
        // Disable button and show loading
        $saveBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
        
        $.ajax({
            url: 'api/update_grid_cell.php',
            method: 'POST',
            data: {
                therapist_id: therapistId,
                day: day,
                time_slot_id: timeSlotId,
                kid_id: kidId || '',
                session_id: sessionId || ''
            },
            success: function(response) {
                var res = typeof response === 'string' ? JSON.parse(response) : response;
                if (res.success) {
                    // Update cell data attributes
                    cell.attr('data-kid-id', kidId || '');
                    
                    // Update session ID if a new session was created
                    if (res.session_id) {
                        cell.attr('data-session-id', res.session_id);
                    }
                    
                    // Update the <p> tag with kid name
                    var displayName = (kidId && selectedKidName && selectedKidName !== '-- Select Kid --') 
                        ? selectedKidName 
                        : 'Empty';
                    cell.find('.cell-kid-name').text(displayName);
                    
                    // Hide popup, show normal view
                    cell.find('.cell-popup-edit').hide();
                    cell.find('.cell-normal-view').show();
                    
                    // Re-enable button
                    $saveBtn.prop('disabled', false).html(originalText);
                    
                    // Trigger live update event
                    $(document).trigger('timetableUpdated', [therapistId]);
                } else {
                    alert('Error: ' + res.message);
                    $saveBtn.prop('disabled', false).html(originalText);
                }
            },
            error: function() {
                alert('Error updating timetable. Please try again.');
                $saveBtn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // Grid cell: Delete session
    $(document).on('click', '.delete-session-cell', function() {
        if (!confirm('Delete this session?')) {
            return;
        }
        
        var sessionId = $(this).data('id');
        var therapistId = $('#therapistFilterGrid').val();
        
        $.ajax({
            url: 'api/delete_session.php',
            method: 'POST',
            data: { id: sessionId },
            success: function(response) {
                var res = typeof response === 'string' ? JSON.parse(response) : response;
                if (res.success) {
                    loadTimetableGrid(therapistId);
                } else {
                    alert('Error: ' + res.message);
                }
            }
        });
    });
    
    // Grid cell: Edit session
    $(document).on('click', '.edit-session-cell', function() {
        var sessionId = $(this).data('id');
        if (!sessionId) {
            alert('No session to edit. Please select a kid first.');
            return;
        }
        
        // Trigger the existing edit session modal
        $('.edit-session[data-id="' + sessionId + '"]').click();
    });
    
    // ============================================================================
    // KIDS MANAGEMENT
    // ============================================================================
    
    var kidsDataTable = null;
    
    // Load kids when kids section is shown
    function loadKidsData() {
        // Show loading state
        $('#kidsTableBody').html(
            '<tr><td colspan="7" class="text-center text-muted py-4">' +
            '<i class="fas fa-spinner fa-spin"></i> Loading kids data...' +
            '</td></tr>'
        );
        
        $.ajax({
            url: 'api/get_all_kids.php',
            method: 'GET',
            dataType: 'json',
            cache: true,
            success: function(res) {
                if (res.success && res.data) {
                    populateKidsTable(res.data);
                    $('#totalKidsCount').text(res.count || res.data.length);
                } else {
                    showKidsError(res.message || 'Error loading kids');
                }
            },
            error: function() {
                showKidsError('Error loading kids data. Please refresh.');
            }
        });
    }
    
    // Helper function to show error messages in kids table
    function showKidsError(message) {
        $('#kidsTableBody').html(
            '<tr><td colspan="7" class="text-center py-4">' +
            '<div class="alert alert-danger mb-0" role="alert">' +
            '<i class="fas fa-exclamation-triangle"></i> ' +
            message +
            '<br><br>' +
            '<button class="btn btn-sm btn-primary" onclick="loadKidsData()">' +
            '<i class="fas fa-sync"></i> Retry' +
            '</button>' +
            '</div>' +
            '</td></tr>'
        );
        $('#totalKidsCount').text('0');
        console.error('Kids table error:', message);
    }
    
    // Populate kids table
    function populateKidsTable(kids) {
        var tbody = $('#kidsTableBody');
        
        if (!kids || kids.length === 0) {
            tbody.html('<tr><td colspan="7" class="text-center text-muted py-4"><i class="fas fa-users fa-3x mb-3"></i><br>No kids found. Click "Add Kid" to add one.</td></tr>');
            if (kidsDataTable) {
                kidsDataTable.destroy();
                kidsDataTable = null;
            }
            return;
        }
        
        // Build all rows at once for better performance
        var rows = [];
        for (var i = 0; i < kids.length; i++) {
            var kid = kids[i];
            if (!kid.kid_id) continue;
            
            rows.push('<tr><td class="fw-semibold">' + kid.kid_id + '</td>' +
                '<td>' + escapeHtml(kid.kid_name || 'N/A') + '</td>' +
                '<td>' + (kid.age || 'N/A') + '</td>' +
                '<td>' + escapeHtml(kid.parent_name || 'N/A') + '</td>' +
                '<td>' + escapeHtml(kid.contact || 'N/A') + '</td>' +
                '<td><span class="badge bg-info">' + escapeHtml(kid.case_type || 'General') + '</span></td>' +
                '<td class="text-center"><div class="btn-group btn-group-sm">' +
                '<button class="btn btn-info edit-kid" data-id="' + kid.kid_id + '"><i class="fas fa-edit"></i></button>' +
                '<button class="btn btn-danger delete-kid" data-id="' + kid.kid_id + '"><i class="fas fa-trash"></i></button>' +
                '</div></td></tr>');
        }
        
        tbody.html(rows.join(''));
        
        // Initialize DataTable - destroy existing instance first
        if (kidsDataTable) {
            kidsDataTable.destroy();
            kidsDataTable = null;
        }
        // Also check using DataTable API in case it was initialized elsewhere
        if ($.fn.DataTable.isDataTable('#kidsTable')) {
            $('#kidsTable').DataTable().destroy();
        }
        
        kidsDataTable = $('#kidsTable').DataTable({
            responsive: true,
            pageLength: 10,
            order: [[0, 'desc']],
            columnDefs: [{ targets: [6], orderable: false, searchable: false }],
            destroy: true
        });
    }
    
    // Helper function to escape HTML (prevent XSS)
    function escapeHtml(text) {
        if (!text) return text;
        var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.toString().replace(/[&<>"']/g, function(m) { return map[m]; });
    }
    
    // Show kids section handler
    $(window).on('hashchange load', function() {
        if (window.location.hash === '#kids') {
            loadKidsData();
        }
    });
    
    // Add kid form submit
    $('#addKidForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        
        $.ajax({
            url: 'api/create_kid.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                var res = typeof response === 'string' ? JSON.parse(response) : response;
                if (res.success) {
                    alert('Kid added successfully!');
                    $('#addKidModal').modal('hide');
                    $('#addKidForm')[0].reset();
                    loadKidsData();
                    // Reload kids list in timetable grid
                    loadKids();
                } else {
                    alert('Error: ' + res.message);
                }
            },
            error: function() {
                alert('An error occurred while adding kid');
            }
        });
    });
    
    // Edit kid button click
    $(document).on('click', '.edit-kid', function() {
        var id = $(this).data('id');
        
        $.ajax({
            url: 'api/get_kid.php?id=' + id,
            method: 'GET',
            success: function(response) {
                var res = typeof response === 'string' ? JSON.parse(response) : response;
                if (res.success) {
                    var kid = res.data;
                    $('#editKidId').val(kid.kid_id);
                    $('#editKidName').val(kid.kid_name || '');
                    $('#editKidAge').val(kid.age || '');
                    $('#editKidParentName').val(kid.parent_name || '');
                    $('#editKidContact').val(kid.contact || '');
                    $('#editKidCase').val(kid.case_type || '');
                    
                    $('#editKidModal').modal('show');
                } else {
                    alert('Error: ' + res.message);
                }
            },
            error: function() {
                alert('Error loading kid data');
            }
        });
    });
    
    // Edit kid form submit
    $('#editKidForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        
        $.ajax({
            url: 'api/update_kid.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                var res = typeof response === 'string' ? JSON.parse(response) : response;
                if (res.success) {
                    alert('Kid updated successfully!');
                    $('#editKidModal').modal('hide');
                    loadKidsData();
                    // Reload kids list in timetable grid
                    loadKids();
                } else {
                    alert('Error: ' + res.message);
                }
            },
            error: function() {
                alert('An error occurred while updating kid');
            }
        });
    });
    
    // Delete kid button click
    $(document).on('click', '.delete-kid', function() {
        if (!confirm('Are you sure you want to delete this kid? This action cannot be undone.')) {
            return;
        }
        
        var id = $(this).data('id');
        
        $.ajax({
            url: 'api/delete_kid.php',
            method: 'POST',
            data: { id: id },
            success: function(response) {
                var res = typeof response === 'string' ? JSON.parse(response) : response;
                if (res.success) {
                    alert('Kid deleted successfully');
                    loadKidsData();
                    // Reload kids list in timetable grid
                    loadKids();
                } else {
                    alert('Error: ' + res.message);
                }
            },
            error: function() {
                alert('An error occurred while deleting kid');
            }
        });
    });
    
    // Kids filter functionality
    $('#filterKidName, #filterKidAge, #filterKidCase').on('keyup change', function() {
        if (kidsDataTable) {
            var name = $('#filterKidName').val();
            var age = $('#filterKidAge').val();
            var caseType = $('#filterKidCase').val();
            
            kidsDataTable
                .column(2).search(name)
                .column(3).search(age)
                .column(6).search(caseType)
                .draw();
        }
    });
    
    // Clear filters
    $('#clearKidFilters').on('click', function() {
        $('#filterKidName').val('');
        $('#filterKidAge').val('');
        $('#filterKidCase').val('');
        
        if (kidsDataTable) {
            kidsDataTable.search('').columns().search('').draw();
        }
    });
    
    // Clear custom timings when modal closes
    $('#createSessionModal').on('hidden.bs.modal', function() {
        customTimingsList = [];
        gridData = {};
        currentTherapistId = null;
        $('#customTimeStart').val('');
        $('#customTimeEnd').val('');
        $('#customTimeDisplay').val('');
        $('#step2').hide();
        $('#step3').hide();
        $('#saveTimetableBtn').hide();
    });
    
    // ============================================================================
    // ADMIN TIME TABLE MANAGEMENT
    // ============================================================================
    
    var adminTimetableDataTable = null;
    
    // Load admin timetable when section is shown
    function loadAdminTimetable() {
        $.ajax({
            url: 'api/get_all_timetables.php',
            method: 'GET',
            success: function(response) {
                var res = typeof response === 'string' ? JSON.parse(response) : response;
                if (res.success) {
                    populateAdminTimetableTable(res.data);
                    $('#totalSessionsCount').text(res.count);
                    
                    // Populate kids filter dropdown
                    populateKidsFilterDropdown();
                } else {
                    $('#adminTimetableBody').html('<tr><td colspan="8" class="text-center text-danger">Error: ' + res.message + '</td></tr>');
                }
            },
            error: function() {
                $('#adminTimetableBody').html('<tr><td colspan="8" class="text-center text-danger">Error loading timetable data</td></tr>');
            }
        });
    }
    
    // Populate kids filter dropdown
    function populateKidsFilterDropdown() {
        $.ajax({
            url: 'api/get_kids.php',
            method: 'GET',
            success: function(response) {
                var res = typeof response === 'string' ? JSON.parse(response) : response;
                if (res.success && res.data) {
                    var select = $('#filterKidTimetable');
                    select.find('option:not(:first)').remove();
                    
                    res.data.forEach(function(kid) {
                        select.append('<option value="' + kid.kid_id + '">' + kid.kid_name + '</option>');
                    });
                }
            }
        });
    }
    
    // Populate admin timetable table
    function populateAdminTimetableTable(timetables) {
        var tbody = $('#adminTimetableBody');
        tbody.empty();
        
        if (timetables.length === 0) {
            tbody.html('<tr><td colspan="8" class="text-center text-muted py-4">No timetable entries found. Click "Add Timetable" to create one.</td></tr>');
            return;
        }
        
        timetables.forEach(function(entry) {
            var therapistName = entry.therapist_name || entry.therapist_username || 'N/A';
            var kidName = entry.kid_name || 'N/A';
            var timeFormatted = entry.time_slot ? entry.time_slot.substring(0, 5) : 'N/A';
            
            var row = '<tr>' +
                '<td class="fw-semibold">' + entry.id + '</td>' +
                '<td>' + therapistName + '</td>' +
                '<td>' + entry.day_of_week + '</td>' +
                '<td>' + timeFormatted + '</td>' +
                '<td>' + kidName + '</td>' +
                '<td>' + (entry.session_type || 'N/A') + '</td>' +
                '<td>' +
                    '<div class="text-truncate" style="max-width: 200px;" title="' + (entry.special_notes || '') + '">' +
                        (entry.special_notes || 'N/A') +
                    '</div>' +
                '</td>' +
                '<td class="text-center">' +
                    '<div class="btn-group btn-group-sm" role="group">' +
                        '<button class="btn btn-info view-timetable-entry" data-id="' + entry.id + '" title="View Details">' +
                            '<i class="fas fa-eye"></i>' +
                        '</button>' +
                        '<button class="btn btn-danger delete-timetable-entry" data-id="' + entry.id + '" title="Delete">' +
                            '<i class="fas fa-trash"></i>' +
                        '</button>' +
                    '</div>' +
                '</td>' +
            '</tr>';
            
            tbody.append(row);
        });
        
        // Initialize/reinitialize DataTable
        if (adminTimetableDataTable !== null) {
            adminTimetableDataTable.destroy();
        }
        
        adminTimetableDataTable = $('#adminTimetableTable').DataTable({
            responsive: true,
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            order: [[2, 'asc'], [3, 'asc']],
            columnDefs: [
                { 
                    targets: [7], 
                    orderable: false, 
                    searchable: false 
                }
            ],
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ sessions",
                infoEmpty: "No sessions found",
                infoFiltered: "(filtered from _MAX_ total sessions)"
            },
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'csv',
                    text: '<i class="fas fa-file-csv"></i> CSV',
                    className: 'btn btn-sm btn-success',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6]
                    }
                },
                {
                    extend: 'excel',
                    text: '<i class="fas fa-file-excel"></i> Excel',
                    className: 'btn btn-sm btn-success',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6]
                    }
                },
                {
                    extend: 'pdf',
                    text: '<i class="fas fa-file-pdf"></i> PDF',
                    className: 'btn btn-sm btn-danger',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6]
                    }
                }
            ]
        });
    }
    
    // Show admin timetable section handler
    $(window).on('hashchange load', function() {
        if (window.location.hash === '#admin-timetable') {
            loadAdminTimetable();
        }
    });
    
    // Timetable filter functionality
    $('#filterTherapistTimetable, #filterDayTimetable, #filterKidTimetable').on('change', function() {
        if (adminTimetableDataTable) {
            var therapist = $('#filterTherapistTimetable option:selected').text().trim();
            var day = $('#filterDayTimetable').val();
            var kidName = $('#filterKidTimetable option:selected').text().trim();
            
            // Apply filters
            adminTimetableDataTable
                .column(1).search(therapist === 'All Therapists' ? '' : therapist)
                .column(2).search(day)
                .column(4).search(kidName === 'All Kids' ? '' : kidName)
                .draw();
        }
    });
    
    // Clear timetable filters
    $('#clearTimetableFilters').on('click', function() {
        $('#filterTherapistTimetable').val('');
        $('#filterDayTimetable').val('');
        $('#filterKidTimetable').val('');
        
        if (adminTimetableDataTable) {
            adminTimetableDataTable.search('').columns().search('').draw();
        }
    });
    
    // View timetable entry details
    $(document).on('click', '.view-timetable-entry', function() {
        var id = $(this).data('id');
        
        // Get the row data from DataTable
        var table = $('#adminTimetableTable').DataTable();
        var rowData = null;
        
        table.rows().every(function() {
            var data = this.data();
            var rowId = $(this.node()).find('.view-timetable-entry').data('id');
            if (rowId == id) {
                rowData = {
                    id: $(this.node()).find('td:eq(0)').text(),
                    therapist: $(this.node()).find('td:eq(1)').text(),
                    day: $(this.node()).find('td:eq(2)').text(),
                    time: $(this.node()).find('td:eq(3)').text(),
                    kid: $(this.node()).find('td:eq(4)').text(),
                    sessionType: $(this.node()).find('td:eq(5)').text(),
                    notes: $(this.node()).find('td:eq(6)').attr('title') || $(this.node()).find('td:eq(6)').text()
                };
                return false;
            }
        });
        
        if (rowData) {
            var detailsHtml = '<div class="p-3">' +
                '<h6 class="mb-3">Session Details</h6>' +
                '<table class="table table-sm">' +
                '<tr><th>ID:</th><td>' + rowData.id + '</td></tr>' +
                '<tr><th>Therapist:</th><td>' + rowData.therapist + '</td></tr>' +
                '<tr><th>Day:</th><td>' + rowData.day + '</td></tr>' +
                '<tr><th>Time:</th><td>' + rowData.time + '</td></tr>' +
                '<tr><th>Kid:</th><td>' + rowData.kid + '</td></tr>' +
                '<tr><th>Session Type:</th><td>' + rowData.sessionType + '</td></tr>' +
                '<tr><th>Notes:</th><td>' + rowData.notes + '</td></tr>' +
                '</table>' +
                '</div>';
            
            // Show in a simple alert or modal
            var modal = '<div class="modal fade" id="viewTimetableModal" tabindex="-1">' +
                '<div class="modal-dialog">' +
                    '<div class="modal-content">' +
                        '<div class="modal-header">' +
                            '<h5 class="modal-title"><i class="fas fa-info-circle"></i> Timetable Entry Details</h5>' +
                            '<button type="button" class="btn-close" data-bs-dismiss="modal"></button>' +
                        '</div>' +
                        '<div class="modal-body">' + detailsHtml + '</div>' +
                        '<div class="modal-footer">' +
                            '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>';
            
            // Remove existing modal if any
            $('#viewTimetableModal').remove();
            
            // Append and show new modal
            $('body').append(modal);
            $('#viewTimetableModal').modal('show');
            
            // Clean up after hide
            $('#viewTimetableModal').on('hidden.bs.modal', function() {
                $(this).remove();
            });
        }
    });
    
    // Delete timetable entry
    $(document).on('click', '.delete-timetable-entry', function() {
        if (!confirm('Are you sure you want to delete this timetable entry? This action cannot be undone.')) {
            return;
        }
        
        var id = $(this).data('id');
        
        $.ajax({
            url: 'api/delete_timetable_entry.php',
            method: 'POST',
            data: { id: id },
            success: function(response) {
                var res = typeof response === 'string' ? JSON.parse(response) : response;
                if (res.success) {
                    alert('Timetable entry deleted successfully');
                    loadAdminTimetable();
                    
                    // Refresh the timetable grid if it's visible
                    var selectedTherapist = $('#therapistFilterGrid').val();
                    if (selectedTherapist) {
                        loadTimetableGrid(selectedTherapist);
                    }
                } else {
                    alert('Error: ' + res.message);
                }
            },
            error: function() {
                alert('An error occurred while deleting timetable entry');
            }
        });
    });
    
    // Export timetable button (uses DataTables export)
    $('#exportTimetableBtn').on('click', function() {
        if (adminTimetableDataTable) {
            // Trigger the Excel export button from DataTables
            $('.buttons-excel').trigger('click');
        } else {
            alert('No data to export. Please wait for the timetable to load.');
        }
    });
});
