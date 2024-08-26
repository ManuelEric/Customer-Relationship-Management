function showLoading()
{
    Swal.fire({
        width: 100,
        backdrop: '#4e4e4e7d',
        allowOutsideClick: false,
    })
    Swal.showLoading();
}

$('form').submit(function(e) {
    e.preventDefault();
    Swal.fire({
        width: 100,
        backdrop: '#4e4e4e7d',
        allowOutsideClick: false,
    })
    Swal.showLoading();
    this.closest('form').submit();
})
