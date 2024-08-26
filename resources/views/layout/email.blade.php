<!DOCTYPE HTML>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="x-apple-disable-message-reformatting">
  <meta name="format-detection" content="date=no">
  <meta name="format-detection" content="telephone=no">
  <style type="text/CSS"></style>
  <style @import url('https://dopplerhealth.com/fonts/BasierCircle/basiercircle-regular-webfont.woff2');></style>
  <title></title>
  <!--[if mso]>
  <style>
    table {border-collapse:collapse;border-spacing:0;border:none;margin:0;}
    div, td {padding:0;}
    div {margin:0 !important;}
	</style>
  <noscript>
    <xml>
      <o:OfficeDocumentSettings>
        <o:PixelsPerInch>96</o:PixelsPerInch>
      </o:OfficeDocumentSettings>
    </xml>
  </noscript>
  <![endif]-->
  @yield('style')
  <style>
    table,
    td,
    div,
    h1,
    p {
      font-family: 'Basier Circle', 'Roboto', 'Helvetica', 'Arial', sans-serif;
    }

    @media screen and (max-width: 530px) {
      .unsub {
        display: block;
        padding: 8px;
        margin-top: 14px;
        border-radius: 6px;
        background-color: #FFEADA;
        text-decoration: none !important;
        font-weight: bold;
      }

      .button {
        min-height: 42px;
        line-height: 42px;
      }

      .col-lge {
        max-width: 100% !important;
      }
    }

    @media screen and (min-width: 531px) {
      .col-sml {
        max-width: 27% !important;
      }

      .col-lge {
        max-width: 73% !important;
      }
    }
  </style>
</head>

