$(function(){

    $('#search').bind('keyup', function(e) {
        var value = $.trim($(this).val());
        if (e.keyCode == 13) {
            if (value == '' || value == null) $('body').unhighlight();
            $('body').unhighlight();
            $('body').highlight(value);
        }
    });

    $('.api-explorer-form').on('submit', function(e){

        e.preventDefault();

        var self = $(this);

        var btn = $(this).find('.generate-response-btn');
        btn.val('Loading...');
        btn.prop('disabled', true);
        self.find('.request-uri, .response-status, .response-header, .response-body').remove();

        var uri = $(this).attr('uri');


        //TODO: Work On This
        var fd = new FormData();
        var parameters = self.find('.parameter-value-text');
        parameters.each(function() {
            fd.append( $(this).attr('name'), $(this).val() );
        });

        var type = self.attr('type');
        var data =	self.serialize();
        var color = self.closest('.doc-box').data('color');
        var headers = self.find('.header-parameter-value-text');
        //TODO: Create your own data without serialize

        $.ajax({
            url: uri,
            type: type,
            dataType: 'json',
            data: data,
            beforeSend: function (request)
            {
                headers.each(function() {
                    request.setRequestHeader($(this).attr('name'), $(this).val());
                });
            },
            success: function(data, status, request)
            {
                body = JSON.stringify(JSON.parse(request.responseText), undefined, 4);
                statusCode = request.statusCode().status;
                statusText = request.statusCode().statusText;
                responseHeaders = request.getAllResponseHeaders();
                updateResponse(self, uri, body, statusCode, statusText, responseHeaders);
                enableBtn(btn);
                refresh_code_prettify();
            },
            error: function (response, status, request)
            {
                enableBtn(btn);

                try {
                    body = JSON.stringify(JSON.parse(response.responseText), undefined, 4);
                } catch (e){
                    body = response.responseText;
                }

                statusCode = response.status;
                statusText = response.statusText;
                responseHeaders = response.getAllResponseHeaders();
                updateResponse(self,uri, body, statusCode, statusText, responseHeaders);
                enableBtn(btn);
                refresh_code_prettify();
            }
        });

        function enableBtn(btn)
        {
            btn.prop('disabled', false);
            btn.val('Generate Example Response');
        }

        function updateResponse(self, requestUri, responseBody, statusCode, statusText, resonseHeaders)
        {
            self.find('.request-uri, .response-status, .response-header, .response-body').remove();
            requestUrl = stringForResponse('Request URL', 'request-uri request request-test', requestUri);
            responseHeaders = stringForResponse('Response Headers', 'response-header response request-test', responseHeaders);
            responseStatus = stringForResponse('Response Status', 'response-status response request-test', statusCode + ' ' + statusText);
            responseBody = stringForResponse('Response Body', 'response-body response request-test', responseBody);

            self.append(requestUrl);
            self.append(responseHeaders);
            self.append(responseStatus);
            self.append(responseBody);
        }

        function stringForResponse(title, className, text)
        {
            var str = 	'<div class="response-block portlet box ' + className + ' ' + color + '">';
            str +=      '<div class="portlet-title">';
            str += 		'<h3>' + title + '</h3>';
            str +=      '</div>';
            str +=      '<div class="portlet-body">';
            str +=		'<div><textarea class="json_code" title>' + text + '</textarea> </div> ';
            str +=      '</div>';
            str +=      '</div>';
            return str;
        }
    });

    var refresh_code_prettify= function() {
        var myTextArea = document.getElementsByClassName('json_code');

        [].forEach.call(myTextArea, function (el) {
            CodeMirror.fromTextArea(el, {
                lineNumbers: true,
                matchBrackets: true,
                styleActiveLine: true,
                theme: "monokai",
                mode: 'javascript',
                readOnly: true
            });
        });

        $('textarea').removeClass('json_code');
    };

    refresh_code_prettify();

});

//# sourceMappingURL=docs.js.map
