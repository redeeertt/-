class MusicPlayer {
    constructor() {
        this.audio = new Audio();
        this.currentTrack = null;
        this.isPlaying = false;
        
        // Инициализация элементов управления
        this.initControls();
        
        // Обработчики событий
        this.audio.addEventListener('timeupdate', () => this.updateProgress());
        this.audio.addEventListener('ended', () => this.nextTrack());
        this.audio.addEventListener('play', () => {
            this.isPlaying = true;
            this.updateControls();
        });
        this.audio.addEventListener('pause', () => {
            this.isPlaying = false;
            this.updateControls();
        });
    }

    initControls() {
        this.playBtn = document.getElementById('play-btn');
        this.pauseBtn = document.getElementById('pause-btn');
        this.progressBar = document.querySelector('.progress-bar');
        this.nowPlaying = document.getElementById('now-playing');
        
        if (this.playBtn) {
            this.playBtn.addEventListener('click', () => this.play());
        }
        
        if (this.pauseBtn) {
            this.pauseBtn.addEventListener('click', () => this.pause());
        }
    }

    playTrack(filePath, trackName) {
        try {
            // Проверяем доступность файла
            fetch(filePath)
                .then(response => {
                    if (!response.ok) throw new Error('Файл не найден');
                    return response.blob();
                })
                .then(blob => {
                    this.currentTrack = {
                        path: filePath,
                        name: trackName
                    };
                    
                    this.audio.src = URL.createObjectURL(blob);
                    this.nowPlaying.textContent = trackName;
                    return this.audio.play();
                })
                .catch(error => {
                    console.error('Ошибка загрузки трека:', error);
                    alert('Не удалось загрузить трек: ' + error.message);
                });
        } catch (error) {
            console.error('Ошибка воспроизведения:', error);
            alert('Ошибка: ' + error.message);
        }
    }

    play() {
        if (this.audio.src) {
            this.audio.play();
        } else if (this.currentTrack) {
            this.playTrack(this.currentTrack.path, this.currentTrack.name);
        }
    }

    pause() {
        this.audio.pause();
    }

    updateControls() {
        if (this.playBtn && this.pauseBtn) {
            this.playBtn.style.display = this.isPlaying ? 'none' : 'block';
            this.pauseBtn.style.display = this.isPlaying ? 'block' : 'none';
        }
    }

    updateProgress() {
        if (this.progressBar) {
            const progress = (this.audio.currentTime / this.audio.duration) * 100 || 0;
            this.progressBar.style.width = `${progress}%`;
        }
    }

    nextTrack() {
        // Реализация переключения на следующий трек
        console.log('Трек завершен, можно включить следующий');
    }
}

// Инициализация плеера при загрузке страницы
document.addEventListener('DOMContentLoaded', () => {
    window.player = new MusicPlayer();
    console.log('Аудиоплеер инициализирован');
});