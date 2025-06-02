$(document).ready(function() {
    loadAlbums();
});

function loadAlbums() {
    $.ajax({
        url: 'includes/get_albums.php',
        success: function(data) {
            $('#albums-container').html(data);
        }
    });
}