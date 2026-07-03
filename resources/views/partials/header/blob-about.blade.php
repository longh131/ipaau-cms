<div class="relative">
    <div class="blobBackground left intersecting">
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
            <div class="purple single"></div>
            <div class="blue single"></div>
            <div class="orange single"></div>
        </div>
    </div>
</div>
