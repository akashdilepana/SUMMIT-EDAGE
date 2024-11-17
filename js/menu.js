$('#addMenuBtn').click(function () {
    $('#saveMenuBtn').data('mode', 'save');
    $('#saveMenuBtn').html('<i class="icon feather icon-save"></i>Save');
    clearForm();
    // $('#menu-table').hide();
    $('#menumodal').modal('show');
});
$('.cls-card').click(function () {
    $('#menu-table').hide();
    $('#dashboard').fadeIn();
});
function clearForm() {
    $('#formSection').find('input[type!=search]').val('');
    $('#formSection').find('select').each(function () {
        if ($(this).data('select')) {
            if ($(this).data('select').ajax) {
                $(this).data('select').data.data = [];
            }
            $(this).data('select').set('');
        }
    });
}

$(document).on('click', '.editrecmenu', function () {
    let id = $(this).parents('tr').data('id');

    fetch(`php/menu/menu.php?id=${id}`)
        .then(resp => {
            if (!resp.ok) {
                throw new Error('Failed to fetch user data');
            }
            return resp.json();
        })
        .then((data) => {
            clearForm();
            $('#menuname').val(data.name);
            $('#price').val(data.price);
            $('#description').val(data.description);
            if (data.image) {
                $('#imagePreview')
                    .attr('src', `${data.image}`) 
                    .show();
            } else {
                $('#imagePreview').hide();
            }
            
            $('#saveMenuBtn').data('mode', 'update');
            $('#saveMenuBtn').html('<i class="icon feather icon-save"></i>Update');
            $('#saveMenuBtn').data('id', id);

            $('#menumodal').modal('show');
            // $('#menu-table').hide();
        })
        .catch((error) => {
            console.error('Error:', error);
            alert('An error occurred while fetching the user data. Please try again.');
        });
});


document.getElementById('saveMenuBtn').addEventListener('click', function () {
    var name = document.getElementById('menuname').value.trim();
    var price = document.getElementById('price').value.trim();
    var description = document.getElementById('description').value.trim();
    var imageInput = document.getElementById('image'); 

    if (name === '') {
        Swal.fire("Empty Name!", "Please Enter a Valid Name!", "warning");
        return;
    }
    if (description === '') {
        Swal.fire("Empty description!", "Please Enter a Valid description!", "warning");
        return;
    }
    if (price === '') {
        Swal.fire("Empty price!", "Please Enter a Valid price!", "warning");
        return;
    }
    if (imageInput.files.length === 0) {
        Swal.fire("No Image!", "Please Upload an Image for the Menu!", "warning");
        return;
    }

    let mode = $('#saveMenuBtn').data('mode');
    const formData = new FormData();

    if (mode === 'update') {
        formData.append('id', $('#saveMenuBtn').data('id'));
    }
    formData.append('name', name);
    formData.append('price', price);
    formData.append('description', description);

    formData.append('image', imageInput.files[0]);
    
    Swal.fire({
        title: 'Are you sure?',
        text: "Menu Will be " + (mode === 'update' ? 'Updated' : 'Saved') + " !",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Continue!',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return fetch('php/menu/' + mode + '-menu.php', {
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
                Swal.fire('Successfull!', 'Menu has been updated.', 'success');
                dmtable.ajax.reload();
                $('#menumodal').modal('hide');
                // document.getElementById('tableSection').style.display = 'block';
            }
        }
    });
});

//delete user
$(document).on('click', '.delrecmenu', function () {
    if (!dmtable) {
        console.error("DataTable is not initialized yet.");
        return;
    }
    let id = $(this).parents('tr').data('id');
    console.log(id);
    Swal.fire({
        title: 'Are you sure?',
        text: "This menu Will be Deleted!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Proceed!',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return fetch('php/menu/deactivate-menu.php', {
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
                Swal.fire('Successfull!', 'Menu has been Deactivated !', 'success');
                dmtable.ajax.reload(null, false); // Reload data without resetting pagination

                $('#menumodal').modal('hide');
                document.getElementById('menu-table').style.display = 'block';
            }
        }
    });
});

//re rec
$(document).on('click', '.rerecmenu', function () {
    let id = $(this).parents('tr').data('id');
    Swal.fire({
        title: 'Are you sure?',
        text: "This Menu Will be Activated!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Proceed!',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return fetch('php/menu/reactivate-menu.php', {
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
                Swal.fire('Successfull!', 'Menu has been Activated !', 'success');
                dmtable.ajax.reload();
                $('#menumodal').modal('hide');
                document.getElementById('menu-table').style.display = 'block';
            }
        }
    });
});