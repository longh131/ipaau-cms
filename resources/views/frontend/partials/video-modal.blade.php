<div
    class="cms-video-modal"
    data-video-modal
    aria-hidden="true"
>
    <div class="cms-video-modal__backdrop" data-video-modal-close tabindex="-1"></div>
    <div
        class="cms-video-modal__dialog"
        role="dialog"
        aria-modal="true"
        aria-labelledby="cms-video-modal-title"
    >
        <div class="cms-video-modal__header">
            <h2 id="cms-video-modal-title" class="cms-video-modal__title">视频播放</h2>
            <button type="button" class="cms-video-modal__close" data-video-modal-close aria-label="关闭">×</button>
        </div>
        <div class="cms-video-modal__player">
            <video
                class="cms-video-modal__video"
                controls
                playsinline
                preload="metadata"
            >
                <source src="" type="video/mp4">
            </video>
        </div>
    </div>
</div>

@once
    @push('scripts')
        <script src="{{ asset('assets/js/video-modal.js') }}" defer></script>
    @endpush
@endonce
