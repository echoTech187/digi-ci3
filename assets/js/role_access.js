/**
 * Role Access & Menu Tree Control
 */
$(document).ready(function() {
    'use strict';

    $('#toggleGuideBtn').on('click', function() {
        $('#instructionDrawer, #instructionOverlay').addClass('open');
        $('body').css('overflow', 'hidden');
    });

    $('#closeDrawerBtn, #instructionOverlay').on('click', function() {
        $('#instructionDrawer, #instructionOverlay').removeClass('open');
        $('body').css('overflow', '');
    });

    const CSRF_NAME = $('meta[name="csrf-token-name"]').attr('content') || 'csrf_token_name';
    const CSRF_HASH = $('meta[name="csrf-token-hash"]').attr('content') || '';

    // Live search filter
    $('#menuSearch').on('keyup', function() {
        var val = $(this).val().toLowerCase();
        $('#roleAccessTable tbody tr').each(function() {
            var text = $(this).find('td:nth-child(2)').text().toLowerCase();
            $(this).toggle(text.indexOf(val) > -1);
        });
    });

    // Toggle access AJAX
    $('.rbac-checkbox').on('change', function() {
        var $input  = $(this);
        var $row    = $input.closest('tr');
        var menuId  = $input.data('menu');
        var roleId  = $input.data('role');

        $row.addClass('saving');

        $.ajax({
            url: window.BASE_URL + 'access-control/roles/change-access',
            type: 'POST',
            data: { menuId: menuId, roleId: roleId, [CSRF_NAME]: CSRF_HASH },
            success: function() {
                $row.removeClass('saving').addClass('saved');
                setTimeout(function() { $row.removeClass('saved'); }, 500);
            },
            error: function() {
                $input.prop('checked', !$input.prop('checked'));
                $row.removeClass('saving');
                Swal.fire({ title: 'Error!', text: 'Failed to update access.', icon: 'error' });
            }
        });
    });

    // Add Menu AJAX
    $('#addMenuForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: window.BASE_URL + 'menu/save/ajax',
            type: 'POST',
            data: $(this).serialize() + "&" + CSRF_NAME + "=" + CSRF_HASH,
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') location.reload();
            }
        });
    });

    // Edit Menu - Load Data
    $('.edit-menu-btn').on('click', function() {
        var id = $(this).data('id');
        $.ajax({
            url: window.BASE_URL + 'menu/get/' + id,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                $('#edit_id').val(data.id);
                $('#edit_title').val(data.title);
                
                var groupMod = data.group_modules ? data.group_modules.toString().trim() : "";
                if(groupMod && $("#edit_group_modules option[value='" + groupMod + "']").length === 0) {
                     $("#edit_group_modules").append(new Option(groupMod, groupMod));
                }
                $('#edit_group_modules').val(groupMod).trigger('change');
                
                $('#edit_url').val(data.url);
                $('#edit_icon').val(data.icon);
                $('#edit_parent_id').val(data.parent_id);
                $('#edit_menu_order').val(data.menu_order);
                $('#editMenuModal').modal('show');
            }
        });
    });

    // Update Menu AJAX
    $('#editMenuForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: window.BASE_URL + 'menu/update-menu/ajax',
            type: 'POST',
            data: $(this).serialize() + "&" + CSRF_NAME + "=" + CSRF_HASH,
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') location.reload();
            }
        });
    });

    // Delete Menu AJAX
    $('.delete-menu-btn').on('click', function() {
        var id = $(this).data('id');
        var title = $(this).data('title');
        Swal.fire({
            title: 'Delete Menu',
            text: 'Are you sure you want to delete "' + title + '"? This will also delete any sub-menus and revoke access from all roles.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: window.BASE_URL + 'menu/delete/ajax',
                    type: 'POST',
                    data: { id: id, [CSRF_NAME]: CSRF_HASH },
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') location.reload();
                        else Swal.fire({ title: 'Error!', text: res.message, icon: 'error' });
                    }
                });
            }
        });
    });

    // Add New Group Module dynamically via Modal
    var activeGroupSelect = null;
    $('.add-group-btn').on('click', function() {
        activeGroupSelect = $(this).closest('.form-group').find('.group-module-select');
        $('#new_group_modal_input').val('');
        $('#addGroupModal').modal('show');
    });

    $('#saveNewGroupBtn').on('click', function() {
        var newGroup = $('#new_group_modal_input').val().trim();
        if(newGroup !== "") {
            var exists = false;
            var existingValue = "";
            
            if(activeGroupSelect) {
                activeGroupSelect.find('option').each(function() {
                    if ($(this).val().toLowerCase() === newGroup.toLowerCase()) {
                        exists = true;
                        existingValue = $(this).val();
                        return false;
                    }
                });
            }

            if (exists) {
                Swal.fire({ title: 'Warning!', text: 'Group Module "' + existingValue + '" sudah ada!', icon: 'warning' });
                if(activeGroupSelect) activeGroupSelect.val(existingValue);
                $('#addGroupModal').modal('hide');
            } else {
                $('.group-module-select').each(function() {
                    $(this).append(new Option(newGroup, newGroup));
                });
                if(activeGroupSelect) activeGroupSelect.val(newGroup);
                $('#addGroupModal').modal('hide');
            }
        }
    });

    $('#new_group_modal_input').on('keypress', function(e) {
        if(e.which == 13) {
            e.preventDefault();
            $('#saveNewGroupBtn').click();
        }
    });
});
