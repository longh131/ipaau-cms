<div class="relative">
    <div
        class="blobBackground animated intersecting"
        style="--blobProgress: 0.5"
    >
        <svg xmlns="http://www.w3.org/2000/svg" hidden="">
            <defs>
                <filter id="blob">
                    <fegaussianblur in="SourceGraphic" stdDeviation="10" result="blur"></fegaussianblur>
                    <fecolormatrix
                        in="blur"
                        mode="matrix"
                        values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 18 -8"
                        result="blob"
                    ></fecolormatrix>
                    <feblend in="SourceGraphic" in2="blob"></feblend>
                </filter>
            </defs>
        </svg>
        <div class="blobContainer" id="blobBackground">
            <div class="purple single lowerRight"></div>
            <div class="blue single middleRight"></div>
            <div class="purple single lowerLeft"></div>
            <div class="blue single lowerLeft"></div>
            <div class="blue single topRight"></div>
            <div class="orange double topRight"></div>
            <div class="interactive" style="transform: translate(1839px, 1207px)"></div>
        </div>
    </div>
</div>
