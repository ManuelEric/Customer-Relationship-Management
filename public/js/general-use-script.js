function showLoading()
{
    Swal.fire({
        width: 100,
        backdrop: '#4e4e4e7d',
        allowOutsideClick: false,
    })
    Swal.showLoading();
}

$('form:not(#live)').submit(function(e) {
    e.preventDefault();
    showLoading()
    this.closest('form').submit();
})
