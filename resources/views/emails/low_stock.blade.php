<!DOCTYPE html>
<html>

<head>
    <title></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <style type="text/css">
        body,
        table,
        td,
        a {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        table,
        td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        img {
            -ms-interpolation-mode: bicubic;
        }

        img {
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }

        table {
            border-collapse: collapse !important;
        }

        body {
            height: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }

        a[x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: none !important;
            font-size: inherit !important;
            font-family: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
        }

        @media screen and (max-width: 480px) {
            .mobile-hide {
                display: none !important;
            }

            .mobile-center {
                text-align: center !important;
            }
        }

        div[style*="margin: 16px 0;"] {
            margin: 0 !important;
        }
    </style>

<body style="margin: 0 !important; padding: 0 !important; background-color: #eeeeee;" bgcolor="#eeeeee">
<div style="display: none; font-size: 1px; color: #fefefe; line-height: 1px; font-family: Open Sans, Helvetica, Arial, sans-serif; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden;">
    TEDMOB
</div>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td align="center" style="background-color: #eeeeee;" bgcolor="#eeeeee">
            <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:600px;">
                <tr>
                    <td align="center" valign="top" style="font-size:0; padding: 35px;"
                        bgcolor="{{env('BACKGROUND_COLOR')}}">
                        {{--<div style="display:inline-block; max-width:50%; min-width:100px; vertical-align:top; width:100%;">--}}
                            {{--<table align="left" border="0" cellpadding="0" cellspacing="0" width="100%"--}}
                                   {{--style="max-width:300px;">--}}
                                {{--<tr>--}}
                                    {{--<td align="left" valign="top"--}}
                                        {{--style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 36px; font-weight: 800; line-height: 48px;"--}}
                                        {{--class="mobile-center">--}}
                                        {{--<h1 style="font-size: 36px; font-weight: 800; margin: 0; color: #ffffff;">NEW ORDER</h1>--}}
                                    {{--</td>--}}
                                {{--</tr>--}}
                            {{--</table>--}}
                        {{--</div>--}}
                        <div style="display:inline-block; max-width:50%; min-width:100px; vertical-align:top; width:100%;"
                             class="mobile-hide">
                            <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%"
                                   style="max-width:300px;">
                                <tr>
                                    <td align="center" valign="top"
                                        style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 48px; font-weight: 400; line-height: 48px;">
                                        <table cellspacing="0" cellpadding="0" border="0" align="center">
                                            <tr>
                                                {{--<td style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400;">--}}
                                                {{--<p style="font-size: 18px; font-weight: 400; margin: 0; color: #ffffff;"><a href="#" target="_blank" style="color: #ffffff; text-decoration: none;">Shop &nbsp;</a></p>--}}
                                                {{--</td>--}}
                                                <td style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 24px;">
                                                    <a href="#" target="_blank"
                                                       style="color: #ffffff; text-decoration: none;"><img
                                                                src="{{secure_url('resources/assets/images/logo_new.png')}}"
                                                                width="80" height="80"
                                                                style="display: block; border: 0px;"/></a></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td align="center" style="padding: 35px 35px 20px 35px; background-color: #ffffff;"
                        bgcolor="#ffffff">
                        <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%"
                               style="max-width:600px;">
                            <tr>
                                <td align="center"
                                    style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 400; line-height: 24px; padding-top: 25px;">
                                    <img src="{{secure_url('public/uploads/images/'.$data['icon'])}}"
                                         width="125" height="120" style="display: block; border: 0px;"/><br>
                                    <h2 style="font-size: 30px; font-weight: 800; line-height: 36px; color: #333333; margin: 0;">{{$data['message']}}</h2>
                                </td>
                            </tr>
                            <tr>
                                <td align="left"
                                    style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 400; line-height: 24px; padding-top: 10px;">
                                    <p style="font-size: 16px; font-weight: 400; line-height: 24px; color: #777777;">
                                        Dear {{env('APP_NAME')}} Team,</p>
                                    <p style="font-size: 16px; font-weight: 400; line-height: 24px; color: #777777;">Please find below all needed details:</p>
                                </td>
                            </tr>
                            <tr>
                                <td align="left" style="padding-top: 20px;">
                                    <table cellspacing="0" cellpadding="0" border="0" width="100%">
                                        <tr>
                                            <td width="25%" align="left" bgcolor="#eeeeee"
                                                style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 800; line-height: 24px; padding: 10px;">
                                                SKU:
                                            </td>
                                            <td width="75%" align="left" bgcolor="#eeeeee"
                                                style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 800; line-height: 24px; padding: 10px;"> {{$data['item']->code}} </td>
                                        </tr>
                                        <tr>
                                            <td width="25%" align="left"
                                                style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 800; line-height: 24px; padding: 5px 10px;">
                                                Name:
                                            </td>
                                            <td width="75%" align="left"
                                                style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 400; line-height: 24px; padding: 5px 10px;"> {{$data['item']->name_en}} </td>
                                        </tr>
                                        <tr>
                                            <td width="25%" align="left"
                                                style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 800; line-height: 24px; padding: 5px 10px;">
                                                Price:
                                            </td>
                                            <td width="75%" align="left"
                                                style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 400; line-height: 24px; padding: 5px 10px;"> {{$data['item']->price}} </td>
                                        </tr>
                                        <tr>
                                            <td width="25%" align="left"
                                                style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 800; line-height: 24px; padding: 5px 10px;">
                                                Category:
                                            </td>
                                            <td width="75%" align="left"
                                                style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 400; line-height: 24px; padding: 5px 10px;"> {{$data['item']->category->name_en}}</td>
                                        </tr>
                                        <tr>
                                            <td width="25%" align="left"
                                                style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 800; line-height: 24px; padding: 15px 10px 5px 10px;">
                                                Brand:
                                            </td>
                                            <td width="75%" align="left"
                                                style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 400; line-height: 24px; padding: 15px 10px 5px 10px;"> @if($data['item']->brand) {{$data['item']->brand->name_en}} @endif </td>
                                        </tr>
                                        <tr>
                                            <td width="25%" align="left"
                                                style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 800; line-height: 24px; padding: 15px 10px 5px 10px;">
                                                Product Type:
                                            </td>
                                            <td width="75%" align="left"
                                                style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 400; line-height: 24px; padding: 15px 10px 5px 10px;"> @if($data['item']->product_type) {{$data['item']->product_type->name_en}} @endif </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td align="left" style="padding-top: 20px;">
                                    <table cellspacing="0" cellpadding="0" border="0" width="100%">
                                        <tr>
                                            <td width="75%" align="left"
                                                style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 800; line-height: 24px; padding: 10px; border-top: 3px solid #eeeeee; border-bottom: 3px solid #eeeeee;">
                                                Remaining Quantity
                                            </td>
                                            <td width="25%" align="left"
                                                style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 800; line-height: 24px; padding: 10px; border-top: 3px solid #eeeeee; border-bottom: 3px solid #eeeeee;"> {{$data['item']->quantity}}</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="center" style=" padding: 35px; background-color: #48A0DB;" bgcolor="#1b9ba3">
                        <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%"
                               style="max-width:600px;">
                            <tr>
                                <td align="center"
                                    style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 400; line-height: 24px; padding-top: 25px;">
                                    <h2 style="font-size: 24px; font-weight: 800; line-height: 30px; color: #ffffff; margin: 0;">
                                        Feel Free to explore the CMS and check item details </h2>
                                </td>
                            </tr>
                            <tr>
                                <td align="center" style="padding: 25px 0 15px 0;">
                                    <table border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td align="center" style="border-radius: 5px;" bgcolor="#66b3b7"><a href="{{$data['item_link']}}"
                                                                                                                target="_blank"
                                                                                                                style="font-size: 18px; font-family: Open Sans, Helvetica, Arial, sans-serif; color: #ffffff; text-decoration: none; border-radius: 5px; background-color: #333333; padding: 15px 30px; border: 1px solid #333333; display: block;">View Item</a></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="center" style="padding: 35px; background-color: #ffffff;" bgcolor="#ffffff">
                        <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%"
                               style="max-width:600px;">
                            <tr>
                                <td align="center"
                                    style="word-break: break-word; vertical-align: top;white-space: nowrap;"
                                    valign="top">
                                    <a href="https://www.facebook.com/" target="_blank"><img alt="Facebook" height="32"
                                                                                             src="{{secure_asset('uploads/images/facebook.png')}}"
                                                                                             style="text-decoration: none; -ms-interpolation-mode: bicubic; height: auto; border: none; display: inline-block;margin:2px"
                                                                                             width="32"/></a>
                                    <a href="https://www.twitter.com/" target="_blank"><img alt="Twitter" height="32"
                                                                                            src="{{secure_asset('uploads/images/twitter.png')}}"
                                                                                            style="text-decoration: none; -ms-interpolation-mode: bicubic; height: auto; border: none; display: inline-block;margin:2px"
                                                                                            width="32"/></a>
                                    <a href="https://www.instagram.com/" target="_blank"><img alt="Instagram"
                                                                                              height="32"
                                                                                              src="{{secure_asset('uploads/images/instagram.png')}}"
                                                                                              style="text-decoration: none; -ms-interpolation-mode: bicubic; height: auto; border: none; display: inline-block;margin:2px"
                                                                                              width="32"/></a>
                                    <a href="https://www.google.com/" target="_blank"><img alt="Website" height="32"
                                                                                           src="{{secure_asset('uploads/images/website.png')}}"
                                                                                           style="text-decoration: none; -ms-interpolation-mode: bicubic; height: auto; border: none; display: inline-block;margin:2px"
                                                                                           width="32"/></a>
                                    <a href="https://www.pinterest.com/" target="_blank"><img alt="Pinterest"
                                                                                              height="32"
                                                                                              src="{{secure_asset('uploads/images/pinterest.png')}}"
                                                                                              style="text-decoration: none; -ms-interpolation-mode: bicubic; height: auto; border: none; display: inline-block;margin:2px"
                                                                                              width="32"/></a>
                                    <a href="https://www.linkedin.com/" target="_blank"><img alt="Linkedin" height="32"
                                                                                             src="{{secure_asset('uploads/images/linkedin.png')}}"
                                                                                             style="text-decoration: none; -ms-interpolation-mode: bicubic; height: auto; border: none; display: inline-block;margin:2px"
                                                                                             width="32"/></a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="center" style="padding: 35px; background-color: #ffffff;" bgcolor="#ffffff">
                        <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%"
                               style="max-width:600px;">
                            <tr>
                                <td align="left"
                                    style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 14px; font-weight: 400; line-height: 24px;">
                                    <p style="font-size: 14px; font-weight: 400; line-height: 20px; color: #777777;">
                                        Sincerly, </p>
                                    <p style="font-size: 20px; font-weight: 400; line-height: 20px; color: #777777;"> {{env('APP_NAME')}}
                                        TEAM </p>
                                </td>
                            </tr>

                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>

</html>