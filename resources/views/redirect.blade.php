<html>
<title>please wait ...</title>
{{$refId}}
{{$mobile}}
<body>
    <script>
        var form = document.createElement("form");
        	form.setAttribute("method", "POST");
        	form.setAttribute("action", "https://bpm.shaparak.ir/pgwchannel/startpay.mellat");
        	form.setAttribute("target", "_self");

            var refId = document.createElement("input");
            var mobile = document.createElement("input");
        	refId.setAttribute("name", "RefId");
        	refId.setAttribute("value", "{{$refId}}");

            mobile.setAttribute("name", "MobileNo");
        	mobile.setAttribute("value", "{{$mobile}}");

            form.appendChild(refId);
            form.appendChild(mobile);

        	document.body.appendChild(form);
        	form.submit();
        	document.body.removeChild(form);
    </script>
</body>

</html>
