/**
 * Created by klemens on 07/09/16.
 */


// Collapsing all folders by default
$(".list-folder-container").hide();

// Toggle folder visibility on click
$(".list-folder-header").click(function () {
    console.log(this);
    var id = this.id.substr(7);
    $("#list_" + id).toggle();
});