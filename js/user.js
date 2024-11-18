
function clearForm() {
    console.log('Clearing form...');

    $('#name').val(''); 
    $('#username').val('');
    $('#userimagePreview').hide().attr('src', ''); 

    if (typeof user_type !== 'undefined' && typeof user_type.clear === 'function') {
        user_type.clear(); 
    } else {
        console.warn('user_type is not defined or clear method is unavailable.');
    }

    $('#saveBtn')
        .removeData('id') 
        .removeData('mode')
        .html('<i class="icon feather icon-save"></i>Save'); 
}
$('#userFormModal').on('hidden.bs.modal', function () {
    clearForm(); 
    console.log('Modal closed and form cleared');
});
$('#addUserBtn').click(function () {
    clearForm(); 
    $('#saveBtn').data('mode', 'save'); 
    $('#saveBtn').html('<i class="icon feather icon-save"></i>Save');
    $('#tableSection').hide(); 
    $('#userFormModal').modal('show'); 
});

$('.cls-card').click(function () {
    $('#name').val(''); 
    $('#username').val(''); 
    $('#userimagePreview').hide().attr('src', '');
    user_type.clear();
    $('#saveBtn')
        .removeData('id') 
        .removeData('mode')
        .html('<i class="icon feather icon-save"></i>Save');
    $('#userFormModal').modal('hide');
    $('#tableSection').fadeIn();
});


$(document).on('click', '.editrec', function () {
    let id = $(this).parents('tr').data('id');

    fetch(`php/user/user.php?id=${id}`)
        .then(resp => {
            if (!resp.ok) {
                throw new Error('Failed to fetch user data');
            }
            return resp.json();
        })
        .then((data) => {
            clearForm();
            $('#name').val(data.name);
            $('#username').val(data.username);
            if (data.image) {
                $('#userimagePreview')
                    .attr('src', `${data.image}`)
                    .show();
            } else {
                $('#userimagePreview').hide();
            };
            if (data.userType) {
                user_type.setData([{ value: data.userType.id, text: data.userTypeName }]);
                user_type.set(data.userType.id);
            };

            $('#saveBtn').data('mode', 'update');
            $('#saveBtn').html('<i class="icon feather icon-save"></i>Update');
            $('#saveBtn').data('id', id);
            $('#userFormModal').modal('show');
            $('#tableSection').hide();
        })
        .catch((error) => {
            console.error('Error:', error);
            alert('An error occurred while fetching the user data. Please try again.');
        });
});


document.getElementById('saveBtn').addEventListener('click', function () {
    clearForm();
    var name = document.getElementById('name').value.trim();
    var username = document.getElementById('username').value.trim();

    var ut = document.getElementById('user_type').value;
    if (ut == 'admin') {
        var userType = 1;
    } else {
        var userType = 2;
    }
    var imageInput = document.getElementById('userimage');

    if (name === '') {
        Swal.fire("Empty Name!", "Please Enter a Valid Name!", "warning");
        return;
    }
    if (username === '') {
        Swal.fire("Empty Username!", "Please Enter a Valid Username!", "warning");
        return;
    }
    if (userType === null || userType === '') {
        Swal.fire("UserType not Selected!", "Please Select a UserType!", "warning");
        return;
    }
    if (imageInput.files.length === 0) {
        Swal.fire("No Image!", "Please Upload an Image for the Menu!", "warning");
        return;
    }

    let mode = $('#saveBtn').data('mode');
    console.log(mode);
    const formData = new FormData();
    if (mode === 'update') {
        formData.append('id', $('#saveBtn').data('id'));
    }
    formData.append('name', name);
    formData.append('username', username);
    if ($('#user_type').val() == 'admin') {
        formData.append('type', 1);
    } else {
        formData.append('type', 2);
    }
    formData.append('image', imageInput.files[0]);


    Swal.fire({
        title: 'Are you sure?',
        text: "User Will be " + (mode === 'update' ? 'Updated' : 'Saved') + " !",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Continue!',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return fetch('php/user/' + mode + '-user.php', {
                method: 'POST',
                body: formData
            }).then(response => {
                if (!response.ok) {
                    throw new Error(response.statusText);
                }
                return response.json();
            }).catch(error => {
                Swal.showValidationMessage('Request failed: ' + error);
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.value) {
            if (result.value.status !== 200) {
                Swal.fire('Error!', result.value.msg, 'error');
            } else {
                Swal.fire('Successfull!', 'User has been updated.', 'success');
                dtable.ajax.reload();
                clearForm()
                $('#userFormModal').modal('hide');
                document.getElementById('tableSection').style.display = 'block';
            }
        }
    });
});

//delete user
$(document).on('click', '.delrec', function () {
    if (!dtable) {
        console.error("DataTable is not initialized yet.");
        return;
    }
    let id = $(this).parents('tr').data('id');
    console.log(id);
    Swal.fire({
        title: 'Are you sure?',
        text: "This User Will be Deleted!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Proceed!',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return fetch('php/user/deactivate-user.php', {
                method: 'POST',
                body: new URLSearchParams({
                    id: id
                })
            }).then(response => {
                if (!response.ok) {
                    throw new Error(response.statusText);
                }
                return response.json();
            }).catch(error => {
                Swal.showValidationMessage('Request failed:' + error);
            });
        },
        allowOutsideClick: () => !Swal.isLoading()

    }).then((result) => {
        if (result.value) {
            if (result.value.status !== 200) {
                Swal.fire('Error!', result.value.msg, 'error');
            } else {
                Swal.fire('Successfull!', 'User has been Deactivated !', 'success');
                dtable.ajax.reload(null, false);

                $('#userFormModal').modal('hide');
                document.getElementById('tableSection').style.display = 'block';
            }
        }
    });
});

//re rec
$(document).on('click', '.rerec', function () {
    let id = $(this).parents('tr').data('id');
    Swal.fire({
        title: 'Are you sure?',
        text: "This User Will be Activated!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Proceed!',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return fetch('php/user/reactivate-user.php', {
                method: 'POST',
                body: new URLSearchParams({
                    id: id
                })
            }).then(response => {
                if (!response.ok) {
                    throw new Error(response.statusText);
                }
                return response.json();
            }).catch(error => {
                Swal.showValidationMessage('Request failed:' + error);
            });
        },
        allowOutsideClick: () => !Swal.isLoading()

    }).then((result) => {
        if (result.value) {
            if (result.value.status !== 200) {
                Swal.fire('Error!', result.value.msg, 'error');
            } else {
                Swal.fire('Successfull!', 'User has been Activated !', 'success');
                dtable.ajax.reload();
                $('#formSection').hide();
                $('#tableSection').fadeIn();
            }
        }
    });
});