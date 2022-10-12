<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width"/>
    <title>{{$data['title']}}</title>
    <style type="text/css">
        body {background-color: #ffffff; font-family: "Helvetica Neue Light", "HelveticaNeue-Light", "Helvetica Neue", Calibri, Helvetica, Arial, sans-serif;}
        @media only screen and (min-device-width: 601px) {
            .wrapper {width: 600px !important; margin: 0 auto}
        }
        table, tbody, tr, td,p {margin: 0; padding: 0}
        table {border-spacing: 0}

        td {
            padding-left: 34px;
            padding-right: 34px;
        }

        p {
            font-size:20px;
            text-align: justify;
        }
    </style>
</head>
<body style="margin: 0; padding: 0">
<!--[if (gte mso 9)|(IE)]>
<table width="600" align="center" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td>
<![endif]-->
<table class="wrapper" style="max-width: 600px; margin: 0 auto">
    <tr>
        <td>
            <table style="width: 100%">
                <tbody>
                <tr>
                    <td style="background-color: {{env('BACKGROUND_COLOR')}}; padding-bottom: 10px; padding-top: 10px;">
                        <p style="font-size:28px;"><img style="width: 50px;" src="{{ secure_url("public/uploads/images/logo_new.png") }}" /></p>
                    </td>
                </tr>
                </tbody>
            </table>

            <table style="width: 100%;">
                <tbody>
                <tr>
                    <td style="padding-bottom: 23px; padding-top: 23px;">
                        <p>Hello <strong>{{$data['user']->full_name}}</strong>,</p>
                    </td>
                </tr>
                </tbody>
            </table>

            <table style="width: 100%;">
                <tbody>
                <tr>
                    <td style="padding-bottom: 23px; padding-top: 23px;">
                        <p>Thank you for bartering with us. Your payment is processed succeffuly and your post will be reviewed within 24 hours. You will be updated once it is live.</p><br>
                        <p>Post Category: <strong>{{$data['post']->category->name_en}}</strong></p><br>
                        <p>Post SubCategory: <strong>{{$data['post']->subcategory->name_en}}</strong></p><br>
                        <p>Post Title: <strong>{{$data['post']->name_en}}</strong></p><br>
                        <p>Post Description: <strong>{{$data['post']->description_en}}</strong></p><br>
                        <p>Post Location: <strong>{{$data['post']->sub_region_id ? $data['post']->sub_region->name_en .','.: ""}} {{$data['post']->city->name_en}}, {{$data['post']->location->name_en}}</strong></p><br>
                        <p>Post Price in AED: <strong>{{$data['post']->price}}</strong></p><br>
                        <p>Payment Amount (incl. VAT): <strong>{{$data['post']->category->price}}</strong></p><br>
                    </td>
                </tr>
                </tbody>
            </table>

            <table style="width: 100%; margin-top: 30px; padding-top: 20px; padding-bottom: 20px; background-color: {{env('BACKGROUND_COLOR')}}; color: #ffffff;">
                <tbody>
                <tr>
                    <td>
                        Sincerely,
                    </td>
                </tr>
                </tbody>
            </table>
            <!-- end -->
        </td>
    </tr>
</table>
<!--[if (gte mso 9)|(IE)]>
</td>
</tr>
</table>
<![endif]-->
</body>
</html>