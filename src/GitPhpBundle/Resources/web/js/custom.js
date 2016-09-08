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


$(".edit-action").click(function () {
    $('#editFileModal').modal();
    var fileName = $(this).parent().data("path");
    $('#editFileNameInput').text(fileName);
});