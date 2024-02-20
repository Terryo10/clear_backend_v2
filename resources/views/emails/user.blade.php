<!doctype html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Clear Building Solutions</title>
    <style>
        /* -------------------------------------
          GLOBAL RESETS
      ------------------------------------- */

        /*All the styling goes here*/

        img {
            border: none;
            -ms-interpolation-mode: bicubic;
            max-width: 100%;
        }

        body {
            background-color: #f6f6f6;
            font-family: sans-serif;
            -webkit-font-smoothing: antialiased;
            font-size: 14px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
        }

        table {
            border-collapse: separate;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
            width: 100%;
        }

        table td {
            font-family: sans-serif;
            font-size: 14px;
            vertical-align: top;
        }

        /* -------------------------------------
          BODY & CONTAINER
      ------------------------------------- */

        .body {
            background-color: #f6f6f6;
            width: 100%;
        }

        /* Set a max-width, and make it display as block so it will automatically stretch to that width, but will also shrink down on a phone or something */
        .container {
            display: block;
            margin: 0 auto !important;
            /* makes it centered */
            max-width: 580px;
            padding: 10px;
            width: 580px;
        }

        /* This should also be a block element, so that it will fill 100% of the .container */
        .content {
            box-sizing: border-box;
            display: block;
            margin: 0 auto;
            max-width: 580px;
            padding: 10px;
        }

        /* -------------------------------------
          HEADER, FOOTER, MAIN
      ------------------------------------- */
        .main {
            background: #ffffff;
            border-radius: 3px;
            width: 100%;
        }

        .wrapper {
            box-sizing: border-box;
            padding: 20px;
        }

        .content-block {
            padding-bottom: 10px;
            padding-top: 10px;
        }

        .footer {
            clear: both;
            margin-top: 10px;
            text-align: center;
            width: 100%;
        }

        .footer td,
        .footer p,
        .footer span,
        .footer a {
            color: #999999;
            font-size: 12px;
            text-align: center;
        }

        /* -------------------------------------
          TYPOGRAPHY
      ------------------------------------- */
        h1,
        h2,
        h3,
        h4 {
            color: #000000;
            font-family: sans-serif;
            font-weight: 400;
            line-height: 1.4;
            margin: 0;
            margin-bottom: 30px;
        }

        h1 {
            font-size: 35px;
            font-weight: 300;
            text-align: center;
            text-transform: capitalize;
        }

        p,
        ul,
        ol {
            font-family: sans-serif;
            font-size: 14px;
            font-weight: normal;
            margin: 0;
            margin-bottom: 15px;
        }

        p li,
        ul li,
        ol li {
            list-style-position: inside;
            margin-left: 5px;
        }

        a {
            color: #3498db;
            text-decoration: underline;
        }

        /* -------------------------------------
          BUTTONS
      ------------------------------------- */
        .btn {
            box-sizing: border-box;
            width: 100%;
        }

        .btn>tbody>tr>td {
            padding-bottom: 15px;
        }

        .btn table {
            width: auto;
        }

        .btn table td {
            background-color: #ffffff;
            border-radius: 5px;
            text-align: center;
        }

        .btn a {
            background-color: #ffffff;
            border: solid 1px #3498db;
            border-radius: 5px;
            box-sizing: border-box;
            color: #3498db;
            cursor: pointer;
            display: inline-block;
            font-size: 14px;
            font-weight: bold;
            margin: 0;
            padding: 12px 25px;
            text-decoration: none;
            text-transform: capitalize;
        }

        .btn-primary table td {
            background-color: #3498db;
        }

        .btn-primary a {
            background-color: #3498db;
            border-color: #3498db;
            color: #ffffff;
        }

        /* -------------------------------------
          OTHER STYLES THAT MIGHT BE USEFUL
      ------------------------------------- */
        .last {
            margin-bottom: 0;
        }

        .first {
            margin-top: 0;
        }

        .align-center {
            text-align: center;
        }

        .align-right {
            text-align: right;
        }

        .align-left {
            text-align: left;
        }

        .clear {
            clear: both;
        }

        .mt0 {
            margin-top: 0;
        }

        .mb0 {
            margin-bottom: 0;
        }

        .preheader {
            color: transparent;
            display: none;
            height: 0;
            max-height: 0;
            max-width: 0;
            opacity: 0;
            overflow: hidden;
            mso-hide: all;
            visibility: hidden;
            width: 0;
        }

        .powered-by a {
            text-decoration: none;
        }

        hr {
            border: 0;
            border-bottom: 1px solid #f6f6f6;
            margin: 20px 0;
        }

        /* -------------------------------------
          RESPONSIVE AND MOBILE FRIENDLY STYLES
      ------------------------------------- */
        @media only screen and (max-width: 620px) {
            table.body h1 {
                font-size: 28px !important;
                margin-bottom: 10px !important;
            }

            table.body p,
            table.body ul,
            table.body ol,
            table.body td,
            table.body span,
            table.body a {
                font-size: 16px !important;
            }

            table.body .wrapper,
            table.body .article {
                padding: 10px !important;
            }

            table.body .content {
                padding: 0 !important;
            }

            table.body .container {
                padding: 0 !important;
                width: 100% !important;
            }

            table.body .main {
                border-left-width: 0 !important;
                border-radius: 0 !important;
                border-right-width: 0 !important;
            }

            table.body .btn table {
                width: 100% !important;
            }

            table.body .btn a {
                width: 100% !important;
            }

            table.body .img-responsive {
                height: auto !important;
                max-width: 100% !important;
                width: auto !important;
            }
        }

        /* -------------------------------------
          PRESERVE THESE STYLES IN THE HEAD
      ------------------------------------- */
        @media all {
            .ExternalClass {
                width: 100%;
            }

            .ExternalClass,
            .ExternalClass p,
            .ExternalClass span,
            .ExternalClass font,
            .ExternalClass td,
            .ExternalClass div {
                line-height: 100%;
            }

            .apple-link a {
                color: inherit !important;
                font-family: inherit !important;
                font-size: inherit !important;
                font-weight: inherit !important;
                line-height: inherit !important;
                text-decoration: none !important;
            }

            #MessageViewBody a {
                color: inherit;
                text-decoration: none;
                font-size: inherit;
                font-family: inherit;
                font-weight: inherit;
                line-height: inherit;
            }

            .btn-primary table td:hover {
                background-color: #34495e !important;
            }

            .btn-primary a:hover {
                background-color: #34495e !important;
                border-color: #34495e !important;
            }
        }
    </style>
