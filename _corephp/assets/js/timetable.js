// Simple Timetable Creation System - NO CUSTOM TIMING
$(document).ready(function() {
    var kidsList = [];
    var timeSlotsList = [];
    var currentTherapistId = null;
    var gridData = {};
    
    // Load kids and time slots when modal opens
    $('#createSessionModal').on('show.bs.modal', function() {
        $('#selectTherapist').val('');
        $('#step2').hide();
        $('#saveTimetableBtn').hide();
        gridData = {};
        loadKids();
        loadTimeSlots();
    });
    
    // Load kids list
    function loadKids() {
        $.ajax({
            url: 'api/get_kids.php',
            method: 'GET',
            success: function(response) {
                var res = typeof response === 'string' ? JSON.parse(response) : response;
                kidsList = (res.success && res.data) ? res.data : [];
            },
            error: function() {
                kidsList = [];
            }
        });
    }
    
    // Load time slots from database (pre-configured only)
    function loadTimeSlots() {
        $.ajax({
            url: 'api/get_time_slots.php',
            method: 'GET',
            success: function(response) {
                var res = typeof response === 'string' ? JSON.parse(response) : response;
                timeSlotsList = (res.success && res.data) ? res.data : [];
            },
            error: function() {
                timeSlotsList = [];
            }
        });
    }
    
    // When therapist is selected
    $('#selectTherapist').on('change', function() {
        var therapistId = $(this).val();
        
        if (therapistId) {
            currentTherapistId = therapistId;
            
            // Wait for data to load
            var checkDataInterval = setInterval(function() {
                if (kidsList.length > 0 && timeSlotsList.length > 0) {
                    clearInterval(checkDataInterval);
                    buildTimetableGrid();
                    loadExistingTimetable(therapistId);
                    $('#step2').show();
                    $('#saveTimetableBtn').show();
                }
            }, 100);
        } else {
            $('#step2').hide();
            $('#saveTimetableBtn').hide();
            currentTherapistId = null;
        }
    });
    
    // Build the timetable grid
    function buildTimetableGrid() {
        var days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        var thead = $('#timetableGrid thead tr');
        var tbody = $('#timetableGrid tbody');
        
        // Clear existing
        thead.find('th:not(:first)').remove();
        tbody.empty();
        
        if (timeSlotsList.length === 0) {
            tbody.append('<tr><td colspan="1" class="text-center text-warning">No time slots found in database.</td></tr>');
            return;
        }
        
        // Add time slot headers (columns)
        timeSlotsList.forEach(function(slot) {
            thead.append('<th style="min-width: 150px; text-align: center;">' + slot.display_name + '</th>');
        });
        
        // Build rows for each day
        days.forEach(function(day) {
            var row = $('<tr></tr>');
            row.append('<td class="fw-bold bg-light" style="position: sticky; left: 0; z-index: 5;">' + day + '</td>');
            
            timeSlotsList.forEach(function(slot) {
                var cell = $('<td style="padding: 5px; text-align: center;"></td>');
                var select = $('<select class="form-select form-select-sm kid-select" data-day="' + day + '" data-timeslot-id="' + slot.id + '"></select>');
                
                select.append('<option value="">-- Select Kid --</option>');
                kidsList.forEach(function(kid) {
                    select.append('<option value="' + kid.kid_id + '">' + kid.kid_name + '</option>');
                });
                
                cell.append(select);
                row.append(cell);
            });
            
            tbody.append(row);
        });
    }
    
    // Load existing timetable
    function loadExistingTimetable(therapistId) {
        $.ajax({
            url: 'api/get_therapist_timetable.php?therapist_id=' + therapistId,
            method: 'GET',
            success: function(response) {
                var res = typeof response === 'string' ? JSON.parse(response) : response;
                if (res.success && res.data) {
                    // Populate grid with existing data
                    res.data.forEach(function(session) {
                        var kidId = extractKidIdFromNotes(session.special_notes);
                        if (kidId) {
                            var $select = $('select[data-day="' + session.day_of_week + '"]').filter(function() {
                                return $(this).closest('td').index() > 0; // Skip first column (day name)
                            });
                            
                            // Find the correct time slot
                            $select.each(function() {
                                var timeSlotId = $(this).data('timeslot-id');
                                var timeSlot = timeSlotsList.find(t => t.id == timeSlotId);
                                if (timeSlot && timeSlot.display_name === session.time_slot) {
                                    $(this).val(kidId);
                                }
                            });
                        }
                    });
                }
            }
        });
    }
    
    // Extract kid ID from special notes
    function extractKidIdFromNotes(notes) {
        if (!notes) return null;
        var match = notes.match(/Kid ID: (\d+)/);
        return match ? match[1] : null;
    }
    
    // Track changes in grid
    $(document).on('change', '.kid-select', function() {
        var day = $(this).data('day');
        var timeSlotId = $(this).data('timeslot-id');
        var kidId = $(this).val() || null;
        
        if (!gridData[day]) {
            gridData[day] = {};
        }
        gridData[day][timeSlotId] = kidId;
    });
    
    // Save timetable
    $('#saveTimetableBtn').on('click', function() {
        if (!currentTherapistId) {
            alert('Please select a therapist');
            return;
        }
        
        // Collect all data from grid
        var days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        gridData = {};
        
        days.forEach(function(day) {
            gridData[day] = {};
            $('select[data-day="' + day + '"]').each(function() {
                var timeSlotId = $(this).data('timeslot-id');
                var kidId = $(this).val();
                if (kidId) {
                    gridData[day][timeSlotId] = kidId;
                }
            });
        });
        
        if (!confirm('Save this weekly timetable? This will replace any existing schedule for this therapist.')) {
            return;
        }
        
        $.ajax({
            url: 'api/save_timetable_simple.php',
            method: 'POST',
            data: {
                therapist_id: currentTherapistId,
                grid_data: JSON.stringify(gridData)
            },
            success: function(response) {
                var res = typeof response === 'string' ? JSON.parse(response) : response;
                if (res.success) {
                    alert('Timetable saved successfully! ' + res.inserted + ' sessions created.');
                    $('#createSessionModal').modal('hide');
                    location.reload();
                } else {
                    alert('Error: ' + res.message);
                }
            },
            error: function(xhr) {
                console.error('Save error:', xhr.responseText);
                alert('An error occurred while saving');
            }
        });
    });
    
    // Clear modal on close
    $('#createSessionModal').on('hidden.bs.modal', function() {
        currentTherapistId = null;
        gridData = {};
        $('#selectTherapist').val('');
        $('#step2').hide();
        $('#saveTimetableBtn').hide();
    });
});
