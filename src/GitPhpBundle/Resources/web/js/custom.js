/**
 * Created by klemens on 07/09/16.
 */


// Collapsing all folders by default
$(".list-folder-container").hide();
$(".file-options").hide();

// Toggle folder visibility on click
$(".list-folder-header").click(function () {
    var id = this.id.substr(7);
    $("#list_" + id).toggle();
});

// Toggle folder visibility on click
$(".file-element").hover(function () {
    $(this).find(".file-options").show();
}, function() {
    $(this).find(".file-options").hide();
});

// Edit file action
$(".edit-action").click(function () {
    //Get file path from parent node
    var filePath = $(this).parent().data("path");
    var fileBody = "";
    $('#editFileNameLabel').text(filePath);
    $('#editFileNameInput').val(filePath);
    // Perform ajax request to get file body
    $.ajax({
        method: "POST",
        url: "/ajax/getFileBody",
        data: {filePath: filePath}
    })
        // On successful
        .success(function(data) {
            // ... and data-correct ajax
            if (data.status == "OK") {
                $('#editFileBodyInput').val(data.fileBody);
                // ... show modal with file name and body input
                $('#editFileModal').modal();
            } else if (data.status == "ERROR" ) {
                alert(data.error);
            }
        });
});

$(".remove-action").click(function() {
    console.log(this);
});