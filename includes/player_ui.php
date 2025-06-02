<div class="player-container fixed-bottom bg-dark text-white py-2">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-4">
                <div id="now-playing" class="text-truncate">Выберите трек</div>
            </div>
            <div class="col-md-4 text-center">
                <button id="play-btn" class="btn btn-success rounded-circle mx-1">
                    <i class="bi bi-play-fill"></i>
                </button>
                <button id="pause-btn" class="btn btn-secondary rounded-circle mx-1" style="display: none;">
                    <i class="bi bi-pause-fill"></i>
                </button>
            </div>
            <div class="col-md-4">
                <div class="progress bg-secondary" style="height: 5px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: 0%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Подключение иконок Bootstrap -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css">