<body style="margin:0;padding:0;word-spacing:normal;background-color:#FDF8F4;">
  <div role="article" aria-roledescription="email" lang="en" style="text-size-adjust:100%;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;background-color:#FDF8F4;">
    <table role="presentation" style="width:100%;border:none;border-spacing:0;">
      <tr>
        <td align="center" style="padding:0;">
          <!--[if mso]>
          <table role="presentation" align="center" style="width:600px;">
          <tr>
          <td>
          <![endif]-->
          <table role="presentation" style="width:94%;max-width:600px;border:none;border-spacing:0;text-align:left;font-family:'Basier Circle', 'Roboto', 'Helvetica', 'Arial', sans-serif;font-size:1em;line-height:1.37em;color:#384049;">
            <!--      Logo headder -->
            <tr>
              <td style="padding:40px 30px 30px 30px;text-align:center;font-size:1.5em;font-weight:bold;">
                <a href="https://edu-all.com" style="text-decoration:none;">
                    <img loading="lazy"  src="{{asset('img/logo.webp')}}" alt="all-inedu" alt="Doppler Health" width="200" style="width:200px;max-width:200px;height:auto;border:none;text-decoration:none;color:#ffffff;" >
                </a>
              </td>
            </tr>
            <!--      Intro Section -->
            <tr>
              <td style="padding:30px;background-color:#ffffff;">
                
                <h1 style="margin-top:0;margin-bottom:1.38em;font-size:1.21em;line-height:1.3;font-weight:bold;letter-spacing:-0.02em; text-align:center">
                    @yield('header')
                </h1>

                @yield('content')
              </td>
            </tr>
            {{-- <tr>
              <td style="padding:30px;text-align:center;font-size: 0.75em;background-color:#ffeada;color:#384049;border: 1em solid #fff;">
                <p style="margin:0 0 0.75em 0;line-height: 0;">
                  <!--      Facebook logo            -->
                  <a href="https://www.facebook.com/allineduspace/" style="display:inline-block;text-decoration:none;margin: 0 5px;">
                    <img loading="lazy"  src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4KPHN2ZyB2aWV3Qm94PSIzNzIuMjUyIC0yNzkuNTEzIDE5NC4yIDE5MyIgd2lkdGg9IjE5NC4yIiBoZWlnaHQ9IjE5MyIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KICA8cGF0aCBkPSJNMTA2MC44LDEwMC44YzAtNTMuNi00My41LTk3LjEtOTcuMS05Ny4xcy05Ny4xLDQzLjUtOTcuMSw5Ny4xYzAsNDguNSwzNS41LDg4LjcsODIsOTUuOXYtNjcuOWgtMjQuOHYtMjhoMjQuOFY3OS40IGMwLTI0LjMsMTQuNC0zNy44LDM2LjYtMzcuOGMxMC42LDAsMjEuNiwyLDIxLjYsMnYyMy43aC0xMi4yYy0xMi4xLDAtMTUuOSw3LjYtMTUuOSwxNS4ydjE4LjJoMjdsLTQuMywyOGgtMjIuN3Y2Ny45IEMxMDI1LjMsMTg5LjUsMTA2MC44LDE0OS4zLDEwNjAuOCwxMDAuOHoiIHN0eWxlPSJmaWxsOiByZ2IoMTc3LCAxODgsIDIwMSk7IiB0cmFuc2Zvcm09Im1hdHJpeCgxLCAwLCAwLCAxLCAtNDk0LjM0NzcxNywgLTI4My4yMTMyNTcpIi8+Cjwvc3ZnPg==" width="20px" height="20px">
                  </a>
                  <!--     Instagram logo               -->
                  <a href="https://www.instagram.com/allineduspace/" style="display:inline-block;text-decoration:none;margin: 0 5px;">
                    <svg viewBox="-448.436 -467.48 999.99 999.997" width="20px" height="20px" alt="i">
                      <path d="M -155.515 -463.983 C -208.715 -461.473 -245.045 -452.983 -276.805 -440.503 C -309.675 -427.693 -337.535 -410.503 -365.255 -382.683 C -392.975 -354.863 -410.045 -326.983 -422.765 -294.063 C -435.075 -262.233 -443.415 -225.873 -445.765 -172.643 C -448.115 -119.413 -448.635 -102.303 -448.375 33.477 C -448.115 169.257 -447.515 186.277 -444.935 239.617 C -442.395 292.807 -433.935 329.127 -421.455 360.897 C -408.625 393.767 -391.455 421.617 -363.625 449.347 C -335.795 477.077 -307.935 494.107 -274.935 506.847 C -243.135 519.137 -206.765 527.517 -153.545 529.847 C -100.325 532.177 -83.195 532.717 52.545 532.457 C 188.285 532.197 205.375 531.597 258.705 529.067 C 312.035 526.537 348.165 518.017 379.945 505.597 C 412.815 492.737 440.685 475.597 468.395 447.757 C 496.105 419.917 513.165 392.017 525.875 359.077 C 538.195 327.277 546.565 290.907 548.875 237.727 C 551.205 184.357 551.755 167.317 551.495 31.557 C 551.235 -104.203 550.625 -121.223 548.095 -174.543 C 545.565 -227.863 537.095 -264.073 524.625 -295.863 C 511.775 -328.733 494.625 -356.563 466.805 -384.313 C 438.985 -412.063 411.065 -429.113 378.135 -441.793 C 346.315 -454.103 309.965 -462.493 256.745 -464.793 C 203.525 -467.093 186.395 -467.683 50.605 -467.423 C -85.185 -467.163 -102.185 -466.583 -155.515 -463.983 M -149.675 439.897 C -198.425 437.777 -224.895 429.677 -242.535 422.897 C -265.895 413.897 -282.535 403.017 -300.115 385.607 C -317.695 368.197 -328.495 351.497 -337.615 328.187 C -344.465 310.547 -352.715 284.107 -354.995 235.357 C -357.475 182.667 -357.995 166.847 -358.285 33.357 C -358.575 -100.133 -358.065 -115.933 -355.755 -168.643 C -353.675 -217.353 -345.525 -243.853 -338.755 -261.483 C -329.755 -284.873 -318.915 -301.483 -301.465 -319.053 C -284.015 -336.623 -267.365 -347.443 -244.035 -356.563 C -226.415 -363.443 -199.975 -371.623 -151.245 -373.943 C -98.515 -376.443 -82.715 -376.943 50.755 -377.233 C 184.225 -377.523 200.065 -377.023 252.815 -374.703 C 301.525 -372.583 328.035 -364.513 345.645 -357.703 C 369.015 -348.703 385.645 -337.893 403.215 -320.413 C 420.785 -302.933 431.615 -286.343 440.735 -262.963 C 447.625 -245.393 455.805 -218.963 458.105 -170.203 C 460.615 -117.473 461.185 -101.663 461.425 31.797 C 461.665 165.257 461.195 181.107 458.885 233.797 C 456.755 282.547 448.675 309.027 441.885 326.687 C 432.885 350.037 422.035 366.687 404.575 384.247 C 387.115 401.807 370.485 412.627 347.145 421.747 C 329.545 428.617 303.075 436.817 254.385 439.137 C 201.655 441.617 185.855 442.137 52.335 442.427 C -81.185 442.717 -96.935 442.177 -149.665 439.897 M 257.935 -234.713 C 258.012 -188.525 308.06 -159.741 348.021 -182.901 C 387.983 -206.062 387.886 -263.797 347.848 -286.824 C 338.712 -292.079 328.353 -294.834 317.815 -294.813 C 284.685 -294.747 257.879 -267.842 257.935 -234.713 M -205.165 33.017 C -204.885 174.817 -89.725 289.507 52.045 289.237 C 193.815 288.967 308.585 173.817 308.315 32.017 C 308.045 -109.783 192.855 -224.503 51.065 -224.223 C -90.725 -223.943 -205.435 -108.763 -205.165 33.017 M -115.105 32.837 C -115.359 -95.465 23.374 -175.929 134.614 -111.997 C 245.854 -48.066 246.171 112.312 135.185 176.683 C 109.877 191.361 81.151 199.119 51.895 199.177 C -40.159 199.371 -114.934 124.891 -115.105 32.837" style="fill: rgb(177, 188, 201);"></path>
                    </svg>
                  </a>
                </p>
                <p style="margin:0;font-size:.75rem;line-height:1.5em;text-align: center; font-weight:bold;">
                    <strong>
                        Edu-ALL
                    </strong>
                </p>
              </td>
            </tr> --}}
          </table>
          <!--[if mso]>
          </td>
          </tr>
          </table>
          <![endif]-->
        </td>
      </tr>
    </table>
  </div>
</body>

</html>