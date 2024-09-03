function toggleEdit(button) {
    var row = button.closest('tr');
    var viewModes = row.querySelectorAll('.view-mode');
    var editModes = row.querySelectorAll('.edit-mode');
    var editButton = row.querySelector('.edit-button');
    var saveButton = row.querySelector('.save-button');

    viewModes.forEach(function(element) {
        element.style.display = 'none';
    });

    editModes.forEach(function(element) {
        element.style.display = 'block';
    });

    editButton.style.display = 'none';
    saveButton.style.display = 'inline-block';
}

function prepareEdit(button) {
    var row = button.closest('tr');
    var newTitle = row.querySelector('input[name="new_title"]').value;
    var newDescription = row.querySelector('textarea[name="new_description"]').value;
    var form = button.closest('form');
    form.querySelector('input[name="new_title"]').value = newTitle;
    form.querySelector('input[name="new_description"]').value = newDescription;
    return true;
}