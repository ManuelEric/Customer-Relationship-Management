function changeActiveTool(event) {
    var element = $(event.target).hasClass("tool-button")
      ? $(event.target)
      : $(event.target).parents(".tool-button").first();
    $(".tool-button.active").removeClass("active");
    $(element).addClass("active");
}

function enableSelector(event) {
    event.preventDefault();
    changeActiveTool(event);
    pdf.enableSelector();
}

function enablePencil(event) {
    event.preventDefault();
    changeActiveTool(event);
    pdf.enablePencil();
}

function addImage(axis=null, type=null) {
    // event.preventDefault();
    pdf.addImageToCanvas(axis, type)
}

function deleteSelectedObject(event) {
  event.preventDefault();
  pdf.deleteSelectedObject();
}


function savePDF(method, filename, route=null) {
    // pdf.savePdf();
    pdf.savePdf(method, filename, route); // save with given file name
}

function clearPage() {
    pdf.clearActivePage();
}

function showPdfData() {
    var string = pdf.serializePdf();
    // const obj =JSON.parse(string);
    
    //     console.log(obj[0].objects.length);

    $('#dataModal .modal-body pre').first().text(string);
    PR.prettyPrint();
    $('#dataModal').modal('show');
}


$('html').keyup(function(e){
    if(e.keyCode == 46) {
        deleteSelectedObject(e)
    }
});