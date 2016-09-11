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
}, function () {
    $(this).find(".file-options").hide();
});

// Edit file action
$(".edit-action").click(function () {
    //Get file path from parent node
    var filePath = $(this).parent().data("path");
    $('#editFileNameLabel').text(filePath);
    $('#editFileNameInput').val(filePath);
    // Perform ajax request to get file body
    $.ajax({
            method: "POST",
            url: "/ajax/getFileBody",
            data: {filePath: filePath}
        })
        // On successful
        .success(function (data) {
            // ... and data-correct ajax
            if (data.status == "OK") {
                $('#editFileBodyInput').val(data.fileBody);
                // ... show modal with file name and body input
                $('#editFileModal').modal();
            } else if (data.status == "ERROR") {
                alert(data.error);
            }
        });
});

// Remove file action
$(".remove-action").click(function () {
    var filePath = $(this).parent().data("path");
    $('#removeFileNameLabel').text(filePath);
    $('#removeFileNameInput').val(filePath);
    $('#removeFileModal').modal();
});


// Merge request action
$("#mergeRequestButton").click(function () {
    console.log(this);
    $.ajax({
            method: "POST",
            url: "/ajax/getBranches"
        })
        .success(function (data) {
            if (data.status == "OK") {
                var branchesOptions = "";
                for (var i = 0; i < data.branches.length; i++) {
                    var disabled = "";
                    if (data.branches[i].substr(0, 1) == "*") {
                        disabled = "disabled";
                    }
                    branchesOptions += "<option" + disabled + ">" + data.branches[i] + "</option>";
                }
                $("#branchesListSelect").html(branchesOptions);

            } else if (data.status == "ERROR") {
                alert(data.error);
            }
        });
    $.ajax({
            method: "POST",
            url: "/ajax/getActiveBranch"
        })
        .success(function (data) {
            if (data.status == "OK") {
                $("#mergeRequestNameLabel").text(data.branch);
                $("#mergeRequestNameInput").val(data.branch);

            } else if (data.status == "ERROR") {
                alert(data.error);
            }
        });


    $('#mergeRequestModal').modal();

});