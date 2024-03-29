<!DOCTYPE html>
<html
lang="en"
xmlns="http://www.w3.org/1999/xhtml"
xmlns:v="urn:schemas-microsoft-com:vml"
xmlns:o="urn:schemas-microsoft-com:office:office"
>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="x-apple-disable-message-reformatting" />
    <title></title>

    <link
    href="https://fonts.googleapis.com/css2?family=Open+Sans&display=swap"
    rel="stylesheet"
    type="text/css"
    />
    <style>
    html,
    body {
        margin: 0 auto !important;
        padding: 0 !important;
        height: 100% !important;
        width: 100% !important;
        font-family: "Open Sans", sans-serif !important;
        font-size: 14px;
        margin-bottom: 10px;
        line-height: 22px;
        color: #526283;
        font-weight: 400;
    }
    * {
        -ms-text-size-adjust: 100%;
        -webkit-text-size-adjust: 100%;
        margin: 0;
        padding: 0;
    }
    table,
    td {
        mso-table-lspace: 0pt !important;
        mso-table-rspace: 0pt !important;
    }
    table {
        border-spacing: 0 !important;
        border-collapse: collapse !important;
        table-layout: fixed !important;
        margin: 0 auto !important;
    }
    table table table {
        table-layout: auto;
    }
    a {
        text-decoration: none;
    }
    img {
        -ms-interpolation-mode: bicubic;
    }
    </style>
</head>

<body
    width="100%"
    style="
    margin: 0;
    padding: 0 !important;
    mso-line-height-rule: exactly;
    background-color: #000;
    "
>
    <center style="width: 100%; background-color: #000">
    <table
        width="100%"
        border="0"
        cellpadding="0"
        cellspacing="0"
        bgcolor="#000"
    >
        <tr>
        <td style="padding: 40px 0">
            <table style="width: 100%; max-width: 620px; margin: 0 auto">
            <tbody>
                <tr>
                <td style="text-align: center; padding-bottom: 25px">
                    <a href="#"
                    ><img
                        style="height: 40px"
                        src="{{env('APP_LOGO')}}"
                        alt="logo"
                    /></a>
                </td>
                </tr>
            </tbody>
            </table>
            <table
            style="
                width: 100%;
                max-width: 620px;
                margin: 0 auto;
                background-color: #ffffff;
                border: 1px solid #e3edf8;
                border-bottom: 4px solid #16a2fd;
            "
            >
            <tbody>
                <tr>
                <td style="padding: 30px 30px 20px">
                    <p style="margin-bottom: 10px; text-transform:capitalize;">
                    Hello {{$name}},
                    </p>
                    <p style="margin-bottom: 8px">
                    We are excited to have you here. Click the button below to verify your email;
                    </p>
                    <p><a style="margin-bottom: 10px; background:#16a2fd; color:#fff; padding:10px 20px;" href="{{$verifyToken}}">
                        Verify Me
                    </a></p>
                    <p style="margin-top: 10px">
                        for further enquiries visit us @
                        <a href="https://www.lms.io"
                            >lms.io</a
                        >
                    </p>
                </td>
                </tr>
            </tbody>
            </table>
            <table style="width: 100%; max-width: 620px; margin: 0 auto">
            <tbody>
                <tr>
                <td style="text-align: center; padding: 25px 0 0">
                    <p style="font-size: 17px; color:#fff;">{{env('APP_NAME')}}</p>

                </td>
                </tr>
            </tbody>
            </table>
        </td>
        </tr>
    </table>
    </center>
</body>
</html>