</head>

<body>
    <span class="preheader">This is preheader text. Some clients will show this text as a preview.</span>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
        <tr>
            <td>&nbsp;</td>
            <td class="container">
                <div class="content">

                    <!-- START CENTERED WHITE CONTAINER -->
                    <table role="presentation" class="main">

                        <!-- START MAIN CONTENT AREA -->
                        <tr>
                            <td class="wrapper">
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td>
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                xmlns:xlink="http://www.w3.org/1999/xlink" width="146" height="43"
                                                viewBox="0 0 146 43" fill="none">
                                                <rect width="146" height="43" fill="url(#pattern0)" />
                                                <defs>
                                                    <pattern id="pattern0" patternContentUnits="objectBoundingBox"
                                                        width="1" height="1">
                                                        <use xlink:href="#image0_186_19410"
                                                            transform="matrix(0.0008 0 0 0.00271628 0 -0.00930233)" />
                                                    </pattern>
                                                    <image id="image0_186_19410" width="1250" height="375"
                                                        xlink:href="data:image/png;base64,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" />
                                                </defs>
                                            </svg>
                                            <p>Hi {{ $user->first_name }} {{ $user->last_name }},</p>
                                            <p>{{ $messagep }}.</p>
                                            <table role="presentation" border="0" cellpadding="0" cellspacing="0"
                                                class="btn btn-primary">
                                                <tbody>
                                                    <tr>
                                                        <td align="left">
                                                            <table role="presentation" border="0" cellpadding="0"
                                                                cellspacing="0">
                                                                <tbody>
                                                                    <tr>
                                                                        <td> <a href="http://app.clearbuildingsolutions.com"
                                                                                target="_blank">Visit Clear Web</a>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <p></p>
                                            <p>Thank You.</p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <!-- END MAIN CONTENT AREA -->
                    </table>
                    <!-- END CENTERED WHITE CONTAINER -->

                    <!-- START FOOTER -->
                    <div class="footer">
                        <table role="presentation" border="0" cellpadding="0" cellspacing="0">

                            <tr>
                                <td class="content-block powered-by">
                                    Powered by <a href="http://app.clearbuildingsolutions.com/">Clear Building
                                        Solutions</a>.
                                </td>
                            </tr>
                        </table>
                    </div>
                    <!-- END FOOTER -->
                </div>
            </td>
            <td>&nbsp;</td>
        </tr>
    </table>
</body>

</html>